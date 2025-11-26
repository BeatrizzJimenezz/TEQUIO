@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('organizer.funds.index') }}">Fondos</a></li>
                    <li class="breadcrumb-item active">Retirar Fondos</li>
                </ol>
            </nav>

            <h1 class="h2 fw-bold mb-4">Retirar Fondos</h1>

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

            <!-- Balance Disponible -->
            <div class="card text-white bg-success mb-4">
                <div class="card-body text-center py-5">
                    <p class="mb-2 opacity-75">Balance Disponible para Retirar</p>
                    <h2 class="display-3 fw-bold mb-0">${{ number_format($balance->available_balance, 2) }}</h2>
                    <p class="mt-2 mb-0 opacity-75"><small>Se retirará el total disponible</small></p>
                </div>
            </div>

            <!-- Información Importante -->
            <div class="alert alert-info d-flex align-items-start mb-4">
                <i class="bi bi-info-circle-fill fs-5 me-3 mt-1"></i>
                <div>
                    <h6 class="alert-heading mb-2">Información sobre retiros:</h6>
                    <ul class="mb-0 ps-3">
                        <li>Monto mínimo de retiro: <strong>${{ number_format($minimumWithdrawal, 2) }}</strong></li>
                        <li>Se retirará todo tu balance disponible</li>
                        <li>El administrador revisará y aprobará tu solicitud</li>
                        <li>El pago se enviará a tu cuenta de PayPal</li>
                        <li>El procesamiento puede tomar de 1 a 3 días hábiles</li>
                    </ul>
                </div>
            </div>

            <!-- Formulario Simplificado -->
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('organizer.funds.withdrawals.store') }}" method="POST">
                        @csrf

                        <!-- Campo oculto con el monto total -->
                        <input type="hidden" name="amount" value="{{ $balance->available_balance }}">

                        <!-- Email de PayPal -->
                        <div class="mb-4">
                            <label for="paypal_email" class="form-label fw-medium">
                                <i class="bi bi-paypal text-primary me-2"></i>
                                Correo Electrónico de PayPal
                            </label>
                            <input
                                type="email"
                                name="paypal_email"
                                id="paypal_email"
                                value="{{ old('paypal_email', $balance->paypal_email) }}"
                                class="form-control form-control-lg @error('paypal_email') is-invalid @enderror"
                                placeholder="tu-email@paypal.com"
                                required
                            >
                            @error('paypal_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-shield-check me-1"></i>
                                El pago será enviado a este correo de PayPal. Asegúrate de que sea correcto.
                            </div>
                        </div>

                        <!-- Confirmación Visual -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Resumen del Retiro</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Monto a retirar:</span>
                                    <span class="fw-bold text-success fs-4">${{ number_format($balance->available_balance, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Balance restante:</span>
                                    <span class="fw-medium">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Advertencia -->
                        <div class="alert alert-warning d-flex align-items-start mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                            <div>
                                <strong>Importante:</strong> Una vez solicitado el retiro, los fondos quedarán pendientes de aprobación del administrador. Puedes cancelar la solicitud mientras esté pendiente.
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-cash-stack me-2"></i>
                                Solicitar Retiro de ${{ number_format($balance->available_balance, 2) }}
                            </button>
                            <a href="{{ route('organizer.funds.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
