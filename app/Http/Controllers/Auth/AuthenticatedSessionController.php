<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    //Mostrar vista login
    public function create(): View
    {
        return view('auth.login');
    }
    //Autentificacion
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        //Revisamos si el usuario debe cambiar su contraseña
        if (auth()->user()->must_change_password) {
            return redirect()->route('password.force-change');
        }
        return redirect()->intended(route('dashboard', absolute: false));
    }

    //Cerrar sesion
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
