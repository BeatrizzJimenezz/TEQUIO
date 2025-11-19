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
    public function index(Event $evento)
    {
        $this->authorize('isOrganizer', $evento);

        $mainOrganizer = $evento->perfilProfesional;

        $collaborators = $evento->colaboradores()
            ->wherePivot('rol', 'Organizador')
            ->with('user')
            ->get();

        return view('eventos.equipo.index', compact('evento', 'mainOrganizer', 'collaborators'));
    }

    /**
     * Show form to add an organizer
     */
    public function create(Event $evento)
    {
        $this->authorize('isOrganizer', $evento);

        $availableUsers = User::whereDoesntHave('perfilProfesional.eventos', function($query) use ($evento) {
            $query->where('eventos.id', $evento->id);
        })
        ->where('id', '!=', $evento->perfilProfesional->user_id)
        ->whereDoesntHave('perfilProfesional', function($query) use ($evento) {
            $query->whereHas('colaboraciones', function($q) use ($evento) {
                $q->where('evento_id', $evento->id)
                  ->where('perfil_evento.rol', 'Organizador');
            });
        })
        ->orderBy('name')
        ->get();

        return view('eventos.equipo.create', compact('evento', 'availableUsers'));
    }

    /**
     * Add an existing or new organizer to the event
     */
    public function store(Request $request, Event $evento)
    {
        $this->authorize('isOrganizer', $evento);

        $rules = ['tipo' => 'required|in:existing,new'];

        if ($request->tipo === 'existing') {
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
            if ($request->tipo === 'new') {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'debe_cambiar_password' => true,
                ]);

                $user->assignRole('Organizador');

                $professionalProfile = $user->perfilProfesional()->create([]);
            } else {
                $user = User::findOrFail($request->user_id);

                if (!$user->hasRole('Organizador')) {
                    $user->assignRole('Organizador');
                }

                $professionalProfile = $user->perfilProfesional ?? $user->perfilProfesional()->create([]);
            }

            $alreadyOrganizer = $evento->colaboradores()
                ->wherePivot('perfil_profesional_id', $professionalProfile->id)
                ->wherePivot('rol', 'Organizador')
                ->exists();

            if ($alreadyOrganizer) {
                DB::rollBack();
                return back()->with('error', 'This user is already an organizer of the event.');
            }

            $evento->colaboradores()->attach($professionalProfile->id, [
                'rol' => 'Organizador',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('eventos.equipo.index', $evento)
                ->with('success', $request->tipo === 'new' 
                    ? 'New organizer created and added successfully.' 
                    : 'Organizer added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error adding organizer: ' . $e->getMessage());
        }
    }

    /**
     * Remove organizer from the team
     */
    public function destroy(Event $evento, $professionalProfileId)
    {
        $this->authorize('isOrganizer', $evento);

        try {
            if ($evento->perfil_profesional_id == $professionalProfileId) {
                return back()->with('error', 'You cannot remove the main organizer of the event.');
            }

            $professionalProfile = PerfilProfesional::findOrFail($professionalProfileId);
            $user = $professionalProfile->user;

            $evento->colaboradores()->detach($professionalProfileId);

            $isMainOrganizer = Event::where('perfil_profesional_id', $professionalProfileId)->exists();

            $isCollaboratorElsewhere = DB::table('perfil_evento')
                ->where('perfil_profesional_id', $professionalProfileId)
                ->where('rol', 'Organizador')
                ->exists();

            if (!$isMainOrganizer && !$isCollaboratorElsewhere) {
                $user->removeRole('Organizador');
            }

            return redirect()->route('eventos.equipo.index', $evento)
                ->with('success', 'Organizer removed from the team successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error removing organizer: ' . $e->getMessage());
        }
    }
}