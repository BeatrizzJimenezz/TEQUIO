@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Header --}}
            <div class="card-admin mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h3 class="mb-1 fw-bold text-brand-deep">
                                <i class="bi bi-gear-fill me-2 text-brand-main"></i>
                                Gestión del Evento
                            </h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3 me-1 text-brand-main"></i>
                                {{ $event->name }} | {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('event.show', $event->id) }}" class="btn btn-brand-main shadow-sm" target="_blank">
                                <i class="bi bi-eye-fill me-1"></i> Ver Evento Público
                            </a>
                            <a href="{{ route('events.index') }}" class="btn btn-white shadow-sm">
                                <i class="bi bi-arrow-left me-1"></i> Volver a Mis Eventos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Navigation Tabs --}}
            <div class="card-admin mb-4 overflow-hidden">
                <ul class="nav nav-pills nav-fill p-2 bg-light" id="eventManagementTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold rounded-3 py-2" id="components-tab" data-bs-toggle="tab" data-bs-target="#components" type="button" role="tab">
                            <i class="bi bi-collection-fill me-2"></i>Componentes
                            <span class="badge bg-white text-brand-deep ms-1 shadow-sm">{{ $components->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold rounded-3 py-2" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab">
                            <i class="bi bi-calendar-week-fill me-2"></i>Agenda
                            @if($conflicts->isNotEmpty())
                                <span class="badge bg-danger ms-1 shadow-sm">{{ $conflicts->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold rounded-3 py-2" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab">
                            <i class="bi bi-people-fill me-2"></i>Equipo
                            <span class="badge bg-white text-brand-deep ms-1 shadow-sm">{{ $collaborators->count() + 1 }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Tab Content --}}
            <div class="tab-content" id="eventManagementTabsContent">
                
                {{-- COMPONENTS TAB --}}
                <div class="tab-pane fade show active" id="components" role="tabpanel">
                    {{-- Action Buttons --}}
                    <div class="row mb-4 g-3">
                        <div class="col-md-4">
                            <a href="{{ route('components.create', $event) }}" class="btn btn-brand-accent w-100 shadow-sm py-2">
                                <i class="bi bi-plus-lg me-1"></i> Agregar Componente
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('offers.index', $event) }}" class="btn btn-white w-100 shadow-sm py-2 text-success border-success">
                                <i class="bi bi-megaphone-fill me-1"></i> Gestionar Ofertas
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('offers.evaluation', $event) }}" class="btn btn-white w-100 shadow-sm py-2 text-warning border-warning">
                                <i class="bi bi-clipboard-check-fill me-1"></i> Evaluar Propuestas
                                @php
                                    $pendingProposals = $event->components()->where('proposal_status', 'proposed')->count();
                                @endphp
                                @if($pendingProposals > 0)
                                    <span class="badge bg-danger ms-1 rounded-pill">{{ $pendingProposals }}</span>
                                @endif
                            </a>
                        </div>
                    </div>

                    {{-- Components List --}}
                    <div class="card-admin">
                        <div class="card-header-admin">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-collection-fill me-2"></i>
                                Componentes del Evento
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @forelse($components as $component)
                                <div class="card border-0 shadow-sm mb-3 overflow-hidden {{ $component->proposal_status == 'open_offer' ? 'border-start border-5 border-success' : '' }}">
                                    <div class="row g-0">
                                        @if($component->cover_image)
                                        <div class="col-md-3">
                                            <img src="{{ $component->cover_image }}"
                                                 class="img-fluid h-100 w-100"
                                                 alt="{{ $component->name }}"
                                                 style="object-fit: cover; min-height: 200px;">
                                        </div>
                                        @endif
                                        <div class="col-md-{{ $component->cover_image ? '9' : '12' }}">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                                                    <div class="flex-grow-1">
                                                        {{-- Header --}}
                                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                                            <h5 class="mb-0 fw-bold text-brand-deep">{{ $component->name }}</h5>

                                                            @if($component->proposal_status == 'open_offer')
                                                                <span class="badge bg-brand-accent text-white rounded-pill px-3">
                                                                    <i class="bi bi-megaphone-fill me-1"></i> Oferta Abierta
                                                                </span>
                                                            @elseif($component->proposal_status == 'proposed')
                                                                <span class="badge bg-warning text-dark rounded-pill px-3">
                                                                    <i class="bi bi-clock-fill me-1"></i> Propuesta Externa
                                                                </span>
                                                            @else
                                                                <span class="badge bg-brand-main text-white rounded-pill px-3">
                                                                    <i class="bi bi-check-circle-fill me-1"></i> Aprobado
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Badges --}}
                                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                                            <span class="badge bg-brand-deep text-white rounded-pill px-3 py-2">
                                                                {{ ucfirst($component->type) }}
                                                            </span>
                                                            <span class="badge bg-brand-main text-white rounded-pill px-3 py-2">
                                                                {{ ucfirst($component->modality) }}
                                                            </span>

                                                            @if($component->level)
                                                                <span class="badge bg-secondary text-white rounded-pill px-3 py-2">
                                                                    {{ ucfirst($component->level) }}
                                                                </span>
                                                            @endif

                                                            @if($component->capacity)
                                                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                                                    <i class="bi bi-people-fill me-1"></i>{{ $component->capacity }} cupos
                                                                </span>
                                                            @endif

                                                            @if($component->attendee_price > 0)
                                                                <span class="badge bg-brand-accent text-white rounded-pill px-3 py-2">
                                                                    ${{ number_format($component->attendee_price, 2) }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-brand-accent text-white rounded-pill px-3 py-2">Gratis</span>
                                                            @endif
                                                        </div>

                                                        {{-- Speaker --}}
                                                        @if($component->speaker)
                                                            <p class="mb-2">
                                                                <i class="bi bi-person-fill me-1 text-brand-main"></i>
                                                                <span class="text-muted">Ponente:</span>
                                                                <strong class="text-brand-deep">{{ $component->speaker->user->name ?? 'Usuario no disponible' }}</strong>
                                                            </p>
                                                        @endif

                                                        {{-- Description --}}
                                                        <p class="text-muted mb-3 small">{{ Str::limit($component->description, 150) }}</p>

                                                        {{-- Location --}}
                                                        @if($component->location)
                                                        <p class="mb-2 small">
                                                            <i class="bi bi-geo-alt-fill me-1 text-brand-accent"></i>
                                                            <span class="text-muted">{{ $component->location }}</span>
                                                        </p>
                                                        @endif

                                                        {{-- Schedules --}}
                                                        @if($component->schedules->count() > 0)
                                                        <div class="mt-3 p-3 rounded-3 bg-light">
                                                            <strong class="d-block mb-2 text-brand-deep small">
                                                                <i class="bi bi-clock-history me-1 text-brand-main"></i>
                                                                Horarios:
                                                            </strong>
                                                            <div class="d-flex flex-wrap gap-2">
                                                                @foreach($component->schedules as $schedule)
                                                                    <span class="badge bg-white text-dark border fw-normal">
                                                                        <i class="bi bi-calendar3 me-1 text-muted"></i>
                                                                        {{ $schedule->date->format('d/m/Y') }}
                                                                        <span class="text-muted mx-1">|</span>
                                                                        {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>

                                                    {{-- Actions --}}
                                                    <div class="d-flex flex-column gap-2">
                                                        @if($component->proposal_status != 'open_offer')
                                                            <a href="{{ route('components.edit', [$event, $component]) }}"
                                                               class="btn btn-sm btn-white text-warning border-warning">
                                                                <i class="bi bi-pencil-fill me-1"></i> Editar
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('schedules.index', [$event, $component]) }}"
                                                           class="btn btn-sm btn-brand-main text-white">
                                                            <i class="bi bi-calendar-event-fill me-1"></i> Horarios
                                                        </a>

                                                        <form action="{{ route('components.destroy', [$event, $component]) }}"
                                                              method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-white text-danger border-danger w-100"
                                                                    onclick="return confirm('¿Estás seguro de eliminar este componente?')">
                                                                <i class="bi bi-trash-fill me-1"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="bi bi-collection-fill text-muted opacity-25 display-1"></i>
                                    </div>
                                    <h5 class="text-brand-deep fw-bold mb-3">No hay componentes para este evento</h5>
                                    <p class="text-muted mb-4">Agrega talleres, presentaciones o actividades a tu evento.</p>
                                    <a href="{{ route('components.create', $event) }}" class="btn btn-brand-accent shadow-sm px-4 py-2">
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
                        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                            <div>
                                <h5 class="alert-heading fw-bold mb-1">Se detectaron {{ $conflicts->count() }} conflicto(s) de horario</h5>
                                <p class="mb-0">Revisa los horarios marcados en rojo.</p>
                            </div>
                        </div>
                        @foreach($conflicts as $conflict)
                            <div class="alert alert-light border-danger border-start border-4 shadow-sm mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <strong class="text-brand-deep me-2">{{ $conflict['component']->name }}</strong>
                                    <span class="badge bg-danger">Conflicto</span>
                                </div>
                                <span class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $conflict['schedule']->date->format('d/m/Y') }} |
                                    {{ substr($conflict['schedule']->start_time, 0, 5) }} - {{ substr($conflict['schedule']->end_time, 0, 5) }}
                                </span>
                                <ul class="mb-0 mt-2 small text-danger">
                                    @foreach($conflict['errors'] as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                            <div>
                                <h5 class="alert-heading fw-bold mb-1">Todo en orden</h5>
                                <p class="mb-0">No hay conflictos de horario detectados en tu agenda.</p>
                            </div>
                        </div>
                    @endif

                    {{-- Statistics --}}
                    <div class="row mb-4 g-3">
                        <div class="col-md-4">
                            <div class="card-admin bg-brand-main text-white h-100">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-collection-fill mb-2 display-4 opacity-50"></i>
                                    <h2 class="mb-0 fw-bold">{{ $event->components->count() }}</h2>
                                    <small class="opacity-75 text-uppercase fw-bold">Componentes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-admin bg-brand-accent text-white h-100">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-calendar-check-fill mb-2 display-4 opacity-50"></i>
                                    <h2 class="mb-0 fw-bold">{{ $schedulesByDate->flatten(1)->count() }}</h2>
                                    <small class="opacity-75 text-uppercase fw-bold">Sesiones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-admin {{ $conflicts->isEmpty() ? 'bg-secondary' : 'bg-danger' }} text-white h-100">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-exclamation-triangle-fill mb-2 display-4 opacity-50"></i>
                                    <h2 class="mb-0 fw-bold">{{ $conflicts->count() }}</h2>
                                    <small class="opacity-75 text-uppercase fw-bold">Conflictos</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule by Date --}}
                    <div class="card-admin">
                        <div class="card-header-admin">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-list-ul me-2"></i>
                                Agenda por Fecha
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @if($schedulesByDate->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-calendar-x-fill text-muted opacity-25 display-1"></i>
                                    </div>
                                    <h5 class="text-muted fw-bold">No hay horarios programados</h5>
                                    <p class="text-muted mb-4">
                                        Agrega horarios a los componentes para verlos aquí.
                                    </p>
                                    <button class="btn btn-brand-main shadow-sm" onclick="document.getElementById('components-tab').click()">
                                        <i class="bi bi-collection-fill me-1"></i>
                                        Ver componentes
                                    </button>
                                </div>
                            @else
                                @foreach($schedulesByDate as $date => $schedules)
                                    <div class="p-3 border-bottom bg-light">
                                        <h6 class="mb-0 fw-bold text-brand-deep">
                                            <i class="bi bi-calendar3 me-2 text-brand-main"></i>
                                            {{ \Carbon\Carbon::parse($date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                        </h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-white text-uppercase small text-muted">
                                                <tr>
                                                    <th class="ps-4 border-0">Horario</th>
                                                    <th class="border-0">Componente</th>
                                                    <th class="border-0">Tipo</th>
                                                    <th class="border-0">Ubicación</th>
                                                    <th class="border-0">Ponente</th>
                                                    <th class="text-center border-0">Estado</th>
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
                                                        <td class="ps-4 fw-bold text-brand-deep">
                                                            {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('schedules.index', [$event, $component]) }}"
                                                               class="text-decoration-none fw-bold text-brand-main">
                                                                {{ $component->name }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-brand-deep text-white rounded-pill">
                                                                {{ ucfirst($component->type) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($component->location)
                                                                <small class="text-muted">
                                                                    <i class="bi bi-geo-alt-fill me-1 text-brand-accent"></i>
                                                                    {{ $component->location }}
                                                                </small>
                                                            @else
                                                                <span class="text-muted small">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($component->speaker && $component->speaker->user)
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-circle me-2 bg-brand-main text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                                                        {{ strtoupper(substr($component->speaker->user->name, 0, 1)) }}
                                                                    </div>
                                                                    <small class="fw-semibold">{{ $component->speaker->user->name }}</small>
                                                                </div>
                                                            @else
                                                                <span class="text-muted small">Sin asignar</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($hasConflict)
                                                                <span class="badge bg-danger rounded-pill">
                                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Conflicto
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success rounded-pill">
                                                                    <i class="bi bi-check-circle-fill me-1"></i> OK
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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
                        <a href="{{ route('events.team.create', $event) }}" class="btn btn-brand-accent shadow-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Agregar Organizador
                        </a>
                    </div>

                    {{-- Lead Organizer --}}
                    <div class="card-admin mb-4">
                        <div class="card-header-admin">
                            <h5 class="mb-0">
                                <i class="bi bi-star-fill text-warning me-2"></i> Organizador Principal
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-brand-main text-white d-flex align-items-center justify-content-center display-6 fw-bold" style="width: 60px; height: 60px;">
                                    {{ strtoupper(substr($leadOrganizer->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold text-brand-deep">{{ $leadOrganizer->user->name }}</h5>
                                    <p class="text-muted mb-0 small">
                                        <i class="bi bi-envelope-fill me-1"></i> {{ $leadOrganizer->user->email }}
                                    </p>
                                    @if($leadOrganizer->current_workplace)
                                    <p class="text-muted mb-0 small">
                                        <i class="bi bi-briefcase-fill me-1"></i> {{ $leadOrganizer->current_workplace }}
                                    </p>
                                    @endif
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill px-3">Creador</span>
                            </div>
                        </div>
                    </div>

                    {{-- Co-Organizers --}}
                    @if($collaborators->count() > 0)
                    <div class="card-admin">
                        <div class="card-header-admin">
                            <h5 class="mb-0">
                                <i class="bi bi-people-fill me-2"></i> Co-Organizadores ({{ $collaborators->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Organizador</th>
                                            <th>Email</th>
                                            <th>Lugar de Trabajo</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($collaborators as $collaborator)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2 bg-brand-main text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                                        {{ strtoupper(substr($collaborator->user->name, 0, 1)) }}
                                                    </div>
                                                    <strong class="text-brand-deep">{{ $collaborator->user->name }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $collaborator->user->email }}</td>
                                            <td>
                                                @if($collaborator->current_workplace)
                                                    {{ $collaborator->current_workplace }}
                                                @else
                                                    <span class="text-muted small">No especificado</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-white text-danger border-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $collaborator->id }}">
                                                    <i class="bi bi-trash-fill"></i> Remover
                                                </button>

                                                <div class="modal fade" id="deleteModal{{ $collaborator->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow">
                                                            <div class="modal-header bg-brand-deep text-white border-0">
                                                                <h5 class="modal-title fw-bold">Confirmar Eliminación</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-start p-4">
                                                                ¿Estás seguro de que deseas remover a <strong>{{ $collaborator->user->name }}</strong> del equipo organizador?
                                                            </div>
                                                            <div class="modal-footer border-0 bg-light">
                                                                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancelar</button>
                                                                <form action="{{ route('events.team.destroy', [$event, $collaborator->id]) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger fw-bold">Sí, Remover</button>
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
                    <div class="card-admin">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-people-fill text-muted opacity-25 display-1"></i>
                            <h4 class="mt-3 text-brand-deep fw-bold">No hay co-organizadores</h4>
                            <p class="text-muted">Agrega organizadores adicionales para gestionar este evento en equipo.</p>
                            <a href="{{ route('events.team.create', $event) }}" class="btn btn-brand-accent text-white mt-2 shadow-sm">
                                <i class="bi bi-person-plus-fill me-1"></i> Agregar Primer Co-Organizador
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