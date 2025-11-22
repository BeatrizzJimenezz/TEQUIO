@extends('layouts.app')

@section('header', 'Detalles de Usuario')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn" style="background-color: #4499BB; color: white;">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            {{-- Tarjeta de perfil --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}"
                             class="rounded-circle mb-3" width="100" height="100"
                             style="object-fit: cover;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=100&background=0C2340&color=fff"
                             class="rounded-circle mb-3" width="100" height="100">
                    @endif

                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <div class="mb-3">
                        @foreach($user->roles as $role)
                            <span class="badge
                                @if($role->name === 'Administrador') bg-danger
                                @elseif($role->name === 'Organizador') bg-primary
                                @else bg-secondary
                                @endif px-3 py-2">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </div>

                    <hr>

                    <div class="text-start">
                        <p class="small mb-2">
                            <i class="bi bi-calendar3 me-2"></i>
                            <strong>Registrado:</strong> {{ $user->created_at->format('d/m/Y') }}
                        </p>
                        @if($user->email_verified_at)
                            <p class="small mb-0 text-success">
                                <i class="bi bi-patch-check-fill me-2"></i>Email verificado
                            </p>
                        @else
                            <p class="small mb-0 text-warning">
                                <i class="bi bi-exclamation-circle me-2"></i>Email no verificado
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            {{-- Perfil profesional --}}
            @if($user->professionalProfile)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header" style="background-color: #0C2340; color: white;">
                        <h5 class="mb-0">
                            <i class="bi bi-person-vcard me-2"></i>Perfil Profesional
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="small mb-2">
                                    <strong>Ocupación:</strong><br>
                                    {{ $user->professionalProfile->occupation ?? 'No especificada' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="small mb-2">
                                    <strong>Institución:</strong><br>
                                    {{ $user->professionalProfile->institution ?? 'No especificada' }}
                                </p>
                            </div>
                        </div>
                        @if($user->professionalProfile->bio)
                            <p class="small mb-0">
                                <strong>Biografía:</strong><br>
                                {{ $user->professionalProfile->bio }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Inscripciones recientes --}}
            <div class="card shadow-sm border-0">
                <div class="card-header" style="background-color: #4499BB; color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-ticket me-2"></i>Inscripciones Recientes
                        <span class="badge bg-light text-dark ms-2">{{ $user->registrations->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($user->registrations->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-ticket-perforated text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No tiene inscripciones.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Evento</th>
                                        <th>Componente</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->registrations->take(5) as $registration)
                                        <tr>
                                            <td>{{ $registration->component->event->name ?? 'N/A' }}</td>
                                            <td>{{ $registration->component->name ?? 'N/A' }}</td>
                                            <td>{{ $registration->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($user->registrations->count() > 5)
                            <div class="card-footer text-center bg-white">
                                <small class="text-muted">
                                    Mostrando 5 de {{ $user->registrations->count() }} inscripciones
                                </small>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
