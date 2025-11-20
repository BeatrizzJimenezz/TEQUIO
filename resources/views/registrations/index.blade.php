@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Header --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #4499BB !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #0C2340;">
                                <i class="bi bi-ticket-perforated me-2" style="color: #4499BB;"></i>
                                Mis Inscripciones
                            </h3>
                            <p class="text-muted mb-0">Gestiona tus inscripciones a eventos y actividades</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn text-white" style="background-color: #8CC63F;">
                            <i class="bi bi-search"></i> Explorar Eventos
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @forelse($registrations as $registration)
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h4 class="fw-bold mb-1" style="color: #0C2340;">
                                            {{ $registration->component->name }}
                                        </h4>
                                        <h6 class="text-muted mb-2">
                                            <i class="bi bi-calendar-event me-1" style="color: #4499BB;"></i>
                                            {{ $registration->component->event->name }}
                                        </h6>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <span class="badge" style="background-color: #0C2340;">
                                        {{ ucfirst($registration->component->type) }}
                                    </span>
                                    <span class="badge" style="background-color: #4499BB;">
                                        {{ ucfirst($registration->component->modality) }}
                                    </span>
                                    @if($registration->component->level)
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($registration->component->level) }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-muted mb-3">
                                    {{ Str::limit($registration->component->description, 150) }}
                                </p>

                                @if($registration->component->location)
                                    <p class="mb-2">
                                        <i class="bi bi-geo-alt me-1" style="color: #8CC63F;"></i>
                                        <span class="text-muted">{{ $registration->component->location }}</span>
                                    </p>
                                @endif

                                <div class="mb-3">
                                    <strong style="color: #0C2340;">
                                        <i class="bi bi-clock-history me-1" style="color: #4499BB;"></i>
                                        Horarios:
                                    </strong>
                                    <ul class="list-unstyled ms-4 mt-2 mb-0">
                                        @foreach($registration->component->schedules as $schedule)
                                            <li class="mb-1">
                                                <i class="bi bi-calendar3 me-1" style="color: #8CC63F;"></i>
                                                {{ $schedule->date->format('d/m/Y') }}
                                                <span class="text-muted">
                                                    de {{ substr($schedule->start_time, 0, 5) }} a {{ substr($schedule->end_time, 0, 5) }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <p class="mb-0">
                                    <small class="text-muted">
                                        <i class="bi bi-check-circle me-1" style="color: #8CC63F;"></i>
                                        Inscrito el: {{ $registration->registered_at->format('d/m/Y H:i') }}
                                    </small>
                                </p>
                            </div>

                            <div class="col-md-4">
                                <div class="text-center p-3 rounded" style="background-color: #f8f9fa;">
                                    <strong class="d-block mb-2" style="color: #0C2340;">Tu Ticket</strong>
                                    <div id="qr-{{ $registration->id }}" class="my-3 d-flex justify-content-center"></div>
                                    <p class="mb-3">
                                        <code class="px-2 py-1 rounded" style="background-color: #e9ecef;">
                                            {{ $registration->ticket_qr }}
                                        </code>
                                    </p>

                                    @php
                                        $canCancel = $registration->canBeCancelled();
                                        $daysUntil = $registration->daysUntilStart();
                                    @endphp

                                    @if($canCancel)
                                        <form action="{{ route('registrations.destroy', $registration->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta inscripción?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                <i class="bi bi-x-circle"></i> Cancelar Inscripción
                                            </button>
                                        </form>
                                    @else
                                        <div class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.85rem;">
                                            <i class="bi bi-info-circle me-1"></i>
                                            No se puede cancelar.
                                            @if($daysUntil !== null && $daysUntil >= 0)
                                                <br><small>Inicia en {{ $daysUntil }} día(s)</small>
                                            @elseif($daysUntil !== null && $daysUntil < 0)
                                                <br><small>La actividad ya comenzó</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-ticket-perforated" style="font-size: 4rem; color: #C8CCC9;"></i>
                        </div>
                        <h5 class="text-muted mb-3">No tienes inscripciones activas</h5>
                        <p class="text-muted mb-4">Explora los eventos disponibles y regístrate en las actividades que te interesen.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-lg text-white" style="background-color: #8CC63F;">
                            <i class="bi bi-search me-1"></i> Explorar Eventos
                        </a>
                    </div>
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
