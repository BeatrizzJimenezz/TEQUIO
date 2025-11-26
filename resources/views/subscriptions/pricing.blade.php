@extends('layouts.app')

@section('header')
    Planes de Suscripción
@endsection

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Elige tu plan</h1>
        <p class="lead text-muted">Desbloquea todas las funcionalidades de TEQUIO y gestiona eventos ilimitados</p>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Current Subscription Status -->
    @if(auth()->user()->hasActiveSubscription())
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                <div>
                    <h5 class="mb-1">Suscripción Activa</h5>
                    <p class="mb-0 text-muted">
                        Plan: <strong>{{ ucfirst(auth()->user()->subscription->plan_id) }}</strong> |
                        Vence: <strong>{{ auth()->user()->subscription->ends_at->format('d/m/Y') }}</strong>
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                <div>
                    <h5 class="mb-1">Cuenta Gratuita</h5>
                    <p class="mb-0">Actualmente puedes crear hasta <strong>1 evento activo</strong>. Suscríbete para gestionar eventos ilimitados.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Pricing Cards -->
    <div class="row g-4 justify-content-center">
        <!-- Monthly Plan -->
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg h-100 hover-lift">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <div class="badge bg-primary bg-gradient mb-3 px-3 py-2">
                            <i class="bi bi-calendar-month me-1"></i> Mensual
                        </div>
                        <h2 class="display-3 fw-bold mb-0">$29<small class="fs-5 text-muted">.99</small></h2>
                        <p class="text-muted">por mes</p>
                    </div>

                    <ul class="list-unstyled mb-4">
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Eventos ilimitados</strong>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Gestión completa de componentes
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Equipo colaborativo sin límites
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Reportes y estadísticas avanzadas
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Ofertas y propuestas ilimitadas
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Soporte prioritario
                        </li>
                    </ul>

                    <button type="button" class="btn btn-primary btn-lg w-100 shadow-sm subscribe-btn" data-plan="monthly">
                        <i class="bi bi-credit-card me-2"></i>Suscribirse Ahora
                    </button>

                    <p class="text-center text-muted small mt-3 mb-0">
                        <i class="bi bi-shield-check me-1"></i>Pago seguro con PayPal
                    </p>
                </div>
            </div>
        </div>

        <!-- Annual Plan (Popular) -->
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg h-100 hover-lift position-relative" style="border: 3px solid #198754 !important;">
                <!-- Popular Badge -->
                <div class="position-absolute top-0 start-50 translate-middle">
                    <span class="badge bg-success bg-gradient px-4 py-2 rounded-pill shadow">
                        <i class="bi bi-star-fill me-1"></i>Más Popular
                    </span>
                </div>

                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4 mt-3">
                        <div class="badge bg-success bg-gradient mb-3 px-3 py-2">
                            <i class="bi bi-calendar-year me-1"></i> Anual
                        </div>
                        <h2 class="display-3 fw-bold mb-0">$299<small class="fs-5 text-muted">.99</small></h2>
                        <p class="text-muted">por año</p>
                        <div class="alert alert-success py-2 px-3 d-inline-block">
                            <strong>¡Ahorra $60 al año!</strong>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-4">
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Todo lo del plan mensual</strong>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>2 meses gratis</strong>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Acceso anticipado a nuevas funciones
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Plantillas premium de eventos
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Exportación avanzada (PDF/Excel)
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Soporte prioritario 24/7
                        </li>
                    </ul>

                    <button type="button" class="btn btn-success btn-lg w-100 shadow-sm subscribe-btn" data-plan="annual">
                        <i class="bi bi-credit-card me-2"></i>Suscribirse Ahora
                    </button>

                    <p class="text-center text-muted small mt-3 mb-0">
                        <i class="bi bi-shield-check me-1"></i>Pago seguro con PayPal
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mt-5 pt-5">
        <h3 class="text-center mb-4">Preguntas Frecuentes</h3>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                ¿Puedo cancelar mi suscripción en cualquier momento?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Sí, puedes cancelar tu suscripción cuando lo desees desde tu cuenta. Seguirás teniendo acceso hasta el final del período pagado.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                ¿Qué sucede si no renuevo mi suscripción?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Tu cuenta volverá al plan gratuito. Podrás mantener solo 1 evento activo, pero tus datos se conservarán de manera segura.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                ¿Los pagos son seguros?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutamente. Todos los pagos se procesan a través de PayPal, uno de los sistemas de pago más seguros del mundo.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PayPal Modal -->
    <div class="modal fade" id="paypalModal" tabindex="-1" aria-labelledby="paypalModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 bg-primary text-white">
                    <h5 class="modal-title" id="paypalModalLabel">
                        <i class="bi bi-paypal me-2"></i>Procesando Suscripción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 text-center">
                    <!-- Loading State -->
                    <div id="paypalLoading">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <h5 class="mb-3">Abriendo ventana de pago seguro...</h5>
                        <p class="text-muted mb-4">Se abrirá una ventana emergente de PayPal.</p>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Importante:</strong> Si la ventana no se abre, verifica que tu navegador permita ventanas emergentes.
                        </div>
                    </div>

                    <!-- Processing State -->
                    <div id="paypalProcessing" style="display: none;">
                        <div class="mb-3">
                            <i class="bi bi-clock-history text-primary" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="mb-3">Esperando confirmación de pago...</h5>
                        <p class="text-muted mb-4">Completa el pago en la ventana de PayPal.</p>
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Verificando...</span>
                        </div>
                        <p class="small text-muted mt-2">Verificando estado cada 3 segundos...</p>
                    </div>

                    <!-- Success State -->
                    <div id="paypalSuccess" style="display: none;">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="mb-3 text-success">¡Suscripción Activada!</h5>
                        <p class="text-muted">Redirigiendo...</p>
                    </div>

                    <!-- Error State -->
                    <div id="paypalError" style="display: none;">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="mb-3">Error al procesar el pago</h5>
                        <p class="text-muted" id="paypalErrorMessage"></p>
                        <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">Cerrar</button>
                    </div>

                    <!-- Cancelled State -->
                    <div id="paypalCancelled" style="display: none;">
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="mb-3">Pago Cancelado</h5>
                        <p class="text-muted">Has cerrado la ventana de pago sin completar la suscripción.</p>
                        <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Transacción segura procesada por PayPal
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
    }

    .card {
        border-radius: 1rem;
    }

    .badge {
        font-size: 0.875rem;
        font-weight: 600;
    }

    .btn-lg {
        padding: 0.875rem 1.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #paypalModal .modal-content {
        border-radius: 1rem;
        overflow: hidden;
    }

    #paypalModal .modal-header {
        padding: 1.5rem;
    }

    .subscribe-btn {
        position: relative;
        overflow: hidden;
    }

    .subscribe-btn:disabled {
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subscribeButtons = document.querySelectorAll('.subscribe-btn');
    const modal = new bootstrap.Modal(document.getElementById('paypalModal'));
    const loadingDiv = document.getElementById('paypalLoading');
    const processingDiv = document.getElementById('paypalProcessing');
    const successDiv = document.getElementById('paypalSuccess');
    const errorDiv = document.getElementById('paypalError');
    const cancelledDiv = document.getElementById('paypalCancelled');
    const errorMessage = document.getElementById('paypalErrorMessage');

    let paypalWindow = null;
    let pollInterval = null;
    let windowCheckInterval = null;

    subscribeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const plan = this.dataset.plan;
            const originalHTML = this.innerHTML;

            // Disable button
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...';

            try {
                // Show modal
                modal.show();

                // Reset modal state
                showState('loading');

                // Make request to get PayPal URL
                const response = await fetch('{{ route("paypal.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ plan: plan })
                });

                const data = await response.json();

                if (data.success && data.approval_url) {
                    // Open PayPal in popup window
                    const width = 600;
                    const height = 700;
                    const left = (screen.width / 2) - (width / 2);
                    const top = (screen.height / 2) - (height / 2);

                    paypalWindow = window.open(
                        data.approval_url,
                        'PayPal',
                        `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
                    );

                    if (!paypalWindow) {
                        throw new Error('No se pudo abrir la ventana de PayPal. Por favor, permite ventanas emergentes y vuelve a intentarlo.');
                    }

                    // Change state to processing
                    setTimeout(() => {
                        showState('processing');
                        startPolling();
                        checkWindowClosed();
                    }, 1000);

                } else {
                    throw new Error(data.message || 'Error al iniciar el proceso de pago');
                }

            } catch (error) {
                console.error('Error:', error);
                showState('error');
                errorMessage.textContent = error.message;
            } finally {
                // Re-enable button
                this.disabled = false;
                this.innerHTML = originalHTML;
            }
        });
    });

    function showState(state) {
        loadingDiv.style.display = 'none';
        processingDiv.style.display = 'none';
        successDiv.style.display = 'none';
        errorDiv.style.display = 'none';
        cancelledDiv.style.display = 'none';

        switch(state) {
            case 'loading':
                loadingDiv.style.display = 'block';
                break;
            case 'processing':
                processingDiv.style.display = 'block';
                break;
            case 'success':
                successDiv.style.display = 'block';
                break;
            case 'error':
                errorDiv.style.display = 'block';
                break;
            case 'cancelled':
                cancelledDiv.style.display = 'block';
                break;
        }
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);

        pollInterval = setInterval(async function() {
            try {
                const response = await fetch('{{ route("paypal.check-status") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.subscribed) {
                    stopPolling();
                    if (paypalWindow && !paypalWindow.closed) {
                        paypalWindow.close();
                    }
                    showState('success');
                    setTimeout(() => {
                        modal.hide();
                        window.location.reload();
                    }, 2000);
                }
            } catch (error) {
                console.error('Poll error:', error);
            }
        }, 3000);
    }

    function checkWindowClosed() {
        if (windowCheckInterval) clearInterval(windowCheckInterval);

        windowCheckInterval = setInterval(function() {
            if (paypalWindow && paypalWindow.closed) {
                clearInterval(windowCheckInterval);

                // Check one last time if subscription completed
                setTimeout(async () => {
                    try {
                        const response = await fetch('{{ route("paypal.check-status") }}', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();

                        if (data.subscribed) {
                            showState('success');
                            setTimeout(() => {
                                modal.hide();
                                window.location.reload();
                            }, 2000);
                        } else {
                            stopPolling();
                            showState('cancelled');
                        }
                    } catch (error) {
                        stopPolling();
                        showState('cancelled');
                    }
                }, 1000);
            }
        }, 500);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
        if (windowCheckInterval) {
            clearInterval(windowCheckInterval);
            windowCheckInterval = null;
        }
    }

    // Handle modal close
    document.getElementById('paypalModal').addEventListener('hidden.bs.modal', function() {
        stopPolling();
        if (paypalWindow && !paypalWindow.closed) {
            paypalWindow.close();
        }
        showState('loading');
    });
});
</script>
@endpush
