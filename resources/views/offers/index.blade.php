@extends('layouts.app')

@section('header', 'Gestionar Ofertas')

@section('content')
<div class="container-fluid">
    {{-- Encabezado del evento --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-bold" style="color: #0C2340;">{{ $event->name }}</h5>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar-event me-1" style="color: #4499BB;"></i>
                        {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                    </p>
                </div>
                <a href="{{ route('events.manage', $event) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Gestión
                </a>
            </div>
        </div>
    </div>

    {{-- Encabezado de ofertas --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold" style="color: #0C2340;">
                    <i class="bi bi-megaphone-fill me-2"></i>Ofertas Abiertas
                </h4>
            </div>
            <a href="{{ route('offers.create', $event) }}" class="btn" style="background-color: #8CC63F; color: white;">
                <i class="bi bi-plus-circle me-1"></i> Publicar Oferta
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-info border-0">
                <i class="bi bi-info-circle-fill me-2"></i>
                Las ofertas abiertas son componentes para los cuales buscas ponentes o talleristas externos.
                Aparecerán públicamente y los usuarios podrán postularse.
            </div>
        </div>
    </div>

    {{-- Lista de ofertas --}}
    @forelse($offers as $offer)
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="mb-0 me-3 fw-bold" style="color: #0C2340;">{{ $offer->name }}</h5>
                            <span class="badge" style="background-color: #8CC63F;">
                                <i class="bi bi-megaphone me-1"></i> Oferta Abierta
                            </span>
                        </div>

                        <div class="mb-2">
                            @php
                                $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
                                $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                $levelLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
                            @endphp
                            <span class="badge" style="background-color: #0C2340;">
                                {{ $typeLabels[$offer->type] ?? ucfirst($offer->type) }}
                            </span>
                            <span class="badge" style="background-color: #4499BB;">
                                {{ $modalityLabels[$offer->modality] ?? ucfirst($offer->modality) }}
                            </span>

                            @if($offer->level)
                                <span class="badge bg-secondary">
                                    {{ $levelLabels[$offer->level] ?? ucfirst($offer->level) }}
                                </span>
                            @endif

                            @if($offer->capacity)
                                <span class="badge bg-info">
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

                        <p class="card-text text-muted mb-2">{{ Str::limit($offer->description, 200) }}</p>

                        @if($offer->location)
                            <p class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $offer->location }}
                                </small>
                            </p>
                        @endif

                        @if($offer->instructor_requirements)
                            <div class="mb-2">
                                <small class="fw-semibold" style="color: #0C2340;">Requisitos del ponente:</small>
                                <p class="mb-0 small text-muted">{{ $offer->instructor_requirements }}</p>
                            </div>
                        @endif

                        @if($offer->schedules->count() > 0)
                            <div class="mt-2">
                                <strong class="small" style="color: #0C2340;">
                                    <i class="bi bi-calendar-week me-1"></i>Horarios disponibles:
                                </strong>
                                <ul class="list-unstyled ms-3 mb-0 mt-1">
                                    @foreach($offer->schedules as $schedule)
                                        <li class="small text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $schedule->date->format('d/m/Y') }} -
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} a
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <p class="text-muted mb-0 mt-2">
                            <small>
                                <i class="bi bi-clock-history me-1"></i>
                                Publicado el {{ $offer->created_at->format('d/m/Y H:i') }}
                            </small>
                        </p>
                    </div>

                    <div class="ms-3 text-end">
                        @if($pendingCount > 0)
                            <span class="badge bg-danger mb-2 d-block">{{ $pendingCount }} pendiente(s)</span>
                        @endif
                        <a href="{{ route('offers.evaluation', $event) }}" class="btn btn-sm" style="background-color: #4499BB; color: white;">
                            <i class="bi bi-eye me-1"></i> Ver Postulaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-megaphone text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-2" style="color: #0C2340;">No has publicado ofertas</h5>
                <p class="text-muted mb-3">Publica ofertas para que ponentes y talleristas puedan postularse.</p>
                <a href="{{ route('offers.create', $event) }}" class="btn" style="background-color: #8CC63F; color: white;">
                    <i class="bi bi-plus-circle me-1"></i> Publicar Primera Oferta
                </a>
            </div>
        </div>
    @endforelse
</div>
@endsection
