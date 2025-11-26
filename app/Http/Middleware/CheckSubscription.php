<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Event;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is Admin or has active subscription, allow
        if ($user->hasRole('Administrador') || $user->hasActiveSubscription()) {
            return $next($request);
        }

        // If user is not subscribed, check event limit
        // Assuming 'Organizador' role.
        // Limit: 1 Active Event (only 'active' status, not 'planning')

        // Count ONLY active events (status = 'active')
        $activeEventsCount = Event::whereHas('professionalProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'active')->count();

        if ($activeEventsCount >= 1) {
            return redirect()->route('paypal.index')
                ->with('warning', 'Has alcanzado el límite de eventos gratuitos (1 evento activo). Suscríbete para crear más eventos activos.');
        }

        return $next($request);
    }
}
