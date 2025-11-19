@extends('layouts.app')

@section('header', 'Configuración de la Cuenta')

{{-- Reutilizamos los estilos base, pero podríamos añadir específicos si fuera necesario --}}
@push('styles')
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <style>
        .settings-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .settings-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
        }
        .settings-body {
            padding: 1.5rem;
            background-color: #fff;
        }
        .section-icon {
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            margin-right: 1rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            
            <!-- Mensajes de Estado -->
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                    <i class="bi bi-check-circle-fill me-2"></i> Información del perfil actualizada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif (session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                    <i class="bi bi-shield-check me-2"></i> Contraseña actualizada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- 1. INFORMACIÓN DEL PERFIL -->
            <div class="settings-card">
                <div class="settings-header d-flex align-items-center">
                    <div class="section-icon" style="background-color: rgba(68, 153, 187, 0.1); color: #4499bb;">
                        <i class="bi bi-person-vcard fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #0c2340;">Información del Perfil</h5>
                        <small class="text-muted">Actualiza tu nombre y correo electrónico.</small>
                    </div>
                </div>
                <div class="settings-body">
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label small text-secondary fw-bold">Nombre Completo</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="email" class="form-label small text-secondary fw-bold">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-2 p-2 bg-light rounded border border-warning text-center">
                                        <p class="text-sm text-muted mb-1">
                                            Tu dirección de correo no está verificada.
                                        </p>
                                        <button form="send-verification" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold text-warning">
                                            Reenviar correo de verificación
                                        </button>

                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 fw-bold text-success small mb-0">
                                                ¡Enlace enviado! Revisa tu bandeja de entrada.
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn text-white px-4 shadow-sm hover-scale" style="background-color: #4499bb; border: none;">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. ACTUALIZAR CONTRASEÑA -->
            <div class="settings-card">
                <div class="settings-header d-flex align-items-center">
                    <div class="section-icon" style="background-color: rgba(140, 198, 63, 0.1); color: #8cc63f;">
                        <i class="bi bi-shield-lock fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #0c2340;">Seguridad</h5>
                        <small class="text-muted">Asegura tu cuenta con una contraseña fuerte.</small>
                    </div>
                </div>
                <div class="settings-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label small text-secondary fw-bold">Contraseña Actual</label>
                            <div class="input-group">
                                <input type="password" class="form-control border-end-0" id="current_password" name="current_password" autocomplete="current-password">
                                <button class="btn btn-outline-secondary border-start-0 bg-white" type="button" onclick="togglePassword('current_password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('current_password', 'updatePassword')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="update_password_password" class="form-label small text-secondary fw-bold">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control border-end-0" id="update_password_password" name="password" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary border-start-0 bg-white" type="button" onclick="togglePassword('update_password_password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="update_password_password_confirmation" class="form-label small text-secondary fw-bold">Confirmar Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control border-end-0" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary border-start-0 bg-white" type="button" onclick="togglePassword('update_password_password_confirmation', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn text-white px-4 shadow-sm hover-scale" style="background-color: #8cc63f; border: none; color: #0a0a0a !important; font-weight: 600;">
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 3. ELIMINAR CUENTA -->
            <div class="settings-card border border-danger border-opacity-25">
                <div class="settings-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold text-danger mb-1"><i class="bi bi-exclamation-triangle me-2"></i>Zona de Peligro</h5>
                        <p class="text-muted mb-0 small">Una vez eliminada, tu cuenta no podrá recuperarse.</p>
                    </div>
                    <button class="btn btn-outline-danger hover-scale btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                        Eliminar Cuenta
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN -->
<div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold" id="confirmUserDeletionModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <p class="text-dark mb-3">
                        ¿Estás seguro de que quieres eliminar tu cuenta? Esta acción es permanente y perderás todos tus datos.
                    </p>
                    <p class="text-secondary small mb-3">
                        Por favor, ingresa tu contraseña para confirmar.
                    </p>

                    <div class="input-group">
                        <input type="password" class="form-control" id="delete_password" name="password" placeholder="Tu contraseña actual">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('delete_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password', 'userDeletion')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold">Sí, eliminar permanentemente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función simple para alternar visibilidad de contraseña
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Abrir modal automáticamente si hay errores al eliminar
    @if($errors->userDeletion->isNotEmpty())
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
            myModal.show();
        });
    @endif
</script>

@endsection