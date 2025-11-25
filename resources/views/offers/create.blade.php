@extends('layouts.app')

@section('header', 'Publicar Oferta')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Información del evento --}}
    <div class="card-admin mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="mb-1 fw-bold text-brand-deep">
                        <i class="bi bi-megaphone-fill me-2 text-brand-accent"></i>
                        Publicar Oferta Abierta
                    </h3>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-1 text-brand-main"></i>
                        {{ $event->name }} | {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                    </p>
                </div>
                <a href="{{ route('offers.index', $event) }}" class="btn btn-white shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Formulario de oferta --}}
    <div class="card-admin mb-4">
        <div class="card-header-admin">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-pencil-square me-2 text-brand-main"></i>
                Detalles de la Oferta
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex">
                    <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                    <div>
                        Las ofertas abiertas son componentes para los cuales buscas ponentes o talleristas externos.
                        Aparecerán públicamente y los usuarios podrán postularse.
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
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

            <form action="{{ route('offers.store', $event) }}" method="POST" id="offerForm">
                @csrf

                {{-- Información básica --}}
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-brand-deep border-bottom pb-2 mb-3">
                            <i class="bi bi-info-circle me-2 text-brand-accent"></i>Información de la Oferta
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label-admin">
                                    Título de la actividad solicitada <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control-admin @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="Ej: Taller de Machine Learning" required>
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
                                    <option value="talk" {{ old('type') == 'talk' ? 'selected' : '' }}>Charla</option>
                                    <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Taller</option>
                                    <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Actividad</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label-admin">
                                    Descripción de la solicitud <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control-admin @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="5" required
                                          placeholder="Describe qué tipo de charla/taller estás buscando, temas de interés, objetivos, etc.">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="level" class="form-label-admin">Nivel esperado</label>
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

                            <div class="col-md-6">
                                <label for="capacity" class="form-label-admin">Número de cupos</label>
                                <input type="number" class="form-control-admin @error('capacity') is-invalid @enderror"
                                       id="capacity" name="capacity" value="{{ old('capacity') }}" min="1"
                                       placeholder="Ej: 30">
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modalidad y ubicación --}}
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-brand-deep border-bottom pb-2 mb-3">
                            <i class="bi bi-geo-alt me-2 text-brand-accent"></i>Modalidad y Ubicación
                        </h6>

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
                                <label for="location" class="form-label-admin">Ubicación</label>
                                <input type="text" class="form-control-admin @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location') }}"
                                       placeholder="Ej: Auditorio principal, Zoom, etc.">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Compensación y requisitos --}}
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-brand-deep border-bottom pb-2 mb-3">
                            <i class="bi bi-cash-stack me-2 text-brand-accent"></i>Compensación y Requisitos
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="organizer_cost" class="form-label-admin">Compensación al ponente</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">$</span>
                                    <input type="number" class="form-control-admin border-start-0 ps-0 @error('organizer_cost') is-invalid @enderror"
                                           id="organizer_cost" name="organizer_cost"
                                           value="{{ old('organizer_cost') }}" min="0" step="0.01"
                                           placeholder="0.00">
                                </div>
                                <div class="form-text">Deja en $0 si no hay compensación económica</div>
                                @error('organizer_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="instructor_requirements" class="form-label-admin">Requisitos para postularse</label>
                                <textarea class="form-control-admin @error('instructor_requirements') is-invalid @enderror"
                                          id="instructor_requirements" name="instructor_requirements" rows="3"
                                          placeholder="Ej: Experiencia demostrable en el tema, certificaciones, portafolio, etc.">{{ old('instructor_requirements') }}</textarea>
                                @error('instructor_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horarios --}}
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-brand-deep border-bottom pb-2 mb-3">
                            <i class="bi bi-calendar-week me-2 text-brand-accent"></i>Horarios Disponibles <span class="text-danger">*</span>
                        </h6>
                        <p class="text-muted mb-3">Define los horarios en los que se podría impartir esta actividad</p>

                        <div id="schedulesContainer">
                            {{-- Horario inicial --}}
                            <div class="schedule-item card border-0 shadow-sm mb-3 bg-white">
                                <div class="card-body">
                                    <div class="row align-items-end g-3">
                                        <div class="col-md-4">
                                            <label class="form-label-admin">Fecha <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control-admin"
                                                   name="schedules[0][date]"
                                                   min="{{ $event->start_date->format('Y-m-d') }}"
                                                   max="{{ $event->end_date->format('Y-m-d') }}"
                                                   required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-admin">Hora inicio <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control-admin"
                                                   name="schedules[0][start_time]" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-admin">Hora fin <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control-admin"
                                                   name="schedules[0][end_time]" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-white text-danger border-danger w-100" onclick="removeSchedule(this)" disabled>
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary border-dashed w-100 py-2" onclick="addSchedule()">
                            <i class="bi bi-plus-circle me-2"></i>Agregar otro horario
                        </button>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-between gap-3">
                    <a href="{{ route('offers.index', $event) }}" class="btn btn-white btn-lg shadow-sm px-4">
                        <i class="bi bi-x-lg me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-brand-main btn-lg shadow-sm px-5 text-white">
                        <i class="bi bi-megaphone-fill me-2"></i>Publicar Oferta
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

function addSchedule() {
    const container = document.getElementById('schedulesContainer');
    const minDate = '{{ $event->start_date->format("Y-m-d") }}';
    const maxDate = '{{ $event->end_date->format("Y-m-d") }}';

    const newSchedule = `
        <div class="schedule-item card border-0 shadow-sm mb-3 bg-white">
            <div class="card-body">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label-admin">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control-admin"
                               name="schedules[${scheduleIndex}][date]"
                               min="${minDate}"
                               max="${maxDate}"
                               required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-admin">Hora inicio <span class="text-danger">*</span></label>
                        <input type="time" class="form-control-admin"
                               name="schedules[${scheduleIndex}][start_time]" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-admin">Hora fin <span class="text-danger">*</span></label>
                        <input type="time" class="form-control-admin"
                               name="schedules[${scheduleIndex}][end_time]" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-white text-danger border-danger w-100" onclick="removeSchedule(this)">
                            <i class="bi bi-trash-fill"></i>
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
