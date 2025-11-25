<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleRequestController extends Controller
{
    /**
     * Vista para que el participante solicite ser organizador
     */
    public function create()
    {
        if (!auth()->check()) {
            abort(401, 'Debes iniciar sesión.');
        }

        // Verificar si ya es organizador
        if (auth()->user()->hasRole('Organizador')) {
            return redirect()->route('dashboard')
                ->with('info', 'Ya tienes el rol de Organizador.');
        }

        // Verificar si tiene una solicitud pendiente
        $pendingRequest = RoleRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return redirect()->route('role-requests.my-requests')
                ->with('info', 'Ya tienes una solicitud pendiente de revisión.');
        }

        return view('role-requests.create');
    }

    /**
     * Guardar la solicitud del participante
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            abort(401, 'Debes iniciar sesión.');
        }

        // Verificar si ya es organizador
        if (auth()->user()->hasRole('Organizador')) {
            return redirect()->route('dashboard')
                ->with('info', 'Ya tienes el rol de Organizador.');
        }

        // Verificar si tiene una solicitud pendiente
        $pendingRequest = RoleRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($pendingRequest) {
            return redirect()->route('role-requests.my-requests')
                ->with('error', 'Ya tienes una solicitud pendiente.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:50|max:1000',
        ], [
            'reason.required' => 'Debes proporcionar un motivo para tu solicitud.',
            'reason.min' => 'El motivo debe tener al menos 50 caracteres.',
            'reason.max' => 'El motivo no puede exceder 1000 caracteres.',
        ]);

        RoleRequest::create([
            'user_id' => auth()->id(),
            'requested_role' => 'Organizador',
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('role-requests.my-requests')
            ->with('success', 'Tu solicitud ha sido enviada. Uno de nuestros colaboradores la revisará pronto.');
    }

    /**
     * Ver mis solicitudes (para el participante)
     */
    public function myRequests()
    {
        if (!auth()->check()) {
            abort(401, 'Debes iniciar sesión.');
        }

        $requests = RoleRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('role-requests.my-requests', compact('requests'));
    }

    /**
     * Panel de administración para ver solicitudes pendientes
     */
    public function index()
    {
        if (!auth()->check() || !auth()->user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $pendingRequests = RoleRequest::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $processedRequests = RoleRequest::whereIn('status', ['approved', 'rejected'])
            ->with(['user', 'reviewer'])
            ->orderBy('reviewed_at', 'desc')
            ->limit(20)
            ->get();

        return view('role-requests.index', compact('pendingRequests', 'processedRequests'));
    }

    /**
     * Aprobar una solicitud
     */
    public function approve(Request $request, RoleRequest $roleRequest)
    {
        if (!auth()->check() || !auth()->user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para esta acción.');
        }

        if (!$roleRequest->isPending()) {
            return back()->with('error', 'Esta solicitud ya fue procesada.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar la solicitud
            $roleRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'admin_notes' => $validated['admin_notes'] ?? null,
                'reviewed_at' => now(),
            ]);

            // Asignar el rol al usuario
            $user = $roleRequest->user;
            $user->assignRole('Organizador');

            // Enviar notificación por correo
            $user->notify(new \App\Notifications\RoleRequestProcessed($roleRequest, 'approved'));

            DB::commit();

            return redirect()->route('role-requests.index')
                ->with('success', "Solicitud aprobada. {$user->name} ahora es Organizador.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar una solicitud
     */
    public function reject(Request $request, RoleRequest $roleRequest)
    {
        if (!auth()->check() || !auth()->user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para esta acción.');
        }

        if (!$roleRequest->isPending()) {
            return back()->with('error', 'Esta solicitud ya fue procesada.');
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:500',
        ], [
            'admin_notes.required' => 'Debes proporcionar un motivo para el rechazo.',
        ]);

        try {
            $roleRequest->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'admin_notes' => $validated['admin_notes'],
                'reviewed_at' => now(),
            ]);

            // Enviar notificación por correo
            $roleRequest->user->notify(new \App\Notifications\RoleRequestProcessed($roleRequest, 'rejected'));

            return redirect()->route('role-requests.index')
                ->with('success', 'Solicitud rechazada.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }
}
