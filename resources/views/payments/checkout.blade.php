@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('registrations.index') }}">Mis Inscripciones</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>

            <h1 class="h2 fw-bold mb-4">Completar Pago</h1>

            <div class="row g-4">
                <!-- Resumen del Componente -->
                <div class="col-12 col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Detalles del Componente</h5>

                            <div class="mb-3">
                                <strong class="text-muted small d-block">Componente:</strong>
                                <p class="mb-0">{{ $component->name }}</p>
                            </div>

                            <div class="mb-3">
                                <strong class="text-muted small d-block">Evento:</strong>
                                <p class="mb-0">{{ $component->event->name }}</p>
                            </div>

                            @if($component->description)
                            <div class="mb-3">
                                <strong class="text-muted small d-block">Descripción:</strong>
                                <p class="text-muted small mb-0">{{ Str::limit($component->description, 200) }}</p>
                            </div>
                            @endif

                            @if($registration)
                            <div>
                                <strong class="text-muted small d-block">Ticket QR:</strong>
                                <p class="mb-0 font-monospace">{{ $registration->ticket_qr }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Métodos de Pago -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Métodos de Pago</h5>

                            @if($component->acceptsPaymentMethod('online'))
                            <div class="mb-4">
                                <h6 class="fw-medium mb-3">Pago en Línea (PayPal)</h6>
                                <p class="text-muted small mb-3">
                                    Paga de forma segura con tu cuenta de PayPal o tarjeta de crédito/débito.
                                </p>
                                <button id="paypal-btn" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-paypal me-2"></i>
                                    Pagar con PayPal
                                </button>
                            </div>
                            @endif

                            @if($component->acceptsPaymentMethod('in_person'))
                            <div class="border rounded p-3 bg-light">
                                <h6 class="fw-medium mb-3">
                                    <i class="bi bi-cash-coin me-2"></i>
                                    Pago en Persona
                                </h6>
                                <p class="text-muted small mb-3">
                                    Confirma tu inscripción ahora y coordina el pago directamente con el organizador del evento.
                                    Tu lugar quedará reservado inmediatamente.
                                </p>
                                <button id="in-person-btn" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Confirmar con Pago en Persona
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Resumen de Pago -->
                <div class="col-12 col-lg-4">
                    <div class="card sticky-top" style="top: 1rem;">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Resumen de Pago</h5>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Precio del componente:</span>
                                    <span class="fw-medium">${{ number_format($fees['component_price'], 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Comisión de plataforma (5%):</span>
                                    <span class="fw-medium">${{ number_format($fees['platform_fee'], 2) }}</span>
                                </div>

                                @if($component->acceptsPaymentMethod('online'))
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Comisión PayPal:</span>
                                    <span class="fw-medium">${{ number_format($fees['paypal_fee'], 2) }}</span>
                                </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-bold">Total a pagar:</span>
                                <span class="h4 text-primary mb-0">${{ number_format($fees['total_paid'], 2) }}</span>
                            </div>

                            <div class="alert alert-info border-0 small">
                                <p class="fw-medium mb-1">El organizador recibe:</p>
                                <p class="h5 text-info mb-1">${{ number_format($fees['organizer_amount'], 2) }}</p>
                                <p class="mb-0" style="font-size: 0.75rem;">100% del precio del componente</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de PayPal -->
<div class="modal fade" id="paypal-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div id="modal-loading">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="fw-medium">Preparando el pago...</p>
                    <p class="text-muted small">Serás redirigido a PayPal en un momento.</p>
                </div>

                <div id="modal-processing" class="d-none">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Procesando...</span>
                    </div>
                    <p class="fw-medium">Procesando tu pago...</p>
                    <p class="text-muted small">No cierres esta ventana.</p>
                </div>

                <div id="modal-error" class="d-none">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <p class="fw-medium mt-3 mb-2">Error al procesar el pago</p>
                    <p id="error-message" class="text-muted small mb-3"></p>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let paypalWindow = null;

document.getElementById('paypal-btn')?.addEventListener('click', async function() {
    const paypalModal = new bootstrap.Modal(document.getElementById('paypal-modal'));
    showModalState('loading');
    paypalModal.show();

    try {
        const initiateUrl = @if($registration)
            '{{ route('payment.paypal.initiate', $registration) }}'
        @else
            '{{ route('payment.paypal.initiate.component', $component) }}'
        @endif;

        const response = await fetch(initiateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success && data.approval_url) {
            // Abrir PayPal en ventana popup
            const width = 500;
            const height = 700;
            const left = (screen.width / 2) - (width / 2);
            const top = (screen.height / 2) - (height / 2);

            paypalWindow = window.open(
                data.approval_url,
                'PayPal',
                `width=${width},height=${height},left=${left},top=${top},toolbar=no,location=no,status=no,menubar=no`
            );

            if (paypalWindow) {
                showModalState('processing');
                
                // El popup se cerrará automáticamente cuando PayPal redirija
                // y la ventana padre será redirigida a la página de confirmación
                const checkClosed = setInterval(() => {
                    if (paypalWindow.closed) {
                        clearInterval(checkClosed);
                        paypalModal.hide();
                    }
                }, 500);
            } else {
                showError('Por favor permite las ventanas emergentes para continuar con el pago.');
            }
        } else {
            showError(data.message || 'Error al iniciar el pago.');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error al comunicarse con el servidor.');
    }
});

function showModalState(state) {
    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-processing').classList.add('d-none');
    document.getElementById('modal-error').classList.add('d-none');

    if (state === 'loading') {
        document.getElementById('modal-loading').classList.remove('d-none');
    } else if (state === 'processing') {
        document.getElementById('modal-processing').classList.remove('d-none');
    } else if (state === 'error') {
        document.getElementById('modal-error').classList.remove('d-none');
    }
}

function showError(message) {
    document.getElementById('error-message').textContent = message;
    showModalState('error');
}

// Manejar botón de pago en persona
document.getElementById('in-person-btn')?.addEventListener('click', async function() {
    const button = this;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Procesando...';
    
    try {
        const registerUrl = '{{ route('registrations.store') }}';
        const componentId = '{{ $component->id }}';
        
        const response = await fetch(registerUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                component_id: componentId,
                payment_method: 'in_person'
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Redirigir a mis inscripciones con mensaje de éxito
            window.location.href = '{{ route('registrations.index') }}';
        } else {
            alert(data.message || 'Error al procesar la inscripción');
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la inscripción');
        button.disabled = false;
        button.innerHTML = originalText;
    }
});
</script>
@endpush
@endsection
