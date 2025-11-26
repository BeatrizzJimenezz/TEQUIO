<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EventComponent;
use App\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventComponentPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede gestionar este componente
     */
    public function manage(User $user, EventComponent $component)
    {
        $event = $component->event;
        
        // Si es el organizador principal del evento
        if ($event->professionalProfile && $event->professionalProfile->user_id === $user->id) {
            return true;
        }

        // Si es co-organizador del evento
        $professionalProfile = $user->professionalProfile;
        if ($professionalProfile) {
            return $event->collaborators()
                ->wherePivot('role', 'Organizador')
                ->where('professional_profiles.id', $professionalProfile->id)
                ->exists();
        }

        return false;
    }

    public function update(User $user, EventComponent $component)
    {
        return $this->manage($user, $component);
    }

    public function delete(User $user, EventComponent $component)
    {
        return $this->manage($user, $component);
    }

    public function view(User $user, EventComponent $component)
    {
        return $this->manage($user, $component);
    }
}