@extends('layouts.app')

@section('header', 'Ofertas Abiertas')

@push('styles')
    <link href="{{ asset('css/public-offers.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="page-header-public">
        <h2 class="page-title">
            <i class="bi bi-megaphone-fill"></i>
            Ofertas Abiertas
        </h2>
        <p class="page-subtitle">
            Explora las oportunidades para participar como ponente o tallerista en nuestros eventos.
        </p>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-public alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-public alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Lista de ofertas --}}
    @forelse($offers as $offer)
        @php
            $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
            $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
            $levelLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
            $myApplications = $offer->applications()
                ->where('professional_profile_id', auth()->user()->professionalProfile?->id)
                ->count();
            $totalApplications = $offer->applications()->count();
        @endphp

        <div class="public-offer-card">
            {{-- Header --}}
            <div class="public-offer-header">
                <div>
                    <h3 class="public-offer-title">
                        {{ $offer->name }}
                        <span class="badge-open">
                            <i class="bi bi-megaphone-fill"></i>
                            Oferta Abierta
                        </span>
                    </h3>
                    <div class="public-offer-event">
                        <i class="bi bi-calendar-event-fill"></i>
                        <span>Evento: <strong>{{ $offer->event->name }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- Badges --}}
            <div class="public-offer-badges">
                <span class="public-badge type">
                    <i class="bi bi-tag-fill"></i>
                    {{ $typeLabels[$offer->type] ?? ucfirst($offer->type) }}
                </span>
                <span class="public-badge modality">
                    <i class="bi bi-laptop"></i>
                    {{ $modalityLabels[$offer->modality] ?? ucfirst($offer->modality) }}
                </span>
                @if($offer->level)
                    <span class="public-badge level">
                        <i class="bi bi-bar-chart-fill"></i>
                        {{ $levelLabels[$offer->level] ?? ucfirst($offer->level) }}
                    </span>
                @endif
                @if($offer->capacity)
                    <span class="public-badge capacity">
                        <i class="bi bi-people-fill"></i>
                        {{ $offer->capacity }} cupos
                    </span>
                @endif
                @if($offer->organizer_cost)
                    <span class="public-badge paid">
                        <i class="bi bi-cash-stack"></i>
                        Remunerado
                    </span>
                @endif
            </div>

            {{-- Body --}}
            <div class="public-offer-body">
                <div class="public-offer-content">
                    {{-- Left Column --}}
                    <div class="public-offer-main">
                        {{-- Description --}}
                        <div class="public-description-box">
                            <div class="box-label">
                                <i class="bi bi-text-paragraph"></i>
                                Descripción
                            </div>
                            <p>{{ Str::limit($offer->description, 300) }}</p>
                        </div>

                        {{-- Requirements --}}
                        @if($offer->instructor_requirements)
                            <div class="public-requirements-box">
                                <div class="box-label">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Requisitos del ponente
                                </div>
                                <p>{{ $offer->instructor_requirements }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Right Column - Sidebar --}}
                    <div class="public-offer-sidebar">
                        {{-- Location --}}
                        <div class="sidebar-card">
                            <div class="card-label">
                                <i class="bi bi-geo-alt-fill"></i>
                                Ubicación
                            </div>
                            <div class="card-value">
                                {{ $offer->location ?? 'No especificada' }}
                            </div>
                        </div>

                        {{-- Schedules --}}
                        @if($offer->schedules->count() > 0)
                            <div class="sidebar-card">
                                <div class="card-label">
                                    <i class="bi bi-clock-fill"></i>
                                    Horarios Disponibles
                                </div>
                                <ul class="public-schedules-list">
                                    @foreach($offer->schedules as $schedule)
                                        <li>
                                            <span class="schedule-date">
                                                <i class="bi bi-calendar-check"></i>
                                                {{ $schedule->date->format('d/m/Y') }}
                                            </span>
                                            <span class="schedule-time">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Apply Section --}}
                        <div class="apply-section">
                            @if($myApplications > 0)
                                <button class="btn-applied" disabled>
                                    <i class="bi bi-check-circle-fill"></i>
                                    Ya te has postulado
                                </button>
                                <p class="apply-info">
                                    <i class="bi bi-hourglass-split"></i>
                                    Tu solicitud está siendo revisada
                                </p>
                            @else
                                <button type="button" class="btn-apply"
                                        data-bs-toggle="modal"
                                        data-bs-target="#applyModal{{ $offer->id }}">
                                    <i class="bi bi-hand-thumbs-up-fill"></i>
                                    Postularme Ahora
                                </button>
                                @if($totalApplications > 0)
                                    <p class="apply-info">
                                        <i class="bi bi-people-fill"></i>
                                        {{ $totalApplications }} persona(s) ya se han postulado
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Postulación --}}
        <div class="modal fade modal-public" id="applyModal{{ $offer->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-send-fill"></i>
                            Postularme a la Oferta
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('offers.apply', $offer) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="modal-info-box">
                                <i class="bi bi-info-circle-fill"></i>
                                <div>
                                    <div class="info-title">Información Importante</div>
                                    <p class="info-text">
                                        Al postularte, el organizador del evento <strong>{{ $offer->event->name }}</strong> 
                                        revisará tu perfil profesional para evaluar tu idoneidad para esta actividad.
                                    </p>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label-public">
                                    Mensaje para el organizador (Opcional)
                                </label>
                                <textarea class="form-control form-control-public"
                                          name="message"
                                          rows="4"
                                          placeholder="Cuéntale brevemente por qué te interesa esta oportunidad y qué puedes aportar..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn-submit-modal">
                                <i class="bi bi-send-fill"></i>
                                Enviar Postulación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state-public">
            <div class="empty-icon">
                <i class="bi bi-search"></i>
            </div>
            <h4>No hay ofertas disponibles</h4>
            <p>Actualmente no hay eventos buscando ponentes. ¡Vuelve pronto!</p>
        </div>
    @endforelse
</div>
@endsection