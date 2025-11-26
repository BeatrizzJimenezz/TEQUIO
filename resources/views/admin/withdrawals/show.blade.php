@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-withdrawals-approve.css') }}">
@endpush

@section('content')
<div class="container py-4">

    {{-- 1. Hero Header --}}
    <div class="hero-header-sm d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-light rounded-circle p-2" 
               style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center; border-color: rgba(255,255,255,0.3);">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Solicitud #{{ $withdrawal->id }}</h4>
                <p class="mb-0 small opacity-75">Detalles completos de la transacción.</p>
            </div>
        </div>
        
        <div>
            @if($withdrawal->status === 'pending')
                <span class="badge bg-warning text-dark border border-warning rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-clock me-1"></i> Pendiente
                </span>
            @elseif($withdrawal->status === 'completed')
                <span class="badge bg-success border border-success rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Completado
                </span>
            @else
                <span class="badge bg-danger border border-danger rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-x-circle me-1"></i> Rechazado
                </span>
            @endif
        </div>

        <i class="bi bi-file-earmark-text hero-pattern"></i>
    </div>

    <div class="row g-4 justify-content-center">
        
        <div class="col-lg-8">
            <div class="admin-card">
                
                <div class="summary-box {{ $withdrawal->status === 'rejected' ? 'reject-mode' : '' }}">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="summary-label">Monto Solicitado</div>
                            <div class="summary-amount">${{ number_format($withdrawal->amount, 2) }}</div>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 bg-white rounded-pill border shadow-sm">
                                <i class="bi bi-paypal text-primary"></i>
                                <span class="fw-bold text-dark">{{ $withdrawal->paypal_email }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="organizer-info">
                        <div class="d-flex align-items-center gap-3">
                            @if($withdrawal->organizer->profile_photo)
                                <img src="{{ asset('storage/' . $withdrawal->organizer->profile_photo) }}" 
                                     class="rounded-circle border" width="48" height="48" style="object-fit:cover;">
                            @else
                                <div class="bg-white border rounded-circle p-2 text-secondary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-person-fill fs-4"></i>
                                </div>
                            @endif
                            
                            <div>
                                <div class="summary-label mb-0">Solicitado por</div>
                                <div class="fw-bold text-dark fs-5">{{ $withdrawal->organizer->name }}</div>
                                <div class="small text-muted">{{ $withdrawal->organizer->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-brand-deep mb-4 ps-2 border-start border-4 border-primary">
                    Detalles de la Transacción
                </h5>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Fecha de Solicitud</label>
                        <p class="fw-medium text-dark fs-6 mb-0">
                            {{ $withdrawal->requested_at->format('d/m/Y') }}
                            <span class="text-muted small ms-1">({{ $withdrawal->requested_at->format('H:i A') }})</span>
                        </p>
                        <small class="text-muted">{{ $withdrawal->requested_at->diffForHumans() }}</small>
                    </div>

                    @if($withdrawal->processed_at)
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Fecha de Proceso</label>
                            <p class="fw-medium text-dark fs-6 mb-0">
                                {{ $withdrawal->processed_at->format('d/m/Y') }}
                                <span class="text-muted small ms-1">({{ $withdrawal->processed_at->format('H:i A') }})</span>
                            </p>
                        </div>
                    @endif

                    @if($withdrawal->paypal_transaction_id)
                        <div class="col-12">
                            <div class="p-3 bg-light rounded border">
                                <label class="form-label text-muted small mb-1 d-block">ID de Transacción / Referencia</label>
                                <code class="fs-6 fw-bold text-primary">{{ $withdrawal->paypal_transaction_id }}</code>
                            </div>
                        </div>
                    @endif

                    @if($withdrawal->notes)
                        <div class="col-12">
                            <label class="form-label text-muted small mb-1">Notas / Motivo</label>
                            <div class="p-3 bg-light rounded border text-secondary">
                                {{ $withdrawal->notes }}
                            </div>
                        </div>
                    @endif
                </div>

                @if($withdrawal->organizer->organizerBalance)
                    <div class="mt-5 pt-4 border-top">
                        <h6 class="fw-bold text-secondary text-uppercase small mb-3">Estado de Cuenta del Organizador</h6>
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="p-2 border rounded text-center bg-white">
                                    <small class="d-block text-muted mb-1" style="font-size: 0.7rem;">DISPONIBLE</small>
                                    <span class="fw-bold text-success">${{ number_format($withdrawal->organizer->organizerBalance->available_balance, 2) }}</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded text-center bg-white">
                                    <small class="d-block text-muted mb-1" style="font-size: 0.7rem;">GANADO</small>
                                    <span class="fw-bold text-primary">${{ number_format($withdrawal->organizer->organizerBalance->total_earned, 2) }}</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded text-center bg-white">
                                    <small class="d-block text-muted mb-1" style="font-size: 0.7rem;">RETIRADO</small>
                                    <span class="fw-bold text-secondary">${{ number_format($withdrawal->organizer->organizerBalance->total_withdrawn, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card h-100 d-flex flex-column">
                <h5 class="fw-bold text-dark mb-4 border-bottom pb-3">Acciones</h5>

                @if($withdrawal->status === 'pending')
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.withdrawals.approve.form', $withdrawal) }}" 
                           class="btn-approve text-center text-decoration-none shadow-sm">
                            <i class="bi bi-check-circle me-2"></i> Aprobar Retiro
                        </a>
                        
                        <a href="{{ route('admin.withdrawals.reject.form', $withdrawal) }}" 
                           class="btn-reject text-center text-decoration-none shadow-sm">
                            <i class="bi bi-x-circle me-2"></i> Rechazar Retiro
                        </a>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded border text-center">
                        <small class="text-muted d-block mb-1">Tiempo transcurrido</small>
                        <span class="fw-bold text-dark">{{ $withdrawal->requested_at->diffForHumans(null, true) }}</span>
                    </div>
                @else
                    <div class="text-center py-4">
                        @if($withdrawal->status === 'completed')
                            <div class="mb-3 text-success">
                                <i class="bi bi-check-circle-fill display-4"></i>
                            </div>
                            <h6 class="fw-bold">Procesado Exitosamente</h6>
                            <p class="text-muted small">Los fondos han sido transferidos.</p>
                        @else
                            <div class="mb-3 text-danger">
                                <i class="bi bi-x-circle-fill display-4"></i>
                            </div>
                            <h6 class="fw-bold">Solicitud Rechazada</h6>
                            <p class="text-muted small">Los fondos fueron devueltos al balance.</p>
                        @endif
                    </div>
                @endif

                <div class="mt-auto pt-4">
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-light w-100 border fw-bold text-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-2"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection