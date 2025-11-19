<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\EventComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    // Register for a component
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to register.'
            ], 401);
        }

        $request->validate([
            'component_id' => 'required|exists:event_components,id'
        ]);

        $userId = auth()->id();
        $componentId = $request->component_id;

        $component = EventComponent::with(['schedules', 'event'])->findOrFail($componentId);

        // VALIDATION 1: Already registered
        $alreadyRegistered = Registration::where('user_id', $userId)
            ->where('component_id', $componentId)
            ->exists();

        if ($alreadyRegistered) {
            return response()->json([
                'success' => false,
                'message' => 'You are already registered for this component.'
            ], 422);
        }

        // VALIDATION 2: Capacity check
        if ($component->capacity) {
            $registeredCount = Registration::where('component_id', $componentId)->count();
            if ($registeredCount >= $component->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, there are no spots available.'
                ], 422);
            }
        }

        // VALIDATION 3: Schedule conflict
        $newSchedules = $component->schedules;

        $registeredSchedules = DB::table('component_schedules as s')
            ->join('registrations as r', 's.component_id', '=', 'r.component_id')
            ->where('r.user_id', $userId)
            ->select('s.*')
            ->get();

        foreach ($newSchedules as $new) {
            foreach ($registeredSchedules as $existing) {
                if ($new->date == $existing->date) {
                    $newStart = strtotime($new->start_time);
                    $newEnd = strtotime($new->end_time);
                    $existingStart = strtotime($existing->start_time);
                    $existingEnd = strtotime($existing->end_time);

                    if ($newStart < $existingEnd && $newEnd > $existingStart) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Schedule conflict. You are already registered in another activity at this time.'
                        ], 422);
                    }
                }
            }
        }

        try {
            $registration = Registration::create([
                'user_id' => $userId,
                'component_id' => $componentId,
                'ticket_qr' => Registration::generateTicketQR(),
                'registration_date' => now(),
                'expiration_date' => $component->event->end_date->addDays(1),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful!',
                'data' => [
                    'registration_id' => $registration->id,
                    'ticket' => $registration->ticket_qr
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating registration: ' . $e->getMessage()
            ], 500);
        }
    }

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
        ->orderBy('created_at', 'desc') // usar created_at si no tienes registration_date
        ->get();

        return view('registrations.my-registrations', compact('registrations'));
    }



    // View my registrations
    public function myRegistrations()
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in.'
            ], 401);
        }

        $registrations = Registration::with([
            'component.schedules',
            'component.event'
        ])
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $registrations
        ]);
    }

    // Cancel registration
    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in.'
            ], 401);
        }

        $registration = Registration::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found or you do not have permission.'
            ], 404);
        }

        try {
            $registration->delete();
            return response()->json([
                'success' => true,
                'message' => 'Registration cancelled successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling registration.'
            ], 500);
        }
    }

    // Check if user is registered
    public function checkRegistration($componentId)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => true,
                'registered' => false,
                'authenticated' => false
            ]);
        }

        $registered = Registration::where('user_id', auth()->id())
            ->where('component_id', $componentId)
            ->exists();

        return response()->json([
            'success' => true,
            'registered' => $registered,
            'authenticated' => true
        ]);
    }
}