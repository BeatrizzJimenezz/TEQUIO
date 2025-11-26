@extends('layouts.app')

@section('header', 'Detalles de Usuario')

@push('styles')
<style>
    :root {
        --brand-deep: #0C2340;
        --brand-medium: #1B3A5B;
        --brand-light: #4499BB;
        --brand-accent: #8CC63F;
        --bg-surface: #F8F9FA;
        --text-secondary: #6C757D;
    }

    body { background-color: var(--bg-surface); }

    /* --- Hero Header Compacto --- */
    .hero-header-sm {
        background: linear-gradient(135deg, var(--brand-deep) 0%, var(--brand-medium) 100%);
        color: white;
        border-radius: 1rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        margin-bottom: -3rem;
        box-shadow: 0 10px 30px rgba(12, 35, 64, 0.15);
        z-index: 1;
    }

    /* --- Tarjetas --- */
    .admin-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        border: none;
        position: relative;
        z-index: 0;
        overflow: hidden;
    }

    .main-card-offset {
        margin-top: 1rem;
        padding-top: 3.5rem; 
    }

    /* --- Perfil Estilizado --- */
    .profile-cover {
        height: 130px;
        background: linear-gradient(135deg, #1B3A5B 0%, #4499BB 100%);
        position: relative;
    }

    .profile-avatar-container {
        position: absolute;
        bottom: -50px;
        left: 50%;
        transform: translateX(-50%);
    }

    .profile-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        object-fit: cover;
        background-color: white;
    }

    /* --- Badges de Rol --- */
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 1rem;
        border-radius: 50rem;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .role-admin { background-color: rgba(12, 35, 64, 0.1); color: var(--brand-deep); }
    .role-organizer { background-color: rgba(68, 153, 187, 0.1); color: var(--brand-light); }
    .role-participant { background-color: rgba(140, 198, 63, 0.1); color: var(--brand-accent); }

    /* --- Secciones de Detalles --- */
    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-secondary);
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .detail-value {
        font-size: 1rem;
        font-weight: 500;
        color: var(--brand-deep);
    }

    /* --- Tabla Historial --- */
    .table-clean th {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--text-secondary);
        font-weight: 700;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 1rem;
    }
    .table-clean td {
        padding: 1rem 0.5rem;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .table-clean tr:last-child td { border-bottom: none; }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- 1. Hero Header --}}
    <div class="hero-header-sm d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light rounded-circle p-2" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Detalles de Usuario</h4>
                <p class="mb-0 small opacity-75">Visualizando perfil de: <strong>{{ $user->name }}</strong></p>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario permanentemente?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-light border-0 d-flex align-items-center gap-2" style="background: rgba(255,0,0,0.2);">
                        <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Eliminar</span>
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-light text-brand-deep fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">Editar Perfil</span>
            </a>
        </div>
        
        <i class="bi bi-person-vcard hero-pattern text-white opacity-25" style="font-size: 5rem; position: absolute; right: -20px; bottom: -20px;"></i>
    </div>

    <div class="row g-4 mt-2">
        
        {{-- COLUMNA IZQUIERDA: Resumen de Perfil --}}
        <div class="col-lg-4">
            <div class="admin-card h-100">
                <div class="profile-cover">
                    <div class="profile-avatar-container">
                        @if($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" class="profile-avatar">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=0C2340&color=fff" class="profile-avatar">
                        @endif
                        
                        @if($user->id === auth()->id())
                            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-2 border-white rounded-circle" title="Tú"></span>
                        @endif
                    </div>
                </div>

                <div class="card-body text-center pt-5 px-4 pb-4">
                    <h4 class="fw-bold text-brand-deep mb-1 mt-3">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                        @foreach($user->roles as $role)
                            @php
                                $badgeClass = match($role->name) {
                                    'Administrador' => 'role-admin',
                                    'Organizador'   => 'role-organizer',
                                    default         => 'role-participant'
                                };
                                $icon = match($role->name) {
                                    'Administrador' => 'bi-shield-shaded',
                                    'Organizador'   => 'bi-calendar-check',
                                    default         => 'bi-person'
                                };
                            @endphp
                            <span class="role-badge {{ $badgeClass }}">
                                <i class="bi {{ $icon }} me-1"></i> {{ $role->name }}
                            </span>
                        @endforeach
                    </div>

                    <div class="row g-0 border-top pt-4 mt-2">
                        <div class="col-6 border-end">
                            <h3 class="fw-bold text-brand-light mb-0">{{ $user->registrations->count() }}</h3>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Inscripciones</small>
                        </div>
                        <div class="col-6">
                            <h3 class="fw-bold text-brand-light mb-0">{{ $user->created_at->diffInDays() }}</h3>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Días Activo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Detalles e Historial --}}
        <div class="col-lg-8">
            
            {{-- 1. Tarjeta Información Profesional --}}
            @if($user->professionalProfile)
                <div class="admin-card mb-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="bg-light rounded-circle p-2 text-brand-deep me-3">
                            <i class="bi bi-briefcase-fill fs-5"></i>
                        </div>
                        <h6 class="fw-bold text-uppercase mb-0 text-brand-deep">Perfil Profesional</h6>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-label">Ocupación</div>
                            <div class="detail-value">{{ $user->professionalProfile->occupation ?? 'No especificada' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Institución / Empresa</div>
                            <div class="detail-value">{{ $user->professionalProfile->institution ?? 'No especificada' }}</div>
                        </div>
                        @if($user->professionalProfile->bio)
                            <div class="col-12">
                                <div class="detail-label mb-2">Biografía</div>
                                <div class="p-3 bg-light rounded-3 text-secondary fst-italic">
                                    "{{ $user->professionalProfile->bio }}"
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 2. Tarjeta Historial Reciente --}}
            <div class="admin-card">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 text-brand-accent me-3">
                            <i class="bi bi-ticket-detailed-fill fs-5"></i>
                        </div>
                        <h6 class="fw-bold text-uppercase mb-0 text-brand-deep">Actividad Reciente</h6>
                    </div>
                    @if($user->registrations->isNotEmpty())
                        <span class="badge bg-light text-secondary border">Últimos 5 registros</span>
                    @endif
                </div>

                @if($user->registrations->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-3 text-muted opacity-25">
                            <i class="bi bi-inbox-fill display-4"></i>
                        </div>
                        <p class="text-muted fw-bold mb-1">Sin actividad registrada</p>
                        <p class="text-muted small">El usuario no se ha inscrito a ningún evento aún.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-clean w-100">
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th>Actividad</th>
                                    <th class="text-end">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->registrations->take(5) as $registration)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded bg-light p-1 me-2 text-brand-light">
                                                    <i class="bi bi-calendar-event"></i>
                                                </div>
                                                <span class="fw-bold text-dark">
                                                    {{ $registration->component->event->name ?? 'Evento no disponible' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-secondary small">
                                            {{ $registration->component->name ?? 'N/A' }}
                                        </td>
                                        <td class="text-end text-muted small fw-bold">
                                            {{ $registration->created_at->format('d M, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($user->registrations->count() > 5)
                        <div class="text-center mt-3 pt-3 border-top">
                            <a href="#" class="btn btn-link text-brand-light text-decoration-none fw-bold small">
                                Ver historial completo <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</div>
@endsection