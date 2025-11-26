@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.withdrawals.index') }}">Retiros</a></li>
                    <li class="breadcrumb-item active">Retiro #{{ $withdrawal->id }}</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 fw-bold">Detalle del Retiro #{{ $withdrawal->id }}</h1>
                <div>
                    @if($withdrawal->status === 'pending')
                        <span class="badge bg-warning px-3 py-2">
                            <i class="bi bi-clock me-1"></i>
                            Pendiente
                        </span>
                    @elseif($withdrawal->status === 'completed')
                        <span class="badge bg-success px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i>
                            Completado
                        </span>
                    @else
                        <span class="badge bg-danger px-3 py-2">
                            <i class="bi bi-x-circle me-1"></i>
                            Rechazado
                        </span>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                <!-- Información del Retiro -->
                <div class="col-12 col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Información del Retiro</h5>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Monto Solicitado</label>
                                    <h2 class="mb-0">${{ number_format($withdrawal->amount, 2) }}</h2>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Cuenta PayPal</label>
                                    <p class="fs-5 mb-0">{{ $withdrawal->paypal_email }}</p>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Fecha de Solicitud</label>
                                    <p class="mb-0">{{ $withdrawal->requested_at->format('d/m/Y H:i') }}</p>
                                    <small class="text-muted">{{ $withdrawal->requested_at->diffForHumans() }}</small>
                                </div>
                                @if($withdrawal->processed_at)
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Fecha de Proceso</label>
                                    <p class="mb-0">{{ $withdrawal->processed_at->format('d/m/Y H:i') }}</p>
                                    <small class="text-muted">{{ $withdrawal->processed_at->diffForHumans() }}</small>
                                </div>
                                @endif
                            </div>

                            @if($withdrawal->paypal_transaction_id)
                            <div class="mb-4">
                                <label class="form-label text-muted small">ID de Transacción PayPal</label>
                                <p class="mb-0 font-monospace">{{ $withdrawal->paypal_transaction_id }}</p>
                            </div>
                            @endif

                            @if($withdrawal->notes)
                            <div>
                                <label class="form-label text-muted small">Notas</label>
                                <p class="mb-0">{{ $withdrawal->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información del Organizador -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Organizador</h5>

                            <div class="mb-4">
                                <p class="fs-5 fw-medium mb-1">{{ $withdrawal->organizer->name }}</p>
                                <p class="text-muted mb-0">{{ $withdrawal->organizer->email }}</p>
                            </div>

                            @if($withdrawal->organizer->organizerBalance)
                            <div class="row g-3 mt-3 pt-3 border-top">
                                <div class="col-4">
                                    <label class="form-label text-muted small">Balance Disponible</label>
                                    <p class="fs-5 fw-bold text-success mb-0">
                                        ${{ number_format($withdrawal->organizer->organizerBalance->available_balance, 2) }}
                                    </p>
                                </div>
                                <div class="col-4">
                                    <label class="form-label text-muted small">Total Ganado</label>
                                    <p class="fs-5 fw-bold text-primary mb-0">
                                        ${{ number_format($withdrawal->organizer->organizerBalance->total_earned, 2) }}
                                    </p>
                                </div>
                                <div class="col-4">
                                    <label class="form-label text-muted small">Total Retirado</label>
                                    <p class="fs-5 fw-bold text-info mb-0">
                                        ${{ number_format($withdrawal->organizer->organizerBalance->total_withdrawn, 2) }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pagos Recientes del Organizador -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Pagos Recientes del Organizador</h5>

                            <div class="list-group list-group-flush">
                                @forelse($recentPayments as $payment)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-1 fw-medium">{{ $payment->component->name }}</p>
                                            <small class="text-muted">{{ $payment->registration->user->name }}</small>
                                        </div>
                                        <div class="text-end ms-3">
                                            <p class="mb-1 fw-bold text-success">+${{ number_format($payment->organizer_amount, 2) }}</p>
                                            <small class="text-muted">{{ $payment->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted py-3 mb-0">No hay pagos recientes</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="col-12 col-lg-4">
                    <div class="card sticky-top" style="top: 1rem;">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Acciones</h5>

                            @if($withdrawal->status === 'pending')
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.withdrawals.approve.form', $withdrawal) }}"
                                       class="btn btn-success">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Aprobar Retiro
                                    </a>
                                    <a href="{{ route('admin.withdrawals.reject.form', $withdrawal) }}"
                                       class="btn btn-danger">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Rechazar Retiro
                                    </a>
                                    <a href="{{ route('admin.withdrawals.index') }}"
                                       class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Volver
                                    </a>
                                </div>
                            @else
                                <div class="d-grid">
                                    <a href="{{ route('admin.withdrawals.index') }}"
                                       class="btn btn-primary">
                                        <i class="bi bi-list me-2"></i>
                                        Volver a la Lista
                                    </a>
                                </div>
                            @endif

                            @if($withdrawal->status !== 'pending')
                            <div class="mt-4 pt-4 border-top">
                                <h6 class="mb-2">Estado Final</h6>
                                <p class="text-muted small mb-0">
                                    @if($withdrawal->status === 'completed')
                                        Este retiro ha sido procesado exitosamente.
                                    @else
                                        Este retiro fue rechazado y los fondos devueltos al organizador.
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
