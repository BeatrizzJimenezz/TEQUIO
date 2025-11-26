@extends('layouts.app')

@section('header', 'Editar Usuario')

@push('styles')
    <link href="{{ asset('css/admin-users-edit.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container py-4">

    <div class="hero-header-sm d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light rounded-circle p-2" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Editar Usuario</h4>
                <p class="mb-0 small opacity-75">Actualizando perfil de: <strong>{{ $user->name }}</strong></p>
            </div>
        </div>
        <i class="bi bi-pencil-square hero-pattern text-white opacity-25" style="font-size: 5rem; position: absolute; right: -20px; bottom: -20px;"></i>
    </div>

    <div class="row g-4">

        <div class="col-lg-8">
            <div class="admin-card main-card-offset">

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Datos Personales --}}
                    <div class="mb-4 pb-3 border-bottom">
                        <h6 class="text-muted fw-bold small text-uppercase mb-4">
                            <i class="bi bi-person-lines-fill me-2"></i>Datos Personales
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label-title">Nombre Completo</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label-title">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Roles (Diseño Cards Mejorado) --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted fw-bold small text-uppercase mb-0">
                                <i class="bi bi-shield-lock-fill me-2"></i>Asignación de Rol
                            </h6>
                            @if($user->id === auth()->id())
                                <span class="badge bg-warning text-dark border border-warning">
                                    <i class="bi bi-lock-fill"></i> Edición restringida
                                </span>
                            @endif
                        </div>

                        <div class="row g-3">
                            @php $allowedRoles = ['Administrador', 'Organizador', 'Participante']; @endphp

                            @foreach($roles as $role)
                                @if(in_array($role->name, $allowedRoles))
                                    <div class="col-md-4">
                                        {{-- Lógica de deshabilitado --}}
                                        @php
                                            $isSelf = $user->id === auth()->id();
                                            $hasRole = $user->hasRole($role->name);
                                            $disabled = $isSelf && !$hasRole;
                                        @endphp

                                        <input type="radio" class="role-selector" name="roles[]"
                                               id="role_{{ $role->id }}" value="{{ $role->name }}"
                                               {{ $hasRole ? 'checked' : '' }}
                                               {{ $disabled ? 'disabled' : '' }}>

                                        <label class="role-card" for="role_{{ $role->id }}">
                                            <i class="bi bi-check-circle-fill check-icon"></i>

                                            @php
                                                $iconClass = match($role->name) {
                                                    'Administrador' => 'icon-admin bi-shield-shaded',
                                                    'Organizador' => 'icon-organizer bi-calendar-check',
                                                    default => 'icon-participant bi-person',
                                                };
                                            @endphp

                                            <div class="role-icon {{ explode(' ', $iconClass)[0] }}">
                                                <i class="bi {{ explode(' ', $iconClass)[1] }}"></i>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark">{{ $role->name }}</h6>
                                            <p class="text-muted small mb-0 lh-sm opacity-75">
                                                @switch($role->name)
                                                    @case('Administrador') Control total del sistema. @break
                                                    @case('Organizador') Gestiona eventos propios. @break
                                                    @default Acceso a eventos públicos.
                                                @endswitch
                                            </p>
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        {{-- Input oculto por seguridad si es el propio usuario --}}
                        @if($user->id === auth()->id())
                            <input type="hidden" name="roles[]" value="Administrador">
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-secondary">Cancelar</a>
                        <button type="submit" class="btn-save shadow">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Acciones y Metadatos --}}
        <div class="col-lg-4">

            {{-- Acciones de Cuenta --}}
            <div class="admin-card main-card-offset mb-4">
                        <h6 class="text-muted fw-bold small text-uppercase mb-4 border-bottom pb-2">
                            <i class="bi bi-person-fill-lock me-2"></i>Acciones de Cuenta
                        </h6>


                <div class="mb-3">
                    <button type="button" class="btn btn-outline-evai w-100"
                            data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="bi bi-key-fill me-2"></i>Resetear contraseña
                    </button>
                </div>

                @if($user->id !== auth()->id())
                    <button type="button" class="btn btn-outline-tequio w-100"
                            data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                        <i class="bi bi-trash-fill me-2"></i>Eliminar cuenta
                    </button>
                @endif
            </div>

            {{-- Metadatos --}}
            <div class="admin-card">
                <h6 class="text-muted fw-bold small text-uppercase mb-4 border-bottom pb-2">
                    <i class="bi bi-info-circle-fill me-2"></i>Información del Sistema
                </h6>


                <div class="d-flex align-items-center mb-4">
                    <div class="meta-icon"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase" style="font-size:0.7rem;">Registrado el</small>
                        <span class="fw-bold text-dark">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="meta-icon"><i class="bi bi-envelope-check"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase" style="font-size:0.7rem;">Estado Email</small>
                        @if($user->email_verified_at)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill">Verificado</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill">Pendiente</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="meta-icon"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <small class="text-muted d-block text-uppercase" style="font-size:0.7rem;">Última Edición</small>
                        <span class="fw-bold text-dark">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL RESET PASSWORD --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-5 text-center">
                <div class="mb-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-4 text-warning">
                        <i class="bi bi-key-fill display-4"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2">¿Resetear contraseña?</h4>
                <p class="text-muted mb-4">
                    Se generará una nueva contraseña temporal para <strong class="text-dark">{{ $user->name }}</strong>.<br>
                    El usuario perderá acceso inmediato con su contraseña actual.
                </p>

                <div class="d-grid gap-3">
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold shadow-sm text-dark">
                            Sí, resetear contraseña
                        </button>
                    </form>
                    <button type="button" class="btn btn-light w-100 rounded-pill py-2 text-muted fw-bold" data-bs-dismiss="modal">
                        Cancelar
                    </button>
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

{{-- MODAL ELIMINAR --}}
@if($user->id !== auth()->id())
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-5 text-center">
                    <div class="mb-4">
                        <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex p-4 text-danger">
                            <i class="bi bi-person-x-fill display-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2">¿Eliminar Usuario?</h4>
                    <p class="text-muted mb-4">
                        Estás a punto de eliminar permanentemente a <strong class="text-dark">{{ $user->name }}</strong>.<br>
                        Esta acción no se puede deshacer.
                    </p>

                    <div class="d-grid gap-3">
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold shadow-sm">
                                Sí, Eliminar Definitivamente
                            </button>
                        </form>
                        <button type="button" class="btn btn-light w-100 rounded-pill py-2 text-muted fw-bold" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
