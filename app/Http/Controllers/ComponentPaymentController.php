<?php

namespace App\Http\Controllers;

use App\Models\EventComponent;
use App\Models\Registration;
use App\Models\Payment;
use App\Models\OrganizerBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class ComponentPaymentController extends Controller
{
    /**
     * Muestra la página de checkout para un componente (SIN registro previo)
     */
    public function checkoutComponent(EventComponent $component)
    {
        // Verificar que el componente requiere pago
        if (!$component->requiresPayment()) {
            return redirect()->back()
                ->with('error', 'Este componente no requiere pago.');
        }

        // Verificar que el usuario NO esté ya inscrito
        $existingRegistration = Registration::where('user_id', Auth::id())
            ->where('component_id', $component->id)
            ->first();

        if ($existingRegistration) {
            if ($existingRegistration->isPaid()) {
                return redirect()->route('registrations.index')
                    ->with('info', 'Ya estás inscrito en este componente.');
            }
            // Si existe pero no está pagado, puede continuar
        }

        // Calcular las tarifas
        $fees = Payment::calculateFees($component->price);

        return view('payments.checkout', [
            'registration' => null, // No hay registro todavía
            'component' => $component,
            'fees' => $fees,
        ]);
    }

    /**
     * Muestra la página de checkout para un componente (con registro existente - legacy)
     */
    public function checkout(Registration $registration)
    {
        // Verificar que la inscripción pertenece al usuario actual
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para acceder a este pago.');
        }

        // Verificar que el componente requiere pago
        if (!$registration->component->requiresPayment()) {
            return redirect()->route('registrations.index')
                ->with('error', 'Este componente no requiere pago.');
        }

        // Verificar que no esté ya pagado
        if ($registration->isPaid()) {
            return redirect()->route('registrations.index')
                ->with('info', 'Esta inscripción ya está pagada.');
        }

        // Calcular las tarifas
        $fees = Payment::calculateFees($registration->component->price);

        return view('payments.checkout', [
            'registration' => $registration,
            'component' => $registration->component,
            'fees' => $fees,
        ]);
    }

    /**
     * Inicia el proceso de pago con PayPal para un componente (SIN registro previo)
     */
    public function initiatePayPalForComponent(Request $request, EventComponent $component)
    {
        try {
            // Verificar que el componente requiere pago
            if (!$component->requiresPayment()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este componente no requiere pago.'
                ], 400);
            }

            // Verificar que el componente acepta pagos en línea
            if (!$component->acceptsPaymentMethod('online')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este componente no acepta pagos en línea.'
                ], 400);
            }

            // Verificar que NO esté ya inscrito y pagado
            $existingRegistration = Registration::where('user_id', Auth::id())
                ->where('component_id', $component->id)
                ->where('payment_status', 'paid')
                ->first();

            if ($existingRegistration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya estás inscrito y pagado en este componente.'
                ], 400);
            }

            // Calcular las tarifas
            $fees = Payment::calculateFees($component->price);

            // Configurar PayPal
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // Crear la orden de pago en PayPal
            $order = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => 'component_' . $component->id . '_user_' . Auth::id(),
                        'description' => 'Inscripción a: ' . $component->name,
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($fees['total_paid'], 2, '.', ''),
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => 'USD',
                                    'value' => number_format($fees['total_paid'], 2, '.', ''),
                                ],
                            ],
                        ],
                        'items' => [
                            [
                                'name' => $component->name,
                                'description' => 'Componente del evento: ' . $component->event->name,
                                'unit_amount' => [
                                    'currency_code' => 'USD',
                                    'value' => number_format($fees['total_paid'], 2, '.', ''),
                                ],
                                'quantity' => '1',
                            ],
                        ],
                    ],
                ],
                'application_context' => [
                    'cancel_url' => route('payment.cancel.component', $component),
                    'return_url' => route('payment.success.component', $component),
                    'brand_name' => config('app.name'),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

            if (isset($order['id']) && isset($order['status'])) {
                // Buscar la URL de aprobación
                $approval_url = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                if ($approval_url) {
                    // Guardar el order_id en sesión para recuperarlo después
                    session([
                        'paypal_component_order_' . $component->id => [
                            'order_id' => $order['id'],
                            'component_id' => $component->id,
                            'user_id' => Auth::id(),
                            'fees' => $fees,
                        ]
                    ]);

                    return response()->json([
                        'success' => true,
                        'approval_url' => $approval_url,
                        'order_id' => $order['id'],
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden de pago en PayPal.',
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Error al iniciar pago con PayPal: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Inicia el proceso de pago con PayPal (con registro existente - legacy)
     */
    public function initiatePayPalPayment(Request $request, Registration $registration)
    {
        try {
            // Verificar que la inscripción pertenece al usuario actual
            if ($registration->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para realizar este pago.'
                ], 403);
            }

            // Verificar que el componente acepta pagos en línea
            if (!$registration->component->acceptsPaymentMethod('online')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este componente no acepta pagos en línea.'
                ], 400);
            }

            // Verificar que no esté ya pagado
            if ($registration->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta inscripción ya está pagada.'
                ], 400);
            }

            // Calcular las tarifas
            $fees = Payment::calculateFees($registration->component->price);

            // Configurar PayPal
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // Crear la orden de pago en PayPal
            $order = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => 'registration_' . $registration->id,
                        'description' => 'Inscripción a: ' . $registration->component->name,
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($fees['total_paid'], 2, '.', ''),
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => 'USD',
                                    'value' => number_format($fees['total_paid'], 2, '.', ''),
                                ],
                            ],
                        ],
                        'items' => [
                            [
                                'name' => $registration->component->name,
                                'description' => 'Componente del evento: ' . $registration->component->event->name,
                                'unit_amount' => [
                                    'currency_code' => 'USD',
                                    'value' => number_format($fees['total_paid'], 2, '.', ''),
                                ],
                                'quantity' => '1',
                            ],
                        ],
                    ],
                ],
                'application_context' => [
                    'cancel_url' => route('payment.cancel', $registration),
                    'return_url' => route('payment.success', $registration),
                    'brand_name' => config('app.name'),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

            if (isset($order['id']) && isset($order['status'])) {
                // Buscar la URL de aprobación
                $approval_url = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                if ($approval_url) {
                    // Crear el registro de pago en estado pendiente
                    Payment::create([
                        'registration_id' => $registration->id,
                        'user_id' => Auth::id(),
                        'organizer_id' => $registration->component->event->professionalProfile->user_id,
                        'component_id' => $registration->component_id,
                        'paypal_order_id' => $order['id'],
                        'component_price' => $fees['component_price'],
                        'platform_fee' => $fees['platform_fee'],
                        'paypal_fee' => $fees['paypal_fee'],
                        'total_paid' => $fees['total_paid'],
                        'organizer_amount' => $fees['organizer_amount'],
                        'payment_method' => 'online',
                        'status' => 'pending',
                    ]);

                    return response()->json([
                        'success' => true,
                        'approval_url' => $approval_url,
                        'order_id' => $order['id'],
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden de pago en PayPal.',
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Error al iniciar pago con PayPal: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Callback de éxito de PayPal para componente (CREA registro + payment)
     */
    public function paypalSuccessComponent(Request $request, EventComponent $component)
    {
        try {
            // Cargar relaciones necesarias
            $component->load('event.professionalProfile.user');
            
            $token = $request->query('token');

            if (!$token) {
                return redirect()->route('dashboard')
                    ->with('error', 'Token de PayPal no válido.');
            }

            // Recuperar datos de la sesión
            $sessionData = session('paypal_component_order_' . $component->id);

            if (!$sessionData || $sessionData['order_id'] !== $token) {
                return redirect()->route('dashboard')
                    ->with('error', 'No se encontró la orden de pago.');
            }

            // Configurar PayPal
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // Capturar el pago
            $result = $provider->capturePaymentOrder($token);

            if (isset($result['status']) && $result['status'] === 'COMPLETED') {
                DB::transaction(function () use ($component, $result, $sessionData, $token) {
                    // 1. CREAR LA INSCRIPCIÓN
                    $registration = Registration::create([
                        'user_id' => Auth::id(),
                        'component_id' => $component->id,
                        'ticket_qr' => Registration::generateTicketQR(),
                        'registered_at' => now(),
                        'expires_at' => $component->event->end_date->addDays(1),
                        'payment_status' => 'paid',
                        'payment_method' => 'online',
                        'paid_at' => now(),
                    ]);

                    // 2. CREAR EL PAGO
                    Payment::create([
                        'registration_id' => $registration->id,
                        'user_id' => Auth::id(),
                        'organizer_id' => $component->event->professionalProfile->user_id,
                        'component_id' => $component->id,
                        'paypal_order_id' => $token,
                        'paypal_transaction_id' => $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                        'component_price' => $sessionData['fees']['component_price'],
                        'platform_fee' => $sessionData['fees']['platform_fee'],
                        'paypal_fee' => $sessionData['fees']['paypal_fee'],
                        'total_paid' => $sessionData['fees']['total_paid'],
                        'organizer_amount' => $sessionData['fees']['organizer_amount'],
                        'payment_method' => 'online',
                        'status' => 'completed',
                        'funds_released' => true,
                    ]);

                    // 3. AGREGAR FONDOS AL ORGANIZADOR
                    $organizer = $component->event->professionalProfile->user;
                    $balance = $organizer->getOrCreateOrganizerBalance();
                    $balance->addFunds($sessionData['fees']['organizer_amount']);

                    // 4. LIMPIAR SESIÓN
                    session()->forget('paypal_component_order_' . $component->id);

                    // Guardar registration en sesión para la confirmación
                    session(['last_registration_id' => $registration->id]);
                });

                // Obtener el registration recién creado
                $registration = Registration::find(session('last_registration_id'));

                // Si estamos en un popup, cerrar y redirigir al padre
                $successScript = "
                    <script>
                        if (window.opener) {
                            window.opener.location.href = '" . route('payment.confirmation', $registration->id) . "';
                            window.close();
                        } else {
                            window.location.href = '" . route('payment.confirmation', $registration->id) . "';
                        }
                    </script>
                ";
                
                return response($successScript);
            }

            // Si el pago no fue completado, manejar el error
            $errorScript = "
                <script>
                    if (window.opener) {
                        window.opener.location.href = '" . route('dashboard') . "?error=" . urlencode('El pago no pudo ser completado') . "';
                        window.close();
                    } else {
                        window.location.href = '" . route('dashboard') . "?error=" . urlencode('El pago no pudo ser completado') . "';
                    }
                </script>
            ";
            
            return response($errorScript);

        } catch (\Exception $e) {
            \Log::error('Error en callback de PayPal para componente: ' . $e->getMessage());

            // Si estamos en un popup, cerrar y notificar al padre
            $errorScript = "
                <script>
                    if (window.opener) {
                        window.opener.location.href = '" . route('dashboard') . "?error=" . urlencode($e->getMessage()) . "';
                        window.close();
                    } else {
                        window.location.href = '" . route('dashboard') . "?error=" . urlencode($e->getMessage()) . "';
                    }
                </script>
            ";
            
            return response($errorScript);
        }
    }

    /**
     * Callback de éxito de PayPal (con registro existente - legacy)
     */
    public function paypalSuccess(Request $request, Registration $registration)
    {
        try {
            $token = $request->query('token');

            if (!$token) {
                return redirect()->route('registrations.index')
                    ->with('error', 'Token de PayPal no válido.');
            }

            // Buscar el pago pendiente
            $payment = Payment::where('registration_id', $registration->id)
                ->where('paypal_order_id', $token)
                ->where('status', 'pending')
                ->first();

            if (!$payment) {
                return redirect()->route('registrations.index')
                    ->with('error', 'No se encontró el pago pendiente.');
            }

            // Configurar PayPal
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // Capturar el pago
            $result = $provider->capturePaymentOrder($token);

            if (isset($result['status']) && $result['status'] === 'COMPLETED') {
                DB::transaction(function () use ($payment, $result, $registration) {
                    // Actualizar el pago como completado
                    $payment->update([
                        'status' => 'completed',
                        'paypal_transaction_id' => $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                        'funds_released' => true,
                    ]);

                    // Marcar la inscripción como pagada
                    $registration->markAsPaid('online');

                    // Obtener o crear el balance del organizador
                    $organizer = $registration->component->event->professionalProfile->user;
                    $balance = $organizer->getOrCreateOrganizerBalance();

                    // Agregar fondos al balance disponible (liberación inmediata)
                    $balance->addFunds($payment->organizer_amount);
                });

                return redirect()->route('payment.confirmation', $registration)
                    ->with('success', 'Pago completado exitosamente.');
            }

            return redirect()->route('registrations.index')
                ->with('error', 'El pago no pudo ser completado.');

        } catch (\Exception $e) {
            \Log::error('Error en callback de PayPal: ' . $e->getMessage());

            return redirect()->route('registrations.index')
                ->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Callback de cancelación de PayPal para componente
     */
    public function paypalCancelComponent(EventComponent $component)
    {
        // Limpiar datos de sesión
        session()->forget('paypal_component_order_' . $component->id);

        // Si estamos en un popup, cerrar y redirigir al padre
        $cancelScript = "
            <script>
                if (window.opener) {
                    window.opener.location.href = '" . route('payment.checkout.component', $component) . "?warning=" . urlencode('El pago fue cancelado. Puedes intentarlo nuevamente.') . "';
                    window.close();
                } else {
                    window.location.href = '" . route('payment.checkout.component', $component) . "?warning=" . urlencode('El pago fue cancelado. Puedes intentarlo nuevamente.') . "';
                }
            </script>
        ";
        
        return response($cancelScript);
    }

    /**
     * Callback de cancelación de PayPal (con registro existente - legacy)
     */
    public function paypalCancel(Registration $registration)
    {
        // Marcar el pago como fallido si existe
        Payment::where('registration_id', $registration->id)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);

        return redirect()->route('payment.checkout', $registration)
            ->with('warning', 'El pago fue cancelado. Puedes intentarlo nuevamente.');
    }

    /**
     * Procesa un pago en persona (solo para organizadores)
     */
    public function processInPersonPayment(Request $request, Registration $registration)
    {
        try {
            // Verificar que el usuario es el organizador del evento
            if ($registration->component->event->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo el organizador puede confirmar pagos en persona.'
                ], 403);
            }

            // Verificar que el componente acepta pagos en persona
            if (!$registration->component->acceptsPaymentMethod('in_person')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este componente no acepta pagos en persona.'
                ], 400);
            }

            // Verificar que no esté ya pagado
            if ($registration->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta inscripción ya está pagada.'
                ], 400);
            }

            // Calcular las tarifas (aunque sea en persona, seguimos la misma estructura)
            $fees = Payment::calculateFees($registration->component->price);

            DB::transaction(function () use ($registration, $fees) {
                // Crear el registro de pago como completado
                Payment::create([
                    'registration_id' => $registration->id,
                    'user_id' => $registration->user_id,
                    'organizer_id' => $registration->component->event->professionalProfile->user_id,
                    'component_id' => $registration->component_id,
                    'component_price' => $fees['component_price'],
                    'platform_fee' => $fees['platform_fee'],
                    'paypal_fee' => 0, // No hay comisión de PayPal en pagos en persona
                    'total_paid' => $fees['component_price'] + $fees['platform_fee'],
                    'organizer_amount' => $fees['organizer_amount'],
                    'payment_method' => 'in_person',
                    'status' => 'completed',
                ]);

                // Marcar la inscripción como pagada
                $registration->markAsPaid('in_person');

                // Agregar fondos al balance disponible del organizador (pago en persona es inmediato)
                $organizer = $registration->component->event->professionalProfile->user;
                $balance = $organizer->getOrCreateOrganizerBalance();
                $balance->addFunds($fees['organizer_amount']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pago en persona confirmado exitosamente.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al procesar pago en persona: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra la confirmación de pago
     */
    public function confirmation(Registration $registration)
    {
        // Verificar que la inscripción pertenece al usuario actual
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        // Verificar que está pagado
        if (!$registration->isPaid()) {
            return redirect()->route('payment.checkout', $registration)
                ->with('warning', 'Esta inscripción aún no está pagada.');
        }

        return view('payments.confirmation', [
            'registration' => $registration,
            'payment' => $registration->payment,
        ]);
    }
}
