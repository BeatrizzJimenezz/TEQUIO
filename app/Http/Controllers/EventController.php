<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tag;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\EventUpdatedNotification;

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

    // Listado de eventos propios con filtros y paginación
    public function index(Request $request)
    {
        $user = auth()->user();
        $isArchivedView = $request->get('view') === 'archived';
        
        // Iniciar consulta con relaciones
        $query = Event::with(['tags', 'components'])
            ->whereHas('professionalProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('start_date', 'desc');

        // Filtros de búsqueda
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

        // Filtrar según la vista (archivados vs activos)
        if ($isArchivedView) {
            $query->archived(); 
        } else {
            $query->active()
                ->whereIn('status', ['active', 'planning', 'finished']); 
        }

        // Ordenar por fecha de inicio (más recientes primero)
        $query->orderBy('start_date', 'desc');

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

        // Capturar valores antes de editar
        $oldValues = $event->only([
            'name', 'start_date', 'end_date', 'start_time',
            'description', 'cover_image', 'logo', 'modality',
            'location', 'visibility', 'status'
        ]);

        // Validación
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

        // Actualizar evento
        $event->update($validated);

        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        } else {
            $event->tags()->detach();
        }

        // Detectar cambios
        $changes = [];
        foreach ($validated as $field => $newValue) {

            $old = $this->normalizeValue($oldValues[$field] ?? null, $field);
            $new = $this->normalizeValue($newValue, $field);

            if ($old !== $new) {
                $changes[$field] = [
                    'old' => $old,
                    'new' => $new
                ];
            }
        }

        // Enviar notificación a inscritos
        if (!empty($changes)) {
            $registrations = $event->components()
                ->with('registrations.user')
                ->get()
                ->pluck('registrations')
                ->flatten();

            foreach ($registrations as $registration) {
                if ($registration->user) {
                    $registration->user->notify(
                        new EventUpdatedNotification($event, $changes)
                    );
                }
            }
        }

        return redirect()->route('events.index')
            ->with('success', 'Evento actualizado exitosamente.');
    }

    // Eliminar evento permanentemente
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

    // Archivar o desarchivar evento
    public function archive(Event $event)
    {
        $this->checkPermissions();
        
        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para archivar este evento.');
        }
        
        $event->is_archived = !$event->is_archived;

        if ($event->is_archived && $event->status === 'active') {
            $event->status = 'finished';
        }
        
        $event->save();
        
        $message = $event->is_archived 
            ? 'Evento archivado correctamente.' 
            : 'Evento restaurado correctamente.';
        
        return redirect()->back()->with('success', $message);
    }

    // Mostrar detalles del evento
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

    /* =====================================================
     *  Normaliza valores para comparación 
     * ===================================================== */
    private function normalizeValue($value, $field)
    {
        if ($value instanceof \Carbon\Carbon) {
            return $value->format('Y-m-d');
        }

        if ($field === 'modality') {
            return [
                'virtual' => 'Virtual',
                'in_person' => 'Presencial',
                'hybrid' => 'Híbrido'
            ][$value] ?? $value;
        }

        if ($field === 'visibility') {
            return [
                'public' => 'Público',
                'private' => 'Privado'
            ][$value] ?? $value;
        }

        return $value;
    }

    // Lista de reportes de todos los eventos del organizador
    public function reportsIndex()
    {
        $this->checkPermissions();

        $user = auth()->user();

        // Obtener todos los eventos del organizador con estadísticas
        $events = Event::with(['components.registrations', 'tags'])
            ->whereHas('professionalProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($event) {
                $event->stats = [
                    'totalComponents' => $event->components->count(),
                    'totalRegistrations' => $event->components->sum(fn($c) => $c->registrations->count()),
                    'talks' => $event->components->where('type', 'talk')->count(),
                    'workshops' => $event->components->where('type', 'workshop')->count(),
                    'activities' => $event->components->where('type', 'activity')->count(),
                ];
                return $event;
            });

        return view('events.reports-index', compact('events'));
    }

    // Reportes del evento
    public function reports(Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver los reportes de este evento.');
        }

        // Cargar relaciones necesarias
        $event->load(['components.schedules', 'components.registrations.user', 'tags']);

        // Estadísticas generales del evento
        $stats = [
            'totalComponents' => $event->components->count(),
            'totalRegistrations' => $event->components->sum(fn($c) => $c->registrations->count()),
            'componentsByType' => [
                'talk' => $event->components->where('type', 'talk')->count(),
                'workshop' => $event->components->where('type', 'workshop')->count(),
                'activity' => $event->components->where('type', 'activity')->count(),
            ],
            'componentsByStatus' => [
                'approved' => $event->components->where('proposal_status', 'approved')->count(),
                'proposed' => $event->components->where('proposal_status', 'proposed')->count(),
                'rejected' => $event->components->where('proposal_status', 'rejected')->count(),
                'offer_open' => $event->components->where('proposal_status', 'offer_open')->count(),
            ],
        ];

        // Componentes con más inscripciones
        $topComponents = $event->components
            ->map(function ($component) {
                $component->registrations_count = $component->registrations->count();
                return $component;
            })
            ->sortByDesc('registrations_count')
            ->take(5);

        // Inscripciones por día
        $registrationsByDay = Registration::whereIn('component_id', $event->components->pluck('id'))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Inscripciones por componente para gráfico
        $registrationsByComponent = $event->components
            ->map(function ($component) {
                return [
                    'name' => \Str::limit($component->name, 20),
                    'count' => $component->registrations->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values();

        // Lista de inscritos
        $allRegistrations = Registration::whereIn('component_id', $event->components->pluck('id'))
            ->with(['user', 'component'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Capacidad vs inscripciones
        $capacityData = $event->components
            ->filter(fn($c) => $c->capacity > 0)
            ->map(function ($component) {
                return [
                    'name' => \Str::limit($component->name, 15),
                    'capacity' => $component->capacity,
                    'registered' => $component->registrations->count(),
                    'percentage' => round(($component->registrations->count() / $component->capacity) * 100, 1),
                ];
            })
            ->values();

        return view('events.reports', compact(
            'event',
            'stats',
            'topComponents',
            'registrationsByDay',
            'registrationsByComponent',
            'allRegistrations',
            'capacityData'
        ));
    }

    public function exportReport(Request $request, Event $event)
    {
        $this->checkPermissions();

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para exportar el reporte de este evento.');
        }

        $format = $request->query('format', 'pdf');

        // Cargar datos
        $event->load(['components.schedules', 'components.registrations.user', 'tags']);

        $stats = [
            'totalComponents' => $event->components->count(),
            'totalRegistrations' => $event->components->sum(fn($c) => $c->registrations->count()),
            'componentsByType' => [
                'talk' => $event->components->where('type', 'talk')->count(),
                'workshop' => $event->components->where('type', 'workshop')->count(),
                'activity' => $event->components->where('type', 'activity')->count(),
            ],
            'componentsByStatus' => [
                'approved' => $event->components->where('proposal_status', 'approved')->count(),
                'proposed' => $event->components->where('proposal_status', 'proposed')->count(),
                'rejected' => $event->components->where('proposal_status', 'rejected')->count(),
                'offer_open' => $event->components->where('proposal_status', 'offer_open')->count(),
            ],
        ];

        $allRegistrations = Registration::whereIn('component_id', $event->components->pluck('id'))
            ->with(['user', 'component'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\EventReportExport($event, $stats, $allRegistrations),
                'reporte-' . \Str::slug($event->name) . '-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        // PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('events.reports-pdf', compact('event', 'stats', 'allRegistrations'));
        return $pdf->download('reporte-' . \Str::slug($event->name) . '-' . now()->format('Y-m-d') . '.pdf');
    }
}