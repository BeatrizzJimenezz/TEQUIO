<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComponent;
use App\Models\ProfessionalProfile;
use App\Services\ScheduleConflictValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventComponentController extends Controller
{
    // Verificar autenticación y roles permitidos
    private function checkPermissions()
    {
        if (!auth()->check()) {
            abort(401, 'Debes iniciar sesión.');
        }

        if (!auth()->user()->hasAnyRole(['Administrador', 'Organizador'])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }

    // Verificar si el usuario es dueño del evento
    private function checkOwner(Event $event)
    {
        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para gestionar este evento.');
        }
    }

    // Mostrar detalles del evento y sus componentes
    public function index(Event $event)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        $components = $event->components()->with('schedules', 'speaker')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('components.index', compact('event', 'components'));
    }

    // Formulario para crear un componente
    public function create(Event $event)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        $speakers = ProfessionalProfile::all();

        return view('components.create', compact('event', 'speakers'));
    }

    // Guardar un nuevo componente
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

            // Validar conflictos de horario
            $validator = app(ScheduleConflictValidator::class);
            $allErrors = [];

            foreach ($request->schedules as $index => $schedule) {
                $conflictCheck = $validator->validate(
                    $component->id,
                    $schedule['date'],
                    $schedule['start_time'],
                    $schedule['end_time']
                );

                if (!$conflictCheck['valid']) {
                    foreach ($conflictCheck['errors'] as $error) {
                        $allErrors[] = "Horario " . ($index + 1) . ": " . $error;
                    }
                }
            }

            // Si hay conflictos, revertimos y retornamos errores
            if (!empty($allErrors)) {
                DB::rollBack();
                return back()->withInput()
                    ->withErrors($allErrors)
                    ->with('error', 'No se pudo crear el componente debido a conflictos de horario.');
            }

            // Creamos los horarios si no hay conflictos
            foreach ($request->schedules as $schedule) {
                $component->schedules()->create([
                    'date' => $schedule['date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }

            DB::commit();

            return redirect()->route('components.index', $event)
                ->with('success', 'Componente creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al crear componente: ' . $e->getMessage());
        }
    }

    // Formulario para editar un componente
    public function edit(Event $event, EventComponent $component)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404, 'Componente no encontrado.');
        }

        $component->load('schedules');
        $speakers = ProfessionalProfile::all();

        return view('components.edit', compact('event', 'component', 'speakers'));
    }

    // Actualizar un componente existente
    public function update(Request $request, Event $event, EventComponent $component)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404, 'Componente no encontrado.');
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

            // Eliminar horarios antiguos primero
            $component->schedules()->delete();

            // Validar nuevos horarios para conflictos
            $validator = app(ScheduleConflictValidator::class);
            $allErrors = [];

            foreach ($request->schedules as $index => $schedule) {
                $conflictCheck = $validator->validate(
                    $component->id,
                    $schedule['date'],
                    $schedule['start_time'],
                    $schedule['end_time']
                );

                if (!$conflictCheck['valid']) {
                    foreach ($conflictCheck['errors'] as $error) {
                        $allErrors[] = "Horario " . ($index + 1) . ": " . $error;
                    }
                }
            }

            // Si hay conflictos, revertir y retornar errores
            if (!empty($allErrors)) {
                DB::rollBack();
                return back()->withInput()
                    ->withErrors($allErrors)
                    ->with('error', 'No se pudo actualizar el componente debido a conflictos de horario.');
            }

            // Crear nuevos horarios si no hay conflictos
            foreach ($request->schedules as $schedule) {
                $component->schedules()->create([
                    'date' => $schedule['date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }

            DB::commit();

            return redirect()->route('components.index', $event)
                ->with('success', 'Componente actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al actualizar componente: ' . $e->getMessage());
        }
    }

    // Eliminar un componente
    public function destroy(Event $event, EventComponent $component)
    {
        $this->checkPermissions();
        $this->checkOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404, 'Componente no encontrado.');
        }

        try {
            $component->delete();
            return redirect()->route('components.index', $event)
                ->with('success', 'Componente eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('components.index', $event)
                ->with('error', 'No se puede eliminar el componente porque tiene registros asociados.');
        }
    }
}