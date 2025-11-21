<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'expires_at' => 'datetime',
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
}