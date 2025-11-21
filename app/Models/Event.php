<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'professional_profile_id',
        'name',
        'start_date',
        'end_date',
        'start_time',
        'description',
        'cover_image',
        'logo',
        'modality',   // Valores esperados: 'virtual', 'in_person', 'hybrid'
        'location',
        'visibility', // Valores esperados: 'public', 'private'
        'status',     // Valores esperados: 'planning', 'active', 'finished'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Perfil profesional que organiza el evento
    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    // Etiquetas asociadas al evento
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_tags');
    }

    // Componentes del evento
    public function components(): HasMany
    {
        return $this->hasMany(EventComponent::class);
    }

    // Componentes del evento con estado aprobado
    public function approvedComponents(): HasMany
    {
        return $this->hasMany(EventComponent::class)->where('proposal_status', 'approved');
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(ProfessionalProfile::class, 'event_profiles')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Scope para filtrar eventos por el ID de usuario del perfil profesional
    public function scopeOfUser($query, $userId)
    {
        return $query->whereHas('professionalProfile', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // Colaboradores del evento con sus roles
    public function collaborators()
    {
        return $this->belongsToMany(
            ProfessionalProfile::class, 
            'event_profiles',
            'event_id',    
            'professional_profile_id'
        )
        ->withPivot('role')
        ->withTimestamps();
    }

}