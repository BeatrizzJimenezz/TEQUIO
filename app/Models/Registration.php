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

    // Relation with user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relation with component
    public function component(): BelongsTo
    {
        return $this->belongsTo(EventComponent::class, 'component_id');
    }

    // Generate unique QR ticket
    public static function generateTicketQR(): string
    {
        do {
            $ticket = 'REG-' . strtoupper(Str::random(3) . '-' . Str::random(3) . '-' . Str::random(3));
        } while (self::where('ticket_qr', $ticket)->exists());

        return $ticket;
    }

    // Check if the ticket is active
    public function isActive(): bool
    {
        if ($this->expires_at) {
            return now()->lte($this->expires_at);
        }
        return true;
    }

    // Check if registration can be cancelled (2 days before first schedule)
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

    // Get days until activity starts
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