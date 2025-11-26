<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\ProfessionalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventTeamController extends Controller
{
    use AuthorizesRequests;

    // Mostrar el equipo de organizadores del evento
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

    // Formulario para agregar un organizador
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

    // Agregar un organizador nuevo o existente al evento
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
        'user_id.required' => 'Debes seleccionar un usuario.',
        'user_id.exists' => 'El usuario seleccionado no existe.',
        'name.required' => 'El nombre es obligatorio.',
        'email.required' => 'El correo es obligatorio.',
        'email.email' => 'Debe ser un correo válido.',
        'email.unique' => 'Este correo ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
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

            // IMPORTANTE: Asignar rol de Organizador si no lo tiene
            if (!$user->hasRole('Organizador')) {
                $user->assignRole('Organizador');
            }

            // Obtener o crear perfil profesional
            $professionalProfile = $user->professionalProfile;
            if (!$professionalProfile) {
                $professionalProfile = $user->professionalProfile()->create([]);
            }
        }

        // Verificar si ya es organizador de este evento
        $alreadyOrganizer = $event->collaborators()
            ->wherePivot('professional_profile_id', $professionalProfile->id)
            ->wherePivot('role', 'Organizador')
            ->exists();

        if ($alreadyOrganizer) {
            DB::rollBack();
            return back()->with('error', 'Este usuario ya es organizador del evento.');
        }

        // Agregar como colaborador
        $event->collaborators()->attach($professionalProfile->id, [
            'role' => 'Organizador',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return redirect()->route('events.team.index', $event)
            ->with('success', $request->type === 'new' 
                ? 'Nuevo organizador creado y agregado exitosamente.' 
                : 'Organizador agregado exitosamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Error al agregar organizador: ' . $e->getMessage());
    }
}

    // Eliminar organizador del equipo
    public function destroy(Event $event, $professionalProfileId)
    {
        $this->authorize('isOrganizer', $event);

        DB::beginTransaction();
        try {
            if ($event->professional_profile_id == $professionalProfileId) {
                DB::rollBack();
                return back()->with('error', 'No puedes eliminar al organizador principal del evento.');
            }

            $professionalProfile = ProfessionalProfile::findOrFail($professionalProfileId);
            $user = $professionalProfile->user;

            // Remover de este evento
            $event->collaborators()->detach($professionalProfileId);

            // Verificar si es organizador principal de algún otro evento
            $isMainOrganizer = Event::where('professional_profile_id', $professionalProfileId)->exists();

            // Verificar si es colaborador organizador en otros eventos
            $isCollaboratorElsewhere = $professionalProfile->events()
                ->wherePivot('role', 'Organizador')
                ->exists();

            // Si no es organizador en ningún lado, remover el rol y asignar Participante
            if (!$isMainOrganizer && !$isCollaboratorElsewhere) {
                $user->removeRole('Organizador');
                
                if (!$user->hasRole('Participante')) {
                    $user->assignRole('Participante');
                }
            }

            DB::commit();

            return redirect()->route('events.team.index', $event)
                ->with('success', 'Organizador eliminado del equipo exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar organizador: ' . $e->getMessage());
        }
    }
}