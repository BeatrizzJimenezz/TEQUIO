@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Breadcrumb al estilo Tequio --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Mis Eventos</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('components.index', $event) }}" style="color: #4499BB;">{{ $event->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('events.team.index', $event) }}" style="color: #4499BB;">Equipo Organizador</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Invitar Organizador</li>
                </ol>
            </nav>

            {{-- Tarjeta Principal --}}
            <div class="card shadow-lg rounded-3">
                <div class="card-header bg-primary-dark text-white rounded-top-3">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i> Invitar Organizador al Equipo
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                    @endif

                    <p class="text-muted mb-4 fs-6">
                        <strong>Evento:</strong> {{ $event->name }}
                    </p>
                    {{-- Separador visual para mejor diseño --}}
                    <hr class="mb-4"> 

                    <form action="{{ route('events.team.store', $event) }}" method="POST" id="addOrganizerForm">
                        @csrf

                        {{-- SELECCIÓN DE TIPO DE USUARIO --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Seleccionar opción:</label>
                            <div class="btn-group w-100 shadow-sm rounded-pill" role="group">
                                {{-- Tipo: Usuario Existente --}}
                                <input type="radio" class="btn-check" name="type" id="typeExisting" value="existing" checked>
                                <label class="btn btn-outline-primary-light py-2 rounded-start-pill" for="typeExisting">
                                    <i class="bi bi-person-check me-1"></i> Usuario Existente
                                </label>

                                {{-- Tipo: Crear Nuevo Usuario (usando el color de acento verde) --}}
                                <input type="radio" class="btn-check" name="type" id="typeNew" value="new">
                                <label class="btn btn-outline-accent-positive py-2 rounded-end-pill" for="typeNew">
                                    <i class="bi bi-person-plus me-1"></i> Crear Nuevo Usuario
                                </label>
                            </div>
                        </div>
                        
                        {{-- 1. FORMULARIO PARA USUARIO EXISTENTE --}}
                        <div id="existingUserForm">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">
                                    <i class="bi bi-search me-1"></i> Buscar Usuario por Nombre o Email
                                </label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" style="width: 100%;">
                                    <option value="">-- Buscar por nombre o email --</option>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if($availableUsers->count() == 0)
                                <div class="alert alert-info mt-3 rounded-3 border-info">
                                    <i class="bi bi-info-circle-fill me-1"></i> No hay usuarios disponibles para invitar. Puedes crear una nueva cuenta.
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- 2. FORMULARIO PARA NUEVO USUARIO --}}
                        <div id="newUserForm" style="display: none;">
                            <div class="alert alert-info rounded-3 border-info">
                                <i class="bi bi-info-circle-fill me-1"></i> Se creará una nueva cuenta con el rol de **Organizador**.
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre Completo <span class="text-accent-required">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej: Juan Pérez">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico <span class="text-accent-required">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña <span class="text-accent-required">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Mostrar Contraseña">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">El nuevo usuario deberá cambiar esta contraseña al iniciar sesión por primera vez.</small>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                            <a href="{{ route('events.team.index', $event) }}" class="btn btn-secondary shadow-sm">
                                <i class="bi bi-arrow-left me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-accent-positive shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Añadir al Equipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    /* Colores base para la vista */
    .bg-primary-dark {
        background-color: #0C2340 !important;
    }
    .text-primary-light {
        color: #4499BB !important;
    }
    
    /* Nuevo estilo para el asterisco requerido con color de la marca */
    .text-accent-required {
        color: #4499BB !important;
        font-weight: bold;
    }

    /* Botones de selección de tipo (Existente) */
    .btn-outline-primary-light {
        color: #4499BB;
        border-color: #4499BB;
        transition: all 0.2s;
    }
    .btn-outline-primary-light:hover,
    .btn-check:checked + .btn-outline-primary-light {
        background-color: #4499BB;
        border-color: #4499BB;
        color: white;
        z-index: 1; /* Asegurar que el botón activo esté sobrepuesto si es necesario */
    }

    /* Botón de acción positiva (Añadir al Equipo) */
    .btn-accent-positive {
        background-color: #00B050; /* Verde de acento */
        border-color: #00B050;
        color: white;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        transition: all 0.2s;
    }
    .btn-accent-positive:hover {
        background-color: #009343;
        border-color: #009343;
        color: white;
    }

    /* Botón de selección de tipo (Nuevo Usuario - verde de acento) */
    .btn-outline-accent-positive {
        color: #00B050;
        border-color: #00B050;
        transition: all 0.2s;
    }
    .btn-outline-accent-positive:hover,
    .btn-check:checked + .btn-outline-accent-positive {
        background-color: #00B050;
        border-color: #00B050;
        color: white;
        z-index: 1; /* Asegurar que el botón activo esté sobrepuesto si es necesario */
    }
    
    /* Grupos de botones con esquinas redondeadas completas (Pills) */
    .rounded-start-pill {
        border-top-left-radius: 50rem!important;
        border-bottom-left-radius: 50rem!important;
    }
    .rounded-end-pill {
        border-top-right-radius: 50rem!important;
        border-bottom-right-radius: 50rem!important;
    }
    .btn-group .btn {
        border-radius: 0; /* Anular Bootstrap por defecto para que las clases rounded-*-pill funcionen */
    }

    /* Personalización de Select2 */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        border-radius: 0.25rem !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single {
        padding: 0.375rem 0.75rem;
    }
    
    /* Alertas con un borde de color para destacarlas */
    .alert-info {
        border-left: 5px solid var(--bs-info);
        padding-left: 1rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeExisting = document.getElementById('typeExisting');
    const typeNew = document.getElementById('typeNew');
    const existingUserForm = document.getElementById('existingUserForm');
    const newUserForm = document.getElementById('newUserForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    // Función para manejar el Select2 en español
    const select2Spanish = {
        noResults: function() {
            return "No se encontraron resultados";
        },
        searching: function() {
            return "Buscando...";
        },
    };

    // Initialize Select2
    $('#user_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Buscar por nombre o email --',
        allowClear: true,
        language: select2Spanish
    });

    // Toggle Forms
    function toggleForms(isNewUser) {
        if (isNewUser) {
            existingUserForm.style.display = 'none';
            newUserForm.style.display = 'block';
            // Limpiar campos del usuario existente
            $('#user_id').val(null).trigger('change');
        } else {
            existingUserForm.style.display = 'block';
            newUserForm.style.display = 'none';
            // Limpiar campos del nuevo usuario
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
        }
    }

    typeExisting.addEventListener('change', function() {
        if (this.checked) {
            toggleForms(false);
        }
    });

    typeNew.addEventListener('change', function() {
        if (this.checked) {
            toggleForms(true);
        }
    });

    // Toggle Password Visibility
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    }

    // Check errors on load to show correct form and maintain Spanish context
    // Si hay errores de validación para los campos del nuevo usuario, forzamos la vista de "Crear Nuevo Usuario"
    @if($errors->has('name') || $errors->has('email') || $errors->has('password') || old('type') === 'new')
        typeNew.checked = true;
        toggleForms(true);
    // Si hay errores de validación para el campo de usuario existente, forzamos la vista de "Usuario Existente"
    @elseif($errors->has('user_id') || old('type') === 'existing' || !old('type'))
        typeExisting.checked = true;
        toggleForms(false);
    @endif
});
</script>
@endpush
@endsection