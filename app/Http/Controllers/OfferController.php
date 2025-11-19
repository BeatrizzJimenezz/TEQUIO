<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    private function verifyOwner(Event $event)
    {
        if (!auth()->check()) {
            abort(401, 'You must be logged in.');
        }

        if (!auth()->user()->hasAnyRole(['Administrador', 'Organizador'])) {
            abort(403, 'You do not have permission to access this section.');
        }

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to manage this event.');
        }
    }

    // Create an open offer
    public function create(Event $event)
    {
        $this->verifyOwner($event);

        return view('offers.create', compact('event'));
    }

    // Store an offer
    public function store(Request $request, Event $event)
    {
        $this->verifyOwner($event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Activity,Presentation,Workshop',
            'modality' => 'required|in:virtual,presential,hybrid',
            'location' => 'nullable|string|max:255',
            'level' => 'nullable|in:Beginner,Intermediate,Advanced',
            'capacity' => 'nullable|integer|min:1',
            'organizer_cost' => 'nullable|numeric|min:0',
            'instructor_requirements' => 'nullable|string',
            'schedules' => 'required|array|min:1',
            'schedules.*.date' => 'required|date',
            'schedules.*.start_time' => 'required',
            'schedules.*.end_time' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $component = $event->components()->create([
                'proposed_by_user_id' => auth()->id(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'modality' => $validated['modality'],
                'location' => $validated['location'] ?? null,
                'level' => $validated['level'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'organizer_cost' => $validated['organizer_cost'] ?? null,
                'instructor_requirements' => $validated['instructor_requirements'] ?? null,
                'proposal_status' => 'open-offer',
                'attendee_price' => 0,
            ]);

            foreach ($request->schedules as $schedule) {
                $component->schedules()->create([
                    'date' => $schedule['date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }

            DB::commit();

            return redirect()->route('offers.index', $event)
                ->with('success', 'Offer published successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error publishing the offer: ' . $e->getMessage());
        }
    }

    // View event offers (for organizer)
    public function index(Event $event)
    {
        $this->verifyOwner($event);

        $offers = $event->components()
            ->where('proposal_status', 'open-offer')
            ->with('schedules')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('offers.index', compact('event', 'offers'));
    }

    // View public offers (for presenters)
    public function publicList()
    {
        $offers = EventComponent::where('proposal_status', 'open-offer')
            ->whereHas('event', function ($q) {
                $q->where('visibility', 'public')
                  ->where('status', '!=', 'finished');
            })
            ->with(['event', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('offers.public', compact('offers'));
    }

    // Apply to an offer
    public function apply(Request $request, EventComponent $offer)
    {
        if ($offer->proposal_status !== 'open-offer') {
            abort(403, 'This offer is no longer available.');
        }

        $profile = auth()->user()->professionalProfile;
        if (!$profile) {
            return redirect()->route('professional-profile.edit')
                ->with('error', 'You must complete your professional profile before applying.');
        }

        $alreadyApplied = \App\Models\OfferApplication::where('component_id', $offer->id)
            ->where('professional_profile_id', $profile->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'You have already applied to this offer.');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            \App\Models\OfferApplication::create([
                'component_id' => $offer->id,
                'professional_profile_id' => $profile->id,
                'message' => $validated['message'] ?? null,
                'status' => 'pending',
            ]);

            return redirect()->route('offers.public')
                ->with('success', 'Application submitted successfully. The organizer will review it.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error applying: ' . $e->getMessage());
        }
    }

    // Evaluation panel (pending proposals and offer applications)
    public function evaluation(Event $event)
    {
        $this->verifyOwner($event);

        $proposals = $event->components()
            ->where('proposal_status', 'proposed')
            ->with(['presenter.user', 'schedules'])
            ->orderBy('created_at', 'asc')
            ->get();

        $offersWithApplications = $event->components()
            ->where('proposal_status', 'open-offer')
            ->whereHas('applications', function ($q) {
                $q->where('status', 'pending');
            })
            ->with(['schedules', 'applications' => function ($q) {
                $q->where('status', 'pending')
                  ->with('professionalProfile.user', 'professionalProfile.academicFormation');
            }])
            ->get();

        return view('offers.evaluation', compact('event', 'proposals', 'offersWithApplications'));
    }

    // Approve proposal
    public function approve(Event $event, EventComponent $component)
    {
        $this->verifyOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404);
        }

        try {
            $component->update(['proposal_status' => 'approved']);

            return redirect()->route('offers.evaluation', $event)
                ->with('success', 'Proposal approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving proposal.');
        }
    }

    // Reject proposal
    public function reject(Request $request, Event $event, EventComponent $component)
    {
        $this->verifyOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404);
        }

        try {
            $component->update(['proposal_status' => 'rejected']);

            return redirect()->route('offers.evaluation', $event)
                ->with('success', 'Proposal rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting proposal.');
        }
    }

    // Accept an application
    public function acceptApplication(Event $event, EventComponent $offer, $applicationId)
    {
        $this->verifyOwner($event);

        if ($offer->event_id !== $event->id) {
            abort(404);
        }

        try {
            $application = \App\Models\OfferApplication::findOrFail($applicationId);

            DB::beginTransaction();

            $application->update(['status' => 'accepted']);

            \App\Models\OfferApplication::where('component_id', $offer->id)
                ->where('id', '!=', $applicationId)
                ->update(['status' => 'rejected']);

            $offer->update([
                'presenter_id' => $application->professional_profile_id,
                'proposal_status' => 'approved',
            ]);

            DB::commit();

            return redirect()->route('offers.evaluation', $event)
                ->with('success', 'Application accepted. Presenter assigned.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error accepting application: ' . $e->getMessage());
        }
    }

    // Reject individual application
    public function rejectApplication(Event $event, EventComponent $offer, $applicationId)
    {
        $this->verifyOwner($event);

        if ($offer->event_id !== $event->id) {
            abort(404);
        }

        try {
            $application = \App\Models\OfferApplication::findOrFail($applicationId);
            $application->update(['status' => 'rejected']);

            return redirect()->route('offers.evaluation', $event)
                ->with('success', 'Application rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting application.');
        }
    }

    // Close offer (stop receiving applications)
    public function closeOffer(Event $event, EventComponent $offer)
    {
        $this->verifyOwner($event);

        if ($offer->event_id !== $event->id) {
            abort(404);
        }

        if ($offer->proposal_status !== 'open-offer') {
            return back()->with('error', 'This offer is no longer open.');
        }

        try {
            DB::beginTransaction();

            \App\Models\OfferApplication::where('component_id', $offer->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            $offer->update(['proposal_status' => 'rejected']);

            DB::commit();

            return redirect()->route('offers.index', $event)
                ->with('success', 'Offer closed. No more applications will be accepted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error closing offer: ' . $e->getMessage());
        }
    }

    // Reopen offer
    public function reopenOffer(Event $event, EventComponent $offer)
    {
        $this->verifyOwner($event);

        if ($offer->event_id !== $event->id) {
            abort(404);
        }

        try {
            $offer->update(['proposal_status' => 'open-offer']);

            return redirect()->route('offers.index', $event)
                ->with('success', 'Offer reopened. New applications can be submitted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error reopening offer.');
        }
    }
}