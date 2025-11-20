@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Header --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #4499BB !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #0C2340;">{{ $event->name }}</h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3 me-1" style="color: #4499BB;"></i>
                                {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver a Mis Eventos
                        </a>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <a href="{{ route('components.create', $event) }}" class="btn w-100 text-white shadow-sm" style="background-color: #8CC63F;">
                        <i class="bi bi-plus-lg me-1"></i> Agregar Componente
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('offers.index', $event) }}" class="btn btn-outline-success w-100">
                        <i class="bi bi-megaphone me-1"></i> Gestionar Ofertas
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('offers.evaluation', $event) }}" class="btn btn-outline-warning w-100">
                        <i class="bi bi-clipboard-check me-1"></i> Evaluar Propuestas
                        @php
                            $pendingProposals = $event->components()->where('proposal_status', 'proposed')->count();
                        @endphp
                        @if($pendingProposals > 0)
                            <span class="badge bg-danger ms-1">{{ $pendingProposals }}</span>
                        @endif
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('event.show', $event->id) }}" class="btn w-100" style="background-color: #4499BB; color: white;" target="_blank">
                        <i class="bi bi-eye me-1"></i> Ver Evento Público
                    </a>
                </div>
            </div>

            {{-- Components List --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-collection me-2" style="color: #4499BB;"></i>
                        Componentes del Evento
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @forelse($components as $component)
                        <div class="card mb-3 border {{ $component->proposal_status == 'open_offer' ? 'border-success' : 'border-light' }} shadow-sm">
                            <div class="row g-0">
                                @if($component->cover_image)
                                <div class="col-md-3">
                                    <img src="{{ $component->cover_image }}"
                                         class="img-fluid rounded-start h-100"
                                         alt="{{ $component->name }}"
                                         style="object-fit: cover; max-height: 250px;">
                                </div>
                                @endif
                                <div class="col-md-{{ $component->cover_image ? '9' : '12' }}">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                        {{-- Header --}}
                                        <div class="d-flex align-items-center mb-3">
                                            <h5 class="mb-0 me-3 fw-bold" style="color: #0C2340;">{{ $component->name }}</h5>

                                            @if($component->proposal_status == 'open_offer')
                                                <span class="badge" style="background-color: #8CC63F;">
                                                    <i class="bi bi-megaphone me-1"></i> Oferta Abierta
                                                </span>
                                            @elseif($component->proposal_status == 'proposed')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock me-1"></i> Propuesta Externa
                                                </span>
                                            @else
                                                <span class="badge" style="background-color: #4499BB;">
                                                    <i class="bi bi-check-circle me-1"></i> Aprobado
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Badges --}}
                                        <div class="mb-3">
                                            <span class="badge me-1" style="background-color: #0C2340;">{{ ucfirst($component->type) }}</span>
                                            <span class="badge me-1" style="background-color: #4499BB;">{{ ucfirst($component->modality) }}</span>

                                            @if($component->level)
                                                <span class="badge bg-secondary me-1">{{ ucfirst($component->level) }}</span>
                                            @endif

                                            @if($component->capacity)
                                                <span class="badge bg-light text-dark border me-1">
                                                    <i class="bi bi-people me-1"></i>{{ $component->capacity }} cupos
                                                </span>
                                            @endif

                                            @if($component->attendee_price > 0)
                                                <span class="badge" style="background-color: #8CC63F;">
                                                    ${{ number_format($component->attendee_price, 2) }}
                                                </span>
                                            @else
                                                <span class="badge" style="background-color: #8CC63F;">Gratis</span>
                                            @endif
                                        </div>

                                        {{-- Speaker --}}
                                        @if($component->speaker)
                                            <p class="mb-2">
                                                <i class="bi bi-person me-1" style="color: #4499BB;"></i>
                                                <span class="text-muted">Ponente:</span>
                                                <strong>{{ $component->speaker->user->name }}</strong>
                                            </p>
                                        @endif

                                        {{-- Description --}}
                                        <p class="text-muted mb-3">{{ Str::limit($component->description, 150) }}</p>

                                        {{-- Location --}}
                                        @if($component->location)
                                        <p class="mb-2">
                                            <i class="bi bi-geo-alt me-1" style="color: #8CC63F;"></i>
                                            <span class="text-muted">{{ $component->location }}</span>
                                        </p>
                                        @endif

                                        {{-- Schedules --}}
                                        @if($component->schedules->count() > 0)
                                        <div class="mt-3 p-3 rounded" style="background-color: #f8f9fa;">
                                            <strong class="d-block mb-2" style="color: #0C2340;">
                                                <i class="bi bi-clock-history me-1" style="color: #4499BB;"></i>
                                                Horarios:
                                            </strong>
                                            @foreach($component->schedules as $schedule)
                                                <span class="badge bg-light text-dark border me-2 mb-1">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $schedule->date->format('d/m/Y') }}
                                                    de {{ substr($schedule->start_time, 0, 5) }} a {{ substr($schedule->end_time, 0, 5) }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    <div class="ms-4">
                                        <div class="btn-group-vertical" role="group">
                                            @if($component->proposal_status != 'open_offer')
                                                <a href="{{ route('components.edit', [$event, $component]) }}"
                                                   class="btn btn-sm btn-outline-warning mb-1">
                                                    <i class="bi bi-pencil me-1"></i> Editar
                                                </a>
                                            @endif

                                            <a href="{{ route('schedules.index', [$event, $component]) }}"
                                               class="btn btn-sm mb-1"
                                               style="background-color: #4499BB; color: white;">
                                                <i class="bi bi-calendar-event me-1"></i> Horarios
                                            </a>

                                            <form action="{{ route('components.destroy', [$event, $component]) }}"
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                                        onclick="return confirm('¿Estás seguro de eliminar este componente?')">
                                                    <i class="bi bi-trash me-1"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-collection" style="font-size: 4rem; color: #C8CCC9;"></i>
                            </div>
                            <h5 class="text-muted mb-3">No hay componentes para este evento</h5>
                            <p class="text-muted mb-4">Agrega talleres, presentaciones o actividades a tu evento.</p>
                            <a href="{{ route('components.create', $event) }}" class="btn btn-lg text-white" style="background-color: #8CC63F;">
                                <i class="bi bi-plus-lg me-1"></i> Agregar el primer componente
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
