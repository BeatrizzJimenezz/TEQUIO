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
                                <i class="bi bi-clock-history me-2" style="color: #4499BB;"></i>
                                Horarios: {{ $component->name }}
                            </h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar-event me-1"></i>
                                Evento: {{ $event->name }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('components.index', $event) }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Componentes
                            </a>
                            <a href="{{ route('schedules.create', [$event, $component]) }}" class="btn text-white" style="background-color: #8CC63F;">
                                <i class="bi bi-plus-lg"></i> Agregar Horario
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Component Info --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tag me-2" style="color: #4499BB;"></i>
                                <div>
                                    <small class="text-muted d-block">Tipo</small>
                                    <strong>{{ ucfirst($component->type) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-display me-2" style="color: #8CC63F;"></i>
                                <div>
                                    <small class="text-muted d-block">Modalidad</small>
                                    <strong>{{ ucfirst($component->modality) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2" style="color: #4499BB;"></i>
                                <div>
                                    <small class="text-muted d-block">Ubicación</small>
                                    <strong>{{ $component->location ?? 'No especificada' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person me-2" style="color: #8CC63F;"></i>
                                <div>
                                    <small class="text-muted d-block">Ponente</small>
                                    <strong>{{ $component->speaker->user->name ?? 'Sin asignar' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Schedules List --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-calendar-week me-2" style="color: #4499BB;"></i>
                        Horarios Programados
                    </h5>
                </div>
                <div class="card-body">
                    @if($schedules->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-calendar-x" style="font-size: 4rem; color: #C8CCC9;"></i>
                            </div>
                            <h5 class="text-muted">No hay horarios programados</h5>
                            <p class="text-muted">Agrega el primer horario para este componente</p>
                            <a href="{{ route('schedules.create', [$event, $component]) }}" class="btn text-white" style="background-color: #8CC63F;">
                                <i class="bi bi-plus-lg"></i> Agregar Primer Horario
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th class="border-0">
                                            <i class="bi bi-calendar3 me-1" style="color: #4499BB;"></i>
                                            Fecha
                                        </th>
                                        <th class="border-0">
                                            <i class="bi bi-clock me-1" style="color: #8CC63F;"></i>
                                            Hora Inicio
                                        </th>
                                        <th class="border-0">
                                            <i class="bi bi-clock-fill me-1" style="color: #8CC63F;"></i>
                                            Hora Fin
                                        </th>
                                        <th class="border-0">
                                            <i class="bi bi-hourglass-split me-1" style="color: #4499BB;"></i>
                                            Duración
                                        </th>
                                        <th class="border-0 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">{{ $schedule->date->format('d/m/Y') }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ substr($schedule->start_time, 0, 5) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ substr($schedule->end_time, 0, 5) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $start = \Carbon\Carbon::parse($schedule->start_time);
                                                    $end = \Carbon\Carbon::parse($schedule->end_time);
                                                    $duration = $start->diffInMinutes($end);
                                                    $hours = floor($duration / 60);
                                                    $minutes = $duration % 60;
                                                @endphp
                                                <span style="color: #4499BB;">
                                                    {{ $hours > 0 ? $hours . 'h ' : '' }}{{ $minutes > 0 ? $minutes . 'min' : '' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('schedules.edit', [$event, $component, $schedule]) }}"
                                                   class="btn btn-sm btn-outline-primary me-1"
                                                   title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('schedules.destroy', [$event, $component, $schedule]) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este horario?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
