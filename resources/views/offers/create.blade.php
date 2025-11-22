@extends('layouts.app')

@section('header', 'Publicar Oferta')

@section('content')
<div class="container-fluid">
    {{-- Información del evento --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-bold" style="color: #0C2340;">{{ $event->name }}</h5>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar-event me-1" style="color: #4499BB;"></i>
                        {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                    </p>
                </div>
                <a href="{{ route('offers.index', $event) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Formulario de oferta --}}
    <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background-color: #0C2340;">
            <h5 class="mb-0">
                <i class="bi bi-megaphone-fill me-2"></i>Publicar Oferta Abierta
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info border-0">
                <i class="bi bi-info-circle-fill me-2"></i>
                Las ofertas abiertas son componentes para los cuales buscas ponentes o talleristas externos.
                Aparecerán públicamente y los usuarios podrán postularse.
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('offers.store', $event) }}" method="POST" id="offerForm">
                @csrf

                {{-- Información básica --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: #0C2340;">
                            <i class="bi bi-info-circle me-2"></i>Información de la Oferta
                        </h6>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="name" class="form-label fw-semibold">
                            Título de la actividad solicitada <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}"
                               placeholder="Ej: Taller de Machine Learning" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="type" class="form-label fw-semibold">
                            Tipo <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('type') is-invalid @enderror"
                                id="type" name="type" required>
                            <option value="">Seleccionar...</option>
                            <option value="talk" {{ old('type') == 'talk' ? 'selected' : '' }}>Charla</option>
                            <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Taller</option>
                            <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Actividad</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label fw-semibold">
                            Descripción de la solicitud <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="5" required
                                  placeholder="Describe qué tipo de charla/taller estás buscando, temas de interés, objetivos, etc.">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="level" class="form-label fw-semibold">Nivel esperado</label>
                        <select class="form-select @error('level') is-invalid @enderror"
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

                    <div class="col-md-6 mb-3">
                        <label for="capacity" class="form-label fw-semibold">Número de cupos</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                               id="capacity" name="capacity" value="{{ old('capacity') }}" min="1"
                               placeholder="Ej: 30">
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Modalidad y ubicación --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: #0C2340;">
                            <i class="bi bi-geo-alt me-2"></i>Modalidad y Ubicación
                        </h6>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="modality" class="form-label fw-semibold">
                            Modalidad <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('modality') is-invalid @enderror"
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

                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label fw-semibold">Ubicación</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                               id="location" name="location" value="{{ old('location') }}"
                               placeholder="Ej: Auditorio principal, Zoom, etc.">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Compensación y requisitos --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: #0C2340;">
                            <i class="bi bi-cash-stack me-2"></i>Compensación y Requisitos
                        </h6>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="organizer_cost" class="form-label fw-semibold">Compensación al ponente</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('organizer_cost') is-invalid @enderror"
                                   id="organizer_cost" name="organizer_cost"
                                   value="{{ old('organizer_cost') }}" min="0" step="0.01"
                                   placeholder="0.00">
                        </div>
                        <div class="form-text">Deja en $0 si no hay compensación económica</div>
                        @error('organizer_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="instructor_requirements" class="form-label fw-semibold">Requisitos para postularse</label>
                        <textarea class="form-control @error('instructor_requirements') is-invalid @enderror"
                                  id="instructor_requirements" name="instructor_requirements" rows="3"
                                  placeholder="Ej: Experiencia demostrable en el tema, certificaciones, portafolio, etc.">{{ old('instructor_requirements') }}</textarea>
                        @error('instructor_requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Horarios --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: #0C2340;">
                            <i class="bi bi-calendar-week me-2"></i>Horarios Disponibles <span class="text-danger">*</span>
                        </h6>
                        <p class="text-muted">Define los horarios en los que se podría impartir esta actividad</p>
                    </div>

                    <div class="col-12">
                        <div id="schedulesContainer">
                            {{-- Horario inicial --}}
                            <div class="schedule-item card mb-3 border">
                                <div class="card-body">
                                    <div class="row align-items-end">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control"
                                                   name="schedules[0][date]"
                                                   min="{{ $event->start_date->format('Y-m-d') }}"
                                                   max="{{ $event->end_date->format('Y-m-d') }}"
                                                   required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label fw-semibold">Hora inicio <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control"
                                                   name="schedules[0][start_time]" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label fw-semibold">Hora fin <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control"
                                                   name="schedules[0][end_time]" required>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSchedule(this)" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary" onclick="addSchedule()">
                            <i class="bi bi-plus-circle me-2"></i>Agregar otro horario
                        </button>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="row">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('offers.index', $event) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn" style="background-color: #8CC63F; color: white;">
                                <i class="bi bi-megaphone me-2"></i>Publicar Oferta
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let scheduleIndex = 1;

function addSchedule() {
    const container = document.getElementById('schedulesContainer');
    const minDate = '{{ $event->start_date->format("Y-m-d") }}';
    const maxDate = '{{ $event->end_date->format("Y-m-d") }}';

    const newSchedule = `
        <div class="schedule-item card mb-3 border">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-2">
                        <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control"
                               name="schedules[${scheduleIndex}][date]"
                               min="${minDate}"
                               max="${maxDate}"
                               required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-semibold">Hora inicio <span class="text-danger">*</span></label>
                        <input type="time" class="form-control"
                               name="schedules[${scheduleIndex}][start_time]" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label fw-semibold">Hora fin <span class="text-danger">*</span></label>
                        <input type="time" class="form-control"
                               name="schedules[${scheduleIndex}][end_time]" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSchedule(this)">
                            <i class="bi bi-trash"></i>
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
    button.closest('.schedule-item').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const schedules = document.querySelectorAll('.schedule-item');
    schedules.forEach((item) => {
        const btnRemove = item.querySelector('button[onclick*="removeSchedule"]');
        btnRemove.disabled = schedules.length === 1;
    });
}
</script>
@endpush
