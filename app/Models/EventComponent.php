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

    /** Relationship with the event */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /** Relationship with speaker (professional profile) */
    public function speaker(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class, 'speaker_id');
    }

    /** Relationship with the user who proposed the component */
    public function proposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by_user_id');
    }

    /** Relationship with schedules */
    public function schedules(): HasMany
    {
        return $this->hasMany(ComponentSchedule::class, 'component_id');
    }

    /** Relationship with registrations */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'component_id');
    }

    /** Relationship with offer applications */
    public function applications(): HasMany
    {
        return $this->hasMany(OfferApplication::class, 'component_id');
    }

    /** Pending applications */
    public function pendingApplications(): HasMany
    {
        return $this->hasMany(OfferApplication::class, 'component_id')
            ->where('status', 'pending');
    }

    /** Available capacity */
    public function getAvailableCapacityAttribute()
    {
        if (!class_exists(\App\Models\Registration::class) || !$this->capacity) {
            return $this->capacity;
        }

        return $this->capacity - $this->registrations()->count();
    }
}
