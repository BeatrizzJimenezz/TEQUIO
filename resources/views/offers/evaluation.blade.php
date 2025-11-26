@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/evaluation-offers.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">

            <div class="card card-banner-blue">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="mb-1">{{ $event->name }}</h5>
                            <p class="mb-0 small opacity-75">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $event->start_date->format('m/d/Y') }} - {{ $event->end_date->format('m/d/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('components.index', $event) }}" class="btn btn-glass btn-sm px-3">
                            <i class="bi bi-arrow-left me-1"></i> Volver al evento
                        </a>
                    </div>
                </div>
            </div>

            <div class="section-header-blue">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard-data-fill"></i>
                    <h2>Panel de Evaluación</h2>
                </div>
                <div>
                    @php
                    $totalPending = $proposals->count() + $offersWithApplications->sum(fn($o) => $o->applications->count());
                    @endphp
                    <span class="badge bg-warning text-dark shadow-sm px-3 py-2 rounded-pill">
                        <i class="bi bi-hourglass-split me-1"></i> {{ $totalPending }} Pendiente
                    </span>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($offersWithApplications->count() > 0)
            <h4 class="mb-3 fw-bold text-brand-deep ps-1 mt-4">
                <i class="bi bi-megaphone-fill me-2 text-brand-accent"></i> Solicitudes de Ofertas Abiertas
            </h4>

            @foreach($offersWithApplications as $offer)
            <div class="card-admin">
                <div class="card-header-blue">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-megaphone me-2"></i> {{ $offer->name }}
                        </h5>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-white text-dark shadow-sm border-0">
                                {{ $offer->applications->count() }} Aplicaciones
                            </span>
                            <form action="{{ route('offers.close', [$event, $offer]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning btn-sm shadow-sm text-dark fw-bold"
                                    onclick="return confirm('Close this offer?')">
                                    <i class="bi bi-lock-fill me-1"></i> Cerrar oferta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4 p-3 bg-light rounded border">
                        <strong class="text-brand-deep d-block mb-1">Descripción de la oferta:</strong>
                        <p class="mb-0 text-muted">{{ $offer->description }}</p>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 fw-bold text-brand-main">Candidatos:</h6>

                    @foreach($offer->applications as $application)
                    <div class="card border-0 shadow-sm mb-3 bg-white">
                        <div class="card-body p-3 border rounded">
                            <div class="row g-3">
                                <div class="col-md-4 border-end">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-circle bg-brand-main text-white me-3 shadow-sm">
                                            {{ strtoupper(substr($application->professionalProfile->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong class="d-block text-brand-deep">{{ $application->professionalProfile->user->name }}</strong>
                                            <small class="text-muted">{{ $application->professionalProfile->user->email }}</small>
                                        </div>
                                    </div>
                                    @if($application->professionalProfile->current_workplace)
                                    <p class="mb-2 text-muted small">
                                        <i class="bi bi-briefcase me-1 text-brand-accent"></i>
                                        {{ $application->professionalProfile->current_workplace }}
                                    </p>
                                    @endif
                                </div>

                                <div class="col-md-8">
                                    @if($application->message)
                                    <div class="mb-3">
                                        <strong class="text-brand-deep">Mensaje:</strong>
                                        <p class="mb-0 mt-1 p-3 bg-light fst-italic border-start border-3 border-brand-accent rounded">
                                            "{{ $application->message }}"
                                        </p>
                                    </div>
                                    @endif

                                    <div class="d-flex gap-2 justify-content-end mt-3">
                                        <form action="{{ route('offers.applications.reject', [$event, $offer, $application->id]) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Rechazar</button>
                                        </form>
                                        <form action="{{ route('offers.applications.accept', [$event, $offer, $application->id]) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-primary btn-sm bg-brand-main border-0">Aceptar y asignar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            @endif

            @if($proposals->count() > 0)
            <h4 class="mb-3 fw-bold text-brand-deep mt-5 ps-1">
                <i class="bi bi-send-fill me-2 text-brand-accent"></i> Propuestas espontáneas
            </h4>
            @endif

            @forelse($proposals as $proposal)
            <div class="card-admin">
                <div class="card-header-blue">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0 fw-bold">{{ $proposal->name }}</h5>
                        <span class="badge bg-warning text-dark shadow-sm">Revisión pendiente</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4 border-end">
                            <div class="d-flex align-items-center mb-3">
                                {{-- LÓGICA INTELIGENTE --}}
                                @php
                                // 1. Intentamos obtener el usuario del Presentador (si existe)
                                // 2. Si no, usamos el usuario que Propuso (proposedBy)
                                $user = $proposal->presenter?->user ?? $proposal->proposedBy;

                                // Valores por defecto si todo falla (para evitar el error "on null")
                                $name = $user?->name ?? 'Usuario Desconocido';
                                $email = $user?->email ?? 'Sin correo';
                                $initials = substr($name, 0, 1);
                                @endphp

                                <div class="avatar-circle bg-brand-deep text-white me-3">
                                    {{ strtoupper($initials) }}
                                </div>
                                <div>
                                    <strong class="d-block text-brand-deep">{{ $name }}</strong>
                                    <small class="text-muted">{{ $email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <strong class="text-brand-deep">Descripción:</strong>
                            <p class="text-muted">{{ $proposal->description }}</p>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <form action="{{ route('proposals.reject', [$event, $proposal]) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-danger btn-sm">Reject</button>
                                </form>
                                <form action="{{ route('proposals.approve', [$event, $proposal]) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-primary btn-sm bg-brand-main border-0">Aprobar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            @if($offersWithApplications->count() == 0)
            <div class="alert alert-light text-center py-5 border">
                <h5 class="text-muted">No hay elementos pendientes</h5>
            </div>
            @endif
            @endforelse

        </div>
    </div>
</div>
@endsection