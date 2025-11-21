<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{

    // Actualizar la contraseña del usuario autenticado
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        return back()->with('status', 'password-updated');
    }

    public function forceUpdate(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Actualizar la contraseña y restablecer el indicador
        auth()->user()->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Tu contraseña ha sido actualizada exitosamente.');
    }
}
