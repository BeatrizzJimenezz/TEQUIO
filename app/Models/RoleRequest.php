<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleRequest extends Model
{
    protected $fillable = [
        'user_id',
        'requested_role',
        'reason',
        'status',
        'reviewed_by',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Usuario que hace la solicitud
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin que revisó la solicitud
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Verificar si está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si fue aprobada
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Verificar si fue rechazada
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
