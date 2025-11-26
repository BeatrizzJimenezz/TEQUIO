@extends('layouts.app')

@section('header', 'Gestionar Ofertas')

@push('styles')
    <link href="{{ asset('css/offers.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="page-header-offers">
        <div class="header-content">
            <div>
                <h3 class="page-title">
                    <i class="bi bi-megaphone-fill"></i>
                    Gestionar Ofertas
                </h3>
                <p class="page-subtitle">
                    <i class="bi bi-calendar3"></i>
                    {{ $event->name }} | {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                </p>
            </div>
            <a href="{{ route('events.manage', $event) }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver a Gestión
            </a>
        </div>
    </div>

    {{-- Offers Section --}}
    <div class="offers-section">
        <div class="offers-header">
            <h5>
                <i class="bi bi-list-ul"></i>
                Ofertas Abiertas
            </h5>
            <a href="{{ route('offers.create', $event) }}" class="btn-publish">
                <i class="bi bi-plus-circle"></i>
                Publicar Oferta
            </a>
        </div>

        <div class="offers-body">
            {{-- Alertas --}}
            @if(session('success'))
                <div class="alert alert-offers alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill alert-icon"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-offers alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Info Box --}}
            <div class="info-box">
                <i class="bi bi-info-circle-fill"></i>
                <p>Las ofertas abiertas son componentes para los cuales buscas ponentes o talleristas externos. Aparecerán públicamente y los usuarios podrán postularse.</p>
            </div>

            {{-- Lista de ofertas --}}
            @forelse($offers as $offer)
                @php
                    $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
                    $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                    $levelLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
                    $applicationsCount = $offer->applications()->count();
                    $pendingCount = $offer->applications()->where('status', 'pending')->count();
                @endphp

                <div class="offer-card">
                    {{-- Header: Título + Badge + Botón --}}
                    <div class="offer-card-header">
                        <div class="offer-header-left">
                            <h5 class="offer-title">
                                {{ $offer->name }}
                                <span class="badge-open">
                                    <i class="bi bi-megaphone"></i>
                                    Oferta Abierta
                                </span>
                            </h5>
                            <div class="offer-badges">
                                <span class="offer-badge type">
                                    {{ $typeLabels[$offer->type] ?? ucfirst($offer->type) }}
                                </span>
                                <span class="offer-badge modality">
                                    {{ $modalityLabels[$offer->modality] ?? ucfirst($offer->modality) }}
                                </span>
                                @if($offer->level)
                                    <span class="offer-badge level">
                                        {{ $levelLabels[$offer->level] ?? ucfirst($offer->level) }}
                                    </span>
                                @endif
                                @if($offer->capacity)
                                    <span class="offer-badge capacity">
                                        <i class="bi bi-people"></i>
                                        {{ $offer->capacity }} cupos
                                    </span>
                                @endif
                                @if($offer->organizer_cost && $offer->organizer_cost > 0)
                                    <span class="offer-badge cost">
                                        <i class="bi bi-cash"></i>
                                        ${{ number_format($offer->organizer_cost, 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="offer-header-right">
                            @if($pendingCount > 0)
                                <span class="badge-pending">{{ $pendingCount }} pendiente(s)</span>
                            @endif
                            <a href="{{ route('offers.evaluation', $event) }}" class="btn-view-applications">
                                <i class="bi bi-eye"></i>
                                Ver Postulaciones
                            </a>
                        </div>
                    </div>

                    {{-- Body: Contenido principal --}}
                    <div class="offer-card-body">
                        {{-- Descripción --}}
                        <p class="offer-description">{{ Str::limit($offer->description, 200) }}</p>

                        {{-- Ubicación --}}
                        @if($offer->location)
                            <div class="offer-location">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>{{ $offer->location }}</span>
                            </div>
                        @endif

                        {{-- Grid de Requisitos y Horarios --}}
                        @if($offer->instructor_requirements || $offer->schedules->count() > 0)
                            <div class="offer-details-row">
                                @if($offer->instructor_requirements)
                                    <div class="offer-detail-box requirements">
                                        <div class="detail-label">
                                            <i class="bi bi-person-check-fill"></i>
                                            Requisitos del ponente
                                        </div>
                                        <p class="detail-text">{{ $offer->instructor_requirements }}</p>
                                    </div>
                                @endif

                                @if($offer->schedules->count() > 0)
                                    <div class="offer-detail-box schedules">
                                        <div class="detail-label">
                                            <i class="bi bi-calendar-week-fill"></i>
                                            Horarios disponibles
                                        </div>
                                        <ul class="schedules-list">
                                            @foreach($offer->schedules as $schedule)
                                                <li>
                                                    <i class="bi bi-clock"></i>
                                                    {{ $schedule->date->format('d/m/Y') }} — 
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} a
                                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="offer-card-footer">
                        <span class="offer-date">
                            <i class="bi bi-clock-history"></i>
                            Publicado el {{ $offer->created_at->format('d/m/Y H:i') }}
                        </span>
                        @if($applicationsCount > 0)
                            <span class="offer-applications-count">
                                <i class="bi bi-people-fill"></i>
                                {{ $applicationsCount }} postulación(es) recibida(s)
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state-offers">
                    <div class="empty-icon">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h5>No has publicado ofertas</h5>
                    <p>Publica ofertas para que ponentes y talleristas puedan postularse a tu evento.</p>
                    <a href="{{ route('offers.create', $event) }}" class="btn-publish">
                        <i class="bi bi-plus-circle"></i>
                        Publicar Primera Oferta
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection