<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Verificar permisos de administrador u organizador
    private function checkPermissions()
    {
        if (!auth()->check()) {
            abort(401, 'Debes iniciar sesión.');
        }

        if (!auth()->user()->hasAnyRole(['Administrador', 'Organizador'])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }

    // Dashboard mostrando todos los eventos públicos activos
    public function dashboard()
    {
        $events = Event::where('visibility', 'public')
            ->where('status', 'active')
            ->with(['tags', 'components'])
            ->orderBy('start_date', 'desc')
            ->paginate(12);

        $tags = Tag::all();

        return view('dashboard', compact('events', 'tags'));
    }

    // Listado de eventos propios con filtros
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Iniciar consulta filtrando por el perfil del usuario actual
        $query = Event::with(['tags', 'components'])
            ->whereHas('professionalProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('start_date', 'desc');

        // Filtro de búsqueda por nombre o descripción
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro de modalidad
        if ($request->filled('modality')) {
            $query->where('modality', $request->modality);
        }

        // Filtro de fecha desde
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        // Filtro de fecha hasta
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        // Filtro de etiquetas
        if ($request->filled('tags')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            });
        }

        $events = $query->paginate(12)->withQueryString();
        $tags = Tag::orderBy('name')->get();

        return view('events.index', compact('events', 'tags'));
    }

    // Vista para crear nuevo evento
    public function create()
    {
        $this->checkPermissions();
        $tags = Tag::orderBy('name')->get();
        
        return view('events.create', compact('tags'));
    }

    // Guardar nuevo evento
    public function store(Request $request)
    {
        $this->checkPermissions();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'description' => 'required|string',
            'cover_image' => 'nullable|url|max:500',
            'logo' => 'nullable|url|max:500',
            'modality' => 'required|in:virtual,in_person,hybrid',
            'location' => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
            'status' => 'required|in:planning,active,finished',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $professionalProfile = auth()->user()->getOrCreateProfessionalProfile();
        $validated['professional_profile_id'] = $professionalProfile->id;

        $event = Event::create($validated);

        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        }

        return redirect()->route('events.index')
            ->with('success', 'Evento creado exitosamente.');
    }

    // Vista para editar evento
    public function edit(Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este evento.');
        }

        $tags = Tag::orderBy('name')->get();
        $selectedTags = $event->tags->pluck('id')->toArray();

        return view('events.edit', compact('event', 'tags', 'selectedTags'));
    }

    // Actualizar evento existente
    public function update(Request $request, Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este evento.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'description' => 'required|string',
            'cover_image' => 'nullable|url|max:500',
            'logo' => 'nullable|url|max:500',
            'modality' => 'required|in:virtual,in_person,hybrid',
            'location' => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
            'status' => 'required|in:planning,active,finished',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $event->update($validated);

        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        } else {
            $event->tags()->detach();
        }

        return redirect()->route('events.index')
            ->with('success', 'Evento actualizado exitosamente.');
    }

    // Eliminar evento
    public function destroy(Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar este evento.');
        }

        try {
            $event->delete();
            return redirect()->route('events.index')
                ->with('success', 'Evento eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('events.index')
                ->with('error', 'No se puede eliminar el evento porque tiene componentes asociados.');
        }
    }

    // Archivar evento (cambiar estado a finalizado)
    public function archive(Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para archivar este evento.');
        }

        $event->update(['status' => 'finished']);

        return redirect()->route('events.index')
            ->with('success', 'Evento archivado exitosamente.');
    }

    // Mostrar detalles públicos del evento
    public function show($id)
    {
        $event = Event::with(['tags', 'components', 'professionalProfile.user', 'approvedComponents.schedules'])
            ->findOrFail($id);

        if ($event->visibility === 'private' &&
            (!auth()->check() || $event->professionalProfile->user_id !== auth()->id())) {
            abort(403, 'Este evento es privado.');
        }

        $componentsByType = $event->approvedComponents->groupBy('type');

        return view('events.show', compact('event', 'componentsByType'));
    }
}