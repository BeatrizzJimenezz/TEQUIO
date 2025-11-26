<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'user_id',
        'organizer_id',
        'component_id',
        'paypal_order_id',
        'paypal_transaction_id',
        'component_price',
        'platform_fee',
        'paypal_fee',
        'total_paid',
        'organizer_amount',
        'status',
        'payment_method',
        'paid_at',
        'funds_released',
    ];

    protected $casts = [
        'component_price' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'paypal_fee' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'organizer_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'funds_released' => 'boolean',
    ];

    /**
     * Relaciones
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function component()
    {
        return $this->belongsTo(EventComponent::class, 'component_id');
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForOrganizer($query, $organizerId)
    {
        return $query->where('user_id', $organizerId);
    }

    /**
     * Métodos auxiliares
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Calcular comisiones
     *
     * @param float $componentPrice
     * @return array
     */
    public static function calculateFees(float $componentPrice): array
    {
        $platformFeePercentage = config('payment.platform_fee_percentage', 5) / 100;
        $paypalFeePercentage = 0.035; // 3.5%
        $paypalFixedFee = 0.30;

        $platformFee = round($componentPrice * $platformFeePercentage, 2);
        $subtotal = $componentPrice + $platformFee;
        $paypalFee = round(($subtotal * $paypalFeePercentage) + $paypalFixedFee, 2);
        $totalPaid = $subtotal + $paypalFee;

        return [
            'component_price' => $componentPrice,
            'platform_fee' => $platformFee,
            'paypal_fee' => $paypalFee,
            'total_paid' => round($totalPaid, 2),
            'organizer_amount' => $componentPrice, // Organizador recibe 100% del precio
        ];
    }
}
