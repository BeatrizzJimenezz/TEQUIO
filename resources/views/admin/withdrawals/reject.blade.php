@extends('layouts.app')

@section('header', 'Rechazar Retiro')

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
                <h4 class="fw-bold mb-0">Rechazar Solicitud #{{ $withdrawal->id }}</h4>
                <p class="mb-0 small opacity-75">El dinero será devuelto al balance del organizador.</p>
            </div>
        </div>
        <i class="bi bi-x-circle hero-pattern"></i>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="admin-card">
                
                <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                    @csrf

                    <div class="summary-box reject-mode">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="summary-label">Monto a Devolver</div>
                                <div class="summary-amount">${{ number_format($withdrawal->amount, 2) }}</div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger fw-bold px-3 py-2 rounded-pill">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reembolso Automático
                                </span>
                            </div>
                        </div>

                        <div class="organizer-info">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white border rounded-circle p-2 text-secondary">
                                    <i class="bi bi-person-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $withdrawal->organizer->name }}</div>
                                    <div class="small text-muted d-flex align-items-center">
                                        <i class="bi bi-envelope me-1"></i> {{ $withdrawal->organizer->email }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="warning-box">
                        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0"></i>
                        <div>
                            <strong>Atención:</strong> Al confirmar esta acción, el estado del retiro cambiará a "Rechazado" y los fondos (${{ number_format($withdrawal->amount, 2) }}) se sumarán nuevamente al balance disponible del organizador de forma inmediata.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reason" class="form-label">Motivo del Rechazo <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" rows="4" class="form-control mb-2" required
                                  placeholder="Explica por qué se rechaza este retiro. Esta información será visible para el organizador.">{{ old('reason') }}</textarea>
                        
                        <div class="mt-3">
                            <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Razones Comunes (Click para usar)</label>
                            <div class="reason-tags">
                                <button type="button" class="reason-btn" onclick="setReason('Información de cuenta PayPal incorrecta o no válida.')">
                                    Cuenta PayPal inválida
                                </button>
                                <button type="button" class="reason-btn" onclick="setReason('Balance insuficiente o inconsistencia en los fondos reportados.')">
                                    Balance insuficiente
                                </button>
                                <button type="button" class="reason-btn" onclick="setReason('Se requiere verificación de identidad adicional antes de procesar.')">
                                    Verificación requerida
                                </button>
                                <button type="button" class="reason-btn" onclick="setReason('Actividad sospechosa detectada en la cuenta.')">
                                    Actividad sospechosa
                                </button>
                            </div>
                        </div>
                        @error('reason')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-3 pt-3 border-top">
                        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold text-secondary flex-fill">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-reject flex-fill shadow" onclick="return confirm('¿Estás seguro de rechazar este retiro? Esta acción es irreversible.')">
                            <i class="bi bi-x-circle me-2"></i> Confirmar Rechazo
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setReason(text) {
        const textarea = document.getElementById('reason');
        textarea.value = text;
        textarea.focus();
    }
</script>
@endpush

@endsection