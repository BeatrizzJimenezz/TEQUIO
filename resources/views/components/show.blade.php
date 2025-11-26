@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/public-event-show.css') }}" rel="stylesheet">
    <style>
        /* Estilos para "Acerca de" y Instructor */
        .instructor-card {
            background: linear-gradient(to right, #ffffff, #f8f9fa);
            border-left: 5px solid #0d6efd;
        }
        .about-section {
            background-color: #fcfcfc;
            border-radius: 12px;
            padding: 2rem;
            position: relative;
            border: 1px solid #e9ecef;
        }
        /* Comilla decorativa sutil */
        .about-section::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: 20px;
            font-size: 80px;
            color: #e9ecef;
            font-family: serif;
            line-height: 1;
            z-index: 0;
        }
        .about-content {
            position: relative;
            z-index: 1;
        }
        /* Ajuste de badges para que se vean modernos */
        .badge-lg {
            padding: 0.5em 1em;
            font-size: 0.9rem;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')

{{-- HERO SECTION: Agregado rounded-3 y overflow-hidden para el redondeado --}}
<div class="event-hero ps-3 rounded-3 overflow-hidden mb-4">
    <div class="event-hero-pattern"></div>
    <div class="container position-relative">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    {{-- Ruta conservada: event.show --}}
                    <a href="{{ route('event.show', $component->event_id) }}" class="text-white-50 text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Evento
                    </a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">Detalle de Actividad</li>
            </ol>
        </nav>
        <h1 class="display-6 fw-bold mb-2">{{ $component->name }}</h1>
        <p class="lead text-white-50">{{ $component->event->name }}</p>
    </div>
</div>

<div class="container overlap-container pb-5">
    <div class="row g-4">
        
        {{-- COLUMNA IZQUIERDA --}}
        <div class="col-lg-8">
            
            {{-- Imagen Principal --}}
            <div class="info-card p-1 mb-4 shadow-sm">
                @if($component->cover_image)
                    <img src="{{ Str::startsWith($component->cover_image, 'http') ? $component->cover_image : asset('storage/' . $component->cover_image) }}"
                         class="img-fluid rounded-3 w-100"
                         alt="{{ $component->name }}"
                         style="max-height: 400px; object-fit: cover;">
                @else
                    <div class="img-placeholder-lg rounded-3 bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <div class="text-center text-secondary opacity-50">
                            <i class="bi bi-card-image display-1"></i>
                        </div>
                    </div>
                @endif
            </div>

            {{-- BADGES --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <span class="badge bg-primary badge-lg shadow-sm">
                    <i class="bi bi-bookmark-star me-1"></i> {{ ucfirst($component->type) }}
                </span>
                <span class="badge bg-dark badge-lg shadow-sm">
                    <i class="bi bi-laptop me-1"></i> {{ ucfirst($component->modality) }}
                </span>
                <span class="badge bg-secondary badge-lg shadow-sm">
                    <i class="bi bi-bar-chart me-1"></i> Nivel: {{ ucfirst($component->level ?? 'General') }}
                </span>
            </div>

            {{-- SECCIÓN: Acerca de la actividad --}}
            <div class="mb-5">
                <h3 class="fw-bold mb-3 text-brand-deep">Acerca de esta actividad</h3>
                
                <div class="about-section shadow-sm">
                    <div class="about-content text-secondary" style="line-height: 1.8; font-size: 1.05rem;">
                        {!! nl2br(e($component->description)) !!}
                    </div>
                </div>

                @if($component->participant_requirements)
                    <div class="mt-4 p-3 bg-warning bg-opacity-10 border border-warning rounded-3 d-flex align-items-start">
                        <i class="bi bi-exclamation-circle-fill text-warning me-3 fs-4 mt-1"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Requisitos para participar</h6>
                            <p class="mb-0 text-muted small">{{ $component->participant_requirements }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- SECCIÓN INSTRUCTOR --}}
            @if($component->speaker)
            <div class="card instructor-card shadow-sm mb-5">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                        <h5 class="fw-bold text-brand-deep m-0">
                            <i class="bi bi-person-video3 me-2"></i>Conoce al Instructor
                        </h5>
                    </div>

                    <div class="row g-4 align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $component->speaker->user ? $component->speaker->user->profile_photo_url : 'https://ui-avatars.com/api/?name='.urlencode($component->speaker->display_name) }}" 
                                     class="rounded-circle shadow-sm border border-3 border-white" 
                                     width="110" height="110" 
                                     style="object-fit: cover;"
                                     alt="{{ $component->speaker->display_name }}">
                                
                                <span class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1 border border-2 border-white" title="Instructor Verificado">
                                    <i class="bi bi-check small"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <h4 class="fw-bold mb-1">{{ $component->speaker->display_name }}</h4>
                            
                            <p class="text-primary fw-medium small mb-2 text-uppercase ls-1">
                                {{ $component->speaker->temp_profession ?? ($component->speaker->academicTrainings->first()->degree ?? 'Profesional') }}
                                @if($component->speaker->temp_company || ($component->speaker->academicTrainings->first()->institution ?? null)) 
                                    <span class="text-muted mx-1">|</span> 
                                    <span class="text-dark">{{ $component->speaker->temp_company ?? ($component->speaker->academicTrainings->first()->institution ?? '') }}</span>
                                @endif
                            </p>
                            
                            @if($component->speaker->about_me)
                                <p class="text-muted small mb-3 fst-italic">"{{ Str::limit($component->speaker->about_me, 130) }}"</p>
                            @endif

                            @if($component->speaker->skills)
                                <div class="mb-3">
                                    @foreach(explode(',', $component->speaker->skills) as $skill)
                                        @if($loop->index < 4)
                                            <span class="badge bg-light text-secondary border fw-normal me-1 mb-1">
                                                {{ trim($skill) }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <div class="d-flex align-items-center gap-3">
                                @if(!$component->speaker->is_temporary && $component->speaker->user)
                                    {{-- Ruta conservada: profile.public --}}
                                    <a href="{{ route('profile.public', $component->speaker->user->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">
                                        Ver perfil completo
                                    </a>
                                @endif
                                
                                @if($component->speaker->user && $component->speaker->user->email)
                                    <a href="mailto:{{ $component->speaker->user->email }}" class="text-secondary" data-bs-toggle="tooltip" title="Contactar por correo">
                                        <i class="bi bi-envelope fs-5"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- COLUMNA DERECHA: SIDEBAR --}}
        <div class="col-lg-4">
            <div class="sticky-sidebar">
                
                <div class="card border-0 shadow-sm mb-4 bg-white">
                    <div class="card-body p-4">
                        @if($registration)
                            <div class="text-center p-3 rounded-3 bg-success bg-opacity-10 mb-3 border border-success border-opacity-25">
                                <div class="display-4 text-success mb-2"><i class="bi bi-check-circle-fill"></i></div>
                                <h5 class="fw-bold text-success mb-1">¡Inscripción Confirmada!</h5>
                                <p class="text-muted small mb-0">Tu cupo está reservado.</p>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-dark" disabled>
                                    <i class="bi bi-qr-code-scan me-2"></i> Ver Ticket (Pronto)
                                </button>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <span class="text-uppercase text-muted small fw-bold">Costo de entrada</span>
                                @if($component->price > 0)
                                    <h2 class="fw-bold text-brand-accent mb-0">${{ number_format($component->price, 2) }}</h2>
                                @else
                                    <span class="badge bg-success fs-6 px-3 py-2">Gratuito</span>
                                @endif
                            </div>

                            @if($component->capacity)
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted"><i class="bi bi-people-fill me-1"></i> Cupos</span>
                                        <span class="fw-bold {{ $component->available_seats < 10 ? 'text-danger' : 'text-dark' }}">
                                            {{ $component->available_seats }} restantes
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        @php $percent = 100 - (($component->available_seats / $component->capacity) * 100); @endphp
                                        <div class="progress-bar {{ $component->available_seats < 10 ? 'bg-danger' : 'bg-success' }}" 
                                             role="progressbar" 
                                             style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="d-grid" id="btn-container-{{ $component->id }}">
                                @if($component->capacity && $component->available_seats <= 0)
                                    <button class="btn btn-secondary py-2" disabled>
                                        <i class="bi bi-x-circle me-1"></i> Cupos Agotados
                                    </button>
                                @else
                                    @auth
                                        <button class="btn btn-evai-green fw-bold text-white btn-register-action py-3 shadow-sm"
                                                data-component-id="{{ $component->id }}"
                                                data-register-url="{{ route('registrations.store') }}">
                                            Confirmar Asistencia
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-evai py-2">
                                            Iniciar sesión para inscribirse
                                        </a>
                                    @endauth
                                @endif
                            </div>
                            <div id="feedback-message" class="mt-3"></div>
                        @endif
                    </div>
                </div>

                <div class="info-card p-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 text-uppercase small ls-1">Detalles Logísticos</h6>
                    
                    <ul class="list-unstyled mb-0">
                        <li class="mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-geo-alt text-danger"></i>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted d-block fw-bold mb-1">Ubicación</small>
                                    <span class="text-dark">{{ $component->location ?? 'Por definir' }}</span>
                                </div>
                            </div>
                        </li>
                        
                        <li>
                            <div class="d-flex mb-2">
                                <div class="me-3">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-clock text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted d-block fw-bold mb-1">Agenda</small>
                                </div>
                            </div>
                            
                            <div class="ps-5">
                                @foreach($component->schedules as $schedule)
                                    <div class="border-start border-2 border-primary ps-3 mb-3 pb-1">
                                        <span class="d-block fw-bold text-dark">{{ $schedule->date->format('l, d M Y') }}</span>
                                        <small class="text-secondary">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/event-show.js') }}"></script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endpush