<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class MyRegistrationsController extends Controller
{
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
        ->orderBy('registration_date', 'desc')
        ->get();

        return view('registrations.index', compact('registrations'));
    }

    public function cancel($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $registration = Registration::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$registration) {
            return redirect()->route('my-registrations')
                ->with('error', 'Registration not found.');
        }

        try {
            $registration->delete();
            return redirect()->route('my-registrations')
                ->with('success', 'Registration cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->route('my-registrations')
                ->with('error', 'Error cancelling the registration.');
        }
    }
}