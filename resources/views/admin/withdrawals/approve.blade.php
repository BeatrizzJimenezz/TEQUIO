@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.withdrawals.index') }}" class="text-decoration-none">Retiros</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="text-decoration-none">Retiro #{{ $withdrawal->id }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aprobar</li>
                </ol>
            </nav>

            <h1 class="h3 fw-bold text-dark mb-4">Aprobar Retiro</h1>

            <!-- Resumen del Retiro -->
            <div class="card bg-success text-white shadow mb-4 border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <p class="mb-1 opacity-75">Monto a Procesar</p>
                            <h2 class="display-6 fw-bold mb-0">${{ number_format($withdrawal->amount, 2) }}</h2>
                        </div>
                        <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                    </div>
                    <div class="row pt-3 border-top border-white border-opacity-25">
                        <div class="col-6">
                            <p class="small mb-0 opacity-75">Organizador</p>
                            <p class="fw-medium mb-0">{{ $withdrawal->organizer->name }}</p>
                        </div>
                        <div class="col-6">
                            <p class="small mb-0 opacity-75">Cuenta PayPal</p>
                            <p class="fw-medium mb-0">{{ $withdrawal->paypal_email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Aprobación -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">Método de Procesamiento</h4>

                    <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST">
                        @csrf

                        <!-- Método de Proceso -->
                        <div class="mb-4">
                            <label class="form-label fw-medium mb-3">Selecciona cómo procesar el retiro:</label>

                            <div class="d-grid gap-3">
                                <!-- PayPal Automático -->
                                <label class="card card-body border-2 cursor-pointer hover-border-primary transition-all" style="cursor: pointer;">
                                    <div class="d-flex">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="process_method" id="method_paypal" value="paypal" checked>
                                        </div>
                                        <div class="ms-2 flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-paypal text-primary me-2"></i>
                                                <span class="fw-bold text-dark">Procesar con PayPal Payouts</span>
                                                <span class="badge bg-primary-subtle text-primary ms-2">Recomendado</span>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                El pago se procesará automáticamente a través de la API de PayPal Payouts. El organizador recibirá el dinero en su cuenta de PayPal.
                                            </p>
                                        </div>
                                    </div>
                                </label>

                                <!-- Manual -->
                                <label class="card card-body border-2 cursor-pointer hover-border-primary transition-all" style="cursor: pointer;">
                                    <div class="d-flex">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="process_method" id="method_manual" value="manual">
                                        </div>
                                        <div class="ms-2 flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-pencil-square text-secondary me-2"></i>
                                                <span class="fw-bold text-dark">Procesamiento Manual</span>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                Procesarás el pago manualmente fuera del sistema y proporcionarás el ID de transacción.
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Campo para ID de transacción manual (se muestra solo si selecciona manual) -->
                        <div id="manual-fields" class="mb-4 d-none">
                            <label for="transaction_id" class="form-label fw-medium">
                                ID de Transacción <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="transaction_id"
                                id="transaction_id"
                                class="form-control"
                                placeholder="Ejemplo: TXN123456789"
                            >
                            <div class="form-text">
                                Ingresa el ID de transacción de PayPal o el número de referencia del pago.
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-medium">
                                Notas (Opcional)
                            </label>
                            <textarea
                                name="notes"
                                id="notes"
                                rows="3"
                                class="form-control"
                                placeholder="Agrega notas sobre este retiro..."
                            >{{ old('notes') }}</textarea>
                        </div>

                        <!-- Advertencia -->
                        <div class="alert alert-warning d-flex align-items-start mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                            <div>
                                <strong>Importante:</strong> Esta acción no se puede deshacer. Asegúrate de que la información sea correcta antes de aprobar el retiro.
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-success btn-lg flex-fill">
                                <i class="bi bi-check-circle me-2"></i>Confirmar y Procesar Retiro
                            </button>
                            <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="btn btn-light btn-lg flex-fill border">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="process_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const manualFields = document.getElementById('manual-fields');
        const transactionInput = document.getElementById('transaction_id');

        if (this.value === 'manual') {
            manualFields.classList.remove('d-none');
            transactionInput.setAttribute('required', 'required');
        } else {
            manualFields.classList.add('d-none');
            transactionInput.removeAttribute('required');
        }
    });
});
</script>
@endpush
@endsection
