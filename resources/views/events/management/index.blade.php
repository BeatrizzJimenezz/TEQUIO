@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="col-12 py-4 px-0">
    
    <div class="hero-header">
        <div class="container-fluid px-md-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('events.index') }}" class="btn btn-hero-light rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $event->name }}</h2>
                        <p class="mb-0 opacity-75 d-flex align-items-center gap-3 text-sm">
                            <span><i class="bi bi-calendar3 me-1"></i> {{ $event->start_date->format('d M, Y') }} - {{ $event->end_date->format('d M, Y') }}</span>
                            <span class="opacity-50">|</span>
                            <span class="badge bg-white border border-white border-opacity-25 fw-normal text-brand-deep">{{ ucfirst($event->status) }}</span>
                        </p>
                    </div>
                </div>

                <div>
                    <a href="{{ route('event.show', $event->id) }}" class="btn btn-hero-light shadow-sm" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-2"></i> Ver evento
                    </a>
                </div>
            </div>
        </div>
        <i class="bi bi-gear-wide-connected hero-pattern"></i>
    </div>

    <div class="main-container col-12 px-md-2">
        
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Pestañas de Gestión --}}
        <ul class="nav nav-pills-custom mb-4" id="eventManagementTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="components-tab" data-bs-toggle="tab" data-bs-target="#components" type="button">
                    <i class="bi bi-layers-fill me-2"></i>Componentes
                    <span class="badge bg-light text-dark ms-2 rounded-pill border">{{ $components->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button">
                    <i class="bi bi-calendar-week-fill me-2"></i>Agenda
                    @if($conflicts->isNotEmpty())
                        <span class="badge bg-danger ms-2 rounded-pill">{{ $conflicts->count() }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="participants-tab" data-bs-toggle="tab" data-bs-target="#participants" type="button">
                    <i class="bi bi-people-fill me-2"></i>Participantes
                    @php $totalReg = $event->components()->withCount('registrations')->get()->sum('registrations_count'); @endphp
                    <span class="badge bg-light text-dark ms-2 rounded-pill border">{{ $totalReg }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button">
                    <i class="bi bi-person-badge-fill me-2"></i>Equipo
                    <span class="badge bg-light text-dark ms-2 rounded-pill border">{{ $collaborators->count() + 1 }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="eventManagementTabsContent">
            
            {{-- ================= COMPONENTES ================= --}}
            <div class="tab-pane fade show active" id="components" role="tabpanel">
                
                {{-- Botones de para componentes --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <a href="{{ route('components.create', $event) }}" class="btn btn-primary w-100 py-2 shadow-sm fw-bold" style="background-color: var(--brand-accent); border:none;">
                            <i class="bi bi-plus-circle me-2"></i> Nuevo componente
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('offers.index', $event) }}" class="btn btn-tequio w-100 py-2 shadow-sm fw-bold">
                            <i class="bi bi-megaphone me-2"></i> Gestionar Ofertas
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('offers.evaluation', $event) }}" class="btn btn-outline-tequio w-100 py-2 shadow-sm fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i> Evaluar Propuestas
                            @php $pending = $event->components()->where('proposal_status', 'proposed')->count(); @endphp
                            @if($pending > 0) <span class="badge bg-danger ms-2">{{ $pending }}</span> @endif
                        </a>
                    </div>
                </div>

                {{-- Componentes existentes --}}
                @forelse($components as $component)
                    <div class="component-card mb-4 {{ $component->proposal_status == 'open_offer' ? 'border-status-open' : ($component->proposal_status == 'proposed' ? 'border-status-proposed' : 'border-status-approved') }}">
                        <div class="row g-0">
                            
                            @if($component->cover_image)
                                <div class="col-12 col-md-3 col-xl-2">
                                    <div class="component-img-wrapper">
                                        <img src="{{ $component->cover_image }}" alt="{{ $component->name }}">
                                        {{-- Overlay sutil --}}
                                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-10"></div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="col-12 {{ $component->cover_image ? 'col-md-9 col-xl-10' : 'col-12' }}">
                                <div class="card-body p-4">
                                    <div class="row">
                                        
                                        <div class="col-12 col-md-10">
                                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                                <h5 class="mb-0 fw-bold text-brand-deep">{{ $component->name }}</h5>
                                                
                                                @if($component->proposal_status == 'open_offer')
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success badge-custom"><i class="bi bi-megaphone-fill me-1"></i> Oferta</span>
                                                @elseif($component->proposal_status == 'proposed')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning badge-custom"><i class="bi bi-hourglass-split me-1"></i> Propuesta</span>
                                                @else
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary badge-custom"><i class="bi bi-check-circle-fill me-1"></i> Aprobado</span>
                                                @endif
                                            </div>

                                            <p class="text-muted small mb-3 lh-sm wrap">{{ Str::limit($component->description, 160) }}</p>

                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <span class="badge bg-light text-secondary border badge-custom">{{ ucfirst($component->type) }}</span>
                                                <span class="badge bg-light text-secondary border badge-custom">{{ ucfirst($component->modality) }}</span>
                                                <span class="badge bg-light text-dark border badge-custom">
                                                    <i class="bi bi-people-fill me-1"></i> {{ $component->capacity ?? 'Ilimitado' }}
                                                </span>
                                                <span class="badge {{ $component->price > 0 ? 'bg-brand-accent text-white' : 'bg-light text-success border border-success' }} badge-custom">
                                                    {{ $component->price > 0 ? '$'.number_format($component->price, 2) : 'Gratis' }}
                                                </span>
                                            </div>

                                            @if($component->speaker)
                                                <div class="d-flex align-items-center small text-muted">
                                                    <i class="bi bi-mic-fill me-2 text-brand-main"></i>
                                                    <span class="fw-bold me-1">Ponente:</span> {{ $component->speaker->user->name ?? 'N/A' }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-2 mt-3 mt-md-0">
                                            <div class="d-flex flex-row flex-md-column justify-content-end align-items-end gap-2 h-100">
                                                
                                                <a href="{{ route('schedules.index', [$event, $component]) }}" 
                                                   class="btn-action-icon btn-schedule" 
                                                   data-bs-toggle="tooltip" title="Horarios">
                                                    <i class="bi bi-calendar-week"></i>
                                                </a>

                                                @if($component->proposal_status != 'open_offer')
                                                    <a href="{{ route('components.edit', [$event, $component]) }}" 
                                                       class="btn-action-icon btn-edit" 
                                                       data-bs-toggle="tooltip" title="Editar">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                @endif

                                                <form action="{{ route('components.destroy', [$event, $component]) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action-icon btn-delete" 
                                                            onclick="return confirm('¿Eliminar componente?')" 
                                                            data-bs-toggle="tooltip" title="Eliminar">
                                                        <i class="bi bi-trash-fill"></i>
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
                    <div class="text-center py-5 card-admin">
                        <div class="py-4">
                            <i class="bi bi-layers text-muted opacity-25 display-1"></i>
                            <h5 class="mt-3 fw-bold text-brand-deep">Sin componentes</h5>
                            <p class="text-muted">Comienza agregando actividades.</p>
                            <a href="{{ route('components.create', $event) }}" class="btn btn-primary rounded-pill px-4 bg-brand-accent border-0">
                                Crear primer componente
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- ================= AGENDA ================= --}}
            <div class="tab-pane fade" id="schedule" role="tabpanel">
                
                @if($conflicts->isNotEmpty())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                            <h6 class="fw-bold mb-0">Se detectaron conflictos de horario</h6>
                        </div>
                        <ul class="mb-0 small ps-4">
                            @foreach($conflicts as $conflict)
                                <li>
                                    <strong>{{ $conflict['component']->name }}</strong>: 
                                    {{ $conflict['schedule']->date->format('d/m') }} ({{ substr($conflict['schedule']->start_time, 0, 5) }} - {{ substr($conflict['schedule']->end_time, 0, 5) }})
                                    <span class="text-danger ms-1">- {{ implode(', ', $conflict['errors']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-admin">
                    <div class="card-header-admin">
                        <h5 class="mb-0 fw-bold text-brand-deep"><i class="bi bi-calendar3 me-2 text-brand-main"></i>Agenda general</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($schedulesByDate->isEmpty())
                            <div class="text-center py-5">
                                <p class="text-muted mb-0">No hay horarios programados aún.</p>
                            </div>
                        @else
                            @foreach($schedulesByDate as $date => $schedules)
                                <div class="bg-light p-3 border-bottom border-top">
                                    <h6 class="mb-0 fw-bold text-brand-deep text-uppercase small">
                                        <i class="bi bi-calendar-event me-2"></i>
                                        {{ \Carbon\Carbon::parse($date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                    </h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-modern mb-0">
                                        <thead>
                                            <tr>
                                                <th>Horario</th>
                                                <th>Actividad</th>
                                                <th>Ubicación</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedules as $item)
                                                @php
                                                    $hasConflict = $conflicts->contains(fn($c) => $c['schedule']->id === $item['schedule']->id);
                                                @endphp
                                                <tr class="{{ $hasConflict ? 'table-danger' : '' }}">
                                                    <td class="fw-bold text-brand-deep" style="width: 150px;">
                                                        {{ substr($item['schedule']->start_time, 0, 5) }} - {{ substr($item['schedule']->end_time, 0, 5) }}
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold d-block">{{ $item['component']->name }}</span>
                                                        <small class="text-muted">{{ ucfirst($item['component']->type) }}</small>
                                                    </td>
                                                    <td class="text-secondary">
                                                        <i class="bi bi-geo-alt me-1"></i>
                                                        {{ $item['component']->location ?? 'Virtual/TBD' }}
                                                    </td>
                                                    <td>
                                                        @if($hasConflict)
                                                            <span class="badge bg-danger">Conflicto</span>
                                                        @else
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">OK</span>
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

            {{-- ================= PARTICIPANTES ================= --}}
            <div class="tab-pane fade" id="participants" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header-admin">
                        <h5 class="mb-0 fw-bold text-brand-deep"><i class="bi bi-people me-2 text-brand-main"></i>Listado de inscritos</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Participante</th>
                                    <th>Componente</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $allRegs = $event->components()->with(['registrations.user'])->get()->pluck('registrations')->flatten()->sortByDesc('registered_at');
                                @endphp
                                @forelse($allRegs as $reg)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-circle bg-primary text-white">
                                                    {{ strtoupper(substr($reg->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $reg->user->name }}</div>
                                                    <div class="small text-muted">{{ $reg->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $reg->component->name }}</td>
                                        <td class="text-muted small">{{ $reg->registered_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($reg->payment_status == 'paid' || $reg->payment_status == 'free')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success badge-custom">Confirmado</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning badge-custom">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">No hay participantes inscritos aún.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================= EQUIPO ================= --}}
            <div class="tab-pane fade" id="team" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-brand-deep mb-0">Equipo organizador</h5>
                    <a href="{{ route('events.team.create', $event) }}" class="btn btn-sm btn-primary shadow-sm" style="background-color: var(--brand-light); border:none;">
                        <i class="bi bi-person-plus me-1"></i> Invitar miembro
                    </a>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card-admin h-100 border-start border-5 border-warning">
                            <div class="card-body d-flex align-items-center gap-3 ps-3 pt-3">
                                <div class="avatar-circle bg-warning text-dark" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                    {{ strtoupper(substr($leadOrganizer->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">{{ $leadOrganizer->user->name }}</h6>
                                    <p class="text-muted small mb-0">{{ $leadOrganizer->user->email }}</p>
                                    <span class="badge bg-warning text-dark mt-1">Organizador Principal</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Collaborators --}}
                    @foreach($collaborators as $collab)
                        <div class="col-md-6">
                            <div class="card-admin h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3 ps-3 pt-3">
                                        <div class="avatar-circle bg-secondary text-white" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                            {{ strtoupper(substr($collab->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0">{{ $collab->user->name }}</h6>
                                            <p class="text-muted small mb-0">{{ $collab->user->email }}</p>
                                            <span class="badge bg-light text-secondary border mt-1">Co-Organizador</span>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-icon btn-icon-delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $collab->id }}">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Eliminar --}}
                        <div class="modal fade" id="deleteModal{{ $collab->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-body p-4 text-center">
                                        <div class="mb-3 text-danger"><i class="bi bi-exclamation-circle display-1"></i></div>
                                        <h5 class="fw-bold">¿Remover miembro?</h5>
                                        <p class="text-muted">Se eliminará el acceso de <strong>{{ $collab->user->name }}</strong> a este evento.</p>
                                        <div class="d-flex justify-content-center gap-2 mt-4">
                                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('events.team.destroy', [$event, $collab->id]) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Sí, remover</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection