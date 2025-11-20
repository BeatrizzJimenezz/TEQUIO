<?php

namespace App\Services;

use App\Models\ComponentSchedule;
use App\Models\EventComponent;
use Illuminate\Support\Collection;

class ScheduleConflictValidator
{
    /**
     * Validate if a schedule can be created without conflicts
     *
     * @param int $componentId The component ID for the new schedule
     * @param string $date The date of the schedule
     * @param string $startTime The start time of the schedule
     * @param string $endTime The end time of the schedule
     * @param int|null $excludeScheduleId Optional schedule ID to exclude from validation (for updates)
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate(
        int $componentId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeScheduleId = null
    ): array {
        $errors = [];

        // Get the component for this schedule
        $component = EventComponent::with('speaker.user')->find($componentId);

        if (!$component) {
            return [
                'valid' => false,
                'errors' => ['Component not found']
            ];
        }

        // Create a temporary schedule object for overlap checking
        $newSchedule = new ComponentSchedule([
            'component_id' => $componentId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Check for location conflicts
        $locationConflict = $this->checkLocationConflict($component, $newSchedule, $excludeScheduleId);
        if ($locationConflict) {
            $errors[] = $locationConflict;
        }

        // Check for speaker conflicts
        $speakerConflict = $this->checkSpeakerConflict($component, $newSchedule, $excludeScheduleId);
        if ($speakerConflict) {
            $errors[] = $speakerConflict;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Check if the schedule conflicts with another component in the same location
     */
    protected function checkLocationConflict(
        EventComponent $component,
        ComponentSchedule $newSchedule,
        ?int $excludeScheduleId = null
    ): ?string {
        // Only check location conflicts for in_person or hybrid modalities
        if (!in_array($component->modality, ['in_person', 'hybrid'])) {
            return null;
        }

        // Location is required for in-person/hybrid components
        if (empty($component->location)) {
            return null;
        }

        // Find all schedules on the same date
        $conflictingSchedules = ComponentSchedule::where('date', $newSchedule->date)
            ->when($excludeScheduleId, function ($query) use ($excludeScheduleId) {
                $query->where('id', '!=', $excludeScheduleId);
            })
            ->with('component')
            ->get()
            ->filter(function ($schedule) use ($component, $newSchedule) {
                // Skip if it's the same component
                if ($schedule->component_id === $component->id) {
                    return false;
                }

                // Only check components with same location
                if ($schedule->component->location !== $component->location) {
                    return false;
                }

                // Only check in_person or hybrid components
                if (!in_array($schedule->component->modality, ['in_person', 'hybrid'])) {
                    return false;
                }

                // Check if times overlap
                return $newSchedule->overlapsWith($schedule);
            });

        if ($conflictingSchedules->isNotEmpty()) {
            $conflictingComponent = $conflictingSchedules->first()->component;
            return "Conflicto de ubicación: '{$component->location}' ya está ocupado por '{$conflictingComponent->name}' en este horario.";
        }

        return null;
    }

    /**
     * Check if the schedule conflicts with another component by the same speaker
     */
    protected function checkSpeakerConflict(
        EventComponent $component,
        ComponentSchedule $newSchedule,
        ?int $excludeScheduleId = null
    ): ?string {
        // Skip if component has no speaker assigned
        if (!$component->speaker_id) {
            return null;
        }

        // Find all schedules on the same date for components with the same speaker
        $conflictingSchedules = ComponentSchedule::where('date', $newSchedule->date)
            ->when($excludeScheduleId, function ($query) use ($excludeScheduleId) {
                $query->where('id', '!=', $excludeScheduleId);
            })
            ->with('component')
            ->get()
            ->filter(function ($schedule) use ($component, $newSchedule) {
                // Skip if it's the same component
                if ($schedule->component_id === $component->id) {
                    return false;
                }

                // Only check components with the same speaker
                if ($schedule->component->speaker_id !== $component->speaker_id) {
                    return false;
                }

                // Check if times overlap
                return $newSchedule->overlapsWith($schedule);
            });

        if ($conflictingSchedules->isNotEmpty()) {
            $conflictingComponent = $conflictingSchedules->first()->component;
            $speakerName = $component->speaker->user->name ?? 'Ponente';
            return "Conflicto de ponente: {$speakerName} ya está asignado a '{$conflictingComponent->name}' en este horario.";
        }

        return null;
    }

    /**
     * Get all conflicts for a given event (for visualization)
     */
    public function getEventConflicts(int $eventId): Collection
    {
        $conflicts = collect();

        // Get all components for the event
        $components = EventComponent::where('event_id', $eventId)
            ->with(['schedules', 'speaker.user'])
            ->get();

        // Check each schedule for conflicts
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

    /**
     * Get schedules grouped by date for an event
     */
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

        // Sort schedules within each date by start time
        return $schedulesByDate->map(function ($schedules) {
            return $schedules->sortBy(function ($item) {
                return $item['schedule']->start_time;
            });
        })->sortKeys();
    }
}
