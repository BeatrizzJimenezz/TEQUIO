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
        'qr_ticket',
        'registration_date',
        'expiration_date',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
        'expiration_date' => 'datetime',
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
    public static function generateQrTicket(): string
    {
        do {
            $ticket = strtoupper(Str::random(3) . '-' . Str::random(3) . '-' . Str::random(3));
        } while (self::where('qr_ticket', $ticket)->exists());
        
        return $ticket;
    }

    // Check if the ticket is active
    public function isActive(): bool
    {
        if ($this->expiration_date) {
            return now()->lte($this->expiration_date);
        }
        return true;
    }
}