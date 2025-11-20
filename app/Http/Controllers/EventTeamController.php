<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\ProfessionalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Role;

class EventTeamController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the event organizers team
     */
    public function index(Event $event)
    {
        $this->authorize('isOrganizer', $event);

        $leadOrganizer = $event->professionalProfile;

        $collaborators = $event->collaborators()
            ->wherePivot('role', 'Organizador')
            ->with('user')
            ->get();

        return view('events.team.index', compact('event', 'leadOrganizer', 'collaborators'));
    }

    /**
     * Show form to add an organizer
     */
    public function create(Event $event)
    {
        $this->authorize('isOrganizer', $event);

        $availableUsers = User::whereDoesntHave('professionalProfile.events', function($query) use ($event) {
            $query->where('events.id', $event->id);
        })
        ->where('id', '!=', $event->professionalProfile->user_id)
        ->whereDoesntHave('professionalProfile', function($query) use ($event) {
            $query->whereHas('events', function($q) use ($event) {
                $q->where('events.id', $event->id)
                ->where('event_profiles.role', 'Organizador');
            });
        })
        ->orderBy('name')
        ->get();

        return view('events.team.create', compact('event', 'availableUsers'));
    }

    /**
     * Add an existing or new organizer to the event
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('isOrganizer', $event);

        $rules = ['type' => 'required|in:existing,new'];

        if ($request->type === 'existing') {
            $rules['user_id'] = 'required|exists:users,id';
        } else {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|min:8';
        }

        $request->validate($rules, [
            'user_id.required' => 'You must select a user.',
            'user_id.exists' => 'The selected user does not exist.',
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Must be a valid email.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->type === 'new') {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'must_change_password' => true,
                ]);

                $user->assignRole('Organizador');

                $professionalProfile = $user->professionalProfile()->create([]);
            } else {
                $user = User::findOrFail($request->user_id);

                if (!$user->hasRole('Organizador')) {
                    $user->assignRole('Organizador');
                }

                $professionalProfile = $user->professionalProfile ?? $user->professionalProfile()->create([]);
            }

            $alreadyOrganizer = $event->collaborators()
                ->wherePivot('professional_profile_id', $professionalProfile->id)
                ->wherePivot('role', 'Organizador')
                ->exists();

            if ($alreadyOrganizer) {
                DB::rollBack();
                return back()->with('error', 'This user is already an organizer of the event.');
            }

            $event->collaborators()->attach($professionalProfile->id, [
                'role' => 'Organizador',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('events.team.index', $event)
                ->with('success', $request->type === 'new' 
                    ? 'New organizer created and added successfully.' 
                    : 'Organizador added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error adding organizer: ' . $e->getMessage());
        }
    }

    /**
     * Remove organizer from the team
     */
    public function destroy(Event $event, $professionalProfileId)
    {
        $this->authorize('isOrganizer', $event);

        DB::beginTransaction();
        try {
            if ($event->professional_profile_id == $professionalProfileId) {
                DB::rollBack();
                return back()->with('error', 'You cannot remove the main organizer of the event.');
            }

            $professionalProfile = ProfessionalProfile::findOrFail($professionalProfileId);
            $user = $professionalProfile->user;

            // Remover de este evento
            $event->collaborators()->detach($professionalProfileId);

            // Verificar si es organizador principal de algún evento
            $isMainOrganizer = Event::where('professional_profile_id', $professionalProfileId)->exists();

            // Verificar si es colaborador organizador en otros eventos usando la relación
            $isCollaboratorElsewhere = $professionalProfile->events()
                ->wherePivot('role', 'Organizador')
                ->exists();

            // Si no es organizador en ningún lado, remover el rol y asignar Participante
            if (!$isMainOrganizer && !$isCollaboratorElsewhere) {
                $user->removeRole('Organizador');
                
                // Asignar rol de Participante si no lo tiene
                if (!$user->hasRole('Participante')) {
                    $user->assignRole('Participante');
                }
            }

            DB::commit();

            return redirect()->route('events.team.index', $event)
                ->with('success', 'Organizador removed from the team successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error removing organizer: ' . $e->getMessage());
        }
    }
}