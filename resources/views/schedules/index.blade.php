@extends('layouts.app')

@section('header', 'Gestionar Horarios')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Header --}}
            <div class="card-admin mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h3 class="mb-1 fw-bold text-brand-deep">
                                <i class="bi bi-clock-history me-2 text-brand-accent"></i>
                                Horarios: {{ $component->name }}
                            </h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3 me-1 text-brand-main"></i>
                                Evento: {{ $event->name }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('components.index', $event) }}" class="btn btn-white shadow-sm">
                                <i class="bi bi-arrow-left me-1"></i> Componentes
                            </a>
                            <a href="{{ route('schedules.create', [$event, $component]) }}" class="btn btn-brand-primary shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i> Agregar Horario
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
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

            {{-- Component Info --}}
            <div class="card-admin mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-brand-light p-3 me-3 text-brand-main">
                                    <i class="bi bi-tag fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">Tipo</small>
                                    <strong class="text-brand-deep">{{ ucfirst($component->type) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-brand-light p-3 me-3 text-brand-accent">
                                    <i class="bi bi-display fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">Modalidad</small>
                                    <strong class="text-brand-deep">{{ ucfirst($component->modality) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-brand-light p-3 me-3 text-brand-main">
                                    <i class="bi bi-geo-alt fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">Ubicación</small>
                                    <strong class="text-brand-deep">{{ $component->location ?? 'No especificada' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-brand-light p-3 me-3 text-brand-accent">
                                    <i class="bi bi-person fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">Ponente</small>
                                    <strong class="text-brand-deep">{{ $component->speaker->user->name ?? 'Sin asignar' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Schedules List --}}
            <div class="card-admin mb-4">
                <div class="card-header-admin">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-calendar-week me-2 text-brand-main"></i>
                        Horarios Programados
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($schedules->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                            <h5 class="text-brand-deep fw-bold">No hay horarios programados</h5>
                            <p class="text-muted mb-4">Agrega el primer horario para este componente</p>
                            <a href="{{ route('schedules.create', [$event, $component]) }}" class="btn btn-brand-primary shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i> Agregar Primer Horario
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 ps-4 py-3 text-brand-deep">
                                            <i class="bi bi-calendar3 me-1 text-brand-main"></i>
                                            Fecha
                                        </th>
                                        <th class="border-0 py-3 text-brand-deep">
                                            <i class="bi bi-clock me-1 text-brand-accent"></i>
                                            Hora Inicio
                                        </th>
                                        <th class="border-0 py-3 text-brand-deep">
                                            <i class="bi bi-clock-fill me-1 text-brand-accent"></i>
                                            Hora Fin
                                        </th>
                                        <th class="border-0 py-3 text-brand-deep">
                                            <i class="bi bi-hourglass-split me-1 text-brand-main"></i>
                                            Duración
                                        </th>
                                        <th class="border-0 pe-4 py-3 text-end text-brand-deep">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="fw-semibold text-brand-deep">{{ $schedule->date->format('d/m/Y') }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-white text-dark border shadow-sm">
                                                    {{ substr($schedule->start_time, 0, 5) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-white text-dark border shadow-sm">
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
                                                <span class="text-brand-main fw-bold">
                                                    {{ $hours > 0 ? $hours . 'h ' : '' }}{{ $minutes > 0 ? $minutes . 'min' : '' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('schedules.edit', [$event, $component, $schedule]) }}"
                                                   class="btn btn-sm btn-white text-brand-main shadow-sm me-1"
                                                   title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('schedules.destroy', [$event, $component, $schedule]) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este horario?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-white text-danger shadow-sm" title="Eliminar">
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
