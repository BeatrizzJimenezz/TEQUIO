<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    // Listado público de eventos
    public function index(Request $request)
    {
        $query = Event::with(['tags', 'professionalProfile', 'approvedComponents'])
            ->where('visibility', 'public')
            ->where('status', 'active');

        // Filtro de búsqueda (por nombre o descripción)
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

        // Filtro de etiquetas
        if ($request->filled('tags')) {
            $tagIds = $request->tags;
            $query->whereHas('tags', function($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        // Filtro de rango de fechas
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        // Ordenar por fecha de inicio ascendente (próximos primero)
        $events = $query->orderBy('start_date', 'asc')
            ->paginate(12)
            ->withQueryString();

        // Obtener todas las etiquetas para los filtros
        $tags = Tag::orderBy('name')->get();

        return view('public.events.index', compact('events', 'tags'));
    }

    // Detalle público de un evento
    public function show($id)
    {
        $event = Event::with([
            'tags',
            'professionalProfile.user',
            'approvedComponents.schedules' => function($query) {
                $query->orderBy('date', 'asc')->orderBy('start_time', 'asc');
            }
        ])
        ->where('visibility', 'public')
        ->findOrFail($id);

        // Asegurar que el evento sea público
        if ($event->visibility !== 'public') {
            abort(404, 'Evento no encontrado o no disponible públicamente.');
        }

        // Agrupar componentes por tipo
        $componentsByType = $event->approvedComponents->groupBy('type');

        // Listado de ID donde el usuario autenticado ya está registrado
        $registeredComponentIds = [];
        if (auth()->check()) {
            $registeredComponentIds = \App\Models\Registration::where('user_id', auth()->id())
                ->whereIn('component_id', $event->approvedComponents->pluck('id'))
                ->pluck('component_id')
                ->toArray();
        }

        return view('public.events.show', compact('event', 'componentsByType', 'registeredComponentIds'));
    }

    public function showComponent($eventId, $componentId)
    {
        // Cargar componente con relaciones necesarias
        $component = \App\Models\EventComponent::with([
            'event', 
            'schedules', 
            'speaker.user', // Para obtener foto y nombre del ponente real
            'speaker.socialNetworks' // Si tienes redes sociales
        ])
        ->where('event_id', $eventId)
        ->where('proposal_status', 'approved') // Solo mostrar aprobados
        ->findOrFail($componentId);

        // Verificar inscripción del usuario actual
        $registration = null;
        if (auth()->check()) {
            $registration = Registration::where('user_id', auth()->id())
                ->where('component_id', $component->id)
                ->first();
        }

        return view('components.show', compact('component', 'registration'));
    }
}
