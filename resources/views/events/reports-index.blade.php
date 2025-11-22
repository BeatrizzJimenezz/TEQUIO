@extends('layouts.app')

@section('header', 'Reportes de Eventos')

@section('content')
<div class="container-fluid">
    {{-- Encabezado --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-bar-chart-fill me-2"></i>Reportes de Mis Eventos
            </h2>
            <p class="text-muted">Selecciona un evento para ver sus estadísticas detalladas</p>
        </div>
    </div>

    {{-- Lista de eventos con estadísticas --}}
    @forelse($events as $event)
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    {{-- Información del evento --}}
                    <div class="col-lg-5 mb-3 mb-lg-0">
                        <h5 class="fw-bold mb-2" style="color: #0C2340;">{{ $event->name }}</h5>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar-event me-1" style="color: #4499BB;"></i>
                            {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                        </p>
                        <div class="mb-2">
                            @php
                                $statusLabels = ['planning' => 'Planificación', 'active' => 'Activo', 'finished' => 'Finalizado'];
                                $statusColors = ['planning' => 'secondary', 'active' => 'success', 'finished' => 'dark'];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$event->status] ?? 'secondary' }}">
                                {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                            </span>
                            @if($event->visibility === 'public')
                                <span class="badge" style="background-color: #8CC63F;">Público</span>
                            @else
                                <span class="badge bg-secondary">Privado</span>
                            @endif
                        </div>
                        @if($event->tags->count() > 0)
                            <div>
                                @foreach($event->tags->take(3) as $tag)
                                    <span class="badge bg-light text-dark border">{{ $tag->name }}</span>
                                @endforeach
                                @if($event->tags->count() > 3)
                                    <span class="badge bg-light text-muted">+{{ $event->tags->count() - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Estadísticas rápidas --}}
                    <div class="col-lg-5 mb-3 mb-lg-0">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-2 rounded" style="background-color: rgba(140, 198, 63, 0.1);">
                                    <h4 class="mb-0 fw-bold" style="color: #8CC63F;">{{ $event->stats['totalRegistrations'] }}</h4>
                                    <small class="text-muted">Inscripciones</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded" style="background-color: rgba(12, 35, 64, 0.1);">
                                    <h4 class="mb-0 fw-bold" style="color: #0C2340;">{{ $event->stats['totalComponents'] }}</h4>
                                    <small class="text-muted">Componentes</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded" style="background-color: rgba(68, 153, 187, 0.1);">
                                    <div class="d-flex justify-content-center gap-1">
                                        @if($event->stats['talks'] > 0)
                                            <span class="badge" style="background-color: #4499BB;" title="Charlas">{{ $event->stats['talks'] }}C</span>
                                        @endif
                                        @if($event->stats['workshops'] > 0)
                                            <span class="badge" style="background-color: #F7941D;" title="Talleres">{{ $event->stats['workshops'] }}T</span>
                                        @endif
                                        @if($event->stats['activities'] > 0)
                                            <span class="badge bg-secondary" title="Actividades">{{ $event->stats['activities'] }}A</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">Por tipo</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botón de acción --}}
                    <div class="col-lg-2 text-center text-lg-end">
                        <a href="{{ route('events.reports', $event) }}" class="btn" style="background-color: #F7941D; color: white;">
                            <i class="bi bi-bar-chart me-1"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-2" style="color: #0C2340;">No tienes eventos</h5>
                <p class="text-muted mb-3">Crea tu primer evento para comenzar a ver reportes</p>
                <a href="{{ route('events.create') }}" class="btn" style="background-color: #8CC63F; color: white;">
                    <i class="bi bi-plus-circle me-2"></i>Crear Evento
                </a>
            </div>
        </div>
    @endforelse
</div>
@endsection
