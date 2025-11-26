<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\OrganizerBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class WithdrawalController extends Controller
{
    /**
     * Muestra la lista de solicitudes de retiro
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = Withdrawal::with('organizer')
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $withdrawals = $query->paginate(20);

        // Estadísticas
        $stats = [
            'pending_count' => Withdrawal::where('status', 'pending')->count(),
            'pending_amount' => Withdrawal::where('status', 'pending')->sum('amount'),
            'completed_count' => Withdrawal::where('status', 'completed')->count(),
            'completed_amount' => Withdrawal::where('status', 'completed')->sum('amount'),
            'rejected_count' => Withdrawal::where('status', 'rejected')->count(),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'stats', 'status'));
    }

    /**
     * Muestra los detalles de un retiro
     */
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load('organizer.organizerBalance');

        // Obtener historial de pagos del organizador
        $recentPayments = \App\Models\Payment::where('user_id', $withdrawal->user_id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.withdrawals.show', compact('withdrawal', 'recentPayments'));
    }

    /**
     * Aprueba un retiro y procesa el pago vía PayPal
     */
    public function approve(Request $request, Withdrawal $withdrawal)
    {
        // Verificar que el retiro está pendiente
        if (!$withdrawal->isPending()) {
            return redirect()->back()
                ->with('error', 'Este retiro ya ha sido procesado.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
            'process_method' => 'required|in:paypal,manual',
        ]);

        try {
            if ($validated['process_method'] === 'paypal') {
                // Procesar con PayPal Payouts
                return $this->processPayPalPayout($withdrawal, $validated['notes'] ?? null);
            } else {
                // Aprobación manual (el admin procesa el pago externamente)
                return $this->approveManually($withdrawal, $validated['notes'] ?? null);
            }

        } catch (\Exception $e) {
            \Log::error('Error al aprobar retiro: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al procesar el retiro: ' . $e->getMessage());
        }
    }

    /**
     * Procesa el retiro usando PayPal Payouts API
     */
    protected function processPayPalPayout(Withdrawal $withdrawal, ?string $notes)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // Crear el payout
            $payoutData = [
                'sender_batch_header' => [
                    'sender_batch_id' => 'withdrawal_' . $withdrawal->id . '_' . time(),
                    'email_subject' => 'Has recibido un pago de ' . config('app.name'),
                    'email_message' => 'Tu retiro ha sido procesado exitosamente.',
                ],
                'items' => [
                    [
                        'recipient_type' => 'EMAIL',
                        'amount' => [
                            'value' => number_format($withdrawal->amount, 2, '.', ''),
                            'currency' => 'USD',
                        ],
                        'receiver' => $withdrawal->paypal_email,
                        'note' => 'Retiro de fondos - ' . config('app.name'),
                        'sender_item_id' => 'withdrawal_' . $withdrawal->id,
                    ],
                ],
            ];

            $response = $provider->createBatchPayout($payoutData);

            if (isset($response['batch_header']['batch_status'])) {
                $batchId = $response['batch_header']['payout_batch_id'];

                // Marcar como completado
                $withdrawal->markAsCompleted(
                    $batchId,
                    ($notes ?? '') . "\nProcesado vía PayPal Payouts. Batch ID: {$batchId}"
                );

                return redirect()->route('admin.withdrawals.index')
                    ->with('success', 'Retiro aprobado y procesado vía PayPal exitosamente.');
            }

            throw new \Exception('Respuesta inválida de PayPal: ' . json_encode($response));

        } catch (\Exception $e) {
            // Si falla PayPal, revertir la deducción de fondos
            $balance = $withdrawal->organizer->getOrCreateOrganizerBalance();
            $balance->increment('available_balance', $withdrawal->amount);
            $balance->decrement('total_withdrawn', $withdrawal->amount);

            throw $e;
        }
    }

    /**
     * Aprueba el retiro manualmente (sin usar PayPal Payouts)
     */
    protected function approveManually(Withdrawal $withdrawal, ?string $notes)
    {
        $validated = request()->validate([
            'transaction_id' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($withdrawal, $validated, $notes) {
            $withdrawal->markAsCompleted(
                $validated['transaction_id'],
                ($notes ?? '') . "\nProcesado manualmente por el administrador."
            );
        });

        return redirect()->route('admin.withdrawals.index')
            ->with('success', 'Retiro aprobado manualmente exitosamente.');
    }

    /**
     * Rechaza un retiro
     */
    public function reject(Request $request, Withdrawal $withdrawal)
    {
        // Verificar que el retiro está pendiente
        if (!$withdrawal->isPending()) {
            return redirect()->back()
                ->with('error', 'Este retiro ya ha sido procesado.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($withdrawal, $validated) {
                // Devolver los fondos al balance disponible
                $balance = $withdrawal->organizer->getOrCreateOrganizerBalance();
                $balance->increment('available_balance', $withdrawal->amount);
                $balance->decrement('total_withdrawn', $withdrawal->amount);

                // Marcar como rechazado
                $withdrawal->markAsRejected($validated['reason']);
            });

            return redirect()->route('admin.withdrawals.index')
                ->with('success', 'Retiro rechazado exitosamente. Los fondos han sido devueltos al organizador.');

        } catch (\Exception $e) {
            \Log::error('Error al rechazar retiro: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al rechazar el retiro.');
        }
    }

    /**
     * Muestra el formulario de aprobación manual
     */
    public function showApprovalForm(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isPending()) {
            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('error', 'Este retiro ya ha sido procesado.');
        }

        return view('admin.withdrawals.approve', compact('withdrawal'));
    }

    /**
     * Muestra el formulario de rechazo
     */
    public function showRejectForm(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isPending()) {
            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('error', 'Este retiro ya ha sido procesado.');
        }

        return view('admin.withdrawals.reject', compact('withdrawal'));
    }
}
