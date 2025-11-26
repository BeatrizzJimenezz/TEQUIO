<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\OrganizerBalance;
use Carbon\Carbon;

class ReleasePendingFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:release-pending-funds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera fondos pendientes de pagos en línea después del período de retención';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $holdingPeriod = config('payment.funds_holding_period', 7);
        $releaseDate = Carbon::now()->subDays($holdingPeriod);

        $this->info("Buscando pagos en línea completados antes de: " . $releaseDate->format('Y-m-d H:i:s'));

        // Obtener pagos en línea completados que aún no han sido liberados
        $payments = Payment::where('payment_method', 'online')
            ->where('status', 'completed')
            ->where('created_at', '<=', $releaseDate)
            ->where('funds_released', false)
            ->get();

        if ($payments->isEmpty()) {
            $this->info("No hay pagos pendientes de liberar.");
            return 0;
        }

        $this->info("Encontrados {$payments->count()} pagos para liberar fondos.");

        $releasedCount = 0;
        $releasedAmount = 0;

        foreach ($payments as $payment) {
            try {
                // Obtener el balance del organizador
                $balance = OrganizerBalance::where('user_id', $payment->organizer_id)->first();

                if (!$balance) {
                    $this->warn("Balance no encontrado para organizador ID: {$payment->organizer_id}");
                    continue;
                }

                // Verificar si hay suficientes fondos pendientes para liberar
                // Nota: Incluso si no hay fondos pendientes (por algún error manual previo),
                // si el pago no ha sido marcado como liberado, deberíamos intentar "liberarlo"
                // o al menos marcarlo como liberado si el balance pendiente es 0.
                // Pero para seguridad, mantenemos la verificación.
                
                if ($balance->pending_balance >= $payment->organizer_amount) {
                    // Liberar los fondos
                    $balance->releasePendingFunds($payment->organizer_amount);
                    
                    // Marcar pago como liberado
                    $payment->update(['funds_released' => true]);

                    $releasedCount++;
                    $releasedAmount += $payment->organizer_amount;

                    $this->info("✓ Liberados \${$payment->organizer_amount} para organizador ID: {$payment->organizer_id} (Pago ID: {$payment->id})");
                } else {
                    $this->warn("Fondos pendientes insuficientes para organizador ID: {$payment->organizer_id}. Pendiente: {$balance->pending_balance}, Requerido: {$payment->organizer_amount}");
                    
                    // Si el balance pendiente es menor que el monto, puede ser un desajuste.
                    // Podríamos forzar la liberación de lo que haya? Mejor no por ahora.
                }
            } catch (\Exception $e) {
                $this->error("Error al liberar fondos del pago ID {$payment->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("═══════════════════════════════════════");
        $this->info("Resumen:");
        $this->info("Pagos procesados: {$releasedCount}");
        $this->info("Total liberado: \$" . number_format($releasedAmount, 2));
        $this->info("═══════════════════════════════════════");

        return 0;
    }
}
