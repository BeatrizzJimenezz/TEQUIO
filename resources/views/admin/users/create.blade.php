@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-users-create.css') }}">
@endpush

@section('content')
<div class="container py-4">

    <div class="hero-header-sm d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3 pt-3 pb-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light rounded-circle p-2" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Crear nuevo usuario</h4>
                <p class="mb-0 small opacity-75">Complete la información para registrar un usuario.</p>
            </div>
        </div>
        <i class="bi bi-person-plus hero-pattern text-white opacity-25" style="font-size: 5rem; position: absolute; right: 5px; bottom: -10px;"></i>
    </div>

    <div class="row justify-content-center pt-3">
        <div class="col-lg-12">
            <div class="main-content-card">
                
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                    @csrf

                    {{-- Sección A: Datos Personales --}}
                    <div class="row g-4 mb-5">
                        <div class="col-12 border-bottom pb-2 mb-2">
                            <h6 class="text-muted fw-bold small text-uppercase"><i class="bi bi-person-badge me-2"></i>Información de Cuenta</h6>
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                   value="{{ old('name') }}" placeholder="Ej. Hector Paiz" required>
                            @error('name') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                   value="{{ old('email') }}" placeholder="usuario@ejemplo.com" required>
                            @error('email') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label">Contraseña Temporal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg font-monospace" 
                                       id="password" name="password" required readonly 
                                       placeholder="Genere una contraseña segura -->">
                                <button class="btn btn-outline-secondary" type="button" id="btnGeneratePass">
                                    <i class="bi bi-magic"></i>
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="btnCopyPass" title="Copiar">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                            <div class="form-text mt-2 small fw-bold" id="passHelper" style="display: none;">
                                <i class="bi bi-check-circle me-1" style="color: var(--brand-accent);"></i> Contraseña generada. Asegúrate de compartirla con el usuario.
                            </div>
                            @error('password') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Sección B: Roles (Selección Única) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-12 border-bottom pb-2 mb-2">
                            <h6 class="text-muted fw-bold small text-uppercase"><i class="bi bi-shield-lock me-2"></i>Asignación de Rol</h6>
                        </div>

                        <div class="col-12">
                            <div class="row g-3">
                                @php
                                    $allowedRoles = ['Administrador', 'Organizador', 'Participante'];
                                @endphp

                                @foreach($roles as $role)
                                    @if(in_array($role->name, $allowedRoles))
                                        <div class="col-md-4">
                                            <input type="radio" class="role-selector" name="roles" 
                                                   id="role_{{ $role->id }}" value="{{ $role->name }}" 
                                                   {{ old('roles') == $role->name ? 'checked' : ($role->name == 'Participante' ? 'checked' : '') }} required>
                                            
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
                                                
                                                <h6 class="fw-bold mb-1">{{ $role->name }}</h6>
                                                <p class="text-muted small mb-0 lh-sm">
                                                    @switch($role->name)
                                                        @case('Administrador') Acceso total al sistema y configuraciones. @break
                                                        @case('Organizador') Puede crear y gestionar eventos propios. @break
                                                        @default Puede inscribirse y ver eventos públicos.
                                                    @endswitch
                                                </p>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @error('roles') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Botones de Acción --}}
                    <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-pill fw-bold text-secondary px-4">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-save">
                            <i class="bi bi-check-lg me-2"></i>Crear Usuario
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Generador de Contraseña
    const passInput = document.getElementById('password');
    const btnGenerate = document.getElementById('btnGeneratePass');
    const btnCopy = document.getElementById('btnCopyPass');
    const helperText = document.getElementById('passHelper');

    function generatePassword(length = 12) {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let retVal = "";
        for (let i = 0, n = charset.length; i < length; ++i) {
            retVal += charset.charAt(Math.floor(Math.random() * n));
        }
        return retVal;
    }

    // Evento al hacer clic en "Generar"
    btnGenerate.addEventListener('click', function() {
        const newPass = generatePassword();
        passInput.value = newPass;
        // Cambiamos el tipo a texto para que se vea
        passInput.type = 'text'; 
        helperText.style.display = 'block';
        
        // Animación visual
        passInput.style.backgroundColor = '#e8f5e9';
        setTimeout(() => passInput.style.backgroundColor = '#f8fafc', 300);
    });

    // Copiar al portapapeles
    btnCopy.addEventListener('click', function() {
        if(passInput.value) {
            passInput.select();
            document.execCommand('copy');
            
            // Feedback visual del botón
            const originalIcon = btnCopy.innerHTML;
            btnCopy.innerHTML = '<i class="bi bi-check"></i>';
            setTimeout(() => btnCopy.innerHTML = originalIcon, 1500);
        }
    });

    // Generar una contraseña automáticamente al cargar si el campo está vacío
    if(!passInput.value) {
        btnGenerate.click();
    }
});
</script>
@endpush