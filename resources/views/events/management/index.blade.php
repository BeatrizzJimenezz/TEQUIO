@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Header --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #4499BB !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #0C2340;">
                                <i class="bi bi-gear-fill me-2" style="color: #4499BB;"></i>
                                Gestión del Evento
                            </h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3 me-1" style="color: #4499BB;"></i>
                                {{ $event->name }} | {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('event.show', $event->id) }}" class="btn me-2" style="background-color: #4499BB; color: white;" target="_blank">
                                <i class="bi bi-eye me-1"></i> Ver Evento Público
                            </a>
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Volver a Mis Eventos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Success/Error Messages --}}
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

            {{-- Navigation Tabs --}}
            <ul class="nav nav-tabs nav-fill mb-4 border-0 shadow-sm" id="eventManagementTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="components-tab" data-bs-toggle="tab" data-bs-target="#components" type="button" role="tab">
                        <i class="bi bi-collection me-2"></i>Componentes
                        <span class="badge bg-secondary ms-1">{{ $components->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab">
                        <i class="bi bi-calendar-week me-2"></i>Agenda
                        @if($conflicts->isNotEmpty())
                            <span class="badge bg-danger ms-1">{{ $conflicts->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab">
                        <i class="bi bi-people-fill me-2"></i>Equipo
                        <span class="badge bg-secondary ms-1">{{ $collaborators->count() + 1 }}</span>
                    </button>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content" id="eventManagementTabsContent">
                
                {{-- COMPONENTS TAB --}}
                <div class="tab-pane fade show active" id="components" role="tabpanel">
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

                {{-- SCHEDULE TAB --}}
                <div class="tab-pane fade" id="schedule" role="tabpanel">
                    {{-- Conflicts Alert --}}
                    @if($conflicts->isNotEmpty())
                        <div class="alert border-0 shadow-sm mb-4" style="background-color: #fff5f5; border-left: 4px solid #dc3545 !important;">
                            <h5 class="alert-heading fw-bold" style="color: #dc3545;">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Se detectaron {{ $conflicts->count() }} conflicto(s) de horario
                            </h5>
                            <hr>
                            @foreach($conflicts as $conflict)
                                <div class="mb-3 p-3 rounded" style="background-color: rgba(220, 53, 69, 0.1);">
                                    <strong style="color: #0C2340;">{{ $conflict['component']->name }}</strong>
                                    <span class="text-muted">
                                        ({{ $conflict['schedule']->date->format('d/m/Y') }} -
                                        {{ substr($conflict['schedule']->start_time, 0, 5) }} a {{ substr($conflict['schedule']->end_time, 0, 5) }})
                                    </span>
                                    <ul class="mb-0 mt-2">
                                        @foreach($conflict['errors'] as $error)
                                            <li class="text-danger">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert border-0 shadow-sm mb-4" style="background-color: #f0fff4; border-left: 4px solid #8CC63F !important;">
                            <i class="bi bi-check-circle-fill me-2" style="color: #8CC63F;"></i>
                            <strong>No hay conflictos de horario detectados</strong>
                        </div>
                    @endif

                    {{-- Statistics --}}
                    <div class="row mb-4 g-3">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4499BB 0%, #3a8aab 100%);">
                                <div class="card-body text-center text-white py-4">
                                    <i class="bi bi-collection mb-2" style="font-size: 2rem;"></i>
                                    <h2 class="mb-0 fw-bold">{{ $event->components->count() }}</h2>
                                    <small class="opacity-75">Componentes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8CC63F 0%, #7ab635 100%);">
                                <div class="card-body text-center text-white py-4">
                                    <i class="bi bi-calendar-check mb-2" style="font-size: 2rem;"></i>
                                    <h2 class="mb-0 fw-bold">{{ $schedulesByDate->flatten(1)->count() }}</h2>
                                    <small class="opacity-75">Sesiones Programadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, {{ $conflicts->isEmpty() ? '#6c757d' : '#dc3545' }} 0%, {{ $conflicts->isEmpty() ? '#5a6268' : '#c82333' }} 100%);">
                                <div class="card-body text-center text-white py-4">
                                    <i class="bi bi-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                                    <h2 class="mb-0 fw-bold">{{ $conflicts->count() }}</h2>
                                    <small class="opacity-75">Conflictos</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule by Date --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                                <i class="bi bi-list-ul me-2" style="color: #4499BB;"></i>
                                Agenda por Fecha
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($schedulesByDate->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-calendar-x" style="font-size: 4rem; color: #C8CCC9;"></i>
                                    </div>
                                    <h5 class="text-muted">No hay horarios programados</h5>
                                    <p class="text-muted mb-3">
                                        Agrega horarios a los componentes para verlos aquí.
                                    </p>
                                    <button class="btn" style="background-color: #4499BB; color: white;" onclick="document.getElementById('components-tab').click()">
                                        <i class="bi bi-collection me-1"></i>
                                        Ver componentes
                                    </button>
                                </div>
                            @else
                                @foreach($schedulesByDate as $date => $schedules)
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-3 p-3 rounded" style="background-color: #f8f9fa;">
                                            <i class="bi bi-calendar3 me-3" style="font-size: 1.5rem; color: #4499BB;"></i>
                                            <h6 class="mb-0 fw-bold" style="color: #0C2340;">
                                                {{ \Carbon\Carbon::parse($date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                            </h6>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead>
                                                    <tr style="background-color: #f8f9fa;">
                                                        <th style="width: 120px;" class="border-0">Horario</th>
                                                        <th class="border-0">Componente</th>
                                                        <th class="border-0">Tipo</th>
                                                        <th class="border-0">Ubicación</th>
                                                        <th class="border-0">Ponente</th>
                                                        <th style="width: 100px;" class="border-0 text-center">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($schedules as $item)
                                                        @php
                                                            $schedule = $item['schedule'];
                                                            $component = $item['component'];
                                                            $hasConflict = $conflicts->contains(function($c) use ($schedule) {
                                                                return $c['schedule']->id === $schedule->id;
                                                            });
                                                        @endphp
                                                        <tr class="{{ $hasConflict ? 'table-danger' : '' }}">
                                                            <td>
                                                                <span class="fw-bold" style="color: #0C2340;">{{ substr($schedule->start_time, 0, 5) }}</span>
                                                                <span class="text-muted">-</span>
                                                                <span>{{ substr($schedule->end_time, 0, 5) }}</span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('schedules.index', [$event, $component]) }}"
                                                                   class="text-decoration-none fw-semibold"
                                                                   style="color: #4499BB;">
                                                                    {{ $component->name }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <span class="badge" style="background-color: #0C2340;">
                                                                    {{ ucfirst($component->type) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($component->location)
                                                                    <i class="bi bi-geo-alt me-1" style="color: #8CC63F;"></i>
                                                                    {{ $component->location }}
                                                                    <small class="text-muted">({{ ucfirst($component->modality) }})</small>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($component->speaker && $component->speaker->user)
                                                                    <i class="bi bi-person me-1" style="color: #4499BB;"></i>
                                                                    {{ $component->speaker->user->name }}
                                                                @else
                                                                    <span class="text-muted">Sin asignar</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($hasConflict)
                                                                    <span class="badge bg-danger">
                                                                        <i class="bi bi-exclamation-triangle"></i> Conflicto
                                                                    </span>
                                                                @else
                                                                    <span class="badge" style="background-color: #8CC63F;">
                                                                        <i class="bi bi-check"></i> OK
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TEAM TAB --}}
                <div class="tab-pane fade" id="team" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <p class="text-muted mb-0">Gestiona el equipo organizador del evento</p>
                        <a href="{{ route('events.team.create', $event) }}" class="btn text-white" style="background-color: #8CC63F;">
                            <i class="bi bi-person-plus-fill"></i> Agregar Organizador
                        </a>
                    </div>

                    {{-- Lead Organizer --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header" style="background-color: #0C2340; color: white;">
                            <h5 class="mb-0">
                                <i class="bi bi-star-fill" style="color: #FFD700;"></i> Organizador Principal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3" style="width: 60px; height: 60px; background-color: #4499BB; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 24px; font-weight: bold;">
                                    {{ strtoupper(substr($leadOrganizer->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $leadOrganizer->user->name }}</h5>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-envelope"></i> {{ $leadOrganizer->user->email }}
                                    </p>
                                    @if($leadOrganizer->current_workplace)
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-briefcase"></i> {{ $leadOrganizer->current_workplace }}
                                    </p>
                                    @endif
                                </div>
                                <span class="badge bg-warning text-dark">Creador</span>
                            </div>
                        </div>
                    </div>

                    {{-- Co-Organizers --}}
                    @if($collaborators->count() > 0)
                    <div class="card shadow-sm">
                        <div class="card-header" style="background-color: #0C2340; color: white;">
                            <h5 class="mb-0">
                                <i class="bi bi-people"></i> Co-Organizadores ({{ $collaborators->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead style="background-color: #F8F9FA;">
                                        <tr>
                                            <th>Organizador</th>
                                            <th>Email</th>
                                            <th>Lugar de Trabajo</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($collaborators as $collaborator)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2" style="width: 40px; height: 40px; background-color: #4499BB; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 16px; font-weight: bold;">
                                                        {{ strtoupper(substr($collaborator->user->name, 0, 1)) }}
                                                    </div>
                                                    <strong>{{ $collaborator->user->name }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $collaborator->user->email }}</td>
                                            <td>
                                                @if($collaborator->current_workplace)
                                                    {{ $collaborator->current_workplace }}
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $collaborator->id }}">
                                                    <i class="bi bi-trash"></i> Remover
                                                </button>

                                                <div class="modal fade" id="deleteModal{{ $collaborator->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="background-color: #0C2340; color: white;">
                                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                ¿Estás seguro de que deseas remover a <strong>{{ $collaborator->user->name }}</strong> del equipo organizador?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <form action="{{ route('events.team.destroy', [$event, $collaborator->id]) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Sí, Remover</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-people" style="font-size: 4rem; color: #CCC;"></i>
                            <h4 class="mt-3 text-muted">No hay co-organizadores</h4>
                            <p class="text-muted">Agrega organizadores adicionales para gestionar este evento en equipo.</p>
                            <a href="{{ route('events.team.create', $event) }}" class="btn text-white mt-2" style="background-color: #8CC63F;">
                                <i class="bi bi-person-plus-fill"></i> Agregar Primer Co-Organizador
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection