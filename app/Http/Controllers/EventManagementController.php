<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\ScheduleConflictValidator;
use Illuminate\Http\Request;

class EventManagementController extends Controller
{
    protected ScheduleConflictValidator $validator;

    public function __construct(ScheduleConflictValidator $validator)
    {
        $this->validator = $validator;
    }

    // Verificar que el usuario está autenticados
    private function checkPermissions()
    {
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión.');
        }
    }

    // Verificar que el usuario autenticado es el propietario del evento
    private function checkOwner(Event $event)
    {
        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para gestionar este evento.');
        }
    }

// Mostrar la vista de gestión del evento
public function index(Event $event)
{
    $this->checkPermissions();
    $this->checkOwner($event);

    $components = $event->components()
        ->with(['schedules', 'speaker.user'])
        ->orderBy('created_at', 'desc')
        ->get();

    $schedulesByDate = $this->validator->getSchedulesByDate($event->id);
    $conflicts = $this->validator->getEventConflicts($event->id);
    
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

    \Log::info('All Components with Schedules:');
    foreach ($components as $component) {
        \Log::info("Component: {$component->name} (ID: {$component->id})");
        foreach ($component->schedules as $schedule) {
            \Log::info("  - Schedule ID {$schedule->id}: {$schedule->date->format('Y-m-d')} {$schedule->start_time}-{$schedule->end_time}");
        }
    }

    $leadOrganizer = $event->professionalProfile;
    $collaborators = collect([]);

    return view('events.management.index', compact(
        'event',
        'components',
        'schedulesByDate',
        'conflicts',
        'leadOrganizer',
        'collaborators'
    ));
}
    private function detectConflicts(Event $event, $components)
    {
        $conflicts = collect();

        foreach ($components as $component) {
            foreach ($component->schedules as $schedule) {
                $errors = [];

                // Revise si la fecha está dentro del rango del evento
                if ($schedule->date < $event->start_date || $schedule->date > $event->end_date) {
                    $errors[] = "La fecha está fuera del rango del evento ({$event->start_date->format('d/m/Y')} - {$event->end_date->format('d/m/Y')})";
                }

                // Revisar solapamientos con otros componentes
                $overlapping = $components->filter(function ($otherComponent) use ($component, $schedule) {
                    if ($otherComponent->id === $component->id) {
                        return false;
                    }

                    return $otherComponent->schedules->contains(function ($otherSchedule) use ($schedule) {
                        return $otherSchedule->date->format('Y-m-d') === $schedule->date->format('Y-m-d')
                            && $this->timeRangesOverlap(
                                $schedule->start_time,
                                $schedule->end_time,
                                $otherSchedule->start_time,
                                $otherSchedule->end_time
                            );
                    });
                });

                if ($overlapping->count() > 0) {
                    $overlappingNames = $overlapping->pluck('name')->join(', ');
                    $errors[] = "Se solapa con: {$overlappingNames}";
                }

                // Revisar si el ponente tiene múltiples sesiones al mismo tiempo
                if ($component->speaker_id) {
                    $speakerConflicts = $components->filter(function ($otherComponent) use ($component, $schedule) {
                        return $otherComponent->id !== $component->id
                            && $otherComponent->speaker_id === $component->speaker_id
                            && $otherComponent->schedules->contains(function ($otherSchedule) use ($schedule) {
                                return $otherSchedule->date->format('Y-m-d') === $schedule->date->format('Y-m-d')
                                    && $this->timeRangesOverlap(
                                        $schedule->start_time,
                                        $schedule->end_time,
                                        $otherSchedule->start_time,
                                        $otherSchedule->end_time
                                    );
                            });
                    });

                    if ($speakerConflicts->count() > 0) {
                        $errors[] = "El ponente tiene otro componente programado en el mismo horario";
                    }
                }

                if (!empty($errors)) {
                    $conflicts->push([
                        'component' => $component,
                        'schedule' => $schedule,
                        'errors' => $errors
                    ]);
                }
            }
        }

        return $conflicts;
    }

    // Verifica si dos rangos de tiempo se solapan
    private function timeRangesOverlap($start1, $end1, $start2, $end2)
    {
        return $start1 < $end2 && $start2 < $end1;
    }
}