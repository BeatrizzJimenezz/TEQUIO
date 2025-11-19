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

    /**
     * Relación con el organizador (Professional Profile)
     * Antes: perfilProfesional
     */
    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    /**
     * Relación con etiquetas
     * Antes: etiquetas
     * [cite_start]Tabla pivote actualizada a 'event_tags' [cite: 73]
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_tags');
    }

    /**
     * Relación con componentes del evento
     * Antes: componentes
     */
    public function components(): HasMany
    {
        return $this->hasMany(EventComponent::class);
    }

    /**
     * Componentes aprobados
     * Antes: componentesAprobados
     * [cite_start]Estado actualizado a 'approved' [cite: 83]
     */
    public function approvedComponents(): HasMany
    {
        return $this->hasMany(EventComponent::class)->where('proposal_status', 'approved');
    }

    /**
     * Relación con colaboradores (equipo organizador)
     * Antes: colaboradores
     * [cite_start]Tabla pivote actualizada a 'event_profiles' y columna a 'role' [cite: 64]
     */
    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(ProfessionalProfile::class, 'event_profiles')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Scope para eventos del usuario actual
     * Antes: scopeDelUsuario
     */
    public function scopeOfUser($query, $userId)
    {
        return $query->whereHas('professionalProfile', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function collaborators()
    {
        return $this->belongsToMany(
            ProfessionalProfile::class, 
            'event_profiles', // tabla pivote correcta
            'event_id',       // FK de Event
            'professional_profile_id' // FK de ProfessionalProfile
        )
        ->withPivot('role')
        ->withTimestamps();
    }

}