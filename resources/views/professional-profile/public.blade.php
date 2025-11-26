@extends('layouts.app')

@section('header', 'Perfil Profesional Público')

@push('styles')
    {{-- Reutilizamos los mismos estilos que en el perfil privado --}}
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">

    {{-- Tarjeta Principal (Banner y Foto) --}}
    <div class="card shadow border-0 mb-4 overflow-hidden">
        
        {{-- Banner decorativo --}}
        <div id="dynamicBanner" style="height: 180px; position: relative; background-color: #0c2340;">
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.15; background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
        </div>
        
        <div class="card-body px-4 pb-4 position-relative">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end">
                
                {{-- Foto de Perfil --}}
                <div class="position-relative" style="margin-top: -90px;">
                    @if($profile->user->profile_photo)
                        {{-- Usamos $profile->user en lugar de Auth::user() --}}
                        <img src="{{ asset('storage/' . $profile->user->profile_photo) }}" 
                             alt="Profile" 
                             class="rounded-circle border border-4 border-white shadow-sm profile-avatar"
                             style="width: 160px; height: 160px; object-fit: cover;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($profile->user->name) }}&background=0c2340&color=fff&size=160" 
                             alt="Default Avatar" 
                             class="rounded-circle border border-4 border-white shadow-sm profile-avatar">
                    @endif
                </div>

                {{-- Información del Usuario --}}
                <div class="ms-md-4 text-center text-md-start mt-3 mt-md-0 flex-grow-1">
                    <h1 class="fw-bold mb-0" style="color: #0c2340;">{{ $profile->user->name }}</h1>
                    
                    {{-- Opcional: Mostrar correo si deseas que sea público --}}
                    {{-- <p class="text-muted mb-2 fw-medium" style="font-size: 1.1rem;">{{ $profile->user->email }}</p> --}}
                    
                    {{-- Profesión / Lugar de trabajo actual --}}
                    <div class="mt-2">
                        @if($profile->current_workplace)
                            <div class="badge border px-3 py-2 rounded-pill text-dark" style="background-color: #f8f9fa; font-weight: 500;">
                                <i class="bi bi-briefcase-fill me-2" style="color: #4499bb;"></i> {{ $profile->current_workplace }}
                            </div>
                        @endif
                        
                        {{-- Título profesional temporal o académico principal --}}
                        @if($profile->temp_profession)
                             <div class="badge border px-3 py-2 rounded-pill text-dark ms-1" style="background-color: #f8f9fa; font-weight: 500;">
                                <i class="bi bi-person-badge-fill me-2" style="color: #8cc63f;"></i> {{ $profile->temp_profession }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Botón Volver (Opcional) --}}
                <div class="mt-4 mt-md-0">
                    <a href="javascript:history.back()" 
                       class="btn btn-outline-secondary rounded-pill px-4 py-2 shadow-sm fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- COLUMNA IZQUIERDA --}}
        <div class="col-lg-4 col-xl-3">
            
            {{-- Sobre el usuario --}}
            <div class="card shadow-sm border-0 mb-4 h-90">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="color: #0c2340;">
                        <i class="bi bi-person-lines-fill me-2" style="color: #4499bb;"></i>Bibliografía
                    </h5>
                    @if($profile->about_me)
                        <p class="card-text text-secondary" style="white-space: pre-wrap; line-height: 1.6;">{{ $profile->about_me }}</p>
                    @else
                        <p class="text-muted small fst-italic">Sin descripción disponible.</p>
                    @endif
                </div>
            </div>
            
            {{-- Habilidades --}}
            @if($profile->skills)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="color: #0c2340;">
                        <i class="bi bi-lightning-charge-fill me-2" style="color: #8cc63f;"></i>Habilidades
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(explode(',', $profile->skills) as $skill)
                            <span class="badge px-3 py-2 rounded-pill" 
                                  style="background-color: rgba(68, 153, 187, 0.1); color: #0c2340; border: 1px solid rgba(68, 153, 187, 0.3);">
                                {{ trim($skill) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Redes sociales --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3" style="color: #0c2340;">
                        <i class="bi bi-share-fill me-2" style="color: #4499bb;"></i>Redes
                    </h5>
                    @if($profile->socialNetworks->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($profile->socialNetworks as $network)
                                <a href="{{ $network->link }}" target="_blank" class="text-decoration-none">
                                    <div class="d-flex align-items-center p-3 border rounded hover-shadow bg-white transition-all">
                                        <div class="me-3">
                                            @switch($network->platform)
                                                @case('LinkedIn') <i class="bi bi-linkedin" style="font-size: 1.5rem; color: #0077B5;"></i> @break
                                                @case('GitHub') <i class="bi bi-github" style="font-size: 1.5rem; color: #181717;"></i> @break
                                                @case('Twitter') <i class="bi bi-twitter-x" style="font-size: 1.5rem; color: #000000;"></i> @break
                                                @case('Facebook') <i class="bi bi-facebook" style="font-size: 1.5rem; color: #1877F2;"></i> @break
                                                @case('Instagram') <i class="bi bi-instagram" style="font-size: 1.5rem; color: #E4405F;"></i> @break
                                                @case('YouTube') <i class="bi bi-youtube" style="font-size: 1.5rem; color: #FF0000;"></i> @break
                                                @default <i class="bi bi-link-45deg" style="font-size: 1.5rem; color: #6c757d;"></i>
                                            @endswitch
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="fw-bold mb-0 text-dark">{{ $network->platform }}</h6>
                                            <span class="small text-muted">Ver perfil <i class="bi bi-box-arrow-up-right ms-1"></i></span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small fst-italic text-center py-3">No hay redes sociales públicas.</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="col-lg-8 col-xl-9">
            
            {{-- Formación académica --}}
            @if($profile->academicTrainings->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                    <h5 class="fw-bold mb-0" style="color: #0c2340;">
                        <i class="bi bi-mortarboard-fill me-2" style="color: #4499bb;"></i>Formación académica
                    </h5>
                </div>
                <div class="card-body ps-4">
                    <div class="timeline">
                        @foreach($profile->academicTrainings as $training)
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <h5 class="fw-bold mb-1 text-dark">{{ $training->degree }}</h5>
                                <div class="fw-semibold mb-1" style="color: #4499bb;">{{ $training->institution }}</div>
                                <div class="text-muted small mb-2">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($training->start_date)->format('M Y') }} - 
                                    {{ $training->end_date ? \Carbon\Carbon::parse($training->end_date)->format('M Y') : 'Presente' }}
                                </div>
                                @if($training->description)
                                    <p class="text-secondary small mb-0 bg-light p-3 rounded border-start border-4" style="border-color: #8cc63f !important;">
                                        {{ $training->description }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Ponencias y Talleres (Actividades) --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                    <h5 class="fw-bold mb-0" style="color: #0c2340;">
                        <i class="bi bi-mic-fill me-2" style="color: #4499bb;"></i>Ponencias y talleres impartidos
                    </h5>
                </div>
                <div class="card-body">
                    @if($activities->count() > 0)
                        <div class="row g-3">
                            @foreach($activities as $activity)
                                <div class="col-md-6">
                                    {{-- Envolvemos en link para ir al detalle del evento/componente --}}
                                    <a href="{{ route('public.components.show', [$activity->event_id, $activity->id]) }}" 
                                       class="text-decoration-none">
                                        <div class="card h-100 border-0 bg-light hover-shadow transition-all">
                                            <div class="card-body position-relative">
                                                <div class="position-absolute start-0 top-0 bottom-0 rounded-start" 
                                                     style="width: 4px; background-color: {{ $activity->type == 'talk' ? '#4499bb' : '#8cc63f' }};"></div>
                                                
                                                <div class="d-flex justify-content-between align-items-start mb-2 ps-2">
                                                    <span class="badge rounded-pill text-white"
                                                        style="background-color: {{ $activity->type == 'talk' ? '#0c2340' : '#0d0d0d' }};">
                                                        {{ ucfirst($activity->type == 'talk' ? 'Charla' : 'Taller') }}
                                                    </span>
                                                    @if($activity->schedules->first())
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            {{ \Carbon\Carbon::parse($activity->schedules->first()->date)->format('d/M/Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <h6 class="fw-bold mb-1 ps-2 text-dark">{{ $activity->name }}</h6>
                                                <small class="text-muted d-block ps-2 mb-2">
                                                    <i class="bi bi-building me-1"></i>{{ $activity->event->name }}
                                                </small>
                                                <small class="text-primary ps-2 fw-bold" style="font-size: 0.8rem;">
                                                    Ver detalles <i class="bi bi-arrow-right"></i>
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 bg-light rounded-3">
                            <i class="bi bi-mic-mute text-muted display-4 mb-3"></i>
                            <p class="text-muted mb-0">Este usuario no ha registrado actividades públicas aún.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection