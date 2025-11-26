<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    // Los atributos que son asignables en masa
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',      
        'must_change_password', 
    ];

    // Los atributos que deben ocultarse para la serialización
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Los atributos que deben convertirse a otros tipos
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relacion con el perfil profesional
    public function professionalProfile()
    {
        return $this->hasOne(ProfessionalProfile::class);
    }

    // Relación con inscripciones
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // Método auxiliar para obtener o crear el perfil
    public function getOrCreateProfessionalProfile()
    {
        if (!$this->professionalProfile) {
            return $this->professionalProfile()->create([]);
        }
        return $this->professionalProfile;
    }

    // Obtener URL de la foto de perfil o avatar predeterminado
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        // Retornar avatar predeterminado
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    // Relación con suscripciones
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    // Verificar si el usuario tiene una suscripción activa
    public function hasActiveSubscription(): bool
    {
        return $this->subscription && $this->subscription->isActive();
    }

    // Relación con balance de organizador
    public function organizerBalance()
    {
        return $this->hasOne(OrganizerBalance::class);
    }

    // Relación con retiros
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'user_id');
    }

    // Obtener o crear el balance del organizador
    public function getOrCreateOrganizerBalance()
    {
        if (!$this->organizerBalance) {
            return $this->organizerBalance()->create([
                'available_balance' => 0,
                'pending_balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
            ]);
        }
        return $this->organizerBalance;
    }
}