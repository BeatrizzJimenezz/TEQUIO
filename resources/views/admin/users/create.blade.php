@extends('layouts.app')

@section('header', 'Crear Usuario')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">Crear Nuevo</li>
                </ol>
            </nav>

            <div class="card-admin">
                <div class="card-header-admin">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i>Información del Nuevo Usuario
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <h6 class="text-brand-main fw-bold mb-3 text-uppercase small ls-1">Datos de Cuenta</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label-admin">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-admin @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" placeholder="Ej. Juan Pérez" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label-admin">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-admin @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" placeholder="usuario@ejemplo.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="password" class="form-label-admin">Contraseña Temporal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-admin @error('password') is-invalid @enderror"
                                           id="password" name="password" required placeholder="Mínimo 8 caracteres">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i>El usuario deberá cambiar esta contraseña en su primer inicio de sesión.
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <h6 class="text-brand-main fw-bold mb-3 text-uppercase small ls-1">Asignación de Roles</h6>
                        <div class="mb-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="form-label-admin mb-3">Seleccione los roles para este usuario <span class="text-danger">*</span></label>
                                <div class="row g-3">
                                    @foreach($roles as $role)
                                        <div class="col-md-4">
                                            <div class="form-check custom-checkbox">
                                                <input class="form-check-input" type="checkbox"
                                                       name="roles[]" value="{{ $role->name }}"
                                                       id="role{{ $role->id }}"
                                                       {{ in_array($role->name, old('roles', ['Participante'])) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-medium" for="role{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('roles')
                                    <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-end mt-5">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light border px-4 fw-bold text-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-evai px-4">
                                <i class="bi bi-check-lg me-2"></i>Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
});
</script>
@endpush
