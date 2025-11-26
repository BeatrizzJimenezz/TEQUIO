<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Subscription;
use Carbon\Carbon;

class PayPalController extends Controller
{
    public function index()
    {
        return view('subscriptions.pricing');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,annual'
        ]);

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();

            if (!isset($token['access_token'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener el token de acceso de PayPal.'
                ], 500);
            }

            // 1. Create Product (only once, or reuse existing)
            $product_response = $provider->createProduct([
                "name" => "Tequio Membership",
                "description" => "Access to all Tequio features",
                "type" => "SERVICE",
                "category" => "SOFTWARE"
            ]);

            if (!isset($product_response['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el producto en PayPal',
                    'details' => $product_response
                ], 500);
            }

            $product_id = $product_response['id'];

            // 2. Create Billing Plan
            $price = $request->plan === 'monthly' ? '29.99' : '299.99';

            $plan_data = [
                "product_id" => $product_id,
                "name" => $request->plan === 'monthly' ? 'Plan Mensual Tequio' : 'Plan Anual Tequio',
                "description" => "Acceso completo a las funcionalidades de Tequio",
                "billing_cycles" => [
                    [
                        "frequency" => [
                            "interval_unit" => $request->plan === 'monthly' ? "MONTH" : "YEAR",
                            "interval_count" => 1
                        ],
                        "tenure_type" => "REGULAR",
                        "sequence" => 1,
                        "total_cycles" => 0, // 0 = infinite
                        "pricing_scheme" => [
                            "fixed_price" => [
                                "value" => $price,
                                "currency_code" => "USD"
                            ]
                        ]
                    ]
                ],
                "payment_preferences" => [
                    "auto_bill_outstanding" => true,
                    "setup_fee" => [
                        "value" => $price,
                        "currency_code" => "USD"
                    ],
                    "setup_fee_failure_action" => "CONTINUE",
                    "payment_failure_threshold" => 3
                ]
            ];

            $plan_response = $provider->createPlan($plan_data);

            if (!isset($plan_response['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el plan en PayPal',
                    'details' => $plan_response
                ], 500);
            }

            $plan_id = $plan_response['id'];

            // 3. Create Subscription
            $startTime = $request->plan === 'monthly' 
                ? now()->addMonth()->toIso8601String() 
                : now()->addYear()->toIso8601String();

            $subscription_data = [
                "plan_id" => $plan_id,
                "start_time" => $startTime,
                "subscriber" => [
                    "name" => [
                        "given_name" => $request->user()->name,
                        "surname" => ""
                    ],
                    "email_address" => $request->user()->email
                ],
                "application_context" => [
                    "brand_name" => "TEQUIO",
                    "locale" => "es-SV",
                    "shipping_preference" => "NO_SHIPPING",
                    "user_action" => "SUBSCRIBE_NOW",
                    "payment_method" => [
                        "payer_selected" => "PAYPAL",
                        "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED"
                    ],
                    "return_url" => route('paypal.success'),
                    "cancel_url" => route('paypal.cancel')
                ]
            ];

            $subscription_response = $provider->createSubscription($subscription_data);

            if (!isset($subscription_response['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la suscripción',
                    'details' => $subscription_response
                ], 500);
            }

            // Save plan type and subscription ID in session for later use
            session([
                'paypal_plan_type' => $request->plan,
                'paypal_subscription_id' => $subscription_response['id']
            ]);

            // Find the approval URL
            $approval_url = null;
            if (isset($subscription_response['links'])) {
                foreach ($subscription_response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        $approval_url = $link['href'];
                        break;
                    }
                }
            }

            if (!$approval_url) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró la URL de aprobación de PayPal'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'approval_url' => $approval_url,
                'subscription_id' => $subscription_response['id']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        
        $subscription_id = $request->subscription_id;
        
        // Verify subscription details
        $response = $provider->showSubscriptionDetails($subscription_id);
        
        if (isset($response['status']) && $response['status'] == 'ACTIVE') {
            
            $planType = session('paypal_plan_type', 'monthly');
            
            // Create or Update Subscription in DB
            Subscription::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'paypal_subscription_id' => $subscription_id,
                    'plan_id' => $planType,
                    'status' => 'active',
                    'starts_at' => Carbon::now(),
                    'ends_at' => $planType === 'monthly' ? Carbon::now()->addMonth() : Carbon::now()->addYear(),
                ]
            );

            return redirect()->route('dashboard')->with('success', '¡Suscripción activada correctamente!');
        }

        return redirect()->route('paypal.index')->with('error', 'La suscripción no se pudo activar.');
    }

    public function cancel()
    {
        return redirect()->route('paypal.index')->with('info', 'Has cancelado el proceso de suscripción.');
    }

    public function checkStatus(Request $request)
    {
        // Check if user has active subscription
        $user = $request->user();
        $hasSubscription = $user->hasActiveSubscription();

        return response()->json([
            'subscribed' => $hasSubscription
        ]);
    }
}
