@extends('layouts.app')

@section('header', 'Editar Usuario')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">Editar: {{ $user->name }}</li>
                </ol>
            </nav>
        </div>

        {{-- Columna Principal: Formulario --}}
        <div class="col-lg-8">
            <div class="card-admin mb-4">
                <div class="card-header-admin">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Editar Información
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form id="editUserForm" action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h6 class="text-brand-main fw-bold mb-3 text-uppercase small ls-1">Datos Personales</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label-admin">Nombre Completo</label>
                                <input type="text" class="form-control form-control-admin @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label-admin">Correo Electrónico</label>
                                <input type="email" class="form-control form-control-admin @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <h6 class="text-brand-main fw-bold mb-3 text-uppercase small ls-1">Roles y Permisos</h6>
                        <div class="p-3 bg-light rounded-3 border mb-4">
                            <div class="row g-3">
                                @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="roles[]" value="{{ $role->name }}"
                                                   id="role{{ $role->id }}"
                                                   {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                                   {{ $role->name === 'Administrador' && $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <label class="form-check-label fw-medium" for="role{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($user->id === auth()->id())
                                <div class="mt-2 text-muted small">
                                    <i class="bi bi-lock-fill me-1"></i>Por seguridad, no puedes quitarte el rol de Administrador a ti mismo.
                                </div>
                                <input type="hidden" name="roles[]" value="Administrador">
                            @endif
                            @error('roles')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light border px-4 fw-bold text-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-evai px-4">
                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Columna Lateral: Acciones e Info --}}
        <div class="col-lg-4">
            {{-- Tarjeta de Acciones --}}
            <div class="card-admin mb-4">
                <div class="card-header-admin accent">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-gear-fill me-2"></i>Acciones de Cuenta
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning w-100 fw-bold"
                                onclick="return confirm('¿Estás seguro? Se generará una contraseña temporal.')">
                            <i class="bi bi-key-fill me-2"></i>Restablecer Contraseña
                        </button>
                    </form>

                    @if($user->id !== auth()->id())
                        <button type="button" class="btn btn-outline-danger w-100 fw-bold"
                                data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="bi bi-trash-fill me-2"></i>Eliminar Usuario
                        </button>
                    @endif
                </div>
            </div>

            {{-- Tarjeta de Suscripción --}}
            @if($user->hasRole('Organizador'))
            <div class="card-admin mb-4">
                <div class="card-header-admin bg-success bg-opacity-10">
                    <h6 class="mb-0 fw-bold text-success">
                        <i class="bi bi-star-fill me-2"></i>Gestionar Suscripción
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($user->hasActiveSubscription())
                        {{-- Suscripción Activa --}}
                        <div class="alert alert-success border-0 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>
                                    <strong>Suscripción Activa</strong>
                                    <p class="mb-0 small">
                                        Plan: <strong>{{ ucfirst($user->subscription->plan_id) }}</strong><br>
                                        Vence: <strong>{{ $user->subscription->ends_at->format('d/m/Y') }}</strong>
                                        @if($user->subscription->ends_at->isFuture())
                                            ({{ $user->subscription->ends_at->diffForHumans() }})
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Extender Suscripción --}}
                        <form action="{{ route('admin.users.subscription.extend', $user) }}" method="POST" class="mb-2">
                            @csrf
                            <label class="form-label small fw-bold">Extender Suscripción</label>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <input type="number" name="amount" class="form-control form-control-sm" placeholder="Cantidad" min="1" max="120" value="1" required>
                                </div>
                                <div class="col-6">
                                    <select name="unit" class="form-select form-select-sm" required>
                                        <option value="months">Mes(es)</option>
                                        <option value="years">Año(s)</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                <i class="bi bi-plus-circle me-1"></i>Extender Suscripción
                            </button>
                        </form>

                        {{-- Cancelar Suscripción --}}
                        <form action="{{ route('admin.users.subscription.cancel', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                    onclick="return confirm('¿Cancelar la suscripción de este usuario?')">
                                <i class="bi bi-x-circle me-1"></i>Cancelar Suscripción
                            </button>
                        </form>
                    @else
                        {{-- Sin Suscripción --}}
                        <div class="alert alert-warning border-0 mb-3">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <strong>Sin suscripción activa</strong>
                        </div>

                        {{-- Activar Suscripción --}}
                        <form action="{{ route('admin.users.subscription.activate', $user) }}" method="POST">
                            @csrf
                            <label class="form-label small fw-bold">Activar Suscripción</label>
                            <select name="plan" class="form-select form-select-sm mb-2" required>
                                <option value="monthly">Mensual (1 mes)</option>
                                <option value="annual">Anual (1 año)</option>
                                <option value="lifetime">Vitalicia (100 años)</option>
                            </select>
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="bi bi-star-fill me-1"></i>Activar Suscripción
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

            {{-- Tarjeta de Metadatos --}}
            <div class="card-admin">
                <div class="card-body p-4">
                    <h6 class="text-brand-deep fw-bold mb-3">Metadatos</h6>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-light p-2 me-3 text-brand-main">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Fecha de Registro</small>
                            <span class="fw-bold text-dark">{{ $user->created_at->format('d M, Y h:i A') }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-light p-2 me-3 text-brand-main">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Última Actualización</small>
                            <span class="fw-bold text-dark">{{ $user->updated_at->format('d M, Y h:i A') }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light p-2 me-3 text-brand-main">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Estado del Email</small>
                            @if($user->email_verified_at)
                                <span class="badge bg-success bg-opacity-10 text-success">Verificado</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning">Pendiente</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Eliminar --}}
@if($user->id !== auth()->id())
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <div class="avatar-circle d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width: 80px; height: 80px;">
                            <i class="bi bi-person-x-fill display-4"></i>
                        </div>
                    </div>
                    <h5 class="mb-3">¿Confirmar eliminación?</h5>
                    <p class="text-muted mb-0">
                        Estás a punto de eliminar a <strong>{{ $user->name }}</strong>.
                        Esta acción es irreversible.
                    </p>
                </div>
                <div class="modal-footer border-0 bg-light justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-bold">
                            Sí, Eliminar
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
    // Lógica de confirmación de cambio de roles (opcional)
    const checkboxes = document.querySelectorAll('input[name="roles[]"]:checked');
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('Debe seleccionar al menos un rol.');
    }
});
</script>
@endpush
