@extends('layouts.app')

@section('header', 'Editar Usuario')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-pencil-square me-2"></i>Editar Usuario
            </h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background-color: #0C2340;">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear me-2"></i>Información del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <form id="editUserForm" action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Nombre completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Roles asignados</label>
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="roles[]" value="{{ $role->name }}"
                                                   id="role{{ $role->id }}"
                                                   {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                                   {{ $role->name === 'Administrador' && $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="role{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($user->id === auth()->id())
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>No puedes quitarte el rol de Administrador a ti mismo.
                                </small>
                                <input type="hidden" name="roles[]" value="Administrador">
                            @endif
                            @error('roles')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn" style="background-color: #8CC63F; color: white;">
                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Acciones adicionales --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header" style="background-color: #4499BB; color: white;">
                    <h6 class="mb-0">
                        <i class="bi bi-gear me-2"></i>Acciones
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Restablecer contraseña --}}
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning w-100"
                                onclick="return confirm('¿Restablecer la contraseña de este usuario?')">
                            <i class="bi bi-key me-2"></i>Restablecer Contraseña
                        </button>
                    </form>

                    @if($user->id !== auth()->id())
                        {{-- Eliminar usuario --}}
                        <button type="button" class="btn btn-outline-danger w-100"
                                data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="bi bi-trash me-2"></i>Eliminar Usuario
                        </button>
                    @endif
                </div>
            </div>

            {{-- Info del usuario --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Información</h6>
                    <p class="small mb-2">
                        <strong>Registrado:</strong><br>
                        {{ $user->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="small mb-2">
                        <strong>Última actualización:</strong><br>
                        {{ $user->updated_at->format('d/m/Y H:i') }}
                    </p>
                    @if($user->email_verified_at)
                        <p class="small mb-0 text-success">
                            <i class="bi bi-check-circle-fill me-1"></i>Email verificado
                        </p>
                    @else
                        <p class="small mb-0 text-warning">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>Email no verificado
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Eliminar --}}
@if($user->id !== auth()->id())
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de eliminar al usuario <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-danger small mb-0">
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Obtener roles seleccionados
    const checkboxes = document.querySelectorAll('input[name="roles[]"]:checked');
    const selectedRoles = Array.from(checkboxes).map(cb => cb.value);

    if (selectedRoles.length === 0) {
        alert('Debe seleccionar al menos un rol.');
        return false;
    }

    // Obtener roles actuales
    const currentRoles = @json($user->roles->pluck('name')->toArray());

    // Verificar si hubo cambios en los roles
    const rolesChanged = selectedRoles.length !== currentRoles.length ||
                         !selectedRoles.every(role => currentRoles.includes(role));

    if (rolesChanged) {
        const rolesText = selectedRoles.join(', ');
        const confirmed = confirm(`¿Está seguro que desea cambiar el rol de este perfil a: ${rolesText}?`);

        if (!confirmed) {
            return false;
        }
    }

    this.submit();
});
</script>
@endpush
