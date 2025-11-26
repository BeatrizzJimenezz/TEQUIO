<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizerBalance extends Model
{
    protected $fillable = [
        'user_id',
        'available_balance',
        'pending_balance',
        'total_earned',
        'total_withdrawn',
        'paypal_email',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
    ];

    /**
     * Relación con el usuario organizador
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con los retiros
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'organizer_id', 'user_id');
    }

    /**
     * Verifica si el organizador puede retirar una cantidad específica
     */
    public function canWithdraw(float $amount): bool
    {
        $minimumWithdrawal = config('payment.minimum_withdrawal', 5.00);

        return $this->available_balance >= $amount
            && $amount >= $minimumWithdrawal;
    }

    /**
     * Agrega fondos al balance disponible
     */
    public function addFunds(float $amount): void
    {
        $this->increment('available_balance', $amount);
        $this->increment('total_earned', $amount);
    }

    /**
     * Agrega fondos al balance pendiente
     */
    public function addPendingFunds(float $amount): void
    {
        $this->increment('pending_balance', $amount);
    }

    /**
     * Mueve fondos de pendiente a disponible
     */
    public function releasePendingFunds(float $amount): void
    {
        if ($this->pending_balance >= $amount) {
            $this->decrement('pending_balance', $amount);
            $this->increment('available_balance', $amount);
            $this->increment('total_earned', $amount);
        }
    }

    /**
     * Deduce fondos del balance disponible (para retiros)
     */
    public function deductFunds(float $amount): bool
    {
        if ($this->available_balance >= $amount) {
            $this->decrement('available_balance', $amount);
            $this->increment('total_withdrawn', $amount);
            return true;
        }

        return false;
    }

    /**
     * Scope para organizadores con balance retirable
     */
    public function scopeWithdrawable($query)
    {
        $minimumWithdrawal = config('payment.minimum_withdrawal', 5.00);

        return $query->where('available_balance', '>=', $minimumWithdrawal);
    }

    /**
     * Obtiene el balance total (disponible + pendiente)
     */
    public function getTotalBalanceAttribute(): float
    {
        return (float) ($this->available_balance + $this->pending_balance);
    }
}
