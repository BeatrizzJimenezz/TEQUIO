<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private function verifyPermissions()
    {
        if (!auth()->check()) {
            abort(401, 'You must log in.');
        }
        
        if (!auth()->user()->hasAnyRole(['Administrador', 'Organizer'])) {
            abort(403, 'You do not have permissions to access this section.');
        }
    }

    public function index()
    {
        $user = auth()->user();
        $professionalProfile = $user->professionalProfile;

        if (!$professionalProfile) {
            $professionalProfile = $user->professionalProfile()->create([]);
        }

        // Obtener eventos donde es organizador principal
        $eventsAsCreator = Event::where('professional_profile_id', $professionalProfile->id)
            ->with(['tags', 'components'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener eventos donde es co-organizador (colaborador)
        $eventsAsCollaborator = $professionalProfile->collaborations()
            ->where('event_profiles.role', 'Organizador')
            ->with(['tags', 'components'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Combinar ambos
        $events = $eventsAsCreator->merge($eventsAsCollaborator)
            ->sortByDesc('created_at');

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $this->verifyPermissions();
        
        $tags = Tag::orderBy('name')->get();
        return view('events.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $this->verifyPermissions();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'description' => 'required|string',
            'cover_image' => 'nullable|url|max:500',
            'logo' => 'nullable|url|max:500',
            'modality' => 'required|in:virtual,presencial,hibrido',
            'location' => 'nullable|string|max:255',
            'visibility' => 'required|in:publico,privado',
            'status' => 'required|in:planificacion,activo,finalizado',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ], [
            'name.required' => 'The event name is required.',
            'start_date.required' => 'The start date is required.',
            'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
            'description.required' => 'The description is required.',
            'modality.required' => 'The modality is required.',
            'visibility.required' => 'The visibility is required.',
            'status.required' => 'The status is required.',
        ]);

        $professionalProfile = auth()->user()->getOrCreateProfessionalProfile();
        $validated['professional_profile_id'] = $professionalProfile->id;

        $event = Event::create($validated);

        // Asociar etiquetas si existen
        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        }

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        $this->verifyPermissions();
        
        // Verificar que el evento pertenece al usuario
        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permissions to edit this event.');
        }
        
        $tags = Tag::orderBy('name')->get();
        $selectedTags = $event->tags->pluck('id')->toArray();
        
        return view('events.edit', compact('event', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Event $event)
    {
        $this->verifyPermissions();
        
        // Verificar que el evento pertenece al usuario
        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permissions to edit this event.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'description' => 'required|string',
            'cover_image' => 'nullable|url|max:500',
            'logo' => 'nullable|url|max:500',
            'modality' => 'required|in:virtual,presencial,hibrido',
            'location' => 'nullable|string|max:255',
            'visibility' => 'required|in:publico,privado',
            'status' => 'required|in:planificacion,activo,finalizado',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $event->update($validated);

        // Actualizar etiquetas
        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        } else {
            $event->tags()->detach();
        }

        return redirect()->route('events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $this->verifyPermissions();
        
        // Verificar que el evento pertenece al usuario
        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permissions to delete this event.');
        }
        
        try {
            $event->delete();
            return redirect()->route('events.index')
                ->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('events.index')
                ->with('error', 'The event cannot be deleted because it has associated components.');
        }
    }

    public function archive(Event $event)
    {
        $this->verifyPermissions();
        
        // Verificar que el evento pertenece al usuario
        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permissions to archive this event.');
        }
        
        $event->update(['status' => 'finalizado']);
        
        return redirect()->route('events.index')
            ->with('success', 'Event archived successfully.');
    }

    // Método para vista pública de UN evento con sus componentes
    public function show(Event $event)
    {
        // Verificar que el evento sea público o que el usuario tenga permisos
        if ($event->visibility === 'privado' && 
            (!auth()->check() || $event->professionalProfile->user_id !== auth()->id())) {
            abort(403, 'This event is private.');
        }

        // Cargar relaciones necesarias
        $event->load([
            'tags',
            'professionalProfile.user',
            'approvedComponents.schedules'
        ]);

        // Agrupar componentes por tipo
        $componentsByType = $event->approvedComponents->groupBy('type');

        return view('events.public.show', compact('event', 'componentsByType'));
    }
}