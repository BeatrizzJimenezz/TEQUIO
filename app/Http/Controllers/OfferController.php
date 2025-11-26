<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComponent;
use App\Models\OfferApplication;
use App\Notifications\ProposalStatusChanged;
use App\Services\ScheduleConflictValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    // Verificar permisos de dueño del evento
    private function verifyOwner(Event $event)
    {
        if (!auth()->check()) {
            abort(401, 'Debes iniciar sesión.');
        }

        if (!auth()->user()->hasAnyRole(['Administrador', 'Organizador'])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        if ($event->professionalProfile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para gestionar este evento.');
        }
    }

    // Formulario para crear una oferta abierta
    public function create(Event $event)
    {
        $this->verifyOwner($event);

        return view('offers.create', compact('event'));
    }

    // Guardar una nueva oferta
    public function store(Request $request, Event $event)
    {
        $this->verifyOwner($event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:activity,talk,workshop',
            'modality' => 'required|in:virtual,in_person,hybrid',
            'location' => 'nullable|string|max:255',
            'level' => 'nullable|in:beginner,intermediate,advanced',
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
                'proposal_status' => 'offer_open',
                'price' => 0,
                'payment_required' => false,
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
                    ->with('error', 'No se pudo publicar la oferta debido a conflictos de horario.');
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

            return redirect()->route('offers.index', $event)
                ->with('success', 'Oferta publicada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al publicar la oferta: ' . $e->getMessage());
        }
    }

    // Ver ofertas del evento (para organizador)
    public function index(Event $event)
    {
        $this->verifyOwner($event);

        $offers = $event->components()
            ->where('proposal_status', 'offer_open')
            ->with('schedules')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('offers.index', compact('event', 'offers'));
    }

    // Ver ofertas públicas (para ponentes)
    public function publicList()
    {
        $offers = EventComponent::where('proposal_status', 'offer_open')
            ->whereHas('event', function ($q) {
                $q->where('visibility', 'public')
                  ->where('status', '!=', 'finished');
            })
            ->with(['event', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('offers.public', compact('offers'));
    }

    // Aplicar a una oferta
    public function apply(Request $request, EventComponent $offer)
    {
        if ($offer->proposal_status !== 'offer_open') {
            abort(403, 'Esta oferta ya no está disponible.');
        }

        $profile = auth()->user()->professionalProfile;
        if (!$profile) {
            return redirect()->route('professional-profile.edit')
                ->with('error', 'Debes completar tu perfil profesional antes de aplicar.');
        }

        $alreadyApplied = OfferApplication::where('component_id', $offer->id)
            ->where('professional_profile_id', $profile->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'Ya has aplicado a esta oferta.');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            $application = OfferApplication::create([
                'component_id' => $offer->id,
                'professional_profile_id' => $profile->id,
                'message' => $validated['message'] ?? null,
                'status' => 'pending',
            ]);

            // Notificar al organizador del evento
            $organizer = $offer->event->professionalProfile->user;
            if ($organizer) {
                $organizer->notify(new \App\Notifications\NewOfferApplication($offer, $application, auth()->user()));
            }

            return redirect()->route('offers.public')
                ->with('success', 'Solicitud enviada exitosamente. El organizador la revisará.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al aplicar: ' . $e->getMessage());
        }
    }

    // Panel de evaluación (propuestas y solicitudes)
    public function evaluation(Event $event)
    {
        $this->verifyOwner($event);

        // CORREGIDO: Cargamos 'proposedBy' para ver quién creó la propuesta
        // y quitamos el 'whereHas' estricto para que aparezcan todas.
        $proposals = $event->components()
            ->where('proposal_status', 'proposed')
            ->with(['proposedBy', 'presenter.user', 'schedules']) 
            ->orderBy('created_at', 'asc')
            ->get();

        $offersWithApplications = $event->components()
            ->where('proposal_status', 'offer_open')
            ->whereHas('applications', function ($q) {
                $q->where('status', 'pending');
            })
            ->with(['schedules', 'applications' => function ($q) {
                $q->where('status', 'pending')
                  ->with('professionalProfile.user', 'professionalProfile.academicTrainings');
            }])
            ->get();

        return view('offers.evaluation', compact('event', 'proposals', 'offersWithApplications'));
    }

    // Aprobar propuesta
    public function approve(Event $event, EventComponent $component)
    {
        $this->verifyOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404);
        }

        try {
            $component->update(['proposal_status' => 'approved']);

            // Notificar al proponente
            if ($component->proposedBy) {
                $component->proposedBy->notify(new ProposalStatusChanged($component, 'approved'));
            }

            return redirect()->route('offers.evaluation', $event)
                ->with('success', 'Propuesta aprobada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al aprobar la propuesta.');
        }
    }

    // Rechazar propuesta
    public function reject(Request $request, Event $event, EventComponent $component)
    {
        $this->verifyOwner($event);

        if ($component->event_id !== $event->id) {
            abort(404);
        }

        try {
            $component->update(['proposal_status' => 'rejected']);

            // Notificar al proponente
            if ($component->proposedBy) {
                $component->proposedBy->notify(new ProposalStatusChanged($component, 'rejected'));
            }

            return redirect()->route('offers.evaluation', $event)
                ->with('success', 'Propuesta rechazada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al rechazar la propuesta.');
        }
    }

    // Aceptar solicitud de un ponente
    public function acceptApplication(Event $event, EventComponent $offer, $applicationId)
    {
        $this->verifyOwner($event);

        if ($offer->event_id !== $event->id) {
            abort(404);
        }

        try {
            $application = OfferApplication::findOrFail($applicationId);

            DB::beginTransaction();

            $application->update(['status' => 'accepted']);

            OfferApplication::where('component_id', $offer->id)
                ->where('id', '!=', $applicationId)
                ->update(['status' => 'rejected']);

            $offer->update([
                'presenter_id' => $application->professional_profile_id,
                'proposal_status' => 'approved',
            ]);

            DB::commit();

            return redirect()->route('offers.evaluation', $event)
                ->with('success', 'Solicitud aceptada. Ponente asignado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aceptar la solicitud: ' . $e->getMessage());
        }
    }

    // Rechazar solicitud individual
    public function rejectApplication(Event $event, EventComponent $offer, $applicationId)
    {
        $this->verifyOwner($event);

        if ($offer->event_id !== $event->id) {
            abort(404);
        }

        try {
            $application = OfferApplication::findOrFail($applicationId);
            $application->update(['status' => 'rejected']);

            return redirect()->route('offers.evaluation', $event)
                ->with('success', 'Solicitud rechazada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al rechazar la solicitud.');
        }
    }

    // Cerrar oferta (no recibir más solicitudes)
    public function closeOffer(Event $event, EventComponent $offer)
    {
        $this->verifyOwner($event);

        if ($offer->event_id !== $event->id) {
            abort(404);
        }

        if ($offer->proposal_status !== 'offer_open') {
            return back()->with('error', 'Esta oferta ya no está abierta.');
        }

        try {
            DB::beginTransaction();

            OfferApplication::where('component_id', $offer->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            $offer->update(['proposal_status' => 'rejected']);

            DB::commit();

            return redirect()->route('offers.index', $event)
                ->with('success', 'Oferta cerrada. No se aceptarán más solicitudes.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cerrar la oferta: ' . $e->getMessage());
        }
    }

    // Reabrir oferta
    public function reopenOffer(Event $event, EventComponent $offer)
    {
        $this->verifyOwner($event);

        if ($offer->event_id !== $event->id) {
            abort(404);
        }

        try {
            $offer->update(['proposal_status' => 'offer_open']);

            return redirect()->route('offers.index', $event)
                ->with('success', 'Oferta reabierta. Se pueden enviar nuevas solicitudes.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al reabrir la oferta.');
        }
    }
}