@extends('layouts.app')

@section('header', 'Enviar Propuesta')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-brand-deep mb-1">
                <i class="bi bi-send-fill me-2"></i>Enviar Propuesta
            </h2>
            <p class="text-muted mb-0">Selecciona un evento para enviar tu propuesta de charla, taller o actividad.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('proposals.my_proposals') }}" class="btn btn-brand-main shadow-sm">
                <i class="bi bi-list-check me-2"></i>Mis Propuestas
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Lista de Eventos --}}
    <div class="row g-4">
        @forelse($events as $event)
            <div class="col-12">
                <div class="card-admin h-100">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h4 class="fw-bold text-brand-deep mb-2">{{ $event->name }}</h4>
                                
                                <div class="d-flex flex-wrap gap-3 mb-3 text-muted">
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-calendar-event text-brand-main me-2"></i>
                                        {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                                    </span>
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-person-fill text-brand-main me-2"></i>
                                        Organizador: <strong class="ms-1">{{ $event->professionalProfile->user->name }}</strong>
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @php
                                        $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                        $statusLabels = ['draft' => 'Borrador', 'published' => 'Publicado', 'active' => 'Activo', 'finished' => 'Finalizado'];
                                    @endphp
                                    
                                    <span class="badge bg-brand-main text-white px-3 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-laptop me-1"></i>{{ $modalityLabels[$event->modality] ?? ucfirst($event->modality) }}
                                    </span>
                                    
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i>{{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                                    </span>

                                    @if($event->location)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2 rounded-pill">
                                            <i class="bi bi-geo-alt-fill me-1"></i>{{ $event->location }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-secondary mb-0">{{ Str::limit($event->description, 200) }}</p>
                            </div>
                            
                            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                                <a href="{{ route('proposals.create', $event) }}" class="btn btn-brand-accent shadow-sm px-4 py-2 fw-bold">
                                    <i class="bi bi-send-fill me-2"></i>Enviar Propuesta
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-admin">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-calendar-x text-muted opacity-25 display-1"></i>
                        </div>
                        <h4 class="fw-bold text-brand-deep">No hay eventos disponibles</h4>
                        <p class="text-muted mb-0">Actualmente no hay eventos públicos aceptando propuestas.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
