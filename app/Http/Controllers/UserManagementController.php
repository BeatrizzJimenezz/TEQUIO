<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Verificar permisos de administrador
     */
    private function checkAdmin()
    {
        if (!auth()->check() || !auth()->user()->hasRole('Administrador')) {
            abort(403, 'Solo el administrador puede gestionar usuarios.');
        }
    }

    /**
     * Formulario para crear usuario
     */
    public function create()
    {
        $this->checkAdmin();

        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'roles.required' => 'Debe asignar al menos un rol.',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'must_change_password' => true,
            ]);

            // Asignar roles
            $user->assignRole($validated['roles']);

            // Enviar correo con datos de acceso
            $user->notify(new \App\Notifications\AccountCreated($validated['password']));

            return redirect()->route('admin.users.index')
                ->with('success', "Usuario {$user->name} creado correctamente. Se envió un correo con los datos de acceso.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Listar todos los usuarios
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = User::with(['roles', 'professionalProfile']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Ver detalles de un usuario
     */
    public function show(User $user)
    {
        $this->checkAdmin();

        $user->load(['roles', 'professionalProfile', 'registrations.component.event']);

        return view('admin.users.show', compact('user'));
    }




    /**
     * Formulario para editar usuario
     */
    public function edit(User $user)
    {
        $this->checkAdmin();

        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Este correo ya está en uso.',
            'roles.required' => 'Debe asignar al menos un rol.',
        ]);

        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Sincronizar roles
            $user->syncRoles($validated['roles']);

            return redirect()->route('admin.users.index')
                ->with('success', "Usuario {$user->name} actualizado correctamente.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Restablecer contraseña del usuario
     */
    public function resetPassword(User $user)
    {
        $this->checkAdmin();

        try {
            // Generar contraseña temporal
            $tempPassword = 'Temp' . rand(1000, 9999);

            $user->update([
                'password' => Hash::make($tempPassword),
                'must_change_password' => true,
            ]);

            return back()->with('success', "Contraseña restablecida. Nueva contraseña temporal: {$tempPassword}");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al restablecer la contraseña.');
        }
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        $this->checkAdmin();

        // No permitir eliminar al propio admin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        try {
            $userName = $user->name;
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', "Usuario {$userName} eliminado correctamente.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleStatus(User $user)
    {
        $this->checkAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivarte a ti mismo.');
        }

        try {
            $user->update([
                'is_active' => !$user->is_active,
            ]);

            $status = $user->is_active ? 'activado' : 'desactivado';

            return back()->with('success', "Usuario {$user->name} {$status}.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado del usuario.');
        }
    }
}
