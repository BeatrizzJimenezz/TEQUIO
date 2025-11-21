<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario debe cambiar su contraseña
        if (auth()->check() && auth()->user()->must_change_password) {
            if (!$request->routeIs('password.force-change') && 
                !$request->routeIs('password.force-update') && 
                !$request->routeIs('logout')) {
                return redirect()->route('password.force-change');
            }
        }
        
        return $next($request);
    }
}