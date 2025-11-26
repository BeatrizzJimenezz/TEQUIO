@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h2 fw-bold mb-2">Mis Retiros</h1>
            <p class="text-muted">Historial de todas tus solicitudes de retiro</p>
        </div>
        @if($balance->available_balance >= $minimumWithdrawal)
        <a href="{{ route('organizer.funds.withdrawals.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Solicitar Retiro
        </a>
        @else
        <button type="button" class="btn btn-primary" disabled title="Balance mínimo requerido: ${{ number_format($minimumWithdrawal, 2) }}">
            <i class="bi bi-plus-circle me-2"></i>
            Solicitar Retiro
        </button>
        @endif
    </div>

    <!-- Mensajes de sesión -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Alerta de balance insuficiente -->
    @if($balance->available_balance < $minimumWithdrawal)
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            <strong>Balance insuficiente para retiros</strong><br>
            <small>Necesitas al menos ${{ number_format($minimumWithdrawal, 2) }} de balance disponible. Tu balance actual es ${{ number_format($balance->available_balance, 2) }}.</small>
        </div>
    </div>
    @endif

    <!-- Tabs de Navegación -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('organizer.funds.index') }}">Resumen</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('organizer.funds.payments') }}">Historial de Pagos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('organizer.funds.withdrawals') }}">Mis Retiros</a>
        </li>
    </ul>

    <!-- Resumen de Estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted mb-0">Total Solicitado</h6>
                        <i class="bi bi-receipt fs-2 text-primary"></i>
                    </div>
                    <h2 class="card-title h3 mb-0">${{ number_format($totals['total_requested'], 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted mb-0">Completados</h6>
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                    <h2 class="card-title h3 mb-0">${{ number_format($totals['total_completed'], 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted mb-0">Pendientes</h6>
                        <i class="bi bi-clock-history fs-2 text-warning"></i>
                    </div>
                    <h2 class="card-title h3 mb-0">${{ number_format($totals['total_pending'], 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted mb-0">Rechazados</h6>
                        <i class="bi bi-x-circle fs-2 text-danger"></i>
                    </div>
                    <h2 class="card-title h3 mb-0">{{ $totals['total_rejected'] }}</h2>
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
                        <th class="text-uppercase small">Fecha Solicitud</th>
                        <th class="text-uppercase small">Monto</th>
                        <th class="text-uppercase small">Cuenta PayPal</th>
                        <th class="text-uppercase small">Estado</th>
                        <th class="text-uppercase small">Fecha Proceso</th>
                        <th class="text-uppercase small text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                    <tr>
                        <td class="align-middle">
                            <div>{{ $withdrawal->requested_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $withdrawal->requested_at->format('H:i') }}</small>
                        </td>
                        <td class="align-middle">
                            <div class="fw-bold">${{ number_format($withdrawal->amount, 2) }}</div>
                        </td>
                        <td class="align-middle">
                            <div>{{ $withdrawal->paypal_email }}</div>
                        </td>
                        <td class="align-middle">
                            @if($withdrawal->status === 'pending')
                                <span class="badge bg-warning d-inline-flex align-items-center">
                                    <i class="bi bi-clock me-1"></i>
                                    Pendiente
                                </span>
                            @elseif($withdrawal->status === 'completed')
                                <span class="badge bg-success d-inline-flex align-items-center">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Completado
                                </span>
                            @else
                                <span class="badge bg-danger d-inline-flex align-items-center">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Rechazado
                                </span>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($withdrawal->processed_at)
                                {{ $withdrawal->processed_at->format('d/m/Y H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="align-middle text-end">
                            @if($withdrawal->status === 'pending')
                                <form action="{{ route('organizer.funds.withdrawals.cancel', $withdrawal) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de cancelar este retiro? Los fondos serán devueltos a tu balance disponible.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-lg me-1"></i>
                                        Cancelar
                                    </button>
                                </form>
                            @elseif($withdrawal->status === 'completed' && $withdrawal->paypal_transaction_id)
                                <small class="text-muted" title="{{ $withdrawal->paypal_transaction_id }}">
                                    ID: {{ Str::limit($withdrawal->paypal_transaction_id, 15) }}
                                </small>
                            @elseif($withdrawal->status === 'rejected' && $withdrawal->notes)
                                <button
                                    type="button"
                                    onclick="alert('Razón de rechazo: {{ addslashes($withdrawal->notes) }}')"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="bi bi-info-circle me-1"></i>
                                    Ver razón
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-wallet2 text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-2">No has solicitado retiros aún</p>
                            @if($balance->available_balance >= $minimumWithdrawal)
                            <a href="{{ route('organizer.funds.withdrawals.create') }}" class="btn btn-sm btn-primary">
                                Solicitar tu primer retiro <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            @else
                            <div class="alert alert-warning d-inline-block">
                                <i class="bi bi-info-circle me-2"></i>
                                Necesitas al menos ${{ number_format($minimumWithdrawal, 2) }} de balance disponible para solicitar un retiro.<br>
                                <small>Balance actual: ${{ number_format($balance->available_balance, 2) }}</small>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($withdrawals->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
