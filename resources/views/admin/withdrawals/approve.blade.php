@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-withdrawals-approve.css') }}">
@endpush

@section('content')
<div class="container py-4">

    <div class="hero-header-sm d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-light rounded-circle p-2" 
               style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center; border-color: rgba(255,255,255,0.3);">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Aprobar Solicitud #{{ $withdrawal->id }}</h4>
                <p class="mb-0 small opacity-75">Confirma los detalles del pago antes de procesar.</p>
            </div>
        </div>
        <i class="bi bi-check-circle hero-pattern"></i>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="admin-card">
                
                <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST">
                    @csrf

                    <div class="summary-box">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="summary-label">Monto a Transferir</div>
                                <div class="summary-amount">${{ number_format($withdrawal->amount, 2) }}</div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <span class="badge bg-white text-success border border-success fw-bold px-3 py-2 rounded-pill">
                                    <i class="bi bi-wallet2 me-1"></i> Fondos Disponibles
                                </span>
                            </div>
                        </div>

                        <div class="organizer-info">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light rounded-circle p-2 text-primary">
                                    <i class="bi bi-person-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $withdrawal->organizer->name }}</div>
                                    <div class="small text-muted d-flex align-items-center">
                                        <i class="bi bi-paypal me-1"></i> {{ $withdrawal->paypal_email }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label mb-3">Método de Procesamiento</label>
                        <div class="row g-3">
                            
                            <div class="col-md-6">
                                <input class="method-selector" type="radio" name="process_method" id="method_paypal" value="paypal" checked>
                                <label class="method-card" for="method_paypal">
                                    <i class="bi bi-check-circle-fill check-icon"></i>
                                    <div class="method-icon-box icon-paypal">
                                        <i class="bi bi-paypal"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">PayPal Payouts</h6>
                                    <p class="text-muted small mb-0 lh-sm">
                                        Transferencia automática vía API. Rápido y seguro.
                                        <span class="badge bg-primary-subtle text-primary ms-1">Recomendado</span>
                                    </p>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <input class="method-selector" type="radio" name="process_method" id="method_manual" value="manual">
                                <label class="method-card" for="method_manual">
                                    <i class="bi bi-check-circle-fill check-icon"></i>
                                    <div class="method-icon-box icon-manual">
                                        <i class="bi bi-pencil-square"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Manual / Externo</h6>
                                    <p class="text-muted small mb-0 lh-sm">
                                        Ingresa el ID de transacción si pagaste fuera del sistema.
                                    </p>
                                </label>
                            </div>

                        </div>
                    </div>

                    <div id="manual-fields" class="mb-4 p-4 bg-light rounded-3 border d-none">
                        <label for="transaction_id" class="form-label">
                            ID de Transacción / Referencia <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="transaction_id" id="transaction_id" 
                               class="form-control form-control-lg" 
                               placeholder="Ej. 9BX123456789">
                        <div class="form-text small">
                            Ingresa el código de confirmación generado por tu banco o PayPal.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Notas Internas (Opcional)</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control" 
                                  placeholder="Detalles adicionales para el registro..."></textarea>
                    </div>

                    <div class="d-flex gap-3 pt-3 border-top">
                        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-secondary flex-fill">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-approve flex-fill shadow">
                            <i class="bi bi-check-lg me-2"></i> Confirmar Transferencia
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="process_method"]');
    const manualFields = document.getElementById('manual-fields');
    const transactionInput = document.getElementById('transaction_id');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'manual') {
                manualFields.classList.remove('d-none');
                transactionInput.setAttribute('required', 'required');
                // Pequeña animación de entrada
                manualFields.style.opacity = 0;
                setTimeout(() => { 
                    manualFields.style.transition = 'opacity 0.3s';
                    manualFields.style.opacity = 1;
                }, 10);
            } else {
                manualFields.classList.add('d-none');
                transactionInput.removeAttribute('required');
            }
        });
    });
});
</script>
@endpush

@endsection