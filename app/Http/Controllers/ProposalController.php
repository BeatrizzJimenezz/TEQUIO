<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProposalController extends Controller
{
    // View public events for proposing a component
    public function index()
    {
        $events = Event::where('visibility', 'public')
            ->where('status', '!=', 'finished')
            ->with('professionalProfile.user')
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('proposals.index', compact('events'));
    }

    // Form to create a new proposal
    public function create(Event $event)
    {
        // Check that the event is public and not finished
        if ($event->visibility !== 'public' || $event->status === 'finished') {
            abort(403, 'You cannot submit proposals to this event.');
        }

        // Check that the user has a professional profile
        $profile = auth()->user()->professionalProfile;
        if (!$profile) {
            return redirect()->route('professional-profile.edit')
                ->with('error', 'You must complete your professional profile before submitting a proposal.');
        }

        return view('proposals.create', compact('event', 'profile'));
    }

    // Store a new proposal
    public function store(Request $request, Event $event)
    {
        if ($event->visibility !== 'public' || $event->status === 'finished') {
            abort(403, 'You cannot submit proposals to this event.');
        }

        $profile = auth()->user()->professionalProfile;
        if (!$profile) {
            return redirect()->route('professional-profile.edit')
                ->with('error', 'You must complete your professional profile before submitting a proposal.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Activity,Presentation,Workshop',
            'modality' => 'required|in:virtual,presential,hybrid',
            'location' => 'nullable|string|max:255',
            'cover' => 'nullable|url|max:500',
            'level' => 'nullable|in:Beginner,Intermediate,Advanced',
            'capacity' => 'nullable|integer|min:1',
            'attendee_price' => 'nullable|numeric|min:0',
            'participant_requirements' => 'nullable|string',
            'schedules' => 'required|array|min:1',
            'schedules.*.date' => 'required|date',
            'schedules.*.start_time' => 'required',
            'schedules.*.end_time' => 'required|after:schedules.*.start_time',
        ], [
            'name.required' => 'The name is required.',
            'description.required' => 'The description is required.',
            'type.required' => 'The type is required.',
            'modality.required' => 'The modality is required.',
            'schedules.required' => 'You must add at least one schedule.',
        ]);

        DB::beginTransaction();
        try {
            // Create the component with status "proposed"
            $component = $event->components()->create([
                'presenter_id' => $profile->id,
                'proposed_by_user_id' => auth()->id(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'modality' => $validated['modality'],
                'location' => $validated['location'] ?? null,
                'cover' => $validated['cover'] ?? null,
                'level' => $validated['level'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'attendee_price' => $validated['attendee_price'] ?? 0,
                'participant_requirements' => $validated['participant_requirements'] ?? null,
                'proposal_status' => 'proposed',
            ]);

            // Create schedules
            foreach ($request->schedules as $schedule) {
                $component->schedules()->create([
                    'date' => $schedule['date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }

            DB::commit();

            return redirect()->route('proposals.my-proposals')
                ->with('success', 'Proposal submitted successfully. The organizer will review it soon.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error submitting the proposal: ' . $e->getMessage());
        }
    }

    // View my proposals
    public function myProposals()
    {
        $proposals = EventComponent::where('proposed_by_user_id', auth()->id())
            ->whereIn('proposal_status', ['proposed', 'approved', 'rejected'])
            ->with(['event', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('proposals.my-proposals', compact('proposals'));
    }
}
