@extends('layouts.app')

@section('header', 'Reportes del Evento')

@section('content')
<div class="container-fluid">
    {{-- Encabezado --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-bar-chart-fill me-2"></i>Reportes: {{ $event->name }}
            </h2>
            <p class="text-muted">
                <i class="bi bi-calendar-event me-1"></i>
                {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('events.reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver a Reportes
            </a>
        </div>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="row mb-4">
        {{-- Total Componentes --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Componentes</h6>
                            <h2 class="mb-0 fw-bold" style="color: #0C2340;">{{ $stats['totalComponents'] }}</h2>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(12, 35, 64, 0.1);">
                            <i class="bi bi-collection-fill fs-3" style="color: #0C2340;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Inscripciones --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Inscripciones</h6>
                            <h2 class="mb-0 fw-bold" style="color: #8CC63F;">{{ $stats['totalRegistrations'] }}</h2>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(140, 198, 63, 0.1);">
                            <i class="bi bi-person-check-fill fs-3" style="color: #8CC63F;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charlas --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Charlas</h6>
                            <h2 class="mb-0 fw-bold" style="color: #4499BB;">{{ $stats['componentsByType']['talk'] }}</h2>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(68, 153, 187, 0.1);">
                            <i class="bi bi-mic-fill fs-3" style="color: #4499BB;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Talleres --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Talleres</h6>
                            <h2 class="mb-0 fw-bold" style="color: #F7941D;">{{ $stats['componentsByType']['workshop'] }}</h2>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(247, 148, 29, 0.1);">
                            <i class="bi bi-tools fs-3" style="color: #F7941D;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row mb-4">
        {{-- Inscripciones por componente --}}
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-bar-chart me-2"></i>Inscripciones por Componente
                    </h6>
                </div>
                <div class="card-body">
                    @if($registrationsByComponent->count() > 0)
                        <canvas id="registrationsChart" height="100"></canvas>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3"></i>
                            <p class="mb-0 mt-2">No hay datos de inscripciones</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Estado de componentes --}}
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-pie-chart me-2"></i>Estado de Componentes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><i class="bi bi-check-circle text-success me-1"></i>Aprobados</small>
                            <small class="fw-bold">{{ $stats['componentsByStatus']['approved'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['totalComponents'] > 0 ? ($stats['componentsByStatus']['approved'] / $stats['totalComponents'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><i class="bi bi-clock text-warning me-1"></i>Pendientes</small>
                            <small class="fw-bold">{{ $stats['componentsByStatus']['proposed'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['totalComponents'] > 0 ? ($stats['componentsByStatus']['proposed'] / $stats['totalComponents'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><i class="bi bi-x-circle text-danger me-1"></i>Rechazados</small>
                            <small class="fw-bold">{{ $stats['componentsByStatus']['rejected'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['totalComponents'] > 0 ? ($stats['componentsByStatus']['rejected'] / $stats['totalComponents'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><i class="bi bi-megaphone text-info me-1"></i>Ofertas abiertas</small>
                            <small class="fw-bold">{{ $stats['componentsByStatus']['offer_open'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ $stats['totalComponents'] > 0 ? ($stats['componentsByStatus']['offer_open'] / $stats['totalComponents'] * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Capacidad y Top componentes --}}
    <div class="row mb-4">
        {{-- Capacidad vs Inscripciones --}}
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-speedometer2 me-2"></i>Ocupación por Componente
                    </h6>
                </div>
                <div class="card-body">
                    @if($capacityData->count() > 0)
                        @foreach($capacityData as $data)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-semibold">{{ $data['name'] }}</small>
                                    <small>{{ $data['registered'] }}/{{ $data['capacity'] }} ({{ $data['percentage'] }}%)</small>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    @php
                                        $color = $data['percentage'] >= 90 ? '#dc3545' : ($data['percentage'] >= 70 ? '#ffc107' : '#8CC63F');
                                    @endphp
                                    <div class="progress-bar" style="width: {{ $data['percentage'] }}%; background-color: {{ $color }};"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3"></i>
                            <p class="mb-0 mt-2">No hay componentes con capacidad definida</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Top componentes --}}
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-trophy me-2"></i>Top Componentes por Inscripciones
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($topComponents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">#</th>
                                        <th class="border-0">Componente</th>
                                        <th class="border-0">Tipo</th>
                                        <th class="border-0 text-end">Inscripciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topComponents as $index => $component)
                                        <tr>
                                            <td>
                                                @if($index == 0)
                                                    <i class="bi bi-trophy-fill text-warning"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ \Str::limit($component->name, 25) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $typeLabels = ['talk' => 'Charla', 'workshop' => 'Taller', 'activity' => 'Actividad'];
                                                @endphp
                                                <span class="badge bg-secondary">{{ $typeLabels[$component->type] ?? $component->type }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge" style="background-color: #8CC63F;">
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
                            <i class="bi bi-inbox fs-3"></i>
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-people me-2"></i>Lista de Inscritos ({{ $allRegistrations->count() }})
                    </h6>
                    @if($allRegistrations->count() > 0)
                        <button class="btn btn-sm btn-outline-primary" onclick="exportToCSV()">
                            <i class="bi bi-download me-1"></i>Exportar CSV
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($allRegistrations->count() > 0)
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0" id="registrationsTable">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="border-0">Participante</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0">Componente</th>
                                        <th class="border-0">Fecha de Inscripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allRegistrations as $registration)
                                        <tr>
                                            <td>
                                                <strong>{{ $registration->user->name }}</strong>
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
                            <i class="bi bi-inbox fs-3"></i>
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
            borderWidth: 1
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
                }
            }
        }
    }
});
@endif

// Función para exportar a CSV
function exportToCSV() {
    const table = document.getElementById('registrationsTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'inscritos_{{ Str::slug($event->name) }}_{{ date("Y-m-d") }}.csv';
    link.click();
}
</script>
@endpush
