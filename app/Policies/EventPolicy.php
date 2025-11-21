<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event; 
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function isOrganizer(User $user, Event $event)
    {
        // Si es el organizador principal
        if ($event->professionalProfile && $event->professionalProfile->user_id === $user->id) {
            return true;
        }

        $professionalProfile = $user->professionalProfile;
        if ($professionalProfile) {
            return $event->collaborators()
                ->wherePivot('role', 'Organizador')
                ->where('professional_profiles.id', $professionalProfile->id)
                ->exists();
        }

        return false;
    }

    public function manage(User $user, Event $event)
    {
        // Check if user is an organizer of this event
        return $event->organizers()->where('organizer_id', $user->organizer->id ?? null)->exists();
    }

    // Permite ver el evento si es público o si el usuario es organizador
    public function view(User $user, Event $event)
    {
        if ($event->visibility === 'public') {
            return true;
        }

        return $this->isOrganizer($user, $event);
    }

    // Permite actualizar el evento solo si el usuario es organizador
    public function update(User $user, Event $event)
    {
        return $this->isOrganizer($user, $event);
    }

    // Permite eliminar el evento solo si el usuario es el organizador principal
    public function delete(User $user, Event $event)
    {
        return $event->professionalProfile && $event->professionalProfile->user_id === $user->id;
    }

    // Permite gestionar el equipo solo si el usuario es organizador
    public function manageTeam(User $user, Event $event)
    {
        return $this->isOrganizer($user, $event);
    }
}
