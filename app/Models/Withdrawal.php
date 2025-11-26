<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'organizer_id',
        'amount',
        'paypal_transaction_id',
        'paypal_email',
        'status',
        'requested_at',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Relación con el organizador
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Scope para retiros pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para retiros completados
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope para retiros rechazados
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Marca el retiro como completado
     */
    public function markAsCompleted(string $transactionId, ?string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'paypal_transaction_id' => $transactionId,
            'processed_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Marca el retiro como rechazado
     */
    public function markAsRejected(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'notes' => $reason,
        ]);
    }

    /**
     * Verifica si el retiro está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica si el retiro está completado
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
