<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferApplication extends Model
{
    use HasFactory;

    protected $table = 'offer_applications';

    protected $fillable = [
        'component_id',
        'professional_profile_id',
        'message',
        'status',
    ];

    // Relation with component (offer)
    public function component(): BelongsTo
    {
        return $this->belongsTo(EventComponent::class, 'component_id');
    }

    // Relation with professional profile (applicant)
    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class, 'professional_profile_id');
    }

    // Scope for pending applications
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for accepted applications
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}
