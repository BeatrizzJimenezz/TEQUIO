<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizerFundsController extends Controller
{
    /**
     * Muestra el dashboard de fondos del organizador
     */
    public function index()
    {
        $user = Auth::user();

        // Obtener o crear el balance del organizador
        $balance = $user->getOrCreateOrganizerBalance();

        // Obtener historial de pagos recibidos
        $payments = Payment::where('organizer_id', $user->id)
            ->where('status', 'completed')
            ->with(['registration.user', 'component'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Obtener solicitudes de retiro
        $withdrawals = Withdrawal::where('organizer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Estadísticas
        $stats = [
            'total_earned' => $balance->total_earned,
            'available_balance' => $balance->available_balance,
            'pending_balance' => $balance->pending_balance,
            'total_withdrawn' => $balance->total_withdrawn,
            'pending_withdrawals' => Withdrawal::where('organizer_id', $user->id)
                ->where('status', 'pending')
                ->sum('amount'),
            'payments_count' => Payment::where('organizer_id', $user->id)
                ->where('status', 'completed')
                ->count(),
        ];

        return view('organizer.funds.index', compact('balance', 'payments', 'withdrawals', 'stats'));
    }

    /**
     * Muestra el formulario para solicitar un retiro
     */
    public function createWithdrawal()
    {
        $user = Auth::user();
        $balance = $user->getOrCreateOrganizerBalance();

        $minimumWithdrawal = config('payment.minimum_withdrawal', 5.00);

        // Verificar si hay balance disponible
        if ($balance->available_balance < $minimumWithdrawal) {
            return redirect()->route('organizer.funds.index')
                ->with('error', "Necesitas al menos \${$minimumWithdrawal} para solicitar un retiro.");
        }

        return view('organizer.funds.create-withdrawal', compact('balance', 'minimumWithdrawal'));
    }

    /**
     * Procesa la solicitud de retiro
     */
    public function storeWithdrawal(Request $request)
    {
        $user = Auth::user();
        $balance = $user->getOrCreateOrganizerBalance();

        $minimumWithdrawal = config('payment.minimum_withdrawal', 5.00);

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:' . $minimumWithdrawal,
                'max:' . $balance->available_balance,
            ],
            'paypal_email' => 'required|email|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated, $balance, $user) {
                // Deducir fondos del balance disponible
                if (!$balance->deductFunds($validated['amount'])) {
                    throw new \Exception('Fondos insuficientes.');
                }

                // Crear la solicitud de retiro
                Withdrawal::create([
                    'organizer_id' => $user->id,
                    'amount' => $validated['amount'],
                    'paypal_email' => $validated['paypal_email'],
                    'status' => 'pending',
                    'requested_at' => now(),
                ]);

                // Actualizar el email de PayPal en el balance si es diferente
                if ($balance->paypal_email !== $validated['paypal_email']) {
                    $balance->update(['paypal_email' => $validated['paypal_email']]);
                }
            });

            return redirect()->route('organizer.funds.index')
                ->with('success', 'Solicitud de retiro enviada exitosamente. Será procesada por el administrador.');

        } catch (\Exception $e) {
            \Log::error('Error al procesar solicitud de retiro: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancela una solicitud de retiro pendiente
     */
    public function cancelWithdrawal(Withdrawal $withdrawal)
    {
        // Verificar que el retiro pertenece al usuario actual
        if ($withdrawal->organizer_id !== Auth::id()) {
            abort(403, 'No tienes permiso para cancelar este retiro.');
        }

        // Solo se pueden cancelar retiros pendientes
        if (!$withdrawal->isPending()) {
            return redirect()->route('organizer.funds.index')
                ->with('error', 'Solo puedes cancelar retiros pendientes.');
        }

        try {
            DB::transaction(function () use ($withdrawal) {
                // Devolver los fondos al balance disponible
                $balance = $withdrawal->organizer->getOrCreateOrganizerBalance();
                $balance->increment('available_balance', $withdrawal->amount);
                $balance->decrement('total_withdrawn', $withdrawal->amount);

                // Actualizar el estado del retiro
                $withdrawal->update([
                    'status' => 'rejected',
                    'processed_at' => now(),
                    'notes' => 'Cancelado por el organizador',
                ]);
            });

            return redirect()->route('organizer.funds.index')
                ->with('success', 'Solicitud de retiro cancelada exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al cancelar retiro: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al cancelar la solicitud.');
        }
    }

    /**
     * Muestra el historial detallado de pagos
     */
    public function paymentsHistory()
    {
        $user = Auth::user();

        $payments = Payment::where('organizer_id', $user->id)
            ->where('status', 'completed')
            ->with(['registration.user', 'registration.component.event'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calcular totales
        $totals = [
            'total_amount' => Payment::where('organizer_id', $user->id)
                ->where('status', 'completed')
                ->sum('organizer_amount'),
            'platform_fees' => Payment::where('organizer_id', $user->id)
                ->where('status', 'completed')
                ->sum('platform_fee'),
            'paypal_fees' => Payment::where('organizer_id', $user->id)
                ->where('status', 'completed')
                ->sum('paypal_fee'),
            'online_payments' => Payment::where('organizer_id', $user->id)
                ->where('status', 'completed')
                ->where('payment_method', 'online')
                ->count(),
            'in_person_payments' => Payment::where('organizer_id', $user->id)
                ->where('status', 'completed')
                ->where('payment_method', 'in_person')
                ->count(),
        ];

        return view('organizer.funds.payments-history', compact('payments', 'totals'));
    }

    /**
     * Muestra el historial de retiros
     */
    public function withdrawalsHistory()
    {
        $user = Auth::user();
        $balance = $user->getOrCreateOrganizerBalance();

        $withdrawals = Withdrawal::where('organizer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calcular totales
        $totals = [
            'total_requested' => Withdrawal::where('organizer_id', $user->id)
                ->sum('amount'),
            'total_completed' => Withdrawal::where('organizer_id', $user->id)
                ->where('status', 'completed')
                ->sum('amount'),
            'total_pending' => Withdrawal::where('organizer_id', $user->id)
                ->where('status', 'pending')
                ->sum('amount'),
            'total_rejected' => Withdrawal::where('organizer_id', $user->id)
                ->where('status', 'rejected')
                ->count(),
        ];

        $minimumWithdrawal = config('payment.minimum_withdrawal', 5.00);

        return view('organizer.funds.withdrawals-history', compact('withdrawals', 'totals', 'balance', 'minimumWithdrawal'));
    }
}
