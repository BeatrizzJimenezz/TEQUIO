@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/organizer-funds.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    <div class="hero-header mb-4">
        <div style="z-index: 2;">
            <h2 class="fw-bold mb-1">Retirar Fondos</h2>
            <p class="mb-0 opacity-75">Transfiere tus ganancias disponibles a tu cuenta PayPal.</p>
        </div>
        <i class="bi bi-wallet-fill hero-pattern"></i>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="fund-card">
                
                <div class="balance-display">
                    <div class="balance-label">Monto Disponible a Retirar</div>
                    <div class="balance-amount">${{ number_format($balance->available_balance, 2) }}</div>
                    <div class="mt-2 small fw-bold text-muted">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                        Se retirará el total del saldo disponible.
                    </div>
                </div>

                <div class="info-box">
                    <h6 class="fw-bold text-brand-light mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i>Detalles del Proceso
                    </h6>
                    <ul class="info-list list-unstyled mb-0">
                        <li><i class="bi bi-dash me-2 text-muted"></i>Monto mínimo: <strong>${{ number_format($minimumWithdrawal, 2) }}</strong></li>
                        <li><i class="bi bi-dash me-2 text-muted"></i>El pago se enviará vía <strong>PayPal</strong>.</li>
                        <li><i class="bi bi-dash me-2 text-muted"></i>Tiempo estimado: <strong>1-3 días hábiles</strong> tras aprobación.</li>
                    </ul>
                </div>

                <form action="{{ route('organizer.funds.withdrawals.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $balance->available_balance }}">

                    <div class="mb-4">
                        <label for="paypal_email" class="form-label">Cuenta de PayPal (Correo)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border: 2px solid #e2e8f0; border-right: none; border-radius: 0.75rem 0 0 0.75rem;">
                                <i class="bi bi-paypal text-primary fs-5"></i>
                            </span>
                            <input
                                type="email"
                                name="paypal_email"
                                id="paypal_email"
                                value="{{ old('paypal_email', $balance->paypal_email) }}"
                                class="form-control form-control-lg border-start-0 ps-0"
                                style="border-radius: 0 0.75rem 0.75rem 0;"
                                placeholder="ejemplo@correo.com"
                                required
                            >
                        </div>
                        @error('paypal_email')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted mt-2 small">
                            Asegúrate de que el correo esté asociado a una cuenta PayPal válida y verificada.
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        <button type="submit" class="btn-action">
                            <i class="bi bi-cash-stack me-2"></i> Confirmar Retiro
                        </button>
                        
                        <a href="{{ route('organizer.funds.index') }}" class="btn btn-link text-decoration-none text-secondary fw-bold">
                            Cancelar y Volver
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection