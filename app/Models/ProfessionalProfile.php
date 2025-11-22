<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_temporary',
        'temp_email',
        'temp_name',
        'temp_profession',
        'created_by_user_id',
        'about_me',
        'current_workplace',
        'skills',
    ];

    protected $casts = [
        'is_temporary' => 'boolean',
    ];

    /**
     * Obtener el nombre para mostrar (del usuario o temporal)
     */
    public function getDisplayNameAttribute()
    {
        if ($this->is_temporary) {
            return $this->temp_name . ' (Temporal)';
        }
        return $this->user ? $this->user->name : 'Sin nombre';
    }

    /**
     * Obtener el email (del usuario o temporal)
     */
    public function getDisplayEmailAttribute()
    {
        if ($this->is_temporary) {
            return $this->temp_email;
        }
        return $this->user ? $this->user->email : null;
    }

    /**
     * Relación con el usuario que creó el perfil temporal
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Relacion con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacion con las formaciones academicas
    public function academicTrainings()
    {
        return $this->hasMany(AcademicTraining::class);
    }

    // Relacion con los eventos en los que ha participado
    public function events()
    {
        return $this->belongsToMany(
            Event::class,
            'event_profiles'
        )->withPivot('role')->withTimestamps();
    }

    // Relacion con los componentes en los que ha sido ponente
    public function componentsAsSpeaker()
    {
        return $this->hasMany(EventComponent::class, 'speaker_id');
    }

    // Relacion con las solicitudes de oferta
    public function offerApplications()
    {
        return $this->hasMany(OfferApplication::class);
    }

    // Relacion con las redes sociales
    public function socialNetworks()
    {
        return $this->hasMany(SocialNetwork::class, 'professional_profile_id');
    }

}
