@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <!-- Icono de éxito -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 p-4 mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h1 class="h2 fw-bold mb-2">¡Pago Completado!</h1>
                <p class="text-muted">Tu inscripción ha sido confirmada exitosamente.</p>
            </div>

            <!-- Detalles del Pago -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-3 mb-4">Detalles del Pago</h5>

                    <!-- Información del Componente -->
                    <div class="mb-3">
                        <strong class="text-muted small d-block mb-1">Componente</strong>
                        <p class="fw-medium mb-0">{{ $registration->component->name }}</p>
                    </div>

                    <div class="mb-3">
                        <strong class="text-muted small d-block mb-1">Evento</strong>
                        <p class="mb-0">{{ $registration->component->event->name }}</p>
                    </div>

                    <!-- Información del Ticket -->
                    <div class="alert alert-primary border-0 mb-4">
                        <strong class="text-primary small d-block mb-2">Tu Ticket</strong>
                        <p class="h4 font-monospace fw-bold text-primary mb-1">{{ $registration->ticket_qr }}</p>
                        <small class="text-primary">Guarda este código para el día del evento</small>
                    </div>

                    <!-- Detalles del Pago -->
                    @if($payment)
                    <div class="pt-4 border-top">
                        <strong class="text-muted small d-block mb-3">Desglose del Pago</strong>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Precio del componente:</span>
                            <span class="fw-medium">${{ number_format($payment->component_price, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Comisión de plataforma:</span>
                            <span class="fw-medium">${{ number_format($payment->platform_fee, 2) }}</span>
                        </div>

                        @if($payment->payment_method === 'online' && $payment->paypal_fee > 0)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Comisión PayPal:</span>
                            <span class="fw-medium">${{ number_format($payment->paypal_fee, 2) }}</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between pt-2 border-top mt-2">
                            <span class="fw-bold">Total pagado:</span>
                            <span class="fw-bold text-success">${{ number_format($payment->total_paid, 2) }}</span>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Método de pago:</span>
                                <span class="fw-medium">
                                    @if($payment->payment_method === 'online')
                                        <i class="bi bi-paypal me-1"></i>
                                        PayPal
                                    @else
                                        <i class="bi bi-cash-coin me-1"></i>
                                        Pago en persona
                                    @endif
                                </span>
                            </div>

                            @if($payment->paypal_transaction_id)
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">ID de transacción:</span>
                                <span class="font-monospace" style="font-size: 0.7rem;">{{ Str::limit($payment->paypal_transaction_id, 20) }}</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Fecha de pago:</span>
                                <span>{{ $registration->paid_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Próximos Pasos -->
            <div class="alert alert-info border-0 mb-4">
                <h6 class="alert-heading fw-bold">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    ¿Qué sigue?
                </h6>
                <ul class="mb-0 ps-3">
                    <li class="mb-2 small">
                        <i class="bi bi-check2 text-info me-1"></i>
                        Recibirás un correo de confirmación con los detalles de tu inscripción.
                    </li>
                    <li class="mb-2 small">
                        <i class="bi bi-check2 text-info me-1"></i>
                        Guarda tu código QR para presentarlo el día del evento.
                    </li>
                    <li class="small">
                        <i class="bi bi-check2 text-info me-1"></i>
                        Puedes ver todas tus inscripciones en tu panel de usuario.
                    </li>
                </ul>
            </div>

            <!-- Botones de Acción -->
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ route('registrations.index') }}" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-ticket-detailed me-2"></i>
                    Ver Mis Inscripciones
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="bi bi-house-door me-2"></i>
                    Volver al Inicio
                </a>
            </div>

            <!-- Información de Contacto -->
            <div class="text-center mt-4">
                <p class="text-muted small mb-0">
                    ¿Tienes preguntas? Contacta al organizador del evento o a nuestro equipo de soporte.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
