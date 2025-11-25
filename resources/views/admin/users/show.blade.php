@extends('layouts.app')

@section('header', 'Detalles de Usuario')

@section('content')
<div class="container-fluid py-4">
    {{-- Header / Breadcrumb --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>
        </nav>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-evai-outline btn-sm">
            <i class="bi bi-pencil-fill me-2"></i>Editar Perfil
        </a>
    </div>

    <div class="row g-4">
        {{-- Columna Izquierda: Tarjeta de Perfil --}}
        <div class="col-lg-4">
            <div class="card-admin h-100 text-center position-relative">
                <div class="card-header-admin h-100px" style="background: linear-gradient(135deg, var(--evai-blue-deep) 0%, var(--evai-blue-main) 100%); height: 120px;"></div>
                
                <div class="card-body px-4 pb-5 mt-n5">
                    <div class="position-relative d-inline-block mb-3" style="margin-top: -60px;">
                        @if($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                 class="avatar-circle bg-white p-1" width="120" height="120">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=0C2340&color=fff"
                                 class="avatar-circle bg-white p-1" width="120" height="120">
                        @endif
                        @if($user->id === auth()->id())
                            <span class="position-absolute bottom-0 end-0 badge rounded-circle bg-success border border-white p-2" title="Tú">
                                <span class="visually-hidden">Tú</span>
                            </span>
                        @endif
                    </div>

                    <h4 class="fw-bold text-brand-deep mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
                        @foreach($user->roles as $role)
                            @php
                                $badgeClass = match($role->name) {
                                    'Administrador' => 'admin',
                                    'Organizador' => 'organizer',
                                    default => 'user'
                                };
                            @endphp
                            <span class="badge-role {{ $badgeClass }}">{{ $role->name }}</span>
                        @endforeach
                    </div>

                    <div class="row g-0 border-top pt-4">
                        <div class="col-6 border-end">
                            <h5 class="fw-bold text-brand-main mb-0">{{ $user->registrations->count() }}</h5>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Inscripciones</small>
                        </div>
                        <div class="col-6">
                            <h5 class="fw-bold text-brand-main mb-0">{{ $user->created_at->diffForHumans(null, true) }}</h5>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Antigüedad</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Detalles --}}
        <div class="col-lg-8">
            
            {{-- Perfil Profesional --}}
            @if($user->professionalProfile)
                <div class="card-admin mb-4">
                    <div class="card-header-admin">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-person-vcard-fill me-2"></i>Perfil Profesional
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Ocupación Actual</label>
                                <p class="fw-medium text-dark mb-0">
                                    {{ $user->professionalProfile->occupation ?? 'No especificada' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Institución / Empresa</label>
                                <p class="fw-medium text-dark mb-0">
                                    {{ $user->professionalProfile->institution ?? 'No especificada' }}
                                </p>
                            </div>
                            @if($user->professionalProfile->bio)
                                <div class="col-12">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Biografía</label>
                                    <p class="text-secondary mb-0 fst-italic bg-light p-3 rounded-3">
                                        "{{ $user->professionalProfile->bio }}"
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actividad Reciente (Inscripciones) --}}
            <div class="card-admin">
                <div class="card-header-admin accent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-ticket-detailed-fill me-2"></i>Últimas Inscripciones
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($user->registrations->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-ticket-perforated text-muted opacity-25 display-4"></i>
                            </div>
                            <p class="text-muted mb-0">Este usuario no se ha inscrito a ningún evento.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 text-muted small text-uppercase">Evento</th>
                                        <th class="text-muted small text-uppercase">Actividad</th>
                                        <th class="text-muted small text-uppercase">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->registrations->take(5) as $registration)
                                        <tr>
                                            <td class="ps-4 fw-medium text-brand-deep">
                                                {{ $registration->component->event->name ?? 'Evento Eliminado' }}
                                            </td>
                                            <td class="text-secondary">
                                                {{ $registration->component->name ?? 'Componente Eliminado' }}
                                            </td>
                                            <td class="text-muted small">
                                                {{ $registration->created_at->format('d M, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($user->registrations->count() > 5)
                            <div class="card-footer bg-white text-center py-3 border-top-0">
                                <a href="#" class="text-decoration-none text-brand-main fw-bold small">
                                    Ver historial completo <i class="bi bi-arrow-right ms-1"></i>
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
