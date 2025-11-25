@extends('layouts.app')

@section('header', 'Reportes de Eventos')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-brand-deep">
                <i class="bi bi-bar-chart-fill me-2 text-brand-accent"></i>Reportes de Mis Eventos
            </h2>
            <p class="text-muted">Selecciona un evento para ver sus estadísticas detalladas</p>
        </div>
    </div>

    {{-- Lista de eventos con estadísticas --}}
    @forelse($events as $event)
        <div class="card-admin mb-3">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    {{-- Información del evento --}}
                    <div class="col-lg-5 mb-3 mb-lg-0">
                        <h5 class="fw-bold mb-2 text-brand-deep">{{ $event->name }}</h5>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar-event me-1 text-brand-main"></i>
                            {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                        </p>
                        <div class="mb-2">
                            @php
                                $statusLabels = ['planning' => 'Planificación', 'active' => 'Activo', 'finished' => 'Finalizado'];
                                $statusClasses = ['planning' => 'bg-secondary', 'active' => 'bg-success', 'finished' => 'bg-dark'];
                            @endphp
                            <span class="badge {{ $statusClasses[$event->status] ?? 'bg-secondary' }} me-1">
                                {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                            </span>
                            @if($event->visibility === 'public')
                                <span class="badge bg-brand-accent">Público</span>
                            @else
                                <span class="badge bg-secondary">Privado</span>
                            @endif
                        </div>
                        @if($event->tags->count() > 0)
                            <div class="mt-2">
                                @foreach($event->tags->take(3) as $tag)
                                    <span class="badge bg-light text-dark border me-1">{{ $tag->name }}</span>
                                @endforeach
                                @if($event->tags->count() > 3)
                                    <span class="badge bg-light text-muted">+{{ $event->tags->count() - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Estadísticas rápidas --}}
                    <div class="col-lg-5 mb-3 mb-lg-0">
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <div class="p-3 rounded bg-brand-light h-100">
                                    <h4 class="mb-0 fw-bold text-brand-accent">{{ $event->stats['totalRegistrations'] }}</h4>
                                    <small class="text-muted d-block mt-1">Inscripciones</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded bg-brand-light h-100">
                                    <h4 class="mb-0 fw-bold text-brand-deep">{{ $event->stats['totalComponents'] }}</h4>
                                    <small class="text-muted d-block mt-1">Componentes</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded bg-brand-light h-100">
                                    <div class="d-flex justify-content-center gap-1 mb-1">
                                        @if($event->stats['talks'] > 0)
                                            <span class="badge bg-brand-main" title="Charlas">{{ $event->stats['talks'] }}C</span>
                                        @endif
                                        @if($event->stats['workshops'] > 0)
                                            <span class="badge bg-brand-accent" title="Talleres">{{ $event->stats['workshops'] }}T</span>
                                        @endif
                                        @if($event->stats['activities'] > 0)
                                            <span class="badge bg-secondary" title="Actividades">{{ $event->stats['activities'] }}A</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block">Por tipo</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botón de acción --}}
                    <div class="col-lg-2 text-center text-lg-end">
                        <a href="{{ route('events.reports', $event) }}" class="btn btn-brand-primary w-100 shadow-sm">
                            <i class="bi bi-bar-chart me-1"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card-admin">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5 class="mt-3 mb-2 text-brand-deep fw-bold">No tienes eventos</h5>
                <p class="text-muted mb-4">Crea tu primer evento para comenzar a ver reportes</p>
                <a href="{{ route('events.create') }}" class="btn btn-brand-primary shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>Crear Evento
                </a>
            </div>
        </div>
    @endforelse
</div>
@endsection
