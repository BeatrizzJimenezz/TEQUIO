@extends('layouts.app')

@section('header', 'Enviar Propuesta')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-send-fill me-2"></i>Enviar Propuesta
            </h2>
            <p class="text-muted">Selecciona un evento para enviar tu propuesta de charla, taller o actividad.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('proposals.my_proposals') }}" class="btn" style="background-color: #4499BB; color: white;">
                <i class="bi bi-list-check me-2"></i>Mis Propuestas
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($events as $event)
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-2" style="color: #0C2340;">{{ $event->name }}</h5>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar-event me-1" style="color: #4499BB;"></i>
                            {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-person me-1" style="color: #4499BB;"></i>
                            <strong>Organizador:</strong> {{ $event->professionalProfile->user->name }}
                        </p>
                        <div class="mb-2">
                            @php
                                $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                $statusLabels = ['draft' => 'Borrador', 'published' => 'Publicado', 'active' => 'Activo', 'finished' => 'Finalizado'];
                            @endphp
                            <span class="badge" style="background-color: #4499BB;">
                                {{ $modalityLabels[$event->modality] ?? ucfirst($event->modality) }}
                            </span>
                            <span class="badge bg-success">
                                {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                            </span>
                            @if($event->location)
                                <span class="badge bg-secondary">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $event->location }}
                                </span>
                            @endif
                        </div>
                        <p class="card-text text-muted">{{ Str::limit($event->description, 200) }}</p>
                    </div>
                    <div class="ms-3">
                        <a href="{{ route('proposals.create', $event) }}" class="btn" style="background-color: #8CC63F; color: white;">
                            <i class="bi bi-send me-2"></i>Enviar Propuesta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-2" style="color: #0C2340;">No hay eventos disponibles</h5>
                <p class="text-muted">Actualmente no hay eventos públicos aceptando propuestas.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
