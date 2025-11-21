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
        'modality',    // Valores esperados: 'virtual', 'in_person', 'hybrid'
        'location',
        'visibility',  // Valores esperados: 'public', 'private'
        'status',      // Valores esperados: 'planning', 'active', 'finished'
        'is_archived'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relación con el organizador (Professional Profile)
     */    
    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    /**
     * Relación con etiquetas
     * Tabla pivote: 'event_tags'
     */    
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'event_tags');
    }

    /**
     * Relación con componentes del evento
     */
    public function components(): HasMany
    {
        return $this->hasMany(EventComponent::class);
    }

    /**
     * Componentes aprobados
     */    
    public function approvedComponents(): HasMany
    {
        return $this->hasMany(EventComponent::class)->where('proposal_status', 'approved');
    }

    /**
     * Relación con colaboradores (equipo organizador)
     * Tabla pivote: 'event_profiles'
     */    
    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(ProfessionalProfile::class, 'event_profiles')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Colaboradores del evento con sus roles (alias de teamMembers)
     */
    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(
            ProfessionalProfile::class,
            'event_profiles',
            'event_id',
            'professional_profile_id'
        )
        ->withPivot('role')
        ->withTimestamps();
    }
    
    /**
     * Scope para eventos del usuario actual
     */
    public function scopeOfUser($query, $userId)
    {
        return $query->whereHas('professionalProfile', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Scope para eventos activos (no archivados)
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope para eventos archivados
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /*
    |--------------------------------------------------------------------------
    | DETECTOR DE CAMBIOS IMPORTANTES (EVENTO YA PUBLICADO)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::updating(function ($event) {
            if ($event->getOriginal('status') !== 'active') {
                return;
            }

            $importantFields = [
                'name',
                'start_date',
                'end_date',
                'start_time',
                'modality',
                'location'
            ];

            $changed = [];
            foreach ($importantFields as $field) {
                if ($event->isDirty($field)) {
                    $changed[$field] = [
                        'old' => $event->getOriginal($field),
                        'new' => $event->$field
                    ];
                }
            }

            if (empty($changed)) {
                return;
            }

            $event->changed_fields_for_notification = $changed;
        });

        static::updated(function ($event) {
            if (!property_exists($event, 'changed_fields_for_notification')) {
                return;
            }

            if (method_exists($event, 'notifyUsersAboutChanges')) {
                $event->notifyUsersAboutChanges(
                    $event->changed_fields_for_notification
                );
            }
        });
    }

    /**
     * Notificar a ponentes y asistentes sobre cambios importantes
     */
    public function notifyUsersAboutChanges(array $changes)
    {
        $components = $this->approvedComponents;
        $usersToNotify = collect();

        // Ponentes
        foreach ($components as $component) {
            if ($component->speaker && $component->speaker->user) {
                $usersToNotify->push($component->speaker->user);
            }
        }

        // Asistentes
        foreach ($components as $component) {
            foreach ($component->registrations as $registration) {
                if ($registration->user) {
                    $usersToNotify->push($registration->user);
                }
            }
        }

        // Eliminar duplicados
        $usersToNotify = $usersToNotify->unique('id');

        // Notificar
        foreach ($usersToNotify as $user) {
            $user->notify(new \App\Notifications\EventUpdatedNotification(
                $this,
                $changes
            ));
        }
    }
}