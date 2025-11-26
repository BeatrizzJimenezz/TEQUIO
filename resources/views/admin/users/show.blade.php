@extends('layouts.app')

@section('header', 'Detalles de Usuario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-users-show.css') }}">
@endpush

@section('content')
<div class="container py-4">

    {{-- 1. Hero Header (Fondo sólido y amplio) --}}
    <div class="hero-header-sm d-flex justify-content-between align-items-start">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light rounded-circle p-2" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center; border-color: rgba(255,255,255,0.3);">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Perfil de usuario</h4>
                <p class="mb-0 opacity-75 small">Detalles y actividad registrada</p>
            </div>
        </div>
        
        {{-- Botones de Acción Superiores --}}
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-light btn-sm px-3 fw-bold rounded-pill shadow-sm text-brand-deep">
                <i class="bi bi-pencil-fill me-1"></i> Editar
            </a>
        </div>

        <i class="bi bi-person-circle hero-pattern"></i>
    </div>

    {{-- 2. Contenedor Superpuesto (Overlap) --}}
    <div class="overlap-container px-2">
        <div class="row g-4">
            
            {{-- COLUMNA IZQUIERDA: Tarjeta Resumen --}}
            <div class="col-lg-4">
                <div class="admin-card p-0 overflow-hidden h-100">
                    
                    {{-- Fondo de cabecera --}}
                    <div class="profile-header-bg"></div>
                    
                    {{-- Avatar centrado --}}
                    <div class="card-body text-center pt-0 px-4 pb-4">
                        <div class="avatar-wrapper">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" class="profile-avatar">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=0C2340&color=fff" class="profile-avatar">
                            @endif
                            
                            {{-- Indicador si es el propio usuario --}}
                            @if($user->id === auth()->id())
                                <span class="status-indicator" title="Tú"></span>
                            @endif
                        </div>

                        <h5 class="fw-bold text-dark mt-3 mb-1">{{ $user->name }}</h5>
                        <p class="text-muted small mb-3">{{ $user->email }}</p>

                        {{-- Roles --}}
                        <div class="d-flex justify-content-center flex-wrap mb-4">
                            @foreach($user->roles as $role)
                                @php
                                    $badgeClass = match($role->name) {
                                        'Administrador' => 'role-admin',
                                        'Organizador'   => 'role-organizer',
                                        default         => 'role-participant'
                                    };
                                @endphp
                                <span class="role-badge {{ $badgeClass }}">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>

                        {{-- Estadísticas Rápidas --}}
                        <div class="row border-top pt-3 g-0">
                            <div class="col-6 border-end">
                                <h4 class="fw-bold mb-0" style="color: #4499BB;">{{ $user->registrations->count() }}</h4>
                                <small class="text-secondary text-uppercase" style="font-size: 0.65rem; font-weight: 700;">Inscripciones</small>
                            </div>
                            @php
                                $days = (int) $user->created_at->diffInDays(now());
                                $hours = (int) $user->created_at->diffInHours(now());
                                $minutes = (int) $user->created_at->diffInMinutes(now());
                                $seconds = (int) $user->created_at->diffInSeconds(now());

                                if ($days >= 1) {
                                    $activeValue = $days;
                                    $activeLabel = $days == 1 ? 'Día Activo' : 'Días Activo';
                                } elseif ($hours >= 1) {
                                    $activeValue = $hours;
                                    $activeLabel = $hours == 1 ? 'Hora Activo' : 'Horas Activo';
                                } elseif ($minutes >= 1) {
                                    $activeValue = $minutes;
                                    $activeLabel = $minutes == 1 ? 'Minuto Activo' : 'Minutos Activo';
                                } else {
                                    $activeValue = $seconds;
                                    $activeLabel = 'Segundos Activo';
                                }
                            @endphp
                            <div class="col-6">
                                <h4 class="fw-bold mb-0" style="color: #4499BB;">{{ $activeValue }}</h4>
                                <small class="text-secondary text-uppercase" style="font-size: 0.65rem; font-weight: 700;">{{ $activeLabel }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Información Detallada --}}
            <div class="col-lg-8">
                
                {{-- A. Datos Profesionales --}}
                <div class="admin-card mb-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="bg-light rounded-circle p-2 text-brand-deep me-3">
                            <i class="bi bi-briefcase-fill fs-5"></i>
                        </div>
                        <h6 class="fw-bold text-uppercase mb-0 text-brand-deep">Perfil Profesional</h6>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-label">Habilidades</div>
                            <div class="detail-value">{{ $user->professionalProfile->skills ?? 'No especificadas' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Lugar de trabajo </div>
                            <div class="detail-value">{{ $user->professionalProfile->current_workplace ?? 'No especificada' }}</div>
                        </div>
                        @if($user->about_me)
                            <div class="col-12">
                                <div class="detail-label mb-2">Biografía</div>
                                <div class="p-3 bg-light rounded-3 text-secondary fst-italic">
                                    "{{ $user->about_me }}"
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- B. Historial de Inscripciones --}}
                <div class="admin-card p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="bg-light rounded-circle p-2 text-brand-deep me-3">
                            <i class="bi bi-briefcase-fill fs-5"></i>
                        </div>
                        <h6 class="fw-bold text-uppercase mb-0 text-brand-deep">Actividad Reciente</h6>
                        @if($user->registrations->isNotEmpty())
                            <span class="badge bg-light text-secondary border">Últimos 5</span>
                        @endif
                    </div>

                    @if($user->registrations->isEmpty())
                        <div class="text-center py-4">
                            <div class="text-muted opacity-25 mb-2">
                                <i class="bi bi-journal-x display-4"></i>
                            </div>
                            <p class="fw-bold text-secondary mb-0">Sin inscripciones</p>
                            <small class="text-muted">El usuario no ha participado en eventos aún.</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-clean w-100 mb-0">
                                <thead>
                                    <tr>
                                        <th>Evento</th>
                                        <th>Rol / Tipo</th>
                                        <th class="text-end">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->registrations->take(5) as $registration)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box">
                                                        <i class="bi bi-ticket-perforated-fill"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                            {{ $registration->component->event->name ?? 'Evento no disponible' }}
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ Str::limit($registration->component->name ?? '', 30) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border fw-normal">Asistente</span>
                                            </td>
                                            <td class="text-end">
                                                <small class="fw-bold text-secondary">
                                                    {{ $registration->created_at->format('d M, Y') }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($user->registrations->count() > 5)
                            <div class="text-center mt-3 pt-3 border-top">
                                <a href="#" class="btn btn-sm btn-link text-decoration-none fw-bold text-muted">
                                    Ver todo el historial <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection