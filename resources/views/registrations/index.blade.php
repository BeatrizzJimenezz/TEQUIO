@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/registrations.css') }}">
@endpush

@section('content')
<div class="container py-4">
    
    <div class="hero-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-4">
            <div class="d-none d-md-block p-3 rounded-circle" style="background: rgba(255,255,255,0.1);">
                <i class="bi bi-ticket-perforated-fill fs-2"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0">Mis inscripciones</h2>
                <p class="mb-0 opacity-75">Gestiona tus pases y accesos a eventos</p>
            </div>
        </div>
        
        <a href="{{ route('dashboard') }}" class="btn btn-light text-brand-deep fw-bold rounded-pill px-4 shadow-sm">
            <i class="bi bi-search me-2"></i>Explorar eventos
        </a>

        <i class="bi bi-ticket-detailed-fill hero-pattern text-white"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            @forelse($registrations as $registration)
                <div class="reg-card">
                    <div class="row g-0">
                        <div class="col-lg-8 border-end border-light">
                            <div class="reg-header d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge border badge-pill badge-activity">
                                            {{ ucfirst($registration->component->type) }}
                                        </span>
                                        <span class="badge border badge-pill badge-component">
                                            {{ ucfirst($registration->component->modality) }}
                                        </span>
                                        @if($registration->payment_status === 'free')
                                            <span class="badge bg-opacity-10 badge-pill badge-payment-free">Gratis</span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info badge-pill">Pagado</span>
                                        @endif
                                    </div>
                                    <h4 class="fw-bold text-brand-deep mb-1">{{ $registration->component->name }}</h4>
                                    <h6 class="text-secondary fw-normal mb-0">
                                        <i class="bi bi-calendar-event me-1"></i> {{ $registration->component->event->name }}
                                    </h6>
                                </div>
                                
                                <a href="{{ route('event.show', $registration->component->event->id) }}" class="btn btn-outline-tequio btn-sm rounded-pill fw-bold" title="Ver detalles del evento">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Ver evento
                                </a>
                            </div>

                            <div class="reg-body">
                                <p class="text-muted small mb-4">
                                    {{ Str::limit($registration->component->description, 180) }}
                                </p>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold text-uppercase mb-2">Horarios</label>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($registration->component->schedules->take(3) as $schedule)
                                                <li class="mb-1 text-dark">
                                                    <i class="bi bi-clock text-brand-accent me-2"></i>
                                                    {{ $schedule->date->format('d/m/Y') }} 
                                                    <span class="text-muted">({{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }})</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold text-uppercase mb-2">Ubicación</label>
                                        <p class="mb-0 small text-dark">
                                            <i class="bi bi-geo-alt-fill text-brand-accent me-2"></i>
                                            {{ $registration->component->location ?? 'Enlace virtual pendiente' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="ticket-stub">
                                <h6 class="fw-bold text-uppercase text-muted small mb-3">Tu pase de acceso</h6>
                                
                                <div class="qr-box">
                                    <div id="qr-{{ $registration->id }}"></div>
                                </div>

                                <code class="bg-white px-3 py-1 rounded border text-brand-deep fw-bold mb-4">
                                    {{ $registration->ticket_qr }}
                                </code>

                                @php
                                    $canCancel = $registration->canBeCancelled();
                                    $daysUntil = $registration->daysUntilStart();
                                @endphp

                                @if($canCancel)
                                    <form action="{{ route('registrations.destroy', $registration->id) }}" method="POST" class="w-100"
                                          onsubmit="return confirm('¿Cancelar inscripción? Perderás tu lugar.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm rounded-pill">
                                            <i class="bi bi-x-circle me-1"></i> Cancelar inscripción
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning py-2 px-3 mb-0 w-100 text-center small border-0 bg-opacity-10 bg-warning text-warning fw-bold">
                                        <i class="bi bi-lock-fill me-1"></i> No cancelable
                                        <div class="fw-normal mt-1" style="font-size: 0.75rem;">
                                            @if($daysUntil !== null && $daysUntil < 0)
                                                El evento ya inició/finalizó
                                            @else
                                                Política de tiempo límite
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                        <i class="bi bi-ticket-perforated text-muted display-4 opacity-50"></i>
                    </div>
                    <h4 class="fw-bold text-muted">Sin inscripciones activas</h4>
                    <p class="text-muted mb-4">Aún no te has registrado a ningún evento. ¡Descubre qué hay de nuevo!</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4 fw-bold" style="background-color: #8CC63F; border:none;">
                        Explorar eventos
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($registrations as $registration)
            new QRCode(document.getElementById("qr-{{ $registration->id }}"), {
                text: "{{ $registration->ticket_qr }}",
                width: 120,
                height: 120,
                colorDark: "#0C2340",
                colorLight: "#ffffff"
            });
        @endforeach
    });
</script>
@endpush