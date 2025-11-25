@extends('layouts.app')

@section('header', 'Nueva Propuesta')

@section('content')
<div class="container-fluid py-4">
    {{-- Información del evento --}}
    <div class="card-admin mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Evento Seleccionado</h6>
                    <h4 class="fw-bold text-brand-deep mb-1">{{ $event->name }}</h4>
                    <p class="text-brand-main mb-0 fw-bold">
                        <i class="bi bi-calendar-event-fill me-2"></i>
                        {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                    </p>
                </div>
                <a href="{{ route('proposals.index') }}" class="btn btn-white shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>Volver a Eventos
                </a>
            </div>
        </div>
    </div>

    {{-- Formulario de propuesta --}}
    <div class="card-admin">
        <div class="card-header-admin">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-file-earmark-text-fill me-2"></i>Formulario de Propuesta
            </h5>
        </div>
        <div class="card-body p-4">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-info border-0 bg-brand-main bg-opacity-10 text-brand-deep mb-4">
                <i class="bi bi-info-circle-fill me-2 text-brand-main"></i>
                Tu propuesta será revisada por el organizador del evento. Recibirás una notificación cuando sea evaluada.
            </div>

            <form action="{{ route('proposals.store', $event) }}" method="POST" id="proposalForm">
                @csrf

                {{-- Información básica --}}
                <div class="mb-5">
                    <h6 class="fw-bold text-brand-deep border-bottom pb-2 mb-4">
                        <i class="bi bi-info-circle-fill me-2 text-brand-main"></i>Información de la Propuesta
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label-admin">
                                Título de la propuesta <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control-admin @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="Ej: Introducción a Laravel" required>
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
                                Descripción detallada <span class="text-danger">*</span>
                            </label>
                            <div class="form-text text-muted mb-2">
                                Explica detalladamente el contenido de tu propuesta. Incluye objetivos, metodología y cualquier información relevante.
                            </div>
                            <textarea class="form-control-admin @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="12" required
                                      placeholder="Escribe aquí la descripción completa...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="level" class="form-label-admin">Nivel de dificultad</label>
                            <select class="form-select form-control-admin @error('level') is-invalid @enderror"
                                    id="level" name="level">
                                <option value="">Seleccionar nivel...</option>
                                <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Principiante</option>
                                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Avanzado</option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="capacity" class="form-label-admin">Cupos sugeridos</label>
                            <input type="number" class="form-control-admin @error('capacity') is-invalid @enderror"
                                   id="capacity" name="capacity" value="{{ old('capacity') }}" min="1"
                                   placeholder="Ej: 30">
                            <div class="form-text text-muted small">Opcional - el organizador puede ajustarlo</div>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Modalidad y ubicación --}}
                <div class="mb-5">
                    <h6 class="fw-bold text-brand-deep border-bottom pb-2 mb-4">
                        <i class="bi bi-geo-alt-fill me-2 text-brand-main"></i>Modalidad y Ubicación
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modality" class="form-label-admin">
                                Modalidad <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-control-admin @error('modality') is-invalid @enderror"
                                    id="modality" name="modality" required>
                                <option value="">Seleccionar modalidad...</option>
                                <option value="virtual" {{ old('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                <option value="in_person" {{ old('modality') == 'in_person' ? 'selected' : '' }}>Presencial</option>
                                <option value="hybrid" {{ old('modality') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                            </select>
                            @error('modality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="location" class="form-label-admin">Ubicación preferida</label>
                            <input type="text" class="form-control-admin @error('location') is-invalid @enderror"
                                   id="location" name="location" value="{{ old('location') }}"
                                   placeholder="Ej: Auditorio principal, Zoom, etc.">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Requisitos --}}
                <div class="mb-5">
                    <h6 class="fw-bold text-brand-deep border-bottom pb-2 mb-4">
                        <i class="bi bi-list-check me-2 text-brand-main"></i>Requisitos y Necesidades
                    </h6>

                    <div class="bg-light p-4 rounded-3 border border-light">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="participant_requirements" class="form-label-admin">
                                    Requisitos para participantes
                                </label>
                                <div class="form-text text-muted mb-2">
                                    Indica qué necesitan los participantes para aprovechar tu sesión (ej: laptop, software específico, conocimientos previos).
                                </div>
                                <textarea class="form-control-admin @error('participant_requirements') is-invalid @enderror"
                                          id="participant_requirements" name="participant_requirements" rows="5"
                                          placeholder="Ej: Laptop con Node.js instalado, conocimientos básicos de HTML...">{{ old('participant_requirements') }}</textarea>
                                @error('participant_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horarios propuestos --}}
                <div class="mb-5">
                    <h6 class="fw-bold text-brand-deep border-bottom pb-2 mb-4">
                        <i class="bi bi-calendar-week-fill me-2 text-brand-main"></i>Horarios Propuestos <span class="text-danger">*</span>
                    </h6>
                    <p class="text-muted small mb-3">Propón los horarios en los que podrías impartir tu charla/taller</p>

                    <div id="schedulesContainer">
                        {{-- Horario inicial --}}
                        <div class="schedule-item bg-light rounded-3 p-3 mb-3 border border-light">
                            <div class="row align-items-end g-3">
                                <div class="col-md-4">
                                    <label class="form-label-admin small">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control-admin"
                                           name="schedules[0][date]"
                                           min="{{ $event->start_date->format('Y-m-d') }}"
                                           max="{{ $event->end_date->format('Y-m-d') }}"
                                           required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-admin small">Hora inicio <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control-admin"
                                           name="schedules[0][start_time]" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-admin small">Hora fin <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control-admin"
                                           name="schedules[0][end_time]" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-white text-danger w-100" onclick="removeSchedule(this)" disabled>
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-evai-outline btn-sm fw-bold" onclick="addSchedule()">
                        <i class="bi bi-plus-circle-fill me-2"></i>Agregar otro horario
                    </button>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                    <a href="{{ route('proposals.index') }}" class="btn btn-white px-4">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-brand-accent px-4 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-2"></i>Enviar Propuesta
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
        <div class="schedule-item bg-light rounded-3 p-3 mb-3 border border-light">
            <div class="row align-items-end g-3">
                <div class="col-md-4">
                    <label class="form-label-admin small">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control-admin"
                           name="schedules[${scheduleIndex}][date]"
                           min="${minDate}"
                           max="${maxDate}"
                           required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-admin small">Hora inicio <span class="text-danger">*</span></label>
                    <input type="time" class="form-control-admin"
                           name="schedules[${scheduleIndex}][start_time]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-admin small">Hora fin <span class="text-danger">*</span></label>
                    <input type="time" class="form-control-admin"
                           name="schedules[${scheduleIndex}][end_time]" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-white text-danger w-100" onclick="removeSchedule(this)">
                        <i class="bi bi-trash-fill"></i>
                    </button>
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
