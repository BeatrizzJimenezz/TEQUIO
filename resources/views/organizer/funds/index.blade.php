@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-2">Gestión de Fondos</h1>
        <p class="text-muted">Administra tus ganancias y solicita retiros</p>
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

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-4 mb-4">
        <!-- Balance Disponible -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle mb-0 opacity-75">Balance Disponible</h6>
                        <i class="bi bi-wallet2 fs-2 opacity-75"></i>
                    </div>
                    <h2 class="card-title mb-1">${{ number_format($stats['available_balance'], 2) }}</h2>
                    <p class="card-text small opacity-75">Listo para retirar</p>
                    @if($stats['available_balance'] >= config('payment.minimum_withdrawal', 5.00))
                    <a href="{{ route('organizer.funds.withdrawals.create') }}" class="btn btn-light btn-sm mt-2 w-100">
                        <i class="bi bi-cash-stack me-1"></i> Solicitar Retiro
                    </a>
                    @else
                    <button type="button" class="btn btn-light btn-sm mt-2 w-100" disabled title="Balance mínimo requerido: ${{ number_format(config('payment.minimum_withdrawal', 5.00), 2) }}">
                        <i class="bi bi-cash-stack me-1"></i> Solicitar Retiro
                    </button>
                    <small class="d-block mt-2 opacity-75">Mínimo: ${{ number_format(config('payment.minimum_withdrawal', 5.00), 2) }}</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Balance Pendiente -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle mb-0 opacity-75">Balance Pendiente</h6>
                        <i class="bi bi-clock-history fs-2 opacity-75"></i>
                    </div>
                    <h2 class="card-title mb-1">${{ number_format($stats['pending_balance'], 2) }}</h2>
                    <p class="card-text small opacity-75">En período de retención</p>
                </div>
            </div>
        </div>

        <!-- Total Ganado -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle mb-0 opacity-75">Total Ganado</h6>
                        <i class="bi bi-graph-up-arrow fs-2 opacity-75"></i>
                    </div>
                    <h2 class="card-title mb-1">${{ number_format($stats['total_earned'], 2) }}</h2>
                    <p class="card-text small opacity-75">{{ $stats['payments_count'] }} pagos recibidos</p>
                </div>
            </div>
        </div>

        <!-- Total Retirado -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle mb-0 opacity-75">Total Retirado</h6>
                        <i class="bi bi-cash-coin fs-2 opacity-75"></i>
                    </div>
                    <h2 class="card-title mb-1">${{ number_format($stats['total_withdrawn'], 2) }}</h2>
                    <p class="card-text small opacity-75">${{ number_format($stats['pending_withdrawals'], 2) }} pendientes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de PayPal -->
    @if(!$balance->paypal_email)
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            <strong>Configura tu correo de PayPal</strong><br>
            <small>Necesitas configurar tu correo de PayPal para recibir retiros. Lo podrás agregar al solicitar tu primer retiro.</small>
        </div>
    </div>
    @else
    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        <div>
            <strong>Correo de PayPal configurado:</strong> {{ $balance->paypal_email }}
        </div>
    </div>
    @endif

    <!-- Tabs de Navegación -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('organizer.funds.index') }}">
                <i class="bi bi-house-fill me-1"></i> Resumen
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('organizer.funds.payments') }}">
                <i class="bi bi-receipt me-1"></i> Historial de Pagos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('organizer.funds.withdrawals') }}">
                <i class="bi bi-cash-stack me-1"></i> Mis Retiros
            </a>
        </li>
    </ul>

    <div class="row g-4">
        <!-- Pagos Recientes -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="card-title mb-0">Pagos Recientes</h5>
                    <a href="{{ route('organizer.funds.payments') }}" class="btn btn-sm btn-outline-primary">
                        Ver todos <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($payments->take(5) as $payment)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $payment->component->name }}</h6>
                                <small class="text-muted">
                                    {{ $payment->registration->user->name }} •
                                    @if($payment->payment_method === 'online')
                                        <span class="text-primary"><i class="bi bi-paypal"></i> PayPal</span>
                                    @else
                                        <span class="text-success"><i class="bi bi-cash"></i> En persona</span>
                                    @endif
                                </small>
                            </div>
                            <div class="text-end">
                                <h6 class="text-success mb-0">+${{ number_format($payment->organizer_amount, 2) }}</h6>
                                <small class="text-muted">{{ $payment->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-inbox display-1 mb-3 d-block"></i>
                        <p class="mb-0">No hay pagos recibidos aún</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Solicitudes de Retiro Recientes -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="card-title mb-0">Retiros Recientes</h5>
                    <a href="{{ route('organizer.funds.withdrawals') }}" class="btn btn-sm btn-outline-primary">
                        Ver todos <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($withdrawals->take(5) as $withdrawal)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${{ number_format($withdrawal->amount, 2) }}</h6>
                                <small class="text-muted">{{ $withdrawal->paypal_email }}</small>
                            </div>
                            <div class="text-end">
                                @if($withdrawal->status === 'pending')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock"></i> Pendiente
                                    </span>
                                @elseif($withdrawal->status === 'completed')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Completado
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i> Rechazado
                                    </span>
                                @endif
                                <br>
                                <small class="text-muted">{{ $withdrawal->requested_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-wallet2 display-1 mb-3 d-block"></i>
                        <p class="mb-0">No has solicitado retiros aún</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
