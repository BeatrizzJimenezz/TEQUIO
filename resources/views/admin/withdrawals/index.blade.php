@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-2">Gestión de Retiros</h1>
        <p class="text-muted">Administra las solicitudes de retiro de los organizadores</p>
    </div>

    <!-- Estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle mb-0 opacity-75">Pendientes</h6>
                        <i class="bi bi-clock-history fs-2 opacity-75"></i>
                    </div>
                    <h2 class="card-title mb-1">{{ $stats['pending_count'] }}</h2>
                    <p class="card-text small opacity-75">${{ number_format($stats['pending_amount'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle mb-0 opacity-75">Completados</h6>
                        <i class="bi bi-check-circle fs-2 opacity-75"></i>
                    </div>
                    <h2 class="card-title mb-1">{{ $stats['completed_count'] }}</h2>
                    <p class="card-text small opacity-75">${{ number_format($stats['completed_amount'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card text-white bg-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle mb-0 opacity-75">Rechazados</h6>
                        <i class="bi bi-x-circle fs-2 opacity-75"></i>
                    </div>
                    <h2 class="card-title mb-1">{{ $stats['rejected_count'] }}</h2>
                    <p class="card-text small opacity-75">Solicitudes</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle mb-0 opacity-75">Total Procesado</h6>
                        <i class="bi bi-graph-up-arrow fs-2 opacity-75"></i>
                    </div>
                    <h2 class="card-title mb-1">${{ number_format($stats['completed_amount'], 2) }}</h2>
                    <p class="card-text small opacity-75">Pagado a organizadores</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="fw-medium me-2">Filtrar por estado:</span>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.withdrawals.index', ['status' => 'all']) }}"
                       class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Todos
                    </a>
                    <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}"
                       class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">
                        Pendientes
                    </a>
                    <a href="{{ route('admin.withdrawals.index', ['status' => 'completed']) }}"
                       class="btn btn-sm {{ $status === 'completed' ? 'btn-success' : 'btn-outline-secondary' }}">
                        Completados
                    </a>
                    <a href="{{ route('admin.withdrawals.index', ['status' => 'rejected']) }}"
                       class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-secondary' }}">
                        Rechazados
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Retiros -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Organizador</th>
                        <th>Monto</th>
                        <th>Cuenta PayPal</th>
                        <th>Estado</th>
                        <th>Fecha Solicitud</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                    <tr>
                        <td class="fw-medium">#{{ $withdrawal->id }}</td>
                        <td>
                            <div>
                                <div class="fw-medium">{{ $withdrawal->organizer->name }}</div>
                                <small class="text-muted">{{ $withdrawal->organizer->email }}</small>
                            </div>
                        </td>
                        <td class="fw-bold">${{ number_format($withdrawal->amount, 2) }}</td>
                        <td>{{ $withdrawal->paypal_email }}</td>
                        <td>
                            @if($withdrawal->status === 'pending')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($withdrawal->status === 'completed')
                                <span class="badge bg-success">Completado</span>
                            @else
                                <span class="badge bg-danger">Rechazado</span>
                            @endif
                        </td>
                        <td>
                            {{ $withdrawal->requested_at->format('d/m/Y H:i') }}
                            <br><small class="text-muted">{{ $withdrawal->requested_at->diffForHumans() }}</small>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($withdrawal->status === 'pending')
                                <a href="{{ route('admin.withdrawals.approve.form', $withdrawal) }}" class="btn btn-sm btn-outline-success me-1">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                                <a href="{{ route('admin.withdrawals.reject.form', $withdrawal) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">No hay retiros para mostrar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($withdrawals->hasPages())
        <div class="card-footer bg-white">
            {{ $withdrawals->appends(['status' => $status])->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
