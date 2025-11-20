@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $event->name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar"></i> {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('components.index', $event) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Agregar Nuevo Componente</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('components.store', $event) }}" method="POST" id="componentForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Información Básica</h5>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Nombre del Componente *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Tipo *</label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Activity" {{ old('type') == 'Activity' ? 'selected' : '' }}>Actividad</option>
                                    <option value="Talk" {{ old('type') == 'Talk' ? 'selected' : '' }}>Conferencia</option>
                                    <option value="Workshop" {{ old('type') == 'Workshop' ? 'selected' : '' }}>Taller</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Descripción *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cover_url" class="form-label">URL de Imagen de Portada</label>
                                <input type="url" class="form-control @error('cover_url') is-invalid @enderror"
                                       id="cover_url" name="cover_url" value="{{ old('cover_url') }}"
                                       placeholder="https://ejemplo.com/imagen.jpg">
                                @error('cover_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Nivel</label>
                                <select class="form-select @error('level') is-invalid @enderror"
                                        id="level" name="level">
                                    <option value="">Seleccionar...</option>
                                    <option value="Beginner" {{ old('level') == 'Beginner' ? 'selected' : '' }}>Principiante</option>
                                    <option value="Intermediate" {{ old('level') == 'Intermediate' ? 'selected' : '' }}>Intermedio</option>
                                    <option value="Advanced" {{ old('level') == 'Advanced' ? 'selected' : '' }}>Avanzado</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Modalidad y Ubicación</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modality" class="form-label">Modalidad *</label>
                                <select class="form-select @error('modality') is-invalid @enderror"
                                        id="modality" name="modality" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="virtual" {{ old('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="presential" {{ old('modality') == 'presential' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hybrid" {{ old('modality') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                                @error('modality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Ubicación</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location') }}"
                                       placeholder="Ej: Sala de Conferencias A, Link de Zoom, etc.">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Capacidad y Precios</h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="slots" class="form-label">Número de Cupos</label>
                                <input type="number" class="form-control @error('slots') is-invalid @enderror"
                                       id="slots" name="slots" value="{{ old('slots') }}" min="1"
                                       placeholder="Ej: 30">
                                <small class="text-muted">Dejar vacío para cupos ilimitados</small>
                                @error('slots')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="attendee_price" class="form-label">Precio para Asistente</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('attendee_price') is-invalid @enderror"
                                           id="attendee_price" name="attendee_price"
                                           value="{{ old('attendee_price', 0) }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted">$0.00 para actividad gratuita</small>
                                @error('attendee_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="organizer_cost" class="form-label">Costo del Organizador</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('organizer_cost') is-invalid @enderror"
                                           id="organizer_cost" name="organizer_cost"
                                           value="{{ old('organizer_cost') }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted">Costo interno (opcional)</small>
                                @error('organizer_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Requisitos</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="participant_requirements" class="form-label">Requisitos del Participante</label>
                                <textarea class="form-control @error('participant_requirements') is-invalid @enderror"
                                          id="participant_requirements" name="participant_requirements" rows="3"
                                          placeholder="Ej: Laptop, conocimientos básicos de programación, etc.">{{ old('participant_requirements') }}</textarea>
                                @error('participant_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="instructor_requirements" class="form-label">Requisitos del Instructor</label>
                                <textarea class="form-control @error('instructor_requirements') is-invalid @enderror"
                                          id="instructor_requirements" name="instructor_requirements" rows="3"
                                          placeholder="Ej: Proyector, micrófono, conexión a internet, etc.">{{ old('instructor_requirements') }}</textarea>
                                @error('instructor_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Horarios *</h5>
                            </div>

                            <div class="col-12">
                                <div id="schedulesContainer">
                                    <div class="schedule-item card mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-end">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Fecha *</label>
                                                    <input type="date" class="form-control @error('schedules.0.date') is-invalid @enderror"
                                                           name="schedules[0][date]"
                                                           value="{{ old('schedules.0.date') }}"
                                                           min="{{ $event->start_date->format('Y-m-d') }}"
                                                           max="{{ $event->end_date->format('Y-m-d') }}"
                                                           required>
                                                    @error('schedules.0.date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">Hora Inicio *</label>
                                                    <input type="time" class="form-control @error('schedules.0.start_time') is-invalid @enderror"
                                                           name="schedules[0][start_time]"
                                                           value="{{ old('schedules.0.start_time') }}"
                                                           required>
                                                    @error('schedules.0.start_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">Hora Fin *</label>
                                                    <input type="time" class="form-control @error('schedules.0.end_time') is-invalid @enderror"
                                                           name="schedules[0][end_time]"
                                                           value="{{ old('schedules.0.end_time') }}"
                                                           required>
                                                    @error('schedules.0.end_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSchedule(this)" disabled>
                                                        <i class="bi bi-trash"></i> Quitar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-primary" onclick="addSchedule()">
                                    <i class="bi bi-plus-circle"></i> Agregar Otro Horario
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('components.index', $event) }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Guardar Componente
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let scheduleIndex = 1;

function addSchedule() {
    const container = document.getElementById('schedulesContainer');
    const minDate = '{{ $event->start_date->format("Y-m-d") }}';
    const maxDate = '{{ $event->end_date->format("Y-m-d") }}';

    const newSchedule = `
        <div class="schedule-item card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Fecha *</label>
                        <input type="date" class="form-control"
                               name="schedules[${scheduleIndex}][date]"
                               min="${minDate}"
                               max="${maxDate}"
                               required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Hora Inicio *</label>
                        <input type="time" class="form-control"
                               name="schedules[${scheduleIndex}][start_time]"
                               required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Hora Fin *</label>
                        <input type="time" class="form-control"
                               name="schedules[${scheduleIndex}][end_time]"
                               required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSchedule(this)">
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

document.getElementById('componentForm')?.addEventListener('submit', function(e) {
    const schedules = document.querySelectorAll('.schedule-item');
    if (schedules.length === 0) {
        e.preventDefault();
        alert('Debes agregar al menos un horario');
        return false;
    }
});
</script>
@endsection