<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User; 

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

    /*
    |--------------------------------------------------------------------------
    | DETECTOR DE CAMBIO DE ESTADO
    |--------------------------------------------------------------------------
    | Cada vez que un componente cambie su estado (proposal_status),
    | se enviará notificación al usuario que lo propuso.
    */
    protected static function booted()
    {
        static::updating(function ($component) {
            // Si el estado NO cambió → no hacemos nada
            if (!$component->isDirty('proposal_status')) {
                return;
            }

            $oldStatus = $component->getOriginal('proposal_status');
            $newStatus = $component->proposal_status;

            // Usuario que creó/propuso el componente
            $proposer = $component->proposedBy;

            if ($proposer) {
                $proposer->notify(
                    new \App\Notifications\ProposalStatusChanged(
                        $component,
                        $oldStatus,
                        $newStatus
                    )
                );
            }
        });
    }

    /**
     * Relación con Event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relación con Speaker (Professional Profile)
     */
    public function speaker(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class, 'speaker_id');
    }

    /**
     * Relación con usuario que propuso el componente
     */
    public function proposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by_user_id');
    }

    /**
     * Relación con horarios
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ComponentSchedule::class, 'component_id');
    }

    /**
     * Relación con inscripciones
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'component_id');
    }

    /**
     * Relación con aplicaciones de oferta
     */
    public function applications(): HasMany
    {
        return $this->hasMany(OfferApplication::class, 'component_id');
    }

    /**
     * Aplicaciones pendientes
     */
    public function pendingApplications(): HasMany
    {
        return $this->hasMany(OfferApplication::class, 'component_id')
            ->where('status', 'pending');
    }

    /**
     * Calcular asientos disponibles
     */
    public function getAvailableSeatsAttribute()
    {
        if (!class_exists(\App\Models\Registration::class) || !$this->capacity) {
            return $this->capacity;
        }

        return $this->capacity - $this->registrations()->count();
    }
}