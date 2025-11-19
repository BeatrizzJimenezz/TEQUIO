<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Verifica que el usuario tenga permisos de administrador u organizador
    private function checkPermissions()
    {
        if (!auth()->check()) {
            abort(401, 'You must be logged in.');
        }

        if (!auth()->user()->hasAnyRole(['Administrador', 'Organizador'])) {
            abort(403, 'You do not have permission to access this section.');
        }
    }

    public function dashboard()
    {
        // Dashboard muestra TODOS los eventos públicos
        $events = Event::where('visibility', 'public')
            ->where('status', 'active')
            ->with(['tags', 'components'])
            ->orderBy('start_date', 'desc')
            ->paginate(12);
        
        $tags = Tag::all();
        
        return view('dashboard', compact('events', 'tags'));
    }


    // Listado de eventos con filtros y paginación
    public function index(Request $request)
    {
        $user = auth()->user();
        $professionalProfile = $user->professionalProfile ?? $user->professionalProfile()->create([]);

        $query = Event::with(['tags', 'components'])
            ->where('status', 'active') // Ajusta según los valores que uses
            ->orderBy('start_date', 'desc');

            $query = Event::with(['tags', 'components'])
        ->whereHas('professionalProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->orderBy('start_date', 'desc');

        $events = $query->paginate(12)->withQueryString();
        $tags = Tag::orderBy('name')->get();

        // Filtro de búsqueda
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

    // Crear nuevo evento
    public function create()
    {
        $this->checkPermissions();
        $tags = Tag::orderBy('name')->get();
        return view('events.create', compact('tags'));
    }

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
            'modality' => 'required|in:virtual,presential,hybrid',
            'location' => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
            'status' => 'required|in:planning,active,fiAdministratornished',
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
            ->with('success', 'Event created successfully.');
    }

    

    // Editar evento
    public function edit(Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to edit this event.');
        }

        $tags = Tag::orderBy('name')->get();
        $selectedTags = $event->tags->pluck('id')->toArray();

        return view('events.edit', compact('event', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to edit this event.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'description' => 'required|string',
            'cover_image' => 'nullable|url|max:500',
            'logo' => 'nullable|url|max:500',
            'modality' => 'required|in:virtual,presential,hybrid',
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
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to delete this event.');
        }

        try {
            $event->delete();
            return redirect()->route('events.index')
                ->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('events.index')
                ->with('error', 'Cannot delete event because it has associated components.');
        }
    }

    public function archive(Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to archive this event.');
        }

        $event->update(['status' => 'finished']);

        return redirect()->route('events.index')
            ->with('success', 'Event archived successfully.');
    }

    // Mostrar evento público
    public function show($id)
    {
        $event = Event::with(['tags', 'components', 'professionalProfile.user', 'approvedComponents.schedules'])->findOrFail($id);

        if ($event->visibility === 'private' &&
            (!auth()->check() || $event->professionalProfile->user_id !== auth()->id())) {
            abort(403, 'This event is private.');
        }

        $componentsByType = $event->approvedComponents->groupBy('type');

        return view('events.show', compact('event', 'componentsByType'));
    }
}