<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComponent;
use App\Models\ComponentSchedule;
use App\Services\ScheduleConflictValidator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComponentScheduleController extends Controller
{
    protected ScheduleConflictValidator $validator;

    public function __construct(ScheduleConflictValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Display schedule overview for an event
     */
    public function eventSchedule(Event $event): View
    {
        $this->authorize('update', $event);

        $schedulesByDate = $this->validator->getSchedulesByDate($event->id);
        $conflicts = $this->validator->getEventConflicts($event->id);

        return view('schedules.event-overview', compact('event', 'schedulesByDate', 'conflicts'));
    }

    /**
     * Display a listing of schedules for a component
     */
    public function index(Event $event, EventComponent $component): View
    {
        $this->authorize('update', $event);

        $schedules = $component->schedules()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('schedules.index', compact('event', 'component', 'schedules'));
    }

    /**
     * Show the form for creating a new schedule
     */
    public function create(Event $event, EventComponent $component): View
    {
        $this->authorize('update', $event);

        return view('schedules.create', compact('event', 'component'));
    }

    /**
     * Store a newly created schedule
     */
    public function store(Request $request, Event $event, EventComponent $component): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // Validate for conflicts
        $conflictCheck = $this->validator->validate(
            $component->id,
            $validated['date'],
            $validated['start_time'],
            $validated['end_time']
        );

        if (!$conflictCheck['valid']) {
            return back()
                ->withInput()
                ->withErrors($conflictCheck['errors']);
        }

        // Create the schedule
        $component->schedules()->create($validated);

        return redirect()
            ->route('schedules.index', [$event, $component])
            ->with('success', 'Horario creado exitosamente.');
    }

    /**
     * Show the form for editing a schedule
     */
    public function edit(Event $event, EventComponent $component, ComponentSchedule $schedule): View
    {
        $this->authorize('update', $event);

        return view('schedules.edit', compact('event', 'component', 'schedule'));
    }

    /**
     * Update the specified schedule
     */
    public function update(Request $request, Event $event, EventComponent $component, ComponentSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // Validate for conflicts (exclude current schedule from check)
        $conflictCheck = $this->validator->validate(
            $component->id,
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $schedule->id
        );

        if (!$conflictCheck['valid']) {
            return back()
                ->withInput()
                ->withErrors($conflictCheck['errors']);
        }

        // Update the schedule
        $schedule->update($validated);

        return redirect()
            ->route('schedules.index', [$event, $component])
            ->with('success', 'Horario actualizado exitosamente.');
    }

    /**
     * Remove the specified schedule
     */
    public function destroy(Event $event, EventComponent $component, ComponentSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $event);

        $schedule->delete();

        return redirect()
            ->route('schedules.index', [$event, $component])
            ->with('success', 'Horario eliminado exitosamente.');
    }
}
