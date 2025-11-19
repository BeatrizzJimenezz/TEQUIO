<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private function checkPermissions()
    {
        if (!auth()->check()) {
            abort(401, 'You must be logged in.');
        }

        if (!auth()->user()->hasAnyRole(['Administrator', 'Organizer'])) {
            abort(403, 'You do not have permission to access this section.');
        }
    }

    public function index()
    {
        $user = auth()->user();
        $professionalProfile = $user->professionalProfile;

        if (!$professionalProfile) {
            $professionalProfile = $user->professionalProfile()->create([]);
        }

        $eventsAsCreator = Event::where('professional_profile_id', $professionalProfile->id)
            ->with(['tags', 'components'])
            ->orderBy('created_at', 'desc')
            ->get();

        $eventsAsCollaborator = $professionalProfile->collaborations()
            ->wherePivot('role', 'Organizer')
            ->with(['tags', 'components'])
            ->orderBy('events.created_at', 'desc')
            ->get();

        $events = $eventsAsCreator
            ->merge($eventsAsCollaborator)
            ->sortByDesc('created_at');

        return view('event.index', compact('events'));
    }

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
            ->with('success', 'Event created successfully.');
    }

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
                ->with('error', 'The event cannot be deleted because it has associated components.');
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

    public function show(Event $event)
    {
        if (
            $event->visibility === 'private' &&
            (!auth()->check() || $event->professionalProfile->user_id !== auth()->id())
        ) {
            abort(403, 'This event is private.');
        }

        $event->load([
            'tags',
            'professionalProfile.user',
            'approvedComponents.schedules'
        ]);

        $componentsByType = $event->approvedComponents->groupBy('type');

        return view('events.public.show', compact('event', 'componentsByType'));
    }
}
