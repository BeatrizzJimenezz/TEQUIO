@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Header --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #4499BB !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #0C2340;">
                                <i class="bi bi-calendar-week me-2" style="color: #4499BB;"></i>
                                Agenda del Evento
                            </h3>
                            <p class="text-muted mb-0">{{ $event->name }}</p>
                        </div>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver a eventos
                        </a>
                    </div>
                </div>
            </div>

            {{-- Conflicts Alert --}}
            @if($conflicts->isNotEmpty())
                <div class="alert border-0 shadow-sm mb-4" style="background-color: #fff5f5; border-left: 4px solid #dc3545 !important;">
                    <h5 class="alert-heading fw-bold" style="color: #dc3545;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Se detectaron {{ $conflicts->count() }} conflicto(s) de horario
                    </h5>
                    <hr>
                    @foreach($conflicts as $conflict)
                        <div class="mb-3 p-3 rounded" style="background-color: rgba(220, 53, 69, 0.1);">
                            <strong style="color: #0C2340;">{{ $conflict['component']->name }}</strong>
                            <span class="text-muted">
                                ({{ $conflict['schedule']->date->format('d/m/Y') }} -
                                {{ substr($conflict['schedule']->start_time, 0, 5) }} a {{ substr($conflict['schedule']->end_time, 0, 5) }})
                            </span>
                            <ul class="mb-0 mt-2">
                                @foreach($conflict['errors'] as $error)
                                    <li class="text-danger">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert border-0 shadow-sm mb-4" style="background-color: #f0fff4; border-left: 4px solid #8CC63F !important;">
                    <i class="bi bi-check-circle-fill me-2" style="color: #8CC63F;"></i>
                    <strong>No hay conflictos de horario detectados</strong>
                </div>
            @endif

            {{-- Statistics --}}
            <div class="row mb-4 g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4499BB 0%, #3a8aab 100%);">
                        <div class="card-body text-center text-white py-4">
                            <i class="bi bi-collection mb-2" style="font-size: 2rem;"></i>
                            <h2 class="mb-0 fw-bold">{{ $event->components->count() }}</h2>
                            <small class="opacity-75">Componentes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8CC63F 0%, #7ab635 100%);">
                        <div class="card-body text-center text-white py-4">
                            <i class="bi bi-calendar-check mb-2" style="font-size: 2rem;"></i>
                            <h2 class="mb-0 fw-bold">{{ $schedulesByDate->flatten(1)->count() }}</h2>
                            <small class="opacity-75">Sesiones Programadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, {{ $conflicts->isEmpty() ? '#6c757d' : '#dc3545' }} 0%, {{ $conflicts->isEmpty() ? '#5a6268' : '#c82333' }} 100%);">
                        <div class="card-body text-center text-white py-4">
                            <i class="bi bi-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                            <h2 class="mb-0 fw-bold">{{ $conflicts->count() }}</h2>
                            <small class="opacity-75">Conflictos</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Schedule by Date --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-list-ul me-2" style="color: #4499BB;"></i>
                        Agenda por Fecha
                    </h5>
                </div>
                <div class="card-body">
                    @if($schedulesByDate->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-calendar-x" style="font-size: 4rem; color: #C8CCC9;"></i>
                            </div>
                            <h5 class="text-muted">No hay horarios programados</h5>
                            <p class="text-muted mb-3">
                                Agrega horarios a los componentes para verlos aquí.
                            </p>
                            <a href="{{ route('components.index', $event) }}" class="btn" style="background-color: #4499BB; color: white;">
                                <i class="bi bi-collection me-1"></i>
                                Ver componentes
                            </a>
                        </div>
                    @else
                        @foreach($schedulesByDate as $date => $schedules)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3 p-3 rounded" style="background-color: #f8f9fa;">
                                    <i class="bi bi-calendar3 me-3" style="font-size: 1.5rem; color: #4499BB;"></i>
                                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                                        {{ \Carbon\Carbon::parse($date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                    </h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr style="background-color: #f8f9fa;">
                                                <th style="width: 120px;" class="border-0">Horario</th>
                                                <th class="border-0">Componente</th>
                                                <th class="border-0">Tipo</th>
                                                <th class="border-0">Ubicación</th>
                                                <th class="border-0">Ponente</th>
                                                <th style="width: 100px;" class="border-0 text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedules as $item)
                                                @php
                                                    $schedule = $item['schedule'];
                                                    $component = $item['component'];
                                                    $hasConflict = $conflicts->contains(function($c) use ($schedule) {
                                                        return $c['schedule']->id === $schedule->id;
                                                    });
                                                @endphp
                                                <tr class="{{ $hasConflict ? 'table-danger' : '' }}">
                                                    <td>
                                                        <span class="fw-bold" style="color: #0C2340;">{{ substr($schedule->start_time, 0, 5) }}</span>
                                                        <span class="text-muted">-</span>
                                                        <span>{{ substr($schedule->end_time, 0, 5) }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('schedules.index', [$event, $component]) }}"
                                                           class="text-decoration-none fw-semibold"
                                                           style="color: #4499BB;">
                                                            {{ $component->name }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge" style="background-color: #0C2340;">
                                                            {{ ucfirst($component->type) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($component->location)
                                                            <i class="bi bi-geo-alt me-1" style="color: #8CC63F;"></i>
                                                            {{ $component->location }}
                                                            <small class="text-muted">({{ ucfirst($component->modality) }})</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($component->speaker && $component->speaker->user)
                                                            <i class="bi bi-person me-1" style="color: #4499BB;"></i>
                                                            {{ $component->speaker->user->name }}
                                                        @else
                                                            <span class="text-muted">Sin asignar</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($hasConflict)
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-exclamation-triangle"></i> Conflicto
                                                            </span>
                                                        @else
                                                            <span class="badge" style="background-color: #8CC63F;">
                                                                <i class="bi bi-check"></i> OK
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
