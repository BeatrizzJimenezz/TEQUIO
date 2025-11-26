@extends('layouts.app')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/organizer-funds.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    <div class="hero-header mb-4">
        <div style="z-index: 2;">
            <h2 class="fw-bold mb-1">Historial de Retiros</h2>
            <p class="mb-0 opacity-75">Gestiona tus solicitudes de transferencia de fondos.</p>
        </div>
        
        <div style="z-index: 2;">
            @if($balance->available_balance >= $minimumWithdrawal)
                <a href="{{ route('organizer.funds.withdrawals.create') }}" class="btn btn-light text-brand-deep fw-bold rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i> Solicitar Retiro
                </a>
            @else
                <button class="btn btn-outline-light rounded-pill px-4 opacity-75" disabled title="Mínimo requerido: ${{ number_format($minimumWithdrawal, 2) }}">
                    <i class="bi bi-lock-fill me-2"></i> Saldo Insuficiente
                </button>
            @endif
        </div>

        <i class="bi bi-cash-stack hero-pattern"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($balance->available_balance < $minimumWithdrawal)
        <div class="alert alert-light border-0 shadow-sm rounded-3 mb-4 d-flex align-items-start">
            <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3 text-warning">
                <i class="bi bi-info-circle-fill fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Balance insuficiente para retirar</h6>
                <p class="mb-0 small text-secondary">
                    Tu balance actual es <strong>${{ number_format($balance->available_balance, 2) }}</strong>. 
                    Necesitas al menos <strong>${{ number_format($minimumWithdrawal, 2) }}</strong> para realizar una solicitud.
                </p>
            </div>
        </div>
    @endif

    {{-- 2. Navegación (Pills) --}}
    <div class="mb-4">
        <div class="nav-pills-custom">
            <a class="nav-link" href="{{ route('organizer.funds.index') }}">Resumen</a>
            <a class="nav-link" href="{{ route('organizer.funds.payments') }}">Historial de Pagos</a>
            <a class="nav-link active" href="{{ route('organizer.funds.withdrawals') }}">Mis Retiros</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        {{-- Total Solicitado --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="mini-stat-card border-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mini-stat-label">Total Solicitado</p>
                        <h3 class="mini-stat-value">${{ number_format($totals['total_requested'], 2) }}</h3>
                    </div>
                    <div class="mini-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completados --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="mini-stat-card border-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mini-stat-label">Completados</p>
                        <h3 class="mini-stat-value">${{ number_format($totals['total_completed'], 2) }}</h3>
                    </div>
                    <div class="mini-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendientes --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="mini-stat-card border-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mini-stat-label">Pendientes</p>
                        <h3 class="mini-stat-value">${{ number_format($totals['total_pending'], 2) }}</h3>
                    </div>
                    <div class="mini-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rechazados --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="mini-stat-card border-danger">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mini-stat-label">Rechazados</p>
                        <h3 class="mini-stat-value">{{ $totals['total_rejected'] }}</h3>
                    </div>
                    <div class="mini-stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        
        <div class="p-4 border-bottom">
            <h5 class="fw-bold text-brand-deep mb-0">
                <i class="bi bi-list-ul me-2"></i>Solicitudes Realizadas
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-funds mb-0">
                <thead>
                    <tr>
                        <th>Fecha Solicitud</th>
                        <th>Monto</th>
                        <th>Cuenta PayPal</th>
                        <th>Estado</th>
                        <th>Procesado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                    <tr>
                        <td>
                            <div class="fw-bold text-brand-deep">{{ $withdrawal->requested_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $withdrawal->requested_at->format('H:i A') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">${{ number_format($withdrawal->amount, 2) }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-paypal me-2 text-primary"></i>
                                {{ $withdrawal->paypal_email }}
                            </div>
                        </td>
                        <td>
                            @if($withdrawal->status === 'pending')
                                <span class="badge-status badge-pending">
                                    <i class="bi bi-clock me-1"></i> Pendiente
                                </span>
                            @elseif($withdrawal->status === 'completed')
                                <span class="badge-status badge-completed">
                                    <i class="bi bi-check-circle me-1"></i> Completado
                                </span>
                            @else
                                <span class="badge-status badge-rejected">
                                    <i class="bi bi-x-circle me-1"></i> Rechazado
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($withdrawal->processed_at)
                                <div class="text-dark small">{{ $withdrawal->processed_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $withdrawal->processed_at->format('H:i') }}</small>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($withdrawal->status === 'pending')
                                <form action="{{ route('organizer.funds.withdrawals.cancel', $withdrawal) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de cancelar este retiro? Los fondos serán devueltos a tu balance disponible.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-action btn-icon-danger" title="Cancelar Solicitud">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            @elseif($withdrawal->status === 'completed' && $withdrawal->paypal_transaction_id)
                                <span class="badge bg-light text-secondary border fw-normal font-monospace" title="ID de Transacción">
                                    {{ Str::limit($withdrawal->paypal_transaction_id, 10) }}
                                </span>
                            @elseif($withdrawal->status === 'rejected' && $withdrawal->notes)
                                <button type="button" class="btn-icon-action btn-icon-info"
                                        onclick="alert('Razón de rechazo: {{ addslashes($withdrawal->notes) }}')"
                                        title="Ver motivo de rechazo">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 text-muted opacity-25">
                                    <i class="bi bi-wallet2 display-3"></i>
                                </div>
                                <h5 class="fw-bold text-muted">Sin solicitudes</h5>
                                <p class="text-muted small mb-0">No has realizado ninguna solicitud de retiro.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($withdrawals->hasPages())
            <div class="pagination-container">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection