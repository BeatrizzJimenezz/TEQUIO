@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-withdrawals.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    <div class="hero-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Solicitudes de retiro</h2>
            <p class="mb-0 opacity-75">Administra los pagos y transferencias a organizadores.</p>
        </div>
        <i class="bi bi-cash-stack hero-pattern"></i>
    </div>

    <div class="row g-4 mb-4">
        {{-- Pendientes --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card stat-pending">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">Pendientes</p>
                        <h3 class="stat-value">{{ $stats['pending_count'] }}</h3>
                        <small class="text-muted fw-bold">${{ number_format($stats['pending_amount'], 2) }}</small>
                    </div>
                    <div class="stat-icon bg-icon-pending">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card stat-completed">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">Completados</p>
                        <h3 class="stat-value">{{ $stats['completed_count'] }}</h3>
                        <small class="text-success fw-bold">${{ number_format($stats['completed_amount'], 2) }}</small>
                    </div>
                    <div class="stat-icon bg-icon-completed">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card stat-rejected">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">Rechazados</p>
                        <h3 class="stat-value">{{ $stats['rejected_count'] }}</h3>
                        <small class="text-danger fw-bold">Solicitudes</small>
                    </div>
                    <div class="stat-icon bg-icon-rejected">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card stat-total">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">Total Procesado</p>
                        <h3 class="stat-value text-truncate" title="${{ number_format($stats['completed_amount'], 2) }}">
                            ${{ number_format($stats['completed_amount'], 0) }}<span class="fs-6">.{{ explode('.', number_format($stats['completed_amount'], 2))[1] }}</span>
                        </h3>
                        <small class="text-primary fw-bold">Pagado</small>
                    </div>
                    <div class="stat-icon bg-icon-total">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        
        <div class="p-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="fw-bold text-brand-deep mb-0">Listado de solicitudes</h5>
            
            <div class="btn-group" role="group">
                <a href="{{ route('admin.withdrawals.index', ['status' => 'all']) }}"
                   class="btn btn-filter {{ $status === 'all' ? 'active' : '' }}">
                    Todos
                </a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}"
                   class="btn btn-filter {{ $status === 'pending' ? 'active' : '' }}">
                    Pendientes
                </a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'completed']) }}"
                   class="btn btn-filter {{ $status === 'completed' ? 'active' : '' }}">
                    Completados
                </a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'rejected']) }}"
                   class="btn btn-filter {{ $status === 'rejected' ? 'active' : '' }}">
                    Rechazados
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Organizador</th>
                        <th>Monto</th>
                        <th>Cuenta PayPal</th>
                        <th>Estado</th>
                        <th>Fecha solicitud</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                    <tr>
                        <td class="fw-bold text-muted">#{{ $withdrawal->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($withdrawal->organizer->profile_photo)
                                    <img src="{{ asset('storage/' . $withdrawal->organizer->profile_photo) }}" 
                                         class="rounded-circle me-2" width="32" height="32" style="object-fit:cover;">
                                @else
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 text-primary fw-bold" 
                                         style="width:32px; height:32px; font-size:0.8rem;">
                                        {{ substr($withdrawal->organizer->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $withdrawal->organizer->name }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $withdrawal->organizer->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-brand-deep">${{ number_format($withdrawal->amount, 2) }}</span>
                        </td>
                        <td class="text-muted small">
                            <i class="bi bi-paypal me-1 text-primary"></i>
                            {{ $withdrawal->paypal_email }}
                        </td>
                        <td>
                            @if($withdrawal->status === 'pending')
                                <span class="status-badge badge-pending">Pendiente</span>
                            @elseif($withdrawal->status === 'completed')
                                <span class="status-badge badge-completed">Completado</span>
                            @else
                                <span class="status-badge badge-rejected">Rechazado</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ $withdrawal->requested_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $withdrawal->requested_at->format('H:i') }}</small>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" 
                                   class="btn btn-icon btn-icon-view" title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($withdrawal->status === 'pending')
                                    <a href="{{ route('admin.withdrawals.approve.form', $withdrawal) }}" 
                                       class="btn btn-icon btn-icon-approve" title="Aprobar">
                                        <i class="bi bi-check-lg"></i>
                                    </a>
                                    <a href="{{ route('admin.withdrawals.reject.form', $withdrawal) }}" 
                                       class="btn btn-icon btn-icon-reject" title="Rechazar">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 text-muted opacity-25">
                                    <i class="bi bi-inbox display-3"></i>
                                </div>
                                <h5 class="fw-bold text-muted">No hay retiros</h5>
                                <p class="text-muted small mb-0">No se encontraron solicitudes con este filtro.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($withdrawals->hasPages())
            <div class="pagination-container">
                {{ $withdrawals->appends(['status' => $status])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection