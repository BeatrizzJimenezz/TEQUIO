@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <div class="col-12">
            <div class="pt-0 px-4">

                <div class="d-flex justify-content-between align-items-center mb-4 mt-0">
                    <div>
                        <h2 class="mb-1">Enviar Propuesta</h2>
                        <p class="text-muted mb-0">Selecciona un evento para enviar tu propuesta de ponencia o taller</p>
                    </div>
                    <a href="{{ route('proposals.my-proposals') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list-check"></i> Mis Propuestas
                    </a>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @forelse($events as $event)
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">

                                <h5 class="card-title mb-3">{{ $event->name }}</h5>

                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar"></i>
                                    {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                                </p>

                                <p class="text-muted mb-2">
                                    <i class="bi bi-person"></i>
                                    Organizado por:
                                    {{ $event->professionalProfile->first_name }} 
                                    {{ $event->professionalProfile->last_name }}
                                </p>

                                <div class="mb-3">
                                    <span class="badge bg-info text-capitalize">
                                        {{ ucfirst($event->modality) }}
                                    </span>

                                    <span class="badge bg-success text-capitalize">
                                        {{ ucfirst($event->status) }}
                                    </span>

                                    @if($event->location)
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                    </span>
                                    @endif
                                </div>

                                <p class="card-text mb-0">
                                    {{ $event->description }}
                                </p>
                            </div>

                            <div class="ms-4">
                                <a href="#" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Enviar Propuesta
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @empty
                <div class="alert alert-info">
                    No hay eventos disponibles por el momento.
                </div>
                @endforelse

            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: white;
        margin: 0;
        padding: 0;
    }

    .container-fluid {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .btn-primary {
        background-color: #0E2FBC;
        border-color: #0E2FBC;
    }

    .btn-primary:hover {
        background-color: #0E2773;
        border-color: #0E2773;
    }

    .btn-outline-primary {
        color: #0E2FBC;
        border-color: #0E2FBC;
    }

    .btn-outline-primary:hover {
        background-color: #0E2FBC;
        border-color: #0E2FBC;
        color: white;
    }

    .badge.bg-info {
        background-color: #0D8B8C !important;
    }

    .badge.bg-success {
        background-color: #0E2FBC !important;
    }
</style>
@endsection
