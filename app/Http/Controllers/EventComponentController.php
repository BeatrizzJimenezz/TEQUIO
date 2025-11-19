<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComponent;
use App\Models\ProfessionalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventComponentController extends Controller
{
    /** Check permissions */
    private function checkPermissions()
    {
        if (!auth()->check()) {
            abort(401, 'You must be logged in.');
        }

        if (!auth()->user()->hasAnyRole(['Administrator', 'Organizer'])) {
            abort(403, 'You do not have permission to access this section.');
        }
    }

    /** Check event owner */
    private function checkOwner(Event $event)
    {
        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to manage this event.');
        }
    }

    /** List event components */
    public function index(Event $event)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        $components = $event->components()
            ->with('schedules', 'speaker')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('event_components.index', compact('event', 'components'));
    }

    /** Show create form */
    public function create(Event $event)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        $speakers = ProfessionalProfile::all();

        return view('event_components.create', compact('event', 'speakers'));
    }

    /** Store component */
    public function store(Request $request, Event $event)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:activity,talk,workshop',
            'modality' => 'required|in:virtual,in_person,hybrid',
            'location' => 'nullable|string|max:255',
            'cover_image' => 'nullable|url|max:500',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'capacity' => 'nullable|integer|min:1',
            'attendee_price' => 'nullable|numeric|min:0',
            'organizer_cost' => 'nullable|numeric|min:0',
            'participant_requirements' => 'nullable|string',
            'instructor_requirements' => 'nullable|string',
            'schedules' => 'required|array|min:1',
            'schedules.*.date' => 'required|date',
            'schedules.*.start_time' => 'required',
            'schedules.*.end_time' => 'required|after:schedules.*.start_time',
            'speaker_id' => 'nullable|exists:professional_profiles,id',
        ]);

        DB::beginTransaction();

        try {
            $component = $event->components()->create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'modality' => $validated['modality'],
                'location' => $validated['location'] ?? null,
                'cover_image' => $validated['cover_image'] ?? null,
                'level' => $validated['level'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'attendee_price' => $validated['attendee_price'] ?? 0,
                'organizer_cost' => $validated['organizer_cost'] ?? null,
                'participant_requirements' => $validated['participant_requirements'] ?? null,
                'instructor_requirements' => $validated['instructor_requirements'] ?? null,
                'proposal_status' => 'approved',
                'proposed_by_user_id' => auth()->id(),
                'speaker_id' => $validated['speaker_id'] ?? null,
            ]);

            foreach ($validated['schedules'] as $schedule) {
                $component->schedules()->create([
                    'date' => $schedule['date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }

            DB::commit();

            return redirect()->route('event_components.index', $event)
                ->with('success', 'Component successfully created.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Error creating component: ' . $e->getMessage());
        }
    }

    /** Edit form */
    public function edit(Event $event, EventComponent $component)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404, 'Component not found.');
        }

        $component->load('schedules');
        $speakers = ProfessionalProfile::all();

        return view('event_components.edit', compact('event', 'component', 'speakers'));
    }

    /** Update component */
    public function update(Request $request, Event $event, EventComponent $component)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404, 'Component not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:activity,talk,workshop',
            'modality' => 'required|in:virtual,in_person,hybrid',
            'location' => 'nullable|string|max:255',
            'cover_image' => 'nullable|url|max:500',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'capacity' => 'nullable|integer|min:1',
            'attendee_price' => 'nullable|numeric|min:0',
            'organizer_cost' => 'nullable|numeric|min:0',
            'participant_requirements' => 'nullable|string',
            'instructor_requirements' => 'nullable|string',
            'schedules' => 'required|array|min:1',
            'schedules.*.date' => 'required|date',
            'schedules.*.start_time' => 'required',
            'schedules.*.end_time' => 'required',
            'speaker_id' => 'nullable|exists:professional_profiles,id',
        ]);

        DB::beginTransaction();

        try {
            $component->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'modality' => $validated['modality'],
                'location' => $validated['location'] ?? null,
                'cover_image' => $validated['cover_image'] ?? null,
                'level' => $validated['level'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'attendee_price' => $validated['attendee_price'] ?? 0,
                'organizer_cost' => $validated['organizer_cost'] ?? null,
                'participant_requirements' => $validated['participant_requirements'] ?? null,
                'instructor_requirements' => $validated['instructor_requirements'] ?? null,
                'speaker_id' => $validated['speaker_id'] ?? null,
            ]);

            $component->schedules()->delete();

            foreach ($validated['schedules'] as $schedule) {
                $component->schedules()->create([
                    'date' => $schedule['date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }

            DB::commit();

            return redirect()->route('event_components.index', $event)
                ->with('success', 'Component updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Error updating component: ' . $e->getMessage());
        }
    }

    /** Delete component */
    public function destroy(Event $event, EventComponent $component)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404, 'Component not found.');
        }

        try {
            $component->delete();

            return redirect()->route('event_components.index', $event)
                ->with('success', 'Component deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('event_components.index', $event)
                ->with('error', 'This component cannot be deleted because it has registrations.');
        }
    }
}
