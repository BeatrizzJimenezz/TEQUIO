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

    // Relacion con el componente del evento
    public function component(): BelongsTo
    {
        return $this->belongsTo(EventComponent::class, 'component_id');
    }

    // Relacion con el perfil profesional (solicitante)
    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class, 'professional_profile_id');
    }

    // Scope para aplicaciones pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para aplicaciones aceptadas
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}
