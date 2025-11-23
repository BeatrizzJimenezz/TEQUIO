@extends('layouts.app')

@section('header', 'Reportes Generales')

@section('content')
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-brand-deep mb-1">
                <i class="bi bi-bar-chart-fill me-2"></i>Reportes Generales
            </h2>
            <p class="text-muted mb-0">Panel de estadísticas y métricas del sistema</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-brand-main dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-2"></i>Exportar Reporte
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('admin.reports.export', ['format' => 'pdf', 'section' => 'all']) }}">
                            <i class="bi bi-file-pdf text-danger me-2"></i>Exportar PDF Completo
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('admin.reports.export', ['format' => 'excel', 'section' => 'all']) }}">
                            <i class="bi bi-file-excel text-success me-2"></i>Exportar Excel Completo
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bg-white px-3 py-2 rounded-pill shadow-sm border">
                <i class="bi bi-calendar-event text-brand-main me-2"></i>
                <span class="fw-bold text-dark">{{ now()->format('d M, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="row g-4 mb-4">
        {{-- Usuarios --}}
        <div class="col-xl-3 col-md-6">
            <div class="card-admin h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">Total Usuarios</p>
                            <h2 class="mb-0 fw-bold text-brand-deep">{{ number_format($stats['users']['total']) }}</h2>
                        </div>
                        <div class="rounded-circle bg-brand-deep p-3 text-white shadow-sm">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($stats['users']['growth'] != 0)
                            <span class="badge {{ $stats['users']['growth'] > 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $stats['users']['growth'] > 0 ? 'text-success' : 'text-danger' }} me-2">
                                <i class="bi bi-{{ $stats['users']['growth'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ abs($stats['users']['growth']) }}%
                            </span>
                        @endif
                        <small class="text-muted">{{ $stats['users']['thisMonth'] }} nuevos este mes</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Eventos --}}
        <div class="col-xl-3 col-md-6">
            <div class="card-admin h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">Total Eventos</p>
                            <h2 class="mb-0 fw-bold text-brand-main">{{ number_format($stats['events']['total']) }}</h2>
                        </div>
                        <div class="rounded-circle bg-brand-main p-3 text-white shadow-sm">
                            <i class="bi bi-calendar-event-fill fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-brand-main bg-opacity-10 text-brand-main me-2">
                            {{ $stats['events']['upcoming'] }}
                        </span>
                        <small class="text-muted">Próximos eventos</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inscripciones --}}
        <div class="col-xl-3 col-md-6">
            <div class="card-admin h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">Inscripciones</p>
                            <h2 class="mb-0 fw-bold text-brand-accent">{{ number_format($stats['registrations']['total']) }}</h2>
                        </div>
                        <div class="rounded-circle bg-brand-accent p-3 text-white shadow-sm">
                            <i class="bi bi-person-check-fill fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-brand-accent bg-opacity-10 text-brand-accent me-2">
                            +{{ $stats['registrations']['thisMonth'] }}
                        </span>
                        <small class="text-muted">Registros este mes</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Componentes --}}
        <div class="col-xl-3 col-md-6">
            <div class="card-admin h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">Componentes</p>
                            <h2 class="mb-0 fw-bold text-warning">{{ number_format($stats['components']['total']) }}</h2>
                        </div>
                        <div class="rounded-circle bg-warning p-3 text-white shadow-sm">
                            <i class="bi bi-collection-fill fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <small class="text-muted">Charlas, talleres y actividades</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row g-4 mb-4">
        {{-- Usuarios por mes --}}
        <div class="col-lg-8">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-graph-up me-2"></i>Actividad Mensual {{ now()->year }}
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                            <li><a class="dropdown-item small" href="{{ route('admin.reports.export', ['format' => 'pdf', 'section' => 'users']) }}"><i class="bi bi-file-pdf text-danger me-2"></i>Exportar Usuarios PDF</a></li>
                            <li><a class="dropdown-item small" href="{{ route('admin.reports.export', ['format' => 'excel', 'section' => 'users']) }}"><i class="bi bi-file-excel text-success me-2"></i>Exportar Usuarios Excel</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="monthlyActivityChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Distribución de eventos por modalidad --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-pie-chart me-2"></i>Eventos por Modalidad
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                            <li><a class="dropdown-item small" href="{{ route('admin.reports.export', ['format' => 'pdf', 'section' => 'events']) }}"><i class="bi bi-file-pdf text-danger me-2"></i>Exportar Eventos PDF</a></li>
                            <li><a class="dropdown-item small" href="{{ route('admin.reports.export', ['format' => 'excel', 'section' => 'events']) }}"><i class="bi bi-file-excel text-success me-2"></i>Exportar Eventos Excel</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <canvas id="modalityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Componentes por tipo --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-collection me-2"></i>Componentes por Tipo
                    </h6>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <canvas id="componentTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Usuarios por rol --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-people me-2"></i>Usuarios por Rol
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="fw-bold text-brand-deep">Administradores</small>
                            <small class="fw-bold">{{ $stats['users']['byRole']['administradores'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-brand-deep" style="width: {{ $stats['users']['total'] > 0 ? ($stats['users']['byRole']['administradores'] / $stats['users']['total'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="fw-bold text-brand-main">Organizadores</small>
                            <small class="fw-bold">{{ $stats['users']['byRole']['organizadores'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-brand-main" style="width: {{ $stats['users']['total'] > 0 ? ($stats['users']['byRole']['organizadores'] / $stats['users']['total'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="fw-bold text-brand-accent">Participantes</small>
                            <small class="fw-bold">{{ $stats['users']['byRole']['participantes'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-brand-accent" style="width: {{ $stats['users']['total'] > 0 ? ($stats['users']['byRole']['participantes'] / $stats['users']['total'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estado de eventos --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-flag me-2"></i>Estado de Eventos
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded hover-bg-light">
                        <span class="badge bg-secondary border border-secondary px-3 text-white">Borrador</span>
                        <span class="fw-bold text-dark">{{ $stats['events']['byStatus']['draft'] }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded hover-bg-light">
                        <span class="badge bg-brand-main border border-brand-main px-3 text-white">Publicado</span>
                        <span class="fw-bold text-dark">{{ $stats['events']['byStatus']['published'] }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded hover-bg-light">
                        <span class="badge bg-success border border-success px-3 text-white">Activo</span>
                        <span class="fw-bold text-dark">{{ $stats['events']['byStatus']['active'] }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2 rounded hover-bg-light">
                        <span class="badge bg-dark border border-dark px-3 text-white">Finalizado</span>
                        <span class="fw-bold text-dark">{{ $stats['events']['byStatus']['finished'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tablas de datos --}}
    <div class="row g-4">
        {{-- Top eventos por inscripciones --}}
        <div class="col-lg-6">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-trophy me-2"></i>Top Eventos por Inscripciones
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($topEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-admin table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 border-0">#</th>
                                        <th class="border-0">Evento</th>
                                        <th class="border-0 text-end pe-4">Inscripciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topEvents as $index => $event)
                                        <tr>
                                            <td class="ps-4">
                                                @if($index == 0)
                                                    <i class="bi bi-trophy-fill text-warning fs-5"></i>
                                                @else
                                                    <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold text-brand-deep">{{ Str::limit($event->name, 40) }}</div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-brand-accent rounded-pill px-3">
                                                    {{ $event->registrations_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-3 opacity-50"></i>
                            <p class="mb-0 mt-2">No hay eventos con inscripciones</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Usuarios recientes --}}
        <div class="col-lg-6">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-person-plus me-2"></i>Usuarios Recientes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-admin table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 border-0">Usuario</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0 text-end pe-4">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-light text-brand-main d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>
                                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-muted small">
                                                {{ Str::limit($user->email, 25) }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <small class="text-muted">{{ $user->created_at->format('d M, Y') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-3 opacity-50"></i>
                            <p class="mb-0 mt-2">No hay usuarios recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas adicionales --}}
    <div class="row g-4 mt-2">
        {{-- Estado de propuestas --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-file-earmark-text me-2"></i>Estado de Propuestas
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="bi bi-clock text-warning me-2"></i>Pendientes</span>
                        <span class="badge bg-warning text-dark rounded-pill px-3">{{ $stats['components']['byProposalStatus']['proposed'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="bi bi-check-circle text-success me-2"></i>Aprobadas</span>
                        <span class="badge bg-success rounded-pill px-3">{{ $stats['components']['byProposalStatus']['approved'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted"><i class="bi bi-x-circle text-danger me-2"></i>Rechazadas</span>
                        <span class="badge bg-danger rounded-pill px-3">{{ $stats['components']['byProposalStatus']['rejected'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="bi bi-megaphone text-info me-2"></i>Ofertas abiertas</span>
                        <span class="badge bg-info rounded-pill px-3">{{ $stats['components']['byProposalStatus']['offer_open'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estado de postulaciones --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-hand-thumbs-up me-2"></i>Postulaciones a Ofertas
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-brand-main display-4 mb-0">{{ $stats['applications']['total'] }}</h2>
                        <small class="text-muted text-uppercase ls-1">Total Postulaciones</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted"><i class="bi bi-hourglass-split text-warning me-2"></i>Pendientes</span>
                        <span class="badge bg-warning text-dark rounded-pill px-3">{{ $stats['applications']['byStatus']['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted"><i class="bi bi-check2-circle text-success me-2"></i>Aceptadas</span>
                        <span class="badge bg-success rounded-pill px-3">{{ $stats['applications']['byStatus']['accepted'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="bi bi-x-circle text-danger me-2"></i>Rechazadas</span>
                        <span class="badge bg-danger rounded-pill px-3">{{ $stats['applications']['byStatus']['rejected'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visibilidad de eventos --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin bg-white">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-eye me-2"></i>Visibilidad de Eventos
                    </h6>
                </div>
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="row w-100 text-center g-0">
                        <div class="col-6 border-end">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-brand-accent bg-opacity-10"
                                 style="width: 70px; height: 70px;">
                                <i class="bi bi-globe fs-2 text-brand-accent"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-brand-accent">{{ $stats['events']['byVisibility']['public'] }}</h3>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                                <i class="bi bi-globe me-1"></i>Públicos
                            </small>
                        </div>
                        <div class="col-6">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-secondary bg-opacity-10"
                                 style="width: 70px; height: 70px;">
                                <i class="bi bi-lock fs-2 text-secondary"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-secondary">{{ $stats['events']['byVisibility']['private'] }}</h3>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                                <i class="bi bi-lock me-1"></i>Privados
                            </small>
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

// Configuración global de fuentes y colores
Chart.defaults.font.family = "'Figtree', sans-serif";
Chart.defaults.color = '#64748b';

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
                backgroundColor: 'rgba(12, 35, 64, 0.05)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#0C2340',
                pointRadius: 3
            },
            {
                label: 'Eventos',
                data: eventsMonthlyData.map(d => d.count),
                borderColor: '#4499BB',
                backgroundColor: 'rgba(68, 153, 187, 0.05)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#4499BB',
                pointRadius: 3
            },
            {
                label: 'Inscripciones',
                data: registrationsMonthlyData.map(d => d.count),
                borderColor: '#8CC63F',
                backgroundColor: 'rgba(140, 198, 63, 0.05)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#8CC63F',
                pointRadius: 3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    boxWidth: 8
                }
            },
            tooltip: {
                backgroundColor: '#0C2340',
                padding: 10,
                cornerRadius: 8,
                displayColors: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    borderDash: [4, 4],
                    drawBorder: false
                },
                ticks: {
                    stepSize: 1
                }
            },
            x: {
                grid: {
                    display: false
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
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            }
        },
        cutout: '70%'
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
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            }
        },
        cutout: '70%'
    }
});
</script>
@endpush
