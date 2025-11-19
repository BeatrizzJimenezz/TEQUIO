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

    public function view(User $user, Event $event)
    {
        if ($event->visibility === 'public') {
            return true;
        }

        return $this->isOrganizer($user, $event);
    }

    public function update(User $user, Event $event)
    {
        return $this->isOrganizer($user, $event);
    }

    public function delete(User $user, Event $event)
    {
        return $event->professionalProfile && $event->professionalProfile->user_id === $user->id;
    }

    public function manageTeam(User $user, Event $event)
    {
        return $this->isOrganizer($user, $event);
    }
}
