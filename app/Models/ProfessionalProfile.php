<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'about_me',
        'current_workplace',
        'skills',
    ];

    /**
     * Relationship: Profile belongs to User (1:1)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: One profile has many academic trainings (1:N)
     */
    public function academicTrainings()
    {
        return $this->hasMany(AcademicTraining::class);
    }

    /**
     * Relationship: One profile participates in many events (N:N)
     */
    public function events()
    {
        return $this->belongsToMany(
            Event::class,
            'event_profiles'
        )->withPivot('role')->withTimestamps();
    }

    /**
     * Relationship: A professional profile can be speaker in components
     */
    public function componentsAsSpeaker()
    {
        return $this->hasMany(EventComponent::class, 'speaker_id');
    }

    /**
     * Relationship: Applications sent by the profile
     */
    public function offerApplications()
    {
        return $this->hasMany(OfferApplication::class);
    }

    public function socialNetworks()
    {
        return $this->hasMany(SocialNetwork::class, 'professional_profile_id');
    }

}
