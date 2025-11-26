@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-withdrawals.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    <div class="hero-header d-flex justify-content-between align-items-center mb-4">
        <div style="z-index: 2;">
            <h2 class="fw-bold mb-1">Mis Fondos</h2>
            <p class="mb-0 opacity-75">Administra tus ganancias y solicita retiros a tu cuenta.</p>
        </div>
        <i class="bi bi-wallet2 hero-pattern"></i>
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

    @if(!$balance->paypal_email)
        <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4 d-flex align-items-start">
            <div class="bg-warning bg-opacity-25 rounded-circle p-2 me-3 text-warning-emphasis">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 alert-heading">Configura tu PayPal</h6>
                <p class="mb-0 small opacity-75">Necesitas configurar tu correo de PayPal para recibir retiros. Podrás agregarlo al solicitar tu primer retiro.</p>
            </div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        
        {{-- Balance Disponible (Usamos estilo 'completed' para verde/positivo) --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card stat-completed h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label mb-1">Balance Disponible</p>
                        <h3 class="stat-value text-brand-accent">${{ number_format($stats['available_balance'], 2) }}</h3>
                    </div>
                    <div class="stat-icon bg-icon-completed">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                
                @if($stats['available_balance'] >= config('payment.minimum_withdrawal', 5.00))
                    <a href="{{ route('organizer.funds.withdrawals.create') }}" class="btn btn-sm w-100 fw-bold text-white shadow-sm" style="background-color: var(--brand-accent); border-radius: 50rem;">
                        <i class="bi bi-cash-stack me-1"></i> Solicitar Retiro
                    </a>
                @else
                    <button disabled class="btn btn-sm w-100 fw-bold border" style="border-radius: 50rem; color: var(--text-secondary); background: #f8fafc;">
                        Mínimo: ${{ number_format(config('payment.minimum_withdrawal', 5.00), 2) }}
                    </button>
                @endif
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card stat-pending">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">En Retención</p>
                        <h3 class="stat-value">${{ number_format($stats['pending_balance'], 2) }}</h3>
                        <small class="text-muted fw-bold">Pendiente de liberar</small>
                    </div>
                    <div class="stat-icon bg-icon-pending">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card stat-total">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">Total Ganado</p>
                        <h3 class="stat-value">${{ number_format($stats['total_earned'], 2) }}</h3>
                        <small class="text-brand-light fw-bold">{{ $stats['payments_count'] }} pagos recibidos</small>
                    </div>
                    <div class="stat-icon bg-icon-total">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card stat-rejected">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">Total Retirado</p>
                        <h3 class="stat-value">${{ number_format($stats['total_withdrawn'], 2) }}</h3>
                        <small class="text-muted fw-bold">Transferido a tu cuenta</small>
                    </div>
                    <div class="stat-icon bg-icon-rejected">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <ul class="nav nav-pills gap-2 p-1 bg-white rounded-pill shadow-sm d-inline-flex" style="border: 1px solid #f0f0f0;">
            <li class="nav-item">
                <a class="nav-link active rounded-pill px-4 fw-bold" href="{{ route('organizer.funds.index') }}" 
                   style="background-color: var(--brand-deep);">
                    Resumen
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 fw-bold text-secondary" href="{{ route('organizer.funds.payments') }}">
                    Historial de Pagos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 fw-bold text-secondary" href="{{ route('organizer.funds.withdrawals') }}">
                    Mis Retiros
                </a>
            </li>
        </ul>
    </div>

    <div class="row g-4">
        
        <div class="col-12 col-xl-6">
            <div class="table-card mt-0 h-100">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-brand-deep mb-0">
                        <i class="bi bi-receipt me-2"></i>Pagos Recientes
                    </h5>
                    <a href="{{ route('organizer.funds.payments') }}" class="btn btn-sm btn-filter">
                        Ver Todos
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Evento / Usuario</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments->take(5) as $payment)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-brand-deep">{{ $payment->component->name }}</div>
                                        <div class="small text-muted">
                                            {{ $payment->registration->user->name }} • 
                                            @if($payment->payment_method === 'online')
                                                <i class="bi bi-paypal text-primary ms-1"></i> PayPal
                                            @else
                                                <i class="bi bi-cash text-success ms-1"></i> Efectivo
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success">+${{ number_format($payment->organizer_amount, 2) }}</span>
                                        <div class="small text-muted">{{ $payment->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <p class="text-muted small mb-0">No hay pagos recientes</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="table-card mt-0 h-100">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-brand-deep mb-0">
                        <i class="bi bi-cash-stack me-2"></i>Retiros Recientes
                    </h5>
                    <a href="{{ route('organizer.funds.withdrawals') }}" class="btn btn-sm btn-filter">
                        Ver Todos
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawals->take(5) as $withdrawal)
                                <tr>
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
                                        <div class="fw-medium text-brand-deep">{{ $withdrawal->requested_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $withdrawal->paypal_email }}</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-brand-deep">${{ number_format($withdrawal->amount, 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <p class="text-muted small mb-0">No has solicitado retiros aún</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection