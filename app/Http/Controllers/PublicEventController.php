<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['tags', 'components'])
            ->where('status', 'active') // ajusta según los valores que uses
            ->orderBy('start_date', 'desc');

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

        return view('dashboard', compact('events', 'tags'));
    }

    public function show($id)
    {
        $event = Event::with(['tags', 'components', 'profiles'])->findOrFail($id);
        return view('events.show', compact('event'));
    }
}