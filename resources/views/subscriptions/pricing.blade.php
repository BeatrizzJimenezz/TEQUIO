@extends('layouts.app')

@section('header', 'Planes de Suscripción')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pricing.css') }}">
@endpush

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold mb-3 text-brand-deep">Elige tu plan</h1>
        <p class="lead text-muted">Desbloquea todas las funcionalidades de TEQUIO y gestiona eventos ilimitados</p>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Current Subscription Status -->
    @if(auth()->user()->hasActiveSubscription())
        <div class="alert alert-success border-0 shadow-sm mb-5 d-flex align-items-center p-4">
            <div class="bg-white rounded-circle p-3 text-success me-3 shadow-sm">
                <i class="bi bi-check-lg fs-3"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1">¡Ya tienes una suscripción activa!</h5>
                <p class="mb-0 opacity-75">
                    Plan actual: <strong>{{ ucfirst(auth()->user()->subscription->plan_id) }}</strong> 
                    <span class="mx-2">•</span>
                    Renovación: <strong>{{ auth()->user()->subscription->ends_at->format('d/m/Y') }}</strong>
                </p>
            </div>
        </div>
    @endif

    <!-- Pricing Cards -->
    <div class="row g-4 justify-content-center mb-5">
        
        <!-- Monthly Plan -->
        <div class="col-md-6 col-lg-5 col-xl-5">
            <div class="card pricing-card shadow-lg hover-lift">
                <div class="card-body p-4 p-lg-4 d-flex flex-column">

                    <div class="text-center mb-4">
                        <div class="badge badge-custom badge-monthly mb-3">
                            <i class="bi bi-calendar-month me-1"></i> Mensual
                        </div>
                        <h2 class="plan-title">$29<small class="fs-4 text-muted">.99</small></h2>
                        <p class="plan-period">facturado mensualmente</p>
                    </div>

                    <ul class="list-unstyled feature-list mb-5 flex-grow-1">
                        <li><i class="bi bi-check-circle-fill check-icon"></i> <strong>Eventos ilimitados</strong></li>
                        <li><i class="bi bi-check-circle-fill check-icon"></i> Gestión completa de componentes</li>
                        <li><i class="bi bi-check-circle-fill check-icon"></i> Equipo colaborativo</li>
                        <li><i class="bi bi-check-circle-fill check-icon"></i> Reportes básicos</li>
                        <li><i class="bi bi-check-circle-fill check-icon"></i> Soporte por correo</li>
                    </ul>

                    <button type="button" class="btn btn-plan btn-primary-plan shadow-sm subscribe-btn" data-plan="monthly" 
                        {{ auth()->user()->hasActiveSubscription() ? 'disabled' : '' }}>
                        <i class="bi bi-credit-card me-2"></i>
                        {{ auth()->user()->hasActiveSubscription() ? 'Plan Actual' : 'Seleccionar Mensual' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Annual Plan (Popular) -->
        <div class="col-md-6 col-lg-5 col-xl-5">
            <div class="card pricing-card shadow-lg hover-lift border-popular">

                <div class="w-500 translate-middle-x position-absolute top-0 start-50 mt-3">
                    <span class="badge badge-custom badge-popular px-4 py-2 rounded-pill">
                        <i class="bi bi-star-fill me-1"></i>Recomendado
                    </span>
                    </div>
                <div class="card-body p-4 p-lg-5 d-flex flex-column">

                    <div class="text-center mb-4 mt-3">
                        <div class="badge badge-custom badge-annual mb-3">
                            <i class="bi bi-calendar-check me-1"></i> Anual
                        </div>
                        <h2 class="plan-title">$299<small class="fs-4 text-muted">.99</small></h2>
                        <p class="plan-period">facturado anualmente</p>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">
                            ¡Ahorra 2 meses!
                        </span>
                    </div>

                    <ul class="list-unstyled feature-list mb-5 flex-grow-1">
                        <li><i class="bi bi-check-circle-fill check-icon"></i> <strong>Todo lo del plan mensual</strong></li>
                        <li><i class="bi bi-check-circle-fill check-icon"></i> Reportes avanzados (Excel/PDF)</li>
                        <li><i class="bi bi-check-circle-fill check-icon"></i> Plantillas premium</li>
                        <li><i class="bi bi-check-circle-fill check-icon"></i> Soporte prioritario 24/7</li>
                        <li><i class="bi bi-check-circle-fill check-icon"></i> Insignia de Organizador Pro</li>
                    </ul>

                    <button type="button" class="btn btn-plan btn-accent-plan shadow-sm subscribe-btn" data-plan="annual"
                        {{ auth()->user()->hasActiveSubscription() ? 'disabled' : '' }}>
                        <i class="bi bi-rocket-takeoff me-2"></i>
                        {{ auth()->user()->hasActiveSubscription() ? 'Plan Actual' : 'Seleccionar Anual' }}
                    </button>
                </div>
            </div>
        </div>


    </div>

    <!-- FAQ Section -->
    <div class="row justify-content-center pt-4">
        <div class="col-lg-8">
            <h3 class="text-center mb-4 fw-bold text-brand-deep">Preguntas Frecuentes</h3>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Puedo cancelar mi suscripción en cualquier momento?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Sí, puedes cancelar tu suscripción cuando lo desees desde la configuración de tu cuenta. Seguirás teniendo acceso premium hasta el final del período ya pagado.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Qué métodos de pago aceptan?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Procesamos todos nuestros pagos de forma segura a través de <strong>PayPal</strong>. Puedes usar tu saldo de PayPal o tarjetas de crédito/débito vinculadas.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="bi bi-shield-lock-fill me-1"></i>
                    Pagos seguros y encriptados. No almacenamos tu información financiera.
                </small>
            </div>
        </div>
    </div>

    <!-- PayPal Modal -->
    <div class="modal fade" id="paypalModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-paypal me-2"></i>Procesando Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 text-center">
                    
                    <!-- 1. Loading -->
                    <div id="paypalLoading">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h5 class="fw-bold mb-2 text-brand-deep">Contactando a PayPal...</h5>
                        <p class="text-muted mb-0 small">Se abrirá una ventana segura para completar tu pago.</p>
                    </div>

                    <!-- 2. Processing -->
                    <div id="paypalProcessing" style="display: none;">
                        <div class="mb-3 text-primary">
                            <i class="bi bi-hourglass-split display-1"></i>
                        </div>
                        <h5 class="fw-bold mb-2 text-brand-deep">Esperando confirmación...</h5>
                        <p class="text-muted mb-3">Por favor, completa el pago en la ventana emergente.</p>
                        <div class="d-flex justify-content-center align-items-center gap-2 text-muted small">
                            <span class="spinner-grow spinner-grow-sm" role="status"></span>
                            Verificando estado...
                        </div>
                    </div>

                    <!-- 3. Success -->
                    <div id="paypalSuccess" style="display: none;">
                        <div class="mb-3 text-success">
                            <i class="bi bi-check-circle-fill display-1"></i>
                        </div>
                        <h4 class="fw-bold text-success mb-2">¡Pago Exitoso!</h4>
                        <p class="text-muted">Tu suscripción se ha activado correctamente.</p>
                    </div>

                    <!-- 4. Error -->
                    <div id="paypalError" style="display: none;">
                        <div class="mb-3 text-danger">
                            <i class="bi bi-x-circle-fill display-1"></i>
                        </div>
                        <h5 class="fw-bold text-danger mb-2">Error en el proceso</h5>
                        <p class="text-muted small mb-3" id="paypalErrorMessage">Ocurrió un error inesperado.</p>
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
                    </div>

                    <!-- 5. Cancelled -->
                    <div id="paypalCancelled" style="display: none;">
                        <div class="mb-3 text-warning">
                            <i class="bi bi-exclamation-circle-fill display-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Pago Cancelado</h5>
                        <p class="text-muted small mb-3">Cerraste la ventana antes de finalizar.</p>
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Intentar de nuevo</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- CONFIGURACIÓN JS (Puente de datos seguro) --}}
    <div id="pricing-config" 
         data-csrf="{{ csrf_token() }}"
         data-route-subscribe="{{ route('paypal.subscribe') }}"
         data-route-check="{{ route('paypal.check-status') }}"
         data-redirect-url="{{ route('dashboard') }}" {{-- O donde quieras redirigir al éxito --}}
         style="display: none;">
    </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pricing.js') }}"></script>
@endpush