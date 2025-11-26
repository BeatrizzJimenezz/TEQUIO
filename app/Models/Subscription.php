<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'paypal_subscription_id',
        'plan_id',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Relationship: Subscription belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->ends_at
            && $this->ends_at->isFuture();
    }

    /**
     * Scope: Active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('ends_at', '>', Carbon::now());
    }
}
