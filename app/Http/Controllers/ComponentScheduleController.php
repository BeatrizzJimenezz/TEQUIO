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
    // Muestra el horario general de un evento
public function eventSchedule(Event $event): View
{
    $this->authorize('update', $event);

    $schedulesByDate = $this->validator->getSchedulesByDate($event->id);
    $conflicts = $this->validator->getEventConflicts($event->id);

    // ====== DEBUG TEMPORAL ======
    \Log::info('=== VISTA INDIVIDUAL (EVENT SCHEDULE) ===');
    \Log::info('Event ID: ' . $event->id);
    \Log::info('Event Dates: ' . $event->start_date->format('Y-m-d') . ' to ' . $event->end_date->format('Y-m-d'));
    \Log::info('Total Conflicts Found: ' . $conflicts->count());
    
    foreach ($conflicts as $index => $conflict) {
        \Log::info("Conflict #{$index}:", [
            'component_id' => $conflict['component']->id,
            'component_name' => $conflict['component']->name,
            'schedule_id' => $conflict['schedule']->id,
            'schedule_date' => $conflict['schedule']->date->format('Y-m-d'),
            'schedule_time' => $conflict['schedule']->start_time . ' - ' . $conflict['schedule']->end_time,
            'errors' => $conflict['errors'],
        ]);
    }

    $components = EventComponent::where('event_id', $event->id)
        ->with(['schedules', 'speaker.user', 'event'])
        ->get();
        
    \Log::info('All Components with Schedules:');
    foreach ($components as $component) {
        \Log::info("Component: {$component->name} (ID: {$component->id})");
        foreach ($component->schedules as $schedule) {
            \Log::info("  - Schedule ID {$schedule->id}: {$schedule->date->format('Y-m-d')} {$schedule->start_time}-{$schedule->end_time}");
        }
    }
    // ====== FIN DEBUG ======

    return view('schedules.event-overview', compact('event', 'schedulesByDate', 'conflicts'));
}

    // Muestra la lista de horarios para un componente específico
    public function index(Event $event, EventComponent $component): View
    {
        $this->authorize('update', $event);

        $schedules = $component->schedules()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('schedules.index', compact('event', 'component', 'schedules'));
    }

    // Muestra el formulario para crear un nuevo horario
    public function create(Event $event, EventComponent $component): View
    {
        $this->authorize('update', $event);

        return view('schedules.create', compact('event', 'component'));
    }

    // Almacena un nuevo horario
    public function store(Request $request, Event $event, EventComponent $component): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // Valida conflictos
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

        // Crea el horario
        $component->schedules()->create($validated);

        return redirect()
            ->route('schedules.index', [$event, $component])
            ->with('success', 'Horario creado exitosamente.');
    }

    // Muestra el formulario para editar un horario existente
    public function edit(Event $event, EventComponent $component, ComponentSchedule $schedule): View
    {
        $this->authorize('update', $event);

        return view('schedules.edit', compact('event', 'component', 'schedule'));
    }

    // Actualiza un horario existente
    public function update(Request $request, Event $event, EventComponent $component, ComponentSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // Valida conflictos excluyendo el horario actual
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

        //  Actualiza el horario
        $schedule->update($validated);

        return redirect()
            ->route('schedules.index', [$event, $component])
            ->with('success', 'Horario actualizado exitosamente.');
    }

    // Elimina un horario
    public function destroy(Event $event, EventComponent $component, ComponentSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $event);

        $schedule->delete();

        return redirect()
            ->route('schedules.index', [$event, $component])
            ->with('success', 'Horario eliminado exitosamente.');
    }
}
