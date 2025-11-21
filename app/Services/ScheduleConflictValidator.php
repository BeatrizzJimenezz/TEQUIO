<?php

namespace App\Services;

use App\Models\ComponentSchedule;
use App\Models\EventComponent;
use Illuminate\Support\Collection;

class ScheduleConflictValidator
{
    // Validar si un horario puede crearse sin conflictos
    public function validate(
        int $componentId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeScheduleId = null
    ): array {
        $errors = [];

        // Obtener el componente para este horario
        $component = EventComponent::with('speaker.user')->find($componentId);

        if (!$component) {
            return [
                'valid' => false,
                'errors' => ['Componente no encontrado']
            ];
        }

        // Crear horario temporal para verificar superposiciones
        $newSchedule = new ComponentSchedule([
            'component_id' => $componentId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Verificar conflictos de ubicación
        $locationConflict = $this->checkLocationConflict($component, $newSchedule, $excludeScheduleId);
        if ($locationConflict) {
            $errors[] = $locationConflict;
        }

        // Verificar conflictos de ponente
        $speakerConflict = $this->checkSpeakerConflict($component, $newSchedule, $excludeScheduleId);
        if ($speakerConflict) {
            $errors[] = $speakerConflict;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    // Verificar conflicto con otro componente en la misma ubicación
    protected function checkLocationConflict(
        EventComponent $component,
        ComponentSchedule $newSchedule,
        ?int $excludeScheduleId = null
    ): ?string {
        // Solo verificar ubicación para presencial o híbrido
        if (!in_array($component->modality, ['in_person', 'hybrid'])) {
            return null;
        }

        // Ubicación requerida para verificar
        if (empty($component->location)) {
            return null;
        }

        // Buscar horarios en la misma fecha excluyendo el actual
        $conflictingSchedules = ComponentSchedule::where('date', $newSchedule->date)
            ->when($excludeScheduleId, function ($query) use ($excludeScheduleId) {
                $query->where('id', '!=', $excludeScheduleId);
            })
            ->with('component')
            ->get()
            ->filter(function ($schedule) use ($component, $newSchedule) {
                // Omitir si es el mismo componente
                if ($schedule->component_id === $component->id) {
                    return false;
                }

                // Solo verificar componentes con la misma ubicación
                if ($schedule->component->location !== $component->location) {
                    return false;
                }

                // Solo verificar componentes presenciales o híbridos
                if (!in_array($schedule->component->modality, ['in_person', 'hybrid'])) {
                    return false;
                }

                // Verificar superposición de horas
                return $newSchedule->overlapsWith($schedule);
            });

        if ($conflictingSchedules->isNotEmpty()) {
            $conflictingComponent = $conflictingSchedules->first()->component;
            return "Conflicto de ubicación: '{$component->location}' ya está ocupado por '{$conflictingComponent->name}' en este horario.";
        }

        return null;
    }

    // Verificar conflicto con otro componente del mismo ponente
    protected function checkSpeakerConflict(
        EventComponent $component,
        ComponentSchedule $newSchedule,
        ?int $excludeScheduleId = null
    ): ?string {
        // Omitir si no hay ponente asignado
        if (!$component->speaker_id) {
            return null;
        }

        // Buscar horarios en la misma fecha
        $conflictingSchedules = ComponentSchedule::where('date', $newSchedule->date)
            ->when($excludeScheduleId, function ($query) use ($excludeScheduleId) {
                $query->where('id', '!=', $excludeScheduleId);
            })
            ->with('component')
            ->get()
            ->filter(function ($schedule) use ($component, $newSchedule) {
                // Omitir si es el mismo componente
                if ($schedule->component_id === $component->id) {
                    return false;
                }

                // Solo verificar componentes del mismo ponente
                if ($schedule->component->speaker_id !== $component->speaker_id) {
                    return false;
                }

                // Verificar superposición de horas
                return $newSchedule->overlapsWith($schedule);
            });

        if ($conflictingSchedules->isNotEmpty()) {
            $conflictingComponent = $conflictingSchedules->first()->component;
            $speakerName = $component->speaker->user->name ?? 'Ponente';
            return "Conflicto de ponente: {$speakerName} ya está asignado a '{$conflictingComponent->name}' en este horario.";
        }

        return null;
    }

    // Obtener todos los conflictos de un evento (para visualización)
    public function getEventConflicts(int $eventId): Collection
    {
        $conflicts = collect();

        $components = EventComponent::where('event_id', $eventId)
            ->with(['schedules', 'speaker.user'])
            ->get();

        foreach ($components as $component) {
            foreach ($component->schedules as $schedule) {
                $validation = $this->validate(
                    $component->id,
                    $schedule->date->format('Y-m-d'),
                    $schedule->start_time,
                    $schedule->end_time,
                    $schedule->id
                );

                if (!$validation['valid']) {
                    $conflicts->push([
                        'schedule' => $schedule,
                        'component' => $component,
                        'errors' => $validation['errors']
                    ]);
                }
            }
        }

        return $conflicts;
    }

    // Obtener horarios agrupados por fecha para un evento
    public function getSchedulesByDate(int $eventId): Collection
    {
        $schedulesByDate = collect();

        $components = EventComponent::where('event_id', $eventId)
            ->with(['schedules' => function ($query) {
                $query->orderBy('date')->orderBy('start_time');
            }, 'speaker.user'])
            ->get();

        foreach ($components as $component) {
            foreach ($component->schedules as $schedule) {
                $dateKey = $schedule->date->format('Y-m-d');

                if (!$schedulesByDate->has($dateKey)) {
                    $schedulesByDate->put($dateKey, collect());
                }

                $schedulesByDate->get($dateKey)->push([
                    'schedule' => $schedule,
                    'component' => $component,
                ]);
            }
        }

        // Ordenar horarios dentro de cada fecha por hora de inicio
        return $schedulesByDate->map(function ($schedules) {
            return $schedules->sortBy(function ($item) {
                return $item['schedule']->start_time;
            });
        })->sortKeys();
    }
}