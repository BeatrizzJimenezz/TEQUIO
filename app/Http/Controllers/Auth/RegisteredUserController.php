<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    // Abrir vista de registro
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Maneja una solicitud entrante de registro de usuario.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u'],

            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],

            'password' => [
                'required', 
                'confirmed', 
                // Reglas de contraseña personalizada
                // Al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo
                Rules\Password::min(8)
                    ->mixedCase()    
                    ->numbers()   
                    ->symbols()      
            ],
        ], [
            'name.regex' => 'El nombre solo puede contener letras.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un símbolo (@, $, !, %, etc).',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}