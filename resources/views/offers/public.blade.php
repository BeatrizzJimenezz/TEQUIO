@extends('layouts.app')

@section('header', 'Ofertas Abiertas')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-brand-deep mb-1">
                <i class="bi bi-megaphone-fill me-2"></i>Ofertas Abiertas
            </h2>
            <p class="text-muted mb-0">Explora las oportunidades para participar como ponente o tallerista en nuestros eventos.</p>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($offers as $offer)
            <div class="col-12">
                <div class="card-admin h-100">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="mb-0 fw-bold text-brand-deep me-3">{{ $offer->name }}</h4>
                                    <span class="badge bg-brand-accent text-white rounded-pill px-3 py-2">
                                        <i class="bi bi-megaphone-fill me-1"></i>Oferta Abierta
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="bi bi-calendar-event-fill text-brand-main me-2"></i>
                                        Evento: <span class="fw-bold text-dark">{{ $offer->event->name }}</span>
                                    </h6>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    @php
                                        $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
                                        $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                        $levelLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
                                    @endphp
                                    
                                    <span class="badge bg-brand-deep text-white px-3 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-tag-fill me-1"></i>{{ $typeLabels[$offer->type] ?? ucfirst($offer->type) }}
                                    </span>
                                    
                                    <span class="badge bg-brand-main text-white px-3 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-laptop me-1"></i>{{ $modalityLabels[$offer->modality] ?? ucfirst($offer->modality) }}
                                    </span>

                                    @if($offer->level)
                                        <span class="badge bg-secondary text-white px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-bar-chart-fill me-1"></i>{{ $levelLabels[$offer->level] ?? ucfirst($offer->level) }}
                                        </span>
                                    @endif

                                    @if($offer->capacity)
                                        <span class="badge bg-info text-white px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-people-fill me-1"></i>{{ $offer->capacity }} cupos
                                        </span>
                                    @endif

                                    @if($offer->organizer_cost)
                                        <span class="badge bg-success text-white px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-cash-stack me-1"></i>Remunerado
                                        </span>
                                    @endif
                                </div>

                                <div class="bg-light p-3 rounded-3 mb-3">
                                    <h6 class="fw-bold text-brand-deep mb-2">Descripción</h6>
                                    <p class="text-secondary mb-0">{{ Str::limit($offer->description, 250) }}</p>
                                </div>

                                @if($offer->instructor_requirements)
                                    <div class="mb-3">
                                        <h6 class="fw-bold text-brand-deep mb-2">
                                            <i class="bi bi-check-circle-fill text-brand-accent me-2"></i>Requisitos del ponente
                                        </h6>
                                        <p class="text-muted small mb-0">{{ $offer->instructor_requirements }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-4 border-start-lg ps-lg-4 mt-4 mt-lg-0">
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-brand-deep mb-3">
                                            <i class="bi bi-geo-alt-fill me-2"></i>Ubicación
                                        </h6>
                                        <p class="text-muted mb-0 small">
                                            {{ $offer->location ?? 'No especificada' }}
                                        </p>
                                    </div>
                                </div>

                                @if($offer->schedules->count() > 0)
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-brand-deep mb-3">
                                            <i class="bi bi-clock-fill me-2"></i>Horarios Disponibles
                                        </h6>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($offer->schedules as $schedule)
                                                <li class="mb-2 d-flex align-items-center text-muted small">
                                                    <i class="bi bi-calendar-check me-2 text-brand-main"></i>
                                                    <span>
                                                        {{ $schedule->date->format('d/m/Y') }} <br>
                                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @php
                                    $myApplications = $offer->applications()
                                        ->where('professional_profile_id', auth()->user()->professionalProfile?->id)
                                        ->count();
                                    $totalApplications = $offer->applications()->count();
                                @endphp

                                <div class="d-grid gap-2">
                                    @if($myApplications > 0)
                                        <button class="btn btn-secondary fw-bold py-2" disabled>
                                            <i class="bi bi-check-circle-fill me-2"></i>Ya te has postulado
                                        </button>
                                        <small class="text-center text-muted">Tu solicitud está siendo revisada.</small>
                                    @else
                                        <button type="button" 
                                                class="btn btn-evai fw-bold py-2 shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#applyModal{{ $offer->id }}">
                                            <i class="bi bi-hand-thumbs-up-fill me-2"></i>Postularme Ahora
                                        </button>
                                        @if($totalApplications > 0)
                                            <small class="text-center text-muted">
                                                <i class="bi bi-people-fill me-1"></i>{{ $totalApplications }} personas ya se han postulado
                                            </small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Postulación --}}
            <div class="modal fade" id="applyModal{{ $offer->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-brand-deep text-white border-0">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-send-fill me-2"></i>Postularme a la Oferta
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('offers.apply', $offer) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="alert alert-info bg-opacity-10 border-0 d-flex mb-4">
                                    <i class="bi bi-info-circle-fill text-info fs-4 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold text-info mb-1">Información Importante</h6>
                                        <p class="mb-0 small text-muted">Al postularte, el organizador del evento <strong>{{ $offer->event->name }}</strong> revisará tu perfil profesional para evaluar tu idoneidad para esta actividad.</p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label-admin fw-bold">Mensaje para el organizador (Opcional)</label>
                                    <textarea class="form-control form-control-admin" 
                                              name="message" 
                                              rows="4"
                                              placeholder="Cuéntale brevemente por qué te interesa esta oportunidad y qué puedes aportar..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light">
                                <button type="button" class="btn btn-light border fw-bold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-evai fw-bold px-4">
                                    <i class="bi bi-paperplane-fill me-2"></i>Enviar Postulación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-admin">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-search text-muted opacity-25 display-1"></i>
                        </div>
                        <h4 class="fw-bold text-brand-deep">No hay ofertas disponibles</h4>
                        <p class="text-muted mb-0">Actualmente no hay eventos buscando ponentes. ¡Vuelve pronto!</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
