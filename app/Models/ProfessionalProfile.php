<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfessionalProfile extends Model
{
    use HasFactory;

    protected $table = 'professional_profiles';

    protected $fillable = [
        'user_id',
        'about_me',
        'current_workplace',
        'skills',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Events where this profile is the main organizer (hasMany)
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_profiles')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Events where this profile participates through pivot table event_profiles
    public function collaborations(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_profiles')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Combine organizer events + collaborations
    public function allEvents()
    {
        return $this->events->merge($this->collaborations);
    }

    public function academicTrainings(): HasMany
    {
        return $this->hasMany(AcademicTraining::class);
    }
}
