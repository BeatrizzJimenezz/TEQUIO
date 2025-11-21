<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComponent;
use App\Services\ScheduleConflictValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProposalController extends Controller
{
    // Ver eventos públicos disponibles para enviar propuestas
    public function index()
    {
        $events = Event::where('visibility', 'public')
            ->where('status', '!=', 'finished')
            ->with('professionalProfile.user')
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('proposals.index', compact('events'));
    }

    // Formulario para crear una nueva propuesta
    public function create(Event $event)
    {
        if ($event->visibility !== 'public' || $event->status === 'finished') {
            abort(403, 'No puedes enviar propuestas a este evento.');
        }

        $profile = auth()->user()->professionalProfile;

        if (!$profile) {
            return redirect()->route('professional-profile.edit')
                ->with('error', 'Debes completar tu perfil profesional antes de enviar una propuesta.');
        }

        return view('proposals.create', compact('event', 'profile'));
    }

    // Guardar una nueva propuesta
    public function store(Request $request, Event $event)
    {
        if ($event->visibility !== 'public' || $event->status === 'finished') {
            abort(403, 'No puedes enviar propuestas a este evento.');
        }

        $profile = auth()->user()->professionalProfile;

        if (!$profile) {
            return redirect()->route('professional-profile.edit')
                ->with('error', 'Debes completar tu perfil profesional antes de enviar una propuesta.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:activity,talk,workshop',
            'modality' => 'required|in:virtual,in_person,hybrid',
            'location' => 'nullable|string|max:255',
            'cover' => 'nullable|url|max:500',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'capacity' => 'nullable|integer|min:1',
            'attendee_price' => 'nullable|numeric|min:0',
            'participant_requirements' => 'nullable|string',
            'schedules' => 'required|array|min:1',
            'schedules.*.date' => 'required|date',
            'schedules.*.start_time' => 'required',
            'schedules.*.end_time' => 'required|after:schedules.*.start_time',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'type.required' => 'El tipo es obligatorio.',
            'modality.required' => 'La modalidad es obligatoria.',
            'schedules.required' => 'Debes agregar al menos un horario.',
        ]);

        DB::beginTransaction();
        try {
            // Crear componente con estado "proposed"
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

            // Si hay conflictos, revertir y retornar errores
            if (!empty($allErrors)) {
                DB::rollBack();
                return back()->withInput()
                    ->withErrors($allErrors)
                    ->with('error', 'No se pudo enviar la propuesta debido a conflictos de horario.');
            }

            // Crear horarios si no hay conflictos
            foreach ($request->schedules as $schedule) {
                $component->schedules()->create([
                    'date' => $schedule['date'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }

            DB::commit();

            return redirect()->route('proposals.my-proposals')
                ->with('success', 'Propuesta enviada exitosamente. El organizador la revisará pronto.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al enviar la propuesta: ' . $e->getMessage());
        }
    }

    // Ver mis propuestas enviadas
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