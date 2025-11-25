<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfessionalProfile;
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
        // Reglas base de validación
        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'register_as_speaker' => ['nullable', 'boolean'],
        ];

        // Si se registra como ponente, agregar validaciones adicionales
        if ($request->register_as_speaker) {
            $rules['about_me'] = ['required', 'string', 'min:20', 'max:1000'];
            $rules['skills'] = ['required', 'string', 'max:500'];
            $rules['current_workplace'] = ['nullable', 'string', 'max:255'];
        }

        $messages = [
            'name.regex' => 'El nombre solo puede contener letras.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un símbolo (@, $, !, %, etc).',
            'about_me.required' => 'La descripción es requerida para registrarse como ponente.',
            'about_me.min' => 'La descripción debe tener al menos 20 caracteres.',
            'skills.required' => 'Las habilidades son requeridas para registrarse como ponente.',
        ];

        $request->validate($rules, $messages);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar rol de Participante por defecto
        $user->assignRole('Participante');

        // Si se registra como ponente, crear su perfil profesional
        if ($request->register_as_speaker) {
            ProfessionalProfile::create([
                'user_id' => $user->id,
                'about_me' => $request->about_me,
                'skills' => $request->skills,
                'current_workplace' => $request->current_workplace,
                'is_temporary' => false,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}