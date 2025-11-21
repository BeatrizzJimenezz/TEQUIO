<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventComponent extends Model
{
    use HasFactory;

    protected $table = 'event_components';

    protected $fillable = [
        'event_id',
        'speaker_id',
        'proposed_by_user_id',
        'name',
        'description',
        'type',
        'proposal_status',
        'modality',
        'location',
        'cover_image',
        'level',
        'capacity',
        'attendee_price',
        'organizer_cost',
        'participant_requirements',
        'instructor_requirements',
    ];

    protected $casts = [
        'attendee_price' => 'decimal:2',
        'organizer_cost' => 'decimal:2',
    ];

    // Relacion con Event
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Relacion con Speaker
    public function speaker(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class, 'speaker_id');
    }

    // Relacion con usuario que propuso el componente
    public function proposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by_user_id');
    }

    // Relacion con horarios
    public function schedules(): HasMany
    {
        return $this->hasMany(ComponentSchedule::class, 'component_id');
    }

    // Relacion con inscripciones
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'component_id');
    }

    // Relacion con aplicaciones de oferta
    public function applications(): HasMany
    {
        return $this->hasMany(OfferApplication::class, 'component_id');
    }

    // Aplicaciones pendientes
    public function pendingApplications(): HasMany
    {
        return $this->hasMany(OfferApplication::class, 'component_id')
            ->where('status', 'pending');
    }

    // Calcular asientos disponibles
    public function getAvailableSeatsAttribute()
    {
        if (!class_exists(\App\Models\Registration::class) || !$this->capacity) {
            return $this->capacity;
        }

        return $this->capacity - $this->registrations()->count();
    }
}