<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\EventComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    // Listar inscripciones del usuario autenticado
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $registrations = Registration::with([
            'component.schedules',
            'component.event'
        ])
        ->where('user_id', auth()->id())
        ->orderBy('registered_at', 'desc')
        ->get();

        return view('registrations.index', compact('registrations'));
    }

    // Inscribirse en un componente
    public function store(Request $request)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes iniciar sesión para inscribirte.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para inscribirte.');
        }

        $request->validate([
            'component_id' => 'required|exists:event_components,id'
        ]);

        $userId = auth()->id();
        $componentId = $request->component_id;

        $component = EventComponent::with(['schedules', 'event'])->findOrFail($componentId);

        // VALIDACIÓN 1: Ya inscrito
        $alreadyRegistered = Registration::where('user_id', $userId)
            ->where('component_id', $componentId)
            ->exists();

        if ($alreadyRegistered) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya estás inscrito en este componente.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Ya estás inscrito en este componente.');
        }

        // VALIDACIÓN 2: Verificación de capacidad
        if ($component->capacity) {
            $registeredCount = Registration::where('component_id', $componentId)->count();
            if ($registeredCount >= $component->capacity) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lo sentimos, ya no hay cupos disponibles.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Lo sentimos, ya no hay cupos disponibles.');
            }
        }

        // VALIDACIÓN 3: Conflicto de horarios
        $newSchedules = $component->schedules;

        $registeredSchedules = DB::table('component_schedules as s')
            ->join('registrations as r', 's.component_id', '=', 'r.component_id')
            ->where('r.user_id', $userId)
            ->select('s.*')
            ->get();

        foreach ($newSchedules as $new) {
            foreach ($registeredSchedules as $existing) {
                if ($new->date->format('Y-m-d') == $existing->date) {
                    $newStart = strtotime($new->start_time);
                    $newEnd = strtotime($new->end_time);
                    $existingStart = strtotime($existing->start_time);
                    $existingEnd = strtotime($existing->end_time);

                    if ($newStart < $existingEnd && $newEnd > $existingStart) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Conflicto de horario. Ya estás inscrito en otra actividad a la misma hora.'
                            ], 422);
                        }
                        return redirect()->back()->with('error', 'Conflicto de horario. Ya estás inscrito en otra actividad a la misma hora.');
                    }
                }
            }
        }

        try {
            $registration = Registration::create([
                'user_id' => $userId,
                'component_id' => $componentId,
                'ticket_qr' => Registration::generateTicketQR(),
                'registered_at' => now(),
                'expires_at' => $component->event->end_date->addDays(1),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Inscripción exitosa!',
                    'data' => [
                        'registration_id' => $registration->id,
                        'ticket' => $registration->ticket_qr
                    ]
                ], 201);
            }

            return redirect()->back()->with('success', '¡Inscripción exitosa! Te has registrado en ' . $component->name);

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la inscripción: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error al crear la inscripción. Por favor intenta de nuevo.');
        }
    }

    // Cancelar inscripción
    public function destroy(Request $request, $id)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes iniciar sesión.'
                ], 401);
            }
            return redirect()->route('login');
        }

        $registration = Registration::with('component.schedules')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$registration) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inscripción no encontrada o no tienes permisos.'
                ], 404);
            }
            return redirect()->route('registrations.index')
                ->with('error', 'Inscripción no encontrada.');
        }

        // VALIDACIÓN: Solo se puede cancelar 2 días antes de que inicie la actividad
        if (!$registration->canBeCancelled()) {
            $daysUntil = $registration->daysUntilStart();
            $message = 'No puedes cancelar tu inscripción. Solo es posible cancelar hasta 2 días antes del inicio de la actividad.';

            if ($daysUntil !== null && $daysUntil >= 0) {
                $message .= " La actividad comienza en {$daysUntil} día(s).";
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }
            return redirect()->route('registrations.index')
                ->with('error', $message);
        }

        try {
            $registration->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inscripción cancelada exitosamente.'
                ]);
            }
            return redirect()->route('registrations.index')
                ->with('success', 'Inscripción cancelada exitosamente.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cancelar la inscripción.'
                ], 500);
            }
            return redirect()->route('registrations.index')
                ->with('error', 'Error al cancelar la inscripción.');
        }
    }

    // Verificar si el usuario está inscrito en un componente
    public function checkStatus($componentId)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => true,
                'registered' => false,
                'authenticated' => false
            ]);
        }

        $registration = Registration::where('user_id', auth()->id())
            ->where('component_id', $componentId)
            ->first();

        $component = EventComponent::find($componentId);
        $availableSlots = $component ? $component->available_seats : null;

        return response()->json([
            'success' => true,
            'registered' => $registration !== null,
            'authenticated' => true,
            'registration_id' => $registration?->id,
            'available_slots' => $availableSlots
        ]);
    }
}
