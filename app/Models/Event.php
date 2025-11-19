<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'professional_profile_id',
        'name',
        'start_date',
        'end_date',
        'start_time',
        'description',
        'cover_image',
        'logo',
        'modality',
        'location',
        'visibility',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function professionalProfiles()
    {
        return $this->belongsToMany(ProfessionalProfile::class, 'event_profiles')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function components(): HasMany
    {
        return $this->hasMany(EventComponent::class, 'event_id');
    }

    public function approvedComponents(): HasMany
    {
        return $this->hasMany(EventComponent::class, 'event_id')
            ->where('proposal_status', 'approved');
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->whereHas('professionalProfile', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
