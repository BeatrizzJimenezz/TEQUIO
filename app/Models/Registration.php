<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Registration extends Model
{
    use HasFactory;

    protected $table = 'registrations';

    protected $fillable = [
        'user_id',
        'component_id',
        'ticket_qr',
        'registered_at',
        'expires_at',
        'payment_status',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relacion con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relacion con el componente
    public function component(): BelongsTo
    {
        return $this->belongsTo(EventComponent::class, 'component_id');
    }

    // Relacion con el pago
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Generar ticket QR único
    public static function generateTicketQR(): string
    {
        do {
            $ticket = 'REG-' . strtoupper(Str::random(3) . '-' . Str::random(3) . '-' . Str::random(3));
        } while (self::where('ticket_qr', $ticket)->exists());

        return $ticket;
    }

    // Verificar si el ticket está activo
    public function isActive(): bool
    {
        if ($this->expires_at) {
            return now()->lte($this->expires_at);
        }
        return true;
    }

    // Verificar si la inscripción puede ser cancelada (2 días antes del primer horario)
    public function canBeCancelled(): bool
    {
        $firstSchedule = $this->component->schedules()
            ->orderBy('date')
            ->orderBy('start_time')
            ->first();

        if (!$firstSchedule) {
            return true;
        }

        $scheduleDateTime = \Carbon\Carbon::parse($firstSchedule->date->format('Y-m-d') . ' ' . $firstSchedule->start_time);
        $twoDaysBefore = $scheduleDateTime->subDays(2);

        return now()->lt($twoDaysBefore);
    }

    // Obtener días hasta que inicia la actividad
    public function daysUntilStart(): ?int
    {
        $firstSchedule = $this->component->schedules()
            ->orderBy('date')
            ->orderBy('start_time')
            ->first();

        if (!$firstSchedule) {
            return null;
        }

        $scheduleDateTime = \Carbon\Carbon::parse($firstSchedule->date->format('Y-m-d') . ' ' . $firstSchedule->start_time);
        return (int) now()->diffInDays($scheduleDateTime, false);
    }

    // Verificar si el pago está completado
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    // Verificar si está pendiente de pago
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    // Verificar si es gratuito
    public function isFree(): bool
    {
        return $this->payment_status === 'free';
    }

    // Verificar si requiere pago
    public function requiresPayment(): bool
    {
        return $this->component && $this->component->requiresPayment();
    }

    // Marcar como pagado
    public function markAsPaid(string $paymentMethod): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);
    }

    // Scope para inscripciones pagadas
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Scope para inscripciones pendientes de pago
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // Alias para compatibilidad
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // Scope para inscripciones gratuitas
    public function scopeFree($query)
    {
        return $query->where('payment_status', 'free');
    }
}