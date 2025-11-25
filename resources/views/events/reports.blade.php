@extends('layouts.app')

@section('header', 'Reportes del Evento')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Encabezado --}}
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-brand-deep">
                <i class="bi bi-bar-chart-fill me-2 text-brand-accent"></i>Reportes: {{ $event->name }}
            </h2>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar-event me-1 text-brand-main"></i>
                {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
            </p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('events.reports.index') }}" class="btn btn-white shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            <div class="dropdown">
                <button class="btn btn-brand-primary shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Exportar
                </button>
                <ul class="dropdown-menu shadow-sm border-0">
                    <li>
                        <a class="dropdown-item" href="{{ route('events.reports.export', ['event' => $event, 'format' => 'pdf']) }}">
                            <i class="bi bi-file-pdf me-2 text-danger"></i>PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('events.reports.export', ['event' => $event, 'format' => 'excel']) }}">
                            <i class="bi bi-file-excel me-2 text-success"></i>Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="row mb-4 g-3">
        {{-- Total Componentes --}}
        <div class="col-xl-3 col-md-6">
            <div class="card-admin h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Componentes</h6>
                            <h2 class="mb-0 fw-bold text-brand-deep">{{ $stats['totalComponents'] }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-brand-light text-brand-deep">
                            <i class="bi bi-collection-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Inscripciones --}}
        <div class="col-xl-3 col-md-6">
            <div class="card-admin h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Inscripciones</h6>
                            <h2 class="mb-0 fw-bold text-brand-accent">{{ $stats['totalRegistrations'] }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-brand-light text-brand-accent">
                            <i class="bi bi-person-check-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charlas --}}
        <div class="col-xl-3 col-md-6">
            <div class="card-admin h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Charlas</h6>
                            <h2 class="mb-0 fw-bold text-brand-main">{{ $stats['componentsByType']['talk'] }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-brand-light text-brand-main">
                            <i class="bi bi-mic-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Talleres --}}
        <div class="col-xl-3 col-md-6">
            <div class="card-admin h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Talleres</h6>
                            <h2 class="mb-0 fw-bold" style="color: #F7941D;">{{ $stats['componentsByType']['workshop'] }}</h2>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(247, 148, 29, 0.1); color: #F7941D;">
                            <i class="bi bi-tools fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row mb-4 g-3">
        {{-- Inscripciones por componente --}}
        <div class="col-lg-8">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-bar-chart me-2 text-brand-main"></i>Inscripciones por Componente
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($registrationsByComponent->count() > 0)
                        <canvas id="registrationsChart" height="100"></canvas>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 opacity-50"></i>
                            <p class="mb-0 mt-2">No hay datos de inscripciones</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Estado de componentes --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-pie-chart me-2 text-brand-accent"></i>Estado de Componentes
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted"><i class="bi bi-check-circle text-success me-1"></i>Aprobados</small>
                            <small class="fw-bold text-brand-deep">{{ $stats['componentsByStatus']['approved'] }}</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['totalComponents'] > 0 ? ($stats['componentsByStatus']['approved'] / $stats['totalComponents'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted"><i class="bi bi-clock text-warning me-1"></i>Pendientes</small>
                            <small class="fw-bold text-brand-deep">{{ $stats['componentsByStatus']['proposed'] }}</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['totalComponents'] > 0 ? ($stats['componentsByStatus']['proposed'] / $stats['totalComponents'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted"><i class="bi bi-x-circle text-danger me-1"></i>Rechazados</small>
                            <small class="fw-bold text-brand-deep">{{ $stats['componentsByStatus']['rejected'] }}</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['totalComponents'] > 0 ? ($stats['componentsByStatus']['rejected'] / $stats['totalComponents'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted"><i class="bi bi-megaphone text-info me-1"></i>Ofertas abiertas</small>
                            <small class="fw-bold text-brand-deep">{{ $stats['componentsByStatus']['offer_open'] }}</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $stats['totalComponents'] > 0 ? ($stats['componentsByStatus']['offer_open'] / $stats['totalComponents'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Capacidad y Top componentes --}}
    <div class="row mb-4 g-3">
        {{-- Capacidad vs Inscripciones --}}
        <div class="col-lg-6">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-speedometer2 me-2 text-brand-main"></i>Ocupación por Componente
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($capacityData->count() > 0)
                        @foreach($capacityData as $data)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-semibold text-brand-deep">{{ $data['name'] }}</small>
                                    <small class="text-muted">{{ $data['registered'] }}/{{ $data['capacity'] }} ({{ $data['percentage'] }}%)</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $color = $data['percentage'] >= 90 ? '#dc3545' : ($data['percentage'] >= 70 ? '#ffc107' : '#8CC63F');
                                    @endphp
                                    <div class="progress-bar" style="width: {{ $data['percentage'] }}%; background-color: {{ $color }};"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 opacity-50"></i>
                            <p class="mb-0 mt-2">No hay componentes con capacidad definida</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Top componentes --}}
        <div class="col-lg-6">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-trophy me-2 text-brand-accent"></i>Top Componentes por Inscripciones
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($topComponents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 ps-4 py-3 text-brand-deep">#</th>
                                        <th class="border-0 py-3 text-brand-deep">Componente</th>
                                        <th class="border-0 py-3 text-brand-deep">Tipo</th>
                                        <th class="border-0 pe-4 py-3 text-end text-brand-deep">Inscripciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topComponents as $index => $component)
                                        <tr>
                                            <td class="ps-4">
                                                @if($index == 0)
                                                    <i class="bi bi-trophy-fill text-warning"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-brand-deep">{{ \Str::limit($component->name, 25) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $typeLabels = ['talk' => 'Charla', 'workshop' => 'Taller', 'activity' => 'Actividad'];
                                                @endphp
                                                <span class="badge bg-secondary">{{ $typeLabels[$component->type] ?? $component->type }}</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-brand-accent">
                                                    {{ $component->registrations_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 opacity-50"></i>
                            <p class="mb-0 mt-2">No hay componentes con inscripciones</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de inscritos --}}
    <div class="row">
        <div class="col-12">
            <div class="card-admin">
                <div class="card-header-admin">
                    <h6 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-people me-2 text-brand-main"></i>Lista de Inscritos ({{ $allRegistrations->count() }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($allRegistrations->count() > 0)
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="border-0 ps-4 py-3 text-brand-deep">Participante</th>
                                        <th class="border-0 py-3 text-brand-deep">Email</th>
                                        <th class="border-0 py-3 text-brand-deep">Componente</th>
                                        <th class="border-0 py-3 text-brand-deep">Fecha de Inscripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allRegistrations as $registration)
                                        <tr>
                                            <td class="ps-4">
                                                <strong class="text-brand-deep">{{ $registration->user->name }}</strong>
                                            </td>
                                            <td class="text-muted">
                                                {{ $registration->user->email }}
                                            </td>
                                            <td>
                                                {{ \Str::limit($registration->component->name, 30) }}
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $registration->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-3 opacity-50"></i>
                            <p class="mb-0 mt-2">No hay inscripciones registradas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Datos para gráfico
const registrationsData = @json($registrationsByComponent);

@if($registrationsByComponent->count() > 0)
// Gráfico de inscripciones por componente
const ctx = document.getElementById('registrationsChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: registrationsData.map(d => d.name),
        datasets: [{
            label: 'Inscripciones',
            data: registrationsData.map(d => d.count),
            backgroundColor: '#8CC63F',
            borderColor: '#7ab534',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                },
                grid: {
                    color: 'rgba(0,0,0,0.05)'
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
@endif
</script>
@endpush
