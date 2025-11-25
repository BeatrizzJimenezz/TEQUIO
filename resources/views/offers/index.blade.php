@extends('layouts.app')

@section('header', 'Gestionar Ofertas')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Encabezado del evento --}}
    <div class="card-admin mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="mb-1 fw-bold text-brand-deep">
                        <i class="bi bi-megaphone-fill me-2 text-brand-accent"></i>
                        Gestionar Ofertas
                    </h3>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-1 text-brand-main"></i>
                        {{ $event->name }} | {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                    </p>
                </div>
                <a href="{{ route('events.manage', $event) }}" class="btn btn-white shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Gestión
                </a>
            </div>
        </div>
    </div>

    {{-- Encabezado de ofertas --}}
    <div class="card-admin mb-4">
        <div class="card-header-admin d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2 text-brand-main"></i>
                    Ofertas Abiertas
                </h5>
            </div>
            <a href="{{ route('offers.create', $event) }}" class="btn btn-brand-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Publicar Oferta
            </a>
        </div>
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex">
                    <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                    <div>
                        Las ofertas abiertas son componentes para los cuales buscas ponentes o talleristas externos.
                        Aparecerán públicamente y los usuarios podrán postularse.
                    </div>
                </div>
            </div>

            {{-- Lista de ofertas --}}
            @forelse($offers as $offer)
                <div class="card border-0 shadow-sm mb-3 bg-light">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2 flex-wrap gap-2">
                                    <h5 class="mb-0 fw-bold text-brand-deep">{{ $offer->name }}</h5>
                                    <span class="badge bg-brand-accent text-white">
                                        <i class="bi bi-megaphone me-1"></i> Oferta Abierta
                                    </span>
                                </div>

                                <div class="mb-3 d-flex flex-wrap gap-2">
                                    @php
                                        $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
                                        $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                        $levelLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
                                    @endphp
                                    <span class="badge bg-brand-deep text-white">
                                        {{ $typeLabels[$offer->type] ?? ucfirst($offer->type) }}
                                    </span>
                                    <span class="badge bg-brand-main text-white">
                                        {{ $modalityLabels[$offer->modality] ?? ucfirst($offer->modality) }}
                                    </span>

                                    @if($offer->level)
                                        <span class="badge bg-secondary">
                                            {{ $levelLabels[$offer->level] ?? ucfirst($offer->level) }}
                                        </span>
                                    @endif

                                    @if($offer->capacity)
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-people me-1"></i>{{ $offer->capacity }} cupos
                                        </span>
                                    @endif

                                    @if($offer->organizer_cost && $offer->organizer_cost > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-cash me-1"></i>${{ number_format($offer->organizer_cost, 2) }}
                                        </span>
                                    @endif

                                    @php
                                        $applicationsCount = $offer->applications()->count();
                                        $pendingCount = $offer->applications()->where('status', 'pending')->count();
                                    @endphp

                                    @if($applicationsCount > 0)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-people-fill me-1"></i>{{ $applicationsCount }} postulación(es)
                                        </span>
                                    @endif
                                </div>

                                <p class="card-text text-muted mb-3">{{ Str::limit($offer->description, 200) }}</p>

                                @if($offer->location)
                                    <p class="mb-2 text-muted small">
                                        <i class="bi bi-geo-alt me-1 text-brand-main"></i>
                                        {{ $offer->location }}
                                    </p>
                                @endif

                                @if($offer->instructor_requirements)
                                    <div class="mb-2 p-3 bg-white rounded border-start border-4 border-brand-main">
                                        <small class="fw-bold text-brand-deep d-block mb-1">Requisitos del ponente:</small>
                                        <p class="mb-0 small text-muted">{{ $offer->instructor_requirements }}</p>
                                    </div>
                                @endif

                                @if($offer->schedules->count() > 0)
                                    <div class="mt-3">
                                        <strong class="small text-brand-deep">
                                            <i class="bi bi-calendar-week me-1"></i>Horarios disponibles:
                                        </strong>
                                        <ul class="list-unstyled ms-3 mb-0 mt-1">
                                            @foreach($offer->schedules as $schedule)
                                                <li class="small text-muted">
                                                    <i class="bi bi-clock me-1 text-brand-accent"></i>
                                                    {{ $schedule->date->format('d/m/Y') }} -
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} a
                                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <p class="text-muted mb-0 mt-3 small">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Publicado el {{ $offer->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <div class="text-end">
                                @if($pendingCount > 0)
                                    <span class="badge bg-danger mb-2 d-block shadow-sm">{{ $pendingCount }} pendiente(s)</span>
                                @endif
                                <a href="{{ route('offers.evaluation', $event) }}" class="btn btn-brand-main btn-sm shadow-sm">
                                    <i class="bi bi-eye me-1"></i> Ver Postulaciones
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-megaphone text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <h5 class="text-brand-deep fw-bold">No has publicado ofertas</h5>
                    <p class="text-muted mb-4">Publica ofertas para que ponentes y talleristas puedan postularse.</p>
                    <a href="{{ route('offers.create', $event) }}" class="btn btn-brand-primary shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Publicar Primera Oferta
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
