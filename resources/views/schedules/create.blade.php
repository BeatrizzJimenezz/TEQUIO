@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Header --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #8CC63F !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #0C2340;">
                                <i class="bi bi-plus-circle me-2" style="color: #8CC63F;"></i>
                                Agregar Horario
                            </h3>
                            <p class="text-muted mb-0">{{ $component->name }} - {{ $event->name }}</p>
                        </div>
                        <a href="{{ route('schedules.index', [$event, $component]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

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

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-clock me-2" style="color: #4499BB;"></i>
                        Nuevo Horario
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('schedules.store', [$event, $component]) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="date" class="form-label fw-semibold" style="color: #0C2340;">
                                <i class="bi bi-calendar3 me-1" style="color: #4499BB;"></i>
                                Fecha <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   class="form-control form-control-lg @error('date') is-invalid @enderror"
                                   id="date"
                                   name="date"
                                   value="{{ old('date', $event->start_date->format('Y-m-d')) }}"
                                   min="{{ $event->start_date->format('Y-m-d') }}"
                                   max="{{ $event->end_date->format('Y-m-d') }}"
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                El evento es del {{ $event->start_date->format('d/m/Y') }} al {{ $event->end_date->format('d/m/Y') }}
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="start_time" class="form-label fw-semibold" style="color: #0C2340;">
                                        <i class="bi bi-clock me-1" style="color: #8CC63F;"></i>
                                        Hora de Inicio <span class="text-danger">*</span>
                                    </label>
                                    <input type="time"
                                           class="form-control form-control-lg @error('start_time') is-invalid @enderror"
                                           id="start_time"
                                           name="start_time"
                                           value="{{ old('start_time', '09:00') }}"
                                           required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="end_time" class="form-label fw-semibold" style="color: #0C2340;">
                                        <i class="bi bi-clock-fill me-1" style="color: #8CC63F;"></i>
                                        Hora de Fin <span class="text-danger">*</span>
                                    </label>
                                    <input type="time"
                                           class="form-control form-control-lg @error('end_time') is-invalid @enderror"
                                           id="end_time"
                                           name="end_time"
                                           value="{{ old('end_time', '10:00') }}"
                                           required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Component Info --}}
                        <div class="p-4 rounded mb-4" style="background-color: #f0f7ff; border-left: 4px solid #4499BB;">
                            <h6 class="fw-bold mb-3" style="color: #0C2340;">
                                <i class="bi bi-info-circle me-2" style="color: #4499BB;"></i>
                                Información del Componente
                            </h6>
                            <div class="row">
                                @if($component->location)
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Ubicación</small>
                                        <strong>{{ $component->location }}</strong>
                                    </div>
                                @endif
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">Modalidad</small>
                                    <strong>{{ ucfirst($component->modality) }}</strong>
                                </div>
                                @if($component->speaker && $component->speaker->user)
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Ponente</small>
                                        <strong>{{ $component->speaker->user->name }}</strong>
                                    </div>
                                @endif
                            </div>
                            <hr class="my-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1" style="color: #8CC63F;"></i>
                                El sistema verificará automáticamente conflictos de ubicación y ponente.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('schedules.index', [$event, $component]) }}" class="btn btn-outline-secondary btn-lg">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-lg text-white" style="background-color: #8CC63F;">
                                <i class="bi bi-check-lg me-1"></i> Crear Horario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
