@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Header --}}
            <div class="card-admin mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h3 class="mb-1 fw-bold text-brand-deep">
                                <i class="bi bi-plus-circle-fill me-2 text-brand-accent"></i>
                                Agregar Nuevo Componente
                            </h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3 me-1 text-brand-main"></i>
                                {{ $event->name }} | {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('components.index', $event) }}" class="btn btn-white shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                    <strong class="d-block mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Error de validación:
                    </strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('components.store', $event) }}" method="POST" id="componentForm">
                @csrf

                {{-- Información Básica --}}
                <div class="card-admin mb-4">
                    <div class="card-header-admin">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle-fill me-2 text-brand-main"></i>
                            Información Básica
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label-admin">
                                    Nombre del Componente <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control-admin @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="type" class="form-label-admin">
                                    Tipo <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-control-admin @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Actividad</option>
                                    <option value="talk" {{ old('type') == 'talk' ? 'selected' : '' }}>Conferencia</option>
                                    <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Taller</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label-admin">
                                    Descripción <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control-admin @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cover_image" class="form-label-admin">
                                    URL de Imagen de Portada
                                </label>
                                <input type="url" class="form-control-admin @error('cover_image') is-invalid @enderror"
                                       id="cover_image" name="cover_image" value="{{ old('cover_image') }}"
                                       placeholder="https://ejemplo.com/imagen.jpg">
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="level" class="form-label-admin">
                                    Nivel
                                </label>
                                <select class="form-select form-control-admin @error('level') is-invalid @enderror"
                                        id="level" name="level">
                                    <option value="">Seleccionar...</option>
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Principiante</option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Avanzado</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ponente --}}
                <div class="card-admin mb-4">
                    <div class="card-header-admin">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-fill me-2 text-brand-accent"></i>
                            Ponente / Tallerista
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        {{-- Tipo de selección de ponente --}}
                        <div class="mb-4">
                            <label class="form-label-admin mb-3">
                                ¿Cómo deseas asignar el ponente?
                            </label>
                            <div class="d-flex gap-4 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="speaker_type" id="speakerExisting"
                                           value="existing" {{ old('speaker_type', 'existing') == 'existing' ? 'checked' : '' }}
                                           onchange="toggleSpeakerSection()">
                                    <label class="form-check-label" for="speakerExisting">
                                        Seleccionar existente
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="speaker_type" id="speakerNew"
                                           value="new" {{ old('speaker_type') == 'new' ? 'checked' : '' }}
                                           onchange="toggleSpeakerSection()">
                                    <label class="form-check-label" for="speakerNew">
                                        Crear perfil temporal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="speaker_type" id="speakerNone"
                                           value="none" {{ old('speaker_type') == 'none' ? 'checked' : '' }}
                                           onchange="toggleSpeakerSection()">
                                    <label class="form-check-label" for="speakerNone">
                                        Sin asignar
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Sección de ponente existente --}}
                        <div id="existingSpeakerSection" class="{{ old('speaker_type', 'existing') == 'existing' ? '' : 'd-none' }}">
                            <div class="col-md-8">
                                <label for="speaker_id" class="form-label-admin">
                                    Seleccionar Ponente
                                </label>
                                <select class="form-select form-control-admin @error('speaker_id') is-invalid @enderror"
                                        id="speaker_id" name="speaker_id">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($speakers as $profile)
                                        <option value="{{ $profile->id }}"
                                            {{ old('speaker_id') == $profile->id ? 'selected' : '' }}>
                                            @if($profile->is_temporary)
                                                {{ $profile->temp_name }} (Temporal - {{ $profile->temp_profession ?? 'Sin profesión' }})
                                            @else
                                                {{ $profile->user->name }} ({{ $profile->skills ?? 'Sin habilidades registradas' }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('speaker_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Sección de nuevo ponente temporal --}}
                        <div id="newSpeakerSection" class="{{ old('speaker_type') == 'new' ? '' : 'd-none' }}">
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                                    <small>Crea un perfil temporal para un ponente que aún no está registrado en el sistema.
                                    Cuando el ponente se registre con el mismo email, podrá vincular este perfil a su cuenta.</small>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="temp_name" class="form-label-admin">
                                        Nombre completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control-admin @error('temp_name') is-invalid @enderror"
                                           id="temp_name" name="temp_name" value="{{ old('temp_name') }}"
                                           placeholder="Ej: Dr. Juan Pérez García">
                                    @error('temp_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="temp_email" class="form-label-admin">
                                        Correo electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control-admin @error('temp_email') is-invalid @enderror"
                                           id="temp_email" name="temp_email" value="{{ old('temp_email') }}"
                                           placeholder="Ej: ponente@email.com">
                                    @error('temp_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="temp_profession" class="form-label-admin">
                                        Profesión / Ocupación
                                    </label>
                                    <input type="text" class="form-control-admin @error('temp_profession') is-invalid @enderror"
                                           id="temp_profession" name="temp_profession" value="{{ old('temp_profession') }}"
                                           placeholder="Ej: Ingeniero en Sistemas, Investigador en IA, Docente universitario">
                                    @error('temp_profession')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-1 d-block">Opcional. Ayuda a identificar al ponente.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modalidad y Ubicación --}}
                <div class="card-admin mb-4">
                    <div class="card-header-admin">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-geo-alt-fill me-2 text-brand-main"></i>
                            Modalidad y Ubicación
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modality" class="form-label-admin">
                                    Modalidad <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-control-admin @error('modality') is-invalid @enderror"
                                        id="modality" name="modality" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="virtual" {{ old('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality') == 'in_person' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hybrid" {{ old('modality') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                                @error('modality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="location" class="form-label-admin">
                                    Ubicación
                                </label>
                                <input type="text" class="form-control-admin @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location') }}"
                                       placeholder="Ej: Sala de Conferencias A, Link de Zoom, etc.">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Capacidad y Precios --}}
                <div class="card-admin mb-4">
                    <div class="card-header-admin">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-currency-dollar me-2 text-brand-accent"></i>
                            Capacidad y Precios
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="capacity" class="form-label-admin">
                                    Número de Cupos
                                </label>
                                <input type="number" class="form-control-admin @error('capacity') is-invalid @enderror"
                                       id="capacity" name="capacity" value="{{ old('capacity') }}" min="1"
                                       placeholder="Ej: 30">
                                <small class="text-muted mt-1 d-block">Dejar vacío para cupos ilimitados</small>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="attendee_price" class="form-label-admin">
                                    Precio para Asistente
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">$</span>
                                    <input type="number" class="form-control-admin border-start-0 ps-0 @error('attendee_price') is-invalid @enderror"
                                           id="attendee_price" name="attendee_price"
                                           value="{{ old('attendee_price', 0) }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted mt-1 d-block">$0.00 para actividad gratuita</small>
                                @error('attendee_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="organizer_cost" class="form-label-admin">
                                    Costo del Organizador
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">$</span>
                                    <input type="number" class="form-control-admin border-start-0 ps-0 @error('organizer_cost') is-invalid @enderror"
                                           id="organizer_cost" name="organizer_cost"
                                           value="{{ old('organizer_cost') }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted mt-1 d-block">Costo interno (opcional)</small>
                                @error('organizer_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Requisitos --}}
                <div class="card-admin mb-4">
                    <div class="card-header-admin">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-list-check me-2 text-brand-main"></i>
                            Requisitos
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="participant_requirements" class="form-label-admin">
                                    Requisitos del Participante
                                </label>
                                <textarea class="form-control-admin @error('participant_requirements') is-invalid @enderror"
                                          id="participant_requirements" name="participant_requirements" rows="3"
                                          placeholder="Ej: Laptop, conocimientos básicos de programación, etc.">{{ old('participant_requirements') }}</textarea>
                                @error('participant_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="instructor_requirements" class="form-label-admin">
                                    Requisitos del Instructor
                                </label>
                                <textarea class="form-control-admin @error('instructor_requirements') is-invalid @enderror"
                                          id="instructor_requirements" name="instructor_requirements" rows="3"
                                          placeholder="Ej: Proyector, micrófono, conexión a internet, etc.">{{ old('instructor_requirements') }}</textarea>
                                @error('instructor_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horarios --}}
                <div class="card-admin mb-4">
                    <div class="card-header-admin">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-clock-fill me-2 text-brand-accent"></i>
                            Horarios <span class="text-danger">*</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div id="schedulesContainer">
                            <div class="schedule-item card border-0 shadow-sm mb-3 bg-light">
                                <div class="card-body">
                                    <div class="row align-items-end g-3">
                                        <div class="col-md-4">
                                            <label class="form-label-admin">Fecha *</label>
                                            <input type="date" class="form-control-admin @error('schedules.0.date') is-invalid @enderror"
                                                   name="schedules[0][date]"
                                                   value="{{ old('schedules.0.date') }}"
                                                   min="{{ $event->start_date->format('Y-m-d') }}"
                                                   max="{{ $event->end_date->format('Y-m-d') }}"
                                                   required>
                                            @error('schedules.0.date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-admin">Hora Inicio *</label>
                                            <input type="time" class="form-control-admin @error('schedules.0.start_time') is-invalid @enderror"
                                                   name="schedules[0][start_time]"
                                                   value="{{ old('schedules.0.start_time') }}"
                                                   required>
                                            @error('schedules.0.start_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-admin">Hora Fin *</label>
                                            <input type="time" class="form-control-admin @error('schedules.0.end_time') is-invalid @enderror"
                                                   name="schedules[0][end_time]"
                                                   value="{{ old('schedules.0.end_time') }}"
                                                   required>
                                            @error('schedules.0.end_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-white text-danger border-danger w-100" onclick="removeSchedule(this)" disabled>
                                                <i class="bi bi-trash-fill"></i> Quitar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary border-dashed w-100 py-2" onclick="addSchedule()">
                            <i class="bi bi-plus-lg me-1"></i> Agregar Otro Horario
                        </button>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-between gap-3">
                    <a href="{{ route('components.index', $event) }}" class="btn btn-white btn-lg shadow-sm px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-brand-main btn-lg shadow-sm px-5 text-white">
                        <i class="bi bi-check-lg me-1"></i> Guardar Componente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let scheduleIndex = 1;

// Toggle entre secciones de ponente
function toggleSpeakerSection() {
    const speakerType = document.querySelector('input[name="speaker_type"]:checked').value;
    const existingSection = document.getElementById('existingSpeakerSection');
    const newSection = document.getElementById('newSpeakerSection');

    // Ocultar ambas secciones primero
    existingSection.classList.add('d-none');
    newSection.classList.add('d-none');

    // Limpiar campos según la selección
    if (speakerType === 'existing') {
        existingSection.classList.remove('d-none');
        // Limpiar campos de nuevo ponente
        document.getElementById('temp_name').value = '';
        document.getElementById('temp_email').value = '';
        document.getElementById('temp_profession').value = '';
    } else if (speakerType === 'new') {
        newSection.classList.remove('d-none');
        // Limpiar selector de ponente existente
        document.getElementById('speaker_id').value = '';
    } else {
        // Sin asignar - limpiar ambos
        document.getElementById('speaker_id').value = '';
        document.getElementById('temp_name').value = '';
        document.getElementById('temp_email').value = '';
        document.getElementById('temp_profession').value = '';
    }
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    toggleSpeakerSection();
});

function addSchedule() {
    const container = document.getElementById('schedulesContainer');
    const minDate = '{{ $event->start_date->format("Y-m-d") }}';
    const maxDate = '{{ $event->end_date->format("Y-m-d") }}';

    const newSchedule = `
        <div class="schedule-item card border-0 shadow-sm mb-3 bg-light">
            <div class="card-body">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label-admin">Fecha *</label>
                        <input type="date" class="form-control-admin"
                               name="schedules[${scheduleIndex}][date]"
                               min="${minDate}"
                               max="${maxDate}"
                               required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-admin">Hora Inicio *</label>
                        <input type="time" class="form-control-admin"
                               name="schedules[${scheduleIndex}][start_time]"
                               required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-admin">Hora Fin *</label>
                        <input type="time" class="form-control-admin"
                               name="schedules[${scheduleIndex}][end_time]"
                               required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-white text-danger border-danger w-100" onclick="removeSchedule(this)">
                            <i class="bi bi-trash-fill"></i> Quitar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', newSchedule);
    scheduleIndex++;
    updateRemoveButtons();
}

function removeSchedule(button) {
    const scheduleItem = button.closest('.schedule-item');
    scheduleItem.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const schedules = document.querySelectorAll('.schedule-item');
    schedules.forEach((item, index) => {
        const btnRemove = item.querySelector('button[onclick*="removeSchedule"]');
        if (schedules.length === 1) {
            btnRemove.disabled = true;
        } else {
            btnRemove.disabled = false;
        }
    });
}

document.getElementById('componentForm')?.addEventListener('submit', function(e) {
    const schedules = document.querySelectorAll('.schedule-item');
    if (schedules.length === 0) {
        e.preventDefault();
        alert('Debes agregar al menos un horario');
        return false;
    }
});
</script>
@endpush
