@extends('layouts.app')

@section('header', 'Reportes Generales')

@section('content')
<div class="container-fluid">
    {{-- Encabezado --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-bar-chart-fill me-2"></i>Reportes Generales
            </h2>
            <p class="text-muted">Panel de estadísticas y métricas del sistema</p>
        </div>
        <div class="col-auto">
            <span class="badge bg-secondary fs-6">
                <i class="bi bi-calendar me-1"></i>{{ now()->format('d/m/Y') }}
            </span>
        </div>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="row mb-4">
        {{-- Usuarios --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Usuarios</h6>
                            <h2 class="mb-0 fw-bold" style="color: #0C2340;">{{ number_format($stats['users']['total']) }}</h2>
                            <small class="text-muted">
                                <i class="bi bi-calendar-month me-1"></i>{{ $stats['users']['thisMonth'] }} este mes
                            </small>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(12, 35, 64, 0.1);">
                            <i class="bi bi-people-fill fs-3" style="color: #0C2340;"></i>
                        </div>
                    </div>
                    @if($stats['users']['growth'] != 0)
                        <div class="mt-2">
                            <span class="badge {{ $stats['users']['growth'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                <i class="bi bi-{{ $stats['users']['growth'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ abs($stats['users']['growth']) }}%
                            </span>
                            <small class="text-muted ms-1">vs mes anterior</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Eventos --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Eventos</h6>
                            <h2 class="mb-0 fw-bold" style="color: #4499BB;">{{ number_format($stats['events']['total']) }}</h2>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>{{ $stats['events']['upcoming'] }} próximos
                            </small>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(68, 153, 187, 0.1);">
                            <i class="bi bi-calendar-event-fill fs-3" style="color: #4499BB;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inscripciones --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Inscripciones</h6>
                            <h2 class="mb-0 fw-bold" style="color: #8CC63F;">{{ number_format($stats['registrations']['total']) }}</h2>
                            <small class="text-muted">
                                <i class="bi bi-calendar-month me-1"></i>{{ $stats['registrations']['thisMonth'] }} este mes
                            </small>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(140, 198, 63, 0.1);">
                            <i class="bi bi-person-check-fill fs-3" style="color: #8CC63F;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Componentes --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Componentes</h6>
                            <h2 class="mb-0 fw-bold" style="color: #F7941D;">{{ number_format($stats['components']['total']) }}</h2>
                            <small class="text-muted">
                                <i class="bi bi-puzzle me-1"></i>Charlas, talleres, actividades
                            </small>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(247, 148, 29, 0.1);">
                            <i class="bi bi-collection-fill fs-3" style="color: #F7941D;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row mb-4">
        {{-- Usuarios por mes --}}
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-graph-up me-2"></i>Actividad Mensual {{ now()->year }}
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyActivityChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Distribución de eventos por modalidad --}}
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-pie-chart me-2"></i>Eventos por Modalidad
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="modalityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Componentes por tipo --}}
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-collection me-2"></i>Componentes por Tipo
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="componentTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Usuarios por rol --}}
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-people me-2"></i>Usuarios por Rol
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Administradores</small>
                            <small class="fw-bold">{{ $stats['users']['byRole']['administradores'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $stats['users']['total'] > 0 ? ($stats['users']['byRole']['administradores'] / $stats['users']['total'] * 100) : 0 }}%; background-color: #0C2340;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Organizadores</small>
                            <small class="fw-bold">{{ $stats['users']['byRole']['organizadores'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $stats['users']['total'] > 0 ? ($stats['users']['byRole']['organizadores'] / $stats['users']['total'] * 100) : 0 }}%; background-color: #4499BB;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Participantes</small>
                            <small class="fw-bold">{{ $stats['users']['byRole']['participantes'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $stats['users']['total'] > 0 ? ($stats['users']['byRole']['participantes'] / $stats['users']['total'] * 100) : 0 }}%; background-color: #8CC63F;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estado de eventos --}}
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-flag me-2"></i>Estado de Eventos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><span class="badge bg-secondary">Borrador</span></small>
                            <small class="fw-bold">{{ $stats['events']['byStatus']['draft'] }}</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><span class="badge" style="background-color: #4499BB;">Publicado</span></small>
                            <small class="fw-bold">{{ $stats['events']['byStatus']['published'] }}</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><span class="badge bg-success">Activo</span></small>
                            <small class="fw-bold">{{ $stats['events']['byStatus']['active'] }}</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><span class="badge bg-dark">Finalizado</span></small>
                            <small class="fw-bold">{{ $stats['events']['byStatus']['finished'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tablas de datos --}}
    <div class="row">
        {{-- Top eventos por inscripciones --}}
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-trophy me-2"></i>Top Eventos por Inscripciones
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($topEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">#</th>
                                        <th class="border-0">Evento</th>
                                        <th class="border-0 text-end">Inscripciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topEvents as $index => $event)
                                        <tr>
                                            <td>
                                                @if($index == 0)
                                                    <i class="bi bi-trophy-fill text-warning"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ Str::limit($event->name, 30) }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge" style="background-color: #8CC63F;">
                                                    {{ $event->registrations_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3"></i>
                            <p class="mb-0 mt-2">No hay eventos con inscripciones</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Usuarios recientes --}}
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-person-plus me-2"></i>Usuarios Recientes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Usuario</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td>
                                                <strong>{{ $user->name }}</strong>
                                            </td>
                                            <td class="text-muted">
                                                {{ Str::limit($user->email, 25) }}
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3"></i>
                            <p class="mb-0 mt-2">No hay usuarios recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas adicionales --}}
    <div class="row mt-3">
        {{-- Estado de propuestas --}}
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-file-earmark-text me-2"></i>Estado de Propuestas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-clock text-warning me-2"></i>Pendientes</span>
                        <span class="badge bg-warning text-dark">{{ $stats['components']['byProposalStatus']['proposed'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-check-circle text-success me-2"></i>Aprobadas</span>
                        <span class="badge bg-success">{{ $stats['components']['byProposalStatus']['approved'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-x-circle text-danger me-2"></i>Rechazadas</span>
                        <span class="badge bg-danger">{{ $stats['components']['byProposalStatus']['rejected'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-megaphone text-info me-2"></i>Ofertas abiertas</span>
                        <span class="badge bg-info">{{ $stats['components']['byProposalStatus']['offer_open'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estado de postulaciones --}}
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-hand-thumbs-up me-2"></i>Postulaciones a Ofertas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="fw-bold" style="color: #4499BB;">{{ $stats['applications']['total'] }}</h3>
                        <small class="text-muted">Total postulaciones</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-hourglass-split text-warning me-2"></i>Pendientes</span>
                        <span class="badge bg-warning text-dark">{{ $stats['applications']['byStatus']['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-check2-circle text-success me-2"></i>Aceptadas</span>
                        <span class="badge bg-success">{{ $stats['applications']['byStatus']['accepted'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-x-circle text-danger me-2"></i>Rechazadas</span>
                        <span class="badge bg-danger">{{ $stats['applications']['byStatus']['rejected'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visibilidad de eventos --}}
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-eye me-2"></i>Visibilidad de Eventos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around text-center">
                        <div>
                            <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 60px; background-color: rgba(140, 198, 63, 0.1);">
                                <i class="bi bi-globe fs-4" style="color: #8CC63F;"></i>
                            </div>
                            <h4 class="fw-bold mb-0" style="color: #8CC63F;">{{ $stats['events']['byVisibility']['public'] }}</h4>
                            <small class="text-muted">Públicos</small>
                        </div>
                        <div>
                            <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 60px; background-color: rgba(108, 117, 125, 0.1);">
                                <i class="bi bi-lock fs-4 text-secondary"></i>
                            </div>
                            <h4 class="fw-bold mb-0 text-secondary">{{ $stats['events']['byVisibility']['private'] }}</h4>
                            <small class="text-muted">Privados</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Datos para gráficos
const monthlyData = @json($charts['usersByMonth']);
const eventsMonthlyData = @json($charts['eventsByMonth']);
const registrationsMonthlyData = @json($charts['registrationsByMonth']);
const modalityData = @json($charts['eventsByModality']);
const componentTypeData = @json($charts['componentsByType']);

// Gráfico de actividad mensual
const monthlyCtx = document.getElementById('monthlyActivityChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [
            {
                label: 'Usuarios',
                data: monthlyData.map(d => d.count),
                borderColor: '#0C2340',
                backgroundColor: 'rgba(12, 35, 64, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Eventos',
                data: eventsMonthlyData.map(d => d.count),
                borderColor: '#4499BB',
                backgroundColor: 'rgba(68, 153, 187, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Inscripciones',
                data: registrationsMonthlyData.map(d => d.count),
                borderColor: '#8CC63F',
                backgroundColor: 'rgba(140, 198, 63, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Gráfico de modalidad
const modalityCtx = document.getElementById('modalityChart').getContext('2d');
new Chart(modalityCtx, {
    type: 'doughnut',
    data: {
        labels: modalityData.map(d => d.label),
        datasets: [{
            data: modalityData.map(d => d.count),
            backgroundColor: ['#4499BB', '#0C2340', '#8CC63F'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de tipos de componentes
const componentCtx = document.getElementById('componentTypeChart').getContext('2d');
new Chart(componentCtx, {
    type: 'doughnut',
    data: {
        labels: componentTypeData.map(d => d.label),
        datasets: [{
            data: componentTypeData.map(d => d.count),
            backgroundColor: ['#F7941D', '#8CC63F', '#4499BB'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
