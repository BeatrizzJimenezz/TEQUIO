@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/organizer-funds.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- 1. Hero Header --}}
    <div class="hero-header d-flex justify-content-between align-items-center mb-4">
        <div style="z-index: 2;">
            <h2 class="fw-bold mb-1">Historial de Pagos</h2>
            <p class="mb-0 opacity-75">Registro detallado de todos los ingresos por eventos.</p>
        </div>
        <i class="bi bi-receipt hero-pattern"></i>
    </div>

    {{-- 2. Navegación (Pills) --}}
    <div class="mb-4">
        <div class="nav-pills-custom">
            <a class="nav-link" href="{{ route('organizer.funds.index') }}">Resumen</a>
            <a class="nav-link active" href="{{ route('organizer.funds.payments') }}">Historial de Pagos</a>
            <a class="nav-link" href="{{ route('organizer.funds.withdrawals') }}">Mis Retiros</a>
        </div>
    </div>

    {{-- 3. Resumen de Estadísticas --}}
    <div class="row g-4 mb-4">
        
        {{-- Total Recibido --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="mini-stat-card border-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mini-stat-label">Total Recibido</p>
                        <h3 class="mini-stat-value">${{ number_format($totals['total_amount'], 2) }}</h3>
                    </div>
                    <div class="mini-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comisiones --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="mini-stat-card border-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mini-stat-label">Comisiones Plataforma</p>
                        <h3 class="mini-stat-value text-secondary">${{ number_format($totals['platform_fees'], 2) }}</h3>
                    </div>
                    <div class="mini-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-pie-chart"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pagos Online --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="mini-stat-card border-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mini-stat-label">Pagos en Línea</p>
                        <h3 class="mini-stat-value">{{ $totals['online_payments'] }}</h3>
                    </div>
                    <div class="mini-stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-credit-card"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pagos en Persona --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="mini-stat-card border-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mini-stat-label">Pagos en Persona</p>
                        <h3 class="mini-stat-value">{{ $totals['in_person_payments'] }}</h3>
                    </div>
                    <div class="mini-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Tabla de Pagos --}}
    <div class="table-card">
        
        <div class="p-4 border-bottom">
            <h5 class="fw-bold text-brand-deep mb-0">
                <i class="bi bi-list-ul me-2"></i>Transacciones
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-funds mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Participante</th>
                        <th>Evento / Componente</th>
                        <th>Método</th>
                        <th class="text-end">Total Pagado</th>
                        <th class="text-end">Tu Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>
                            <div class="fw-bold text-brand-deep">{{ $payment->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $payment->created_at->format('H:i A') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $payment->registration->user->name }}</div>
                            <small class="text-muted">{{ $payment->registration->user->email }}</small>
                        </td>
                        <td>
                            <div class="fw-medium text-brand-deep">{{ $payment->registration->component->event->name }}</div>
                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                <i class="bi bi-ticket-perforated me-1"></i>
                                {{ $payment->registration->component->name }}
                            </small>
                        </td>
                        <td>
                            @if($payment->payment_method === 'online')
                                <span class="method-badge badge-paypal">
                                    <i class="bi bi-paypal"></i> PayPal
                                </span>
                            @else
                                <span class="method-badge badge-cash">
                                    <i class="bi bi-cash"></i> Efectivo
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="fw-medium">${{ number_format($payment->total_paid, 2) }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">
                                Comisión: -${{ number_format($payment->platform_fee + $payment->paypal_fee, 2) }}
                            </small>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-success fs-6">
                                +${{ number_format($payment->organizer_amount, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 text-muted opacity-25">
                                    <i class="bi bi-inbox display-3"></i>
                                </div>
                                <h5 class="fw-bold text-muted">Sin pagos registrados</h5>
                                <p class="text-muted small mb-0">Aún no has recibido pagos por tus eventos.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($payments->hasPages())
            <div class="pagination-container">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection