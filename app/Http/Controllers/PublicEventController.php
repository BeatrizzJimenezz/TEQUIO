<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    /**
     * Public catalog of events
     * Shows only public and active events
     */
    public function index(Request $request)
    {
        $query = Event::with(['tags', 'professionalProfile', 'approvedComponents'])
            ->where('visibility', 'public')
            ->where('status', 'active');

        // Search filter (by name or description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Modality filter
        if ($request->filled('modality')) {
            $query->where('modality', $request->modality);
        }

        // Tags filter
        if ($request->filled('tags')) {
            $tagIds = $request->tags;
            $query->whereHas('tags', function($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        // Order by start date ascending (upcoming first)
        $events = $query->orderBy('start_date', 'asc')
            ->paginate(12)
            ->withQueryString();

        // Get all tags for filters
        $tags = Tag::orderBy('name')->get();

        return view('public.events.index', compact('events', 'tags'));
    }

    /**
     * Public detail of an event
     * Shows the event with all its approved components
     */
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

        // Ensure the event is public
        if ($event->visibility !== 'public') {
            abort(404, 'Event not found or not publicly available.');
        }

        // Group components by type
        $componentsByType = $event->approvedComponents->groupBy('type');

        return view('public.events.show', compact('event', 'componentsByType'));
    }
}
