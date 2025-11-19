<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'modality',
        'location',
        'visibility',
        'status',
    ];

    public function profiles()
    {
        return $this->belongsToMany(
            ProfessionalProfile::class,
            'event_profiles'
        )->withPivot('role')->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'event_tags');
    }

    public function components()
    {
        return $this->hasMany(EventComponent::class);
    }
}
