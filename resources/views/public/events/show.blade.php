@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/public-event-show.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div class="event-hero ps-3">
        <div class="event-hero-pattern"></div>
        <div class="container position-relative">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-arrow-left me-1"></i> Catálogo</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalles del Evento</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold mb-2">{{ $event->name }}</h1>
        </div>
    </div>

    <div class="container overlap-container pb-5" data-check-url-base="{{ url('/registrations/check') }}">
        <div class="row g-4">
            
            <div class="col-lg-8">
                <div class="info-card p-1 mb-4">
                    @if($event->cover_image)
                        <img src="{{ Str::startsWith($event->cover_image, 'http') ? $event->cover_image : asset('storage/' . $event->cover_image) }}"
                             class="img-fluid rounded-3 w-100"
                             alt="{{ $event->name }}"
                             style="max-height: 450px; object-fit: cover;"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'img-placeholder-lg rounded-3\'><i class=\'bi bi-image-alt text-muted display-1 opacity-25\'></i></div>'">
                    @else
                        <div class="img-placeholder-lg rounded-3">
                            <i class="bi bi-calendar2-event text-secondary opacity-25 display-1"></i>
                        </div>
                    @endif
                </div>

                <div class="mb-5">
                    <div class="mb-3">
                        @php
                            $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                            $statusLabels = ['planning' => 'En planificación', 'active' => 'Activo', 'finished' => 'Finalizado'];
                        @endphp
                        <span class="badge badge-status">
                            {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                        </span>
                        <span class="badge badge-modality">
                            {{ $modalityLabels[$event->modality] ?? ucfirst($event->modality) }}
                        </span>
                        @foreach($event->tags as $tag)
                            <span class="badge bg-secondary">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    <h3 class="fw-bold mb-3 text-brand-deep">Sobre este evento</h3>
                    <p class="text-secondary" style="line-height: 1.8; font-size: 1.05rem;">
                        {{ $event->description }}
                    </p>
                </div>

                <div id="feedback-message"></div>

                @if($event->approvedComponents->count() > 0)
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="fw-bold mb-0 text-brand-deep">
                            Actividades y Talleres
                        </h3>
                        <span class="badge bg-light text-secondary border rounded-pill">
                            {{ $event->approvedComponents->count() }} Disponibles
                        </span>
                    </div>

                    @if($componentsByType->count() > 1)
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        @foreach($componentsByType as $type => $components)
                        @php
                            $typeLabels = ['workshop' => 'Talleres', 'talk' => 'Charlas', 'activity' => 'Actividades'];
                        @endphp
                        <li class="nav-item">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#{{ Str::slug($type) }}"
                                    type="button">
                                {{ $typeLabels[$type] ?? ucfirst($type) }} 
                                <span class="badge bg-light text-dark ms-2">{{ $components->count() }}</span>
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <div class="tab-content">
                        @foreach($componentsByType as $type => $components)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ Str::slug($type) }}">
                            <div class="d-flex flex-column gap-3">
                                @foreach($components as $component)
                                <div class="component-card p-4">
                                    <div class="row g-4">
                                        @if($component->cover_image)
                                        <div class="col-md-4">
                                            <img src="{{ $component->cover_image }}" 
                                                 class="img-fluid rounded-3 w-100 h-100" 
                                                 alt="{{ $component->name }}"
                                                 style="object-fit: cover; min-height: 200px;">
                                        </div>
                                        @endif

                                        <div class="{{ $component->cover_image ? 'col-md-8' : 'col-12' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="fw-bold text-dark mb-0">{{ $component->name }}</h5>
                                                @php
                                                    $typeLabels = ['workshop' => 'Taller', 'talk' => 'Ponencia', 'activity' => 'Actividad'];
                                                    $compModalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                                    $compDificultyLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado', '' => 'Avanzado'];
                                                @endphp
                                                <div>
                                                     <span class="badge badge-type">
                                                        {{ $typeLabels[$component->type] ?? ucfirst($component->type) }}
                                                    </span>
                                                    <span class="badge badge-modality">
                                                        {{ $compModalityLabels[$component->modality] ?? ucfirst($component->modality) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                                <span><i class="bi bi-bar-chart-fill me-1 text-brand-main"></i> 
                                                    {{ $compDificultyLabels[$component->level] ?? ucfirst($component->level) }}
                                                </span>
                                                
                                                @if($component->attendee_price > 0)
                                                    <span><i class="bi bi-tag-fill me-1 text-brand-accent"></i> ${{ number_format($component->attendee_price, 2) }}</span>
                                                @else
                                                    <span><i class="bi bi-gift-fill me-1 text-brand-accent"></i> Gratis</span>
                                                @endif
                                                
                                                @if($component->location)
                                                    <span><i class="bi bi-geo-alt-fill me-1 text-danger"></i> {{ $component->location }}</span>
                                                @endif
                                            </div>

                                            <p class="text-secondary mb-3">{{ $component->description }}</p>

                                            <div class="bg-light p-3 rounded-3 mb-3 border border-light">
                                                <div class="row g-3 small">
                                                    @if($component->participant_requirements)
                                                    <div class="col-12">
                                                        <strong class="d-block text-dark mb-1">Requisitos:</strong>
                                                        <span class="text-muted">{{ $component->participant_requirements }}</span>
                                                    </div>
                                                    @endif
                                                    
                                                    @if($component->capacity)
                                                    <div class="col-sm-6">
                                                        <strong class="d-block text-dark mb-1">Cupos:</strong>
                                                        <span id="slots-count-{{ $component->id }}">{{ $component->available_seats }}</span> disponibles de {{ $component->capacity }}
                                                        @if($component->available_seats <= 5 && $component->available_seats > 0)
                                                            <span class="text-danger fw-bold">(¡Quedan pocos!)</span>
                                                        @endif
                                                    </div>
                                                    @endif

                                                    <div class="col-sm-6">
                                                        <strong class="d-block text-dark mb-1">Horarios:</strong>
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach($component->schedules as $schedule)
                                                            <li>
                                                                {{ $schedule->date->format('d/m/Y') }}: 
                                                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="btn-container-{{ $component->id }}" class="mt-2">
                                                @auth
                                                    @if($component->capacity && $component->available_seats <= 0)
                                                        <button class="btn btn-evai-green w-100" disabled>
                                                            <i class="bi bi-x-circle me-2"></i>Agotado
                                                        </button>
                                                    @else
                                                        <button class="btn btn-evai-green w-100 fw-bold text-white btn-register-action"
                                                                data-component-id="{{ $component->id }}"
                                                                data-register-url="{{ route('registrations.store') }}">
                                                            Inscribirme ahora
                                                        </button>
                                                    @endif
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-outline-evai w-100 text-center text-decoration-none d-block">
                                                        Iniciar sesión para inscribirse
                                                    </a>
                                                @endauth
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 border rounded-3 bg-light">
                        <i class="bi bi-calendar-x text-muted display-4 mb-3 d-block opacity-50"></i>
                        <p class="text-muted mb-0">Este evento aún no tiene actividades publicadas.</p>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="info-card p-4 mb-4">
                        <h5 class="fw-bold mb-4 text-brand-deep pb-2 border-bottom">
                            Información del evento
                        </h5>

                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 pb-2 border-bottom border-light">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Organizador</small>
                                <div class="d-flex align-items-start mb-1">
                                    <i class="bi bi-file-person me-2 mt-1 text-brand-accent"></i>
                                    <span class="fw-medium text-dark">
                                        {{ $event->professionalProfile->full_name ?? $event->professionalProfile->user->name }}
                                    </span>
                                </div>
                            </li>

                            <li class="mb-3 pb-2 border-bottom border-light">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Fecha y Hora</small>
                                <div class="d-flex align-items-start mb-1">
                                    <i class="bi bi-calendar-range me-2 mt-1 text-brand-accent"></i>
                                    <span>{{ $event->start_date->format('d M') }} - {{ $event->end_date->format('d M, Y') }}</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-clock me-2 mt-1 text-brand-accent"></i>
                                    <span>{{ $event->start_time }}</span>
                                </div>
                            </li>

                            <li class="mb-3 pb-2 border-bottom border-light">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Ubicación</small>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-geo-alt me-2 text-success mt-1"></i>
                                    <span>{{ $event->location ?? 'No especificada' }}</span>
                                </div>
                            </li>

                            <li class="mb-0">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Resumen</small>
                                <div class="row text-center g-2">
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded border">
                                            <strong class="d-block h5 mb-0 text-dark">{{ $event->approvedComponents->count() }}</strong>
                                            <small class="text-secondary" style="font-size: 0.75rem;">Actividades</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded border">
                                            <strong class="d-block h5 mb-0 text-dark">{{ $event->approvedComponents->sum('capacity') ?: '∞' }}</strong>
                                            <small class="text-secondary" style="font-size: 0.75rem;">Cupos</small>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        @auth
                            <div class="d-grid mt-4">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-evai rounded-pill py-2">
                                    Volver al catálogo
                                </a>
                            </div>
                        @else
                            <div class="d-grid gap-2 mt-3">
                                <a href="{{ route('login') }}" class="btn btn-evai-green shadow-sm text-center text-decoration-none">
                                    Iniciar sesión para inscribirse
                                </a>
                                <a href="{{ route('dashboard') }}" class="btn btn-link text-secondary text-decoration-none text-center small">
                                    Volver al catálogo
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/event-show.js') }}"></script>
@endpush