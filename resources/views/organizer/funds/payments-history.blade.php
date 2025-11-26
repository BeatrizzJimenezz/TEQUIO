@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4">
        <h1 class="h2 fw-bold mb-2">Historial de Pagos</h1>
        <p class="text-muted">Todos los pagos recibidos de tus componentes</p>
    </div>

    <!-- Tabs de Navegación -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('organizer.funds.index') }}">Resumen</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('organizer.funds.payments') }}">Historial de Pagos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('organizer.funds.withdrawals') }}">Mis Retiros</a>
        </li>
    </ul>

    <!-- Resumen de Estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted mb-0">Total Recibido</h6>
                        <i class="bi bi-currency-dollar fs-2 text-success"></i>
                    </div>
                    <h2 class="card-title h3 mb-0">${{ number_format($totals['total_amount'], 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted mb-0">Comisión Plataforma</h6>
                        <i class="bi bi-calculator fs-2 text-primary"></i>
                    </div>
                    <h2 class="card-title h3 mb-0">${{ number_format($totals['platform_fees'], 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted mb-0">Pagos en Línea</h6>
                        <i class="bi bi-credit-card fs-2 text-info"></i>
                    </div>
                    <h2 class="card-title h3 mb-0">{{ $totals['online_payments'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted mb-0">Pagos en Persona</h6>
                        <i class="bi bi-cash-coin fs-2 text-warning"></i>
                    </div>
                    <h2 class="card-title h3 mb-0">{{ $totals['in_person_payments'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Pagos -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase small">Fecha</th>
                        <th class="text-uppercase small">Participante</th>
                        <th class="text-uppercase small">Componente / Evento</th>
                        <th class="text-uppercase small">Método</th>
                        <th class="text-uppercase small text-end">Total Pagado</th>
                        <th class="text-uppercase small text-end">Tu Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="align-middle">
                            <div>{{ $payment->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                        </td>
                        <td class="align-middle">
                            <div class="fw-medium">{{ $payment->registration->user->name }}</div>
                            <small class="text-muted">{{ $payment->registration->user->email }}</small>
                        </td>
                        <td class="align-middle">
                            <div class="fw-medium">{{ $payment->registration->component->name }}</div>
                            <small class="text-muted">{{ $payment->registration->component->event->name }}</small>
                        </td>
                        <td class="align-middle">
                            @if($payment->payment_method === 'online')
                                <span class="badge bg-primary d-inline-flex align-items-center">
                                    <i class="bi bi-paypal me-1"></i>
                                    PayPal
                                </span>
                            @else
                                <span class="badge bg-success d-inline-flex align-items-center">
                                    <i class="bi bi-cash-coin me-1"></i>
                                    En Persona
                                </span>
                            @endif
                        </td>
                        <td class="align-middle text-end">
                            <div class="fw-medium">${{ number_format($payment->total_paid, 2) }}</div>
                            <small class="text-muted">Comisión: ${{ number_format($payment->platform_fee + $payment->paypal_fee, 2) }}</small>
                        </td>
                        <td class="align-middle text-end fw-bold text-success">
                            ${{ number_format($payment->organizer_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-file-text text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">No hay pagos registrados aún</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($payments->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
