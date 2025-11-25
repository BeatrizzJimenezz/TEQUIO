@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Header --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #4499BB !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #0C2340;">
                                <i class="bi bi-pencil-square me-2" style="color: #4499BB;"></i>
                                Editar Componente
                            </h3>
                            <p class="text-muted mb-0">
                                {{ $component->name }} - {{ $event->name }}
                            </p>
                        </div>
                        <a href="{{ route('components.index', $event) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="alert border-0 shadow-sm mb-4" style="background-color: #fff5f5; border-left: 4px solid #dc3545 !important;">
                    <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert border-0 shadow-sm mb-4" style="background-color: #fff5f5; border-left: 4px solid #dc3545 !important;">
                    <strong class="text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Error de validación:
                    </strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('components.update', [$event, $component]) }}" method="POST" id="componentForm">
                @csrf
                @method('PUT')

                {{-- Información Básica --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-info-circle me-2" style="color: #4499BB;"></i>
                            Información Básica
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label fw-semibold" style="color: #0C2340;">
                                    Nombre del Componente <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $component->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label fw-semibold" style="color: #0C2340;">
                                    Tipo <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="activity" {{ old('type', $component->type) == 'activity' ? 'selected' : '' }}>Actividad</option>
                                    <option value="talk" {{ old('type', $component->type) == 'talk' ? 'selected' : '' }}>Conferencia</option>
                                    <option value="workshop" {{ old('type', $component->type) == 'workshop' ? 'selected' : '' }}>Taller</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label fw-semibold" style="color: #0C2340;">
                                    Descripción <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4" required>{{ old('description', $component->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cover_image" class="form-label fw-semibold" style="color: #0C2340;">
                                    URL de Imagen de Portada
                                </label>
                                <input type="url" class="form-control @error('cover_image') is-invalid @enderror"
                                       id="cover_image" name="cover_image" value="{{ old('cover_image', $component->cover_image) }}"
                                       placeholder="https://ejemplo.com/imagen.jpg">
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label fw-semibold" style="color: #0C2340;">
                                    Nivel
                                </label>
                                <select class="form-select @error('level') is-invalid @enderror"
                                        id="level" name="level">
                                    <option value="">Seleccionar...</option>
                                    <option value="beginner" {{ old('level', $component->level) == 'beginner' ? 'selected' : '' }}>Principiante</option>
                                    <option value="intermediate" {{ old('level', $component->level) == 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                                    <option value="advanced" {{ old('level', $component->level) == 'advanced' ? 'selected' : '' }}>Avanzado</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ponente --}}
                @php
                    // Determinar el tipo de ponente actual
                    $currentSpeakerType = 'none';
                    if ($component->speaker_id) {
                        $currentSpeakerType = 'existing';
                    }
                @endphp
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-person me-2" style="color: #8CC63F;"></i>
                            Ponente / Tallerista
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        {{-- Tipo de selección de ponente --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #0C2340;">
                                ¿Cómo deseas asignar el ponente?
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="speaker_type" id="speakerExisting"
                                           value="existing" {{ old('speaker_type', $currentSpeakerType) == 'existing' ? 'checked' : '' }}
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
                                           value="none" {{ old('speaker_type', $currentSpeakerType) == 'none' ? 'checked' : '' }}
                                           onchange="toggleSpeakerSection()">
                                    <label class="form-check-label" for="speakerNone">
                                        Sin asignar
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Sección de ponente existente --}}
                        <div id="existingSpeakerSection" class="{{ old('speaker_type', $currentSpeakerType) == 'existing' ? '' : 'd-none' }}">
                            <div class="col-md-8 mb-3">
                                <label for="speaker_id" class="form-label fw-semibold" style="color: #0C2340;">
                                    Seleccionar Ponente
                                </label>
                                <select class="form-select @error('speaker_id') is-invalid @enderror"
                                        id="speaker_id" name="speaker_id">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($speakers as $profile)
                                        <option value="{{ $profile->id }}"
                                            {{ old('speaker_id', $component->speaker_id) == $profile->id ? 'selected' : '' }}>
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
                            <div class="alert border-0 mb-3" style="background-color: #e8f4f8; border-left: 4px solid #4499BB !important;">
                                <i class="bi bi-info-circle me-2" style="color: #4499BB;"></i>
                                <small>Crea un perfil temporal para un ponente que aún no está registrado en el sistema.
                                Cuando el ponente se registre con el mismo email, podrá vincular este perfil a su cuenta.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="temp_name" class="form-label fw-semibold" style="color: #0C2340;">
                                        Nombre completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('temp_name') is-invalid @enderror"
                                           id="temp_name" name="temp_name" value="{{ old('temp_name') }}"
                                           placeholder="Ej: Dr. Juan Pérez García">
                                    @error('temp_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="temp_email" class="form-label fw-semibold" style="color: #0C2340;">
                                        Correo electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('temp_email') is-invalid @enderror"
                                           id="temp_email" name="temp_email" value="{{ old('temp_email') }}"
                                           placeholder="Ej: ponente@email.com">
                                    @error('temp_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="temp_profession" class="form-label fw-semibold" style="color: #0C2340;">
                                        Profesión / Ocupación
                                    </label>
                                    <input type="text" class="form-control @error('temp_profession') is-invalid @enderror"
                                           id="temp_profession" name="temp_profession" value="{{ old('temp_profession') }}"
                                           placeholder="Ej: Ingeniero en Sistemas, Investigador en IA, Docente universitario">
                                    @error('temp_profession')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Opcional. Ayuda a identificar al ponente.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modalidad y Ubicación --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-geo-alt me-2" style="color: #4499BB;"></i>
                            Modalidad y Ubicación
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modality" class="form-label fw-semibold" style="color: #0C2340;">
                                    Modalidad <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('modality') is-invalid @enderror"
                                        id="modality" name="modality" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="virtual" {{ old('modality', $component->modality) == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality', $component->modality) == 'in_person' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hybrid" {{ old('modality', $component->modality) == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                                @error('modality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label fw-semibold" style="color: #0C2340;">
                                    Ubicación
                                </label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location', $component->location) }}"
                                       placeholder="Ej: Sala de Conferencias A, Link de Zoom, etc.">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Capacidad y Precios --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-currency-dollar me-2" style="color: #8CC63F;"></i>
                            Capacidad y Precios
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="capacity" class="form-label fw-semibold" style="color: #0C2340;">
                                    Número de Cupos
                                </label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                       id="capacity" name="capacity" value="{{ old('capacity', $component->capacity) }}" min="1"
                                       placeholder="Ej: 30">
                                <small class="text-muted">Dejar vacío para cupos ilimitados</small>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="attendee_price" class="form-label fw-semibold" style="color: #0C2340;">
                                    Precio para Asistente
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('attendee_price') is-invalid @enderror"
                                           id="attendee_price" name="attendee_price"
                                           value="{{ old('attendee_price', $component->attendee_price) }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted">$0.00 para actividad gratuita</small>
                                @error('attendee_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="organizer_cost" class="form-label fw-semibold" style="color: #0C2340;">
                                    Costo del Organizador
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('organizer_cost') is-invalid @enderror"
                                           id="organizer_cost" name="organizer_cost"
                                           value="{{ old('organizer_cost', $component->organizer_cost) }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted">Costo interno (opcional)</small>
                                @error('organizer_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Requisitos --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-list-check me-2" style="color: #4499BB;"></i>
                            Requisitos
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="participant_requirements" class="form-label fw-semibold" style="color: #0C2340;">
                                    Requisitos del Participante
                                </label>
                                <textarea class="form-control @error('participant_requirements') is-invalid @enderror"
                                          id="participant_requirements" name="participant_requirements" rows="3"
                                          placeholder="Ej: Laptop, conocimientos básicos de programación, etc.">{{ old('participant_requirements', $component->participant_requirements) }}</textarea>
                                @error('participant_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="instructor_requirements" class="form-label fw-semibold" style="color: #0C2340;">
                                    Requisitos del Instructor
                                </label>
                                <textarea class="form-control @error('instructor_requirements') is-invalid @enderror"
                                          id="instructor_requirements" name="instructor_requirements" rows="3"
                                          placeholder="Ej: Proyector, micrófono, conexión a internet, etc.">{{ old('instructor_requirements', $component->instructor_requirements) }}</textarea>
                                @error('instructor_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horarios --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-clock me-2" style="color: #8CC63F;"></i>
                            Horarios <span class="text-danger">*</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div id="schedulesContainer">
                            @if(old('schedules'))
                                @foreach(old('schedules') as $index => $schedule)
                                    <div class="schedule-item card mb-3 border">
                                        <div class="card-body">
                                            <div class="row align-items-end">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label fw-semibold" style="color: #0C2340;">Fecha *</label>
                                                    <input type="date" class="form-control @error('schedules.'.$index.'.date') is-invalid @enderror"
                                                           name="schedules[{{ $index }}][date]"
                                                           value="{{ $schedule['date'] }}"
                                                           min="{{ $event->start_date->format('Y-m-d') }}"
                                                           max="{{ $event->end_date->format('Y-m-d') }}"
                                                           required>
                                                    @error('schedules.'.$index.'.date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label fw-semibold" style="color: #0C2340;">Hora Inicio *</label>
                                                    <input type="time" class="form-control @error('schedules.'.$index.'.start_time') is-invalid @enderror"
                                                           name="schedules[{{ $index }}][start_time]"
                                                           value="{{ $schedule['start_time'] }}"
                                                           required>
                                                    @error('schedules.'.$index.'.start_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label fw-semibold" style="color: #0C2340;">Hora Fin *</label>
                                                    <input type="time" class="form-control @error('schedules.'.$index.'.end_time') is-invalid @enderror"
                                                           name="schedules[{{ $index }}][end_time]"
                                                           value="{{ $schedule['end_time'] }}"
                                                           required>
                                                    @error('schedules.'.$index.'.end_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSchedule(this)">
                                                        <i class="bi bi-trash"></i> Quitar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @foreach($component->schedules as $index => $schedule)
                                    <div class="schedule-item card mb-3 border">
                                        <div class="card-body">
                                            <div class="row align-items-end">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label fw-semibold" style="color: #0C2340;">Fecha *</label>
                                                    <input type="date" class="form-control"
                                                           name="schedules[{{ $index }}][date]"
                                                           value="{{ $schedule->date->format('Y-m-d') }}"
                                                           min="{{ $event->start_date->format('Y-m-d') }}"
                                                           max="{{ $event->end_date->format('Y-m-d') }}"
                                                           required>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label fw-semibold" style="color: #0C2340;">Hora Inicio *</label>
                                                    <input type="time" class="form-control"
                                                           name="schedules[{{ $index }}][start_time]"
                                                           value="{{ $schedule->start_time }}"
                                                           required>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label fw-semibold" style="color: #0C2340;">Hora Fin *</label>
                                                    <input type="time" class="form-control"
                                                           name="schedules[{{ $index }}][end_time]"
                                                           value="{{ $schedule->end_time }}"
                                                           required>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSchedule(this)">
                                                        <i class="bi bi-trash"></i> Quitar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button type="button" class="btn btn-outline-primary" onclick="addSchedule()">
                            <i class="bi bi-plus-lg me-1"></i> Agregar Otro Horario
                        </button>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('components.index', $event) }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-lg text-white" style="background-color: #4499BB;">
                        <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let scheduleIndex = {{ old('schedules') ? count(old('schedules')) : $component->schedules->count() }};

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

function addSchedule() {
    const container = document.getElementById('schedulesContainer');
    const minDate = '{{ $event->start_date->format("Y-m-d") }}';
    const maxDate = '{{ $event->end_date->format("Y-m-d") }}';

    const newSchedule = `
        <div class="schedule-item card mb-3 border">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-2">
                        <label class="form-label fw-semibold" style="color: #0C2340;">Fecha *</label>
                        <input type="date" class="form-control"
                               name="schedules[${scheduleIndex}][date]"
                               min="${minDate}"
                               max="${maxDate}"
                               required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-semibold" style="color: #0C2340;">Hora Inicio *</label>
                        <input type="time" class="form-control"
                               name="schedules[${scheduleIndex}][start_time]"
                               required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-semibold" style="color: #0C2340;">Hora Fin *</label>
                        <input type="time" class="form-control"
                               name="schedules[${scheduleIndex}][end_time]"
                               required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSchedule(this)">
                            <i class="bi bi-trash"></i> Quitar
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

document.addEventListener('DOMContentLoaded', function() {
    updateRemoveButtons();
});

document.getElementById('componentForm')?.addEventListener('submit', function(e) {
    const schedules = document.querySelectorAll('.schedule-item');
    if (schedules.length === 0) {
        e.preventDefault();
        alert('Debes agregar al menos un horario');
        return false;
    }
});

// Inicializar la sección de ponente al cargar
document.addEventListener('DOMContentLoaded', function() {
    // No limpiar campos en edit, ya que pueden tener valores existentes
});
</script>
@endpush
