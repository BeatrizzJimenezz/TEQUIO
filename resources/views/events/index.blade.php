@extends('layouts.app')

@section('header', 'Mis Eventos')

@push('styles')
    <link href="{{ asset('css/events-index.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- HEADER DE GESTIÓN -->
<div class="management-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-1" style="color: var(--brand-deep);">Panel de organización</h2>
                <p class="text-muted mb-0">Gestiona, edita y supervisa el ciclo de vida de tus eventos.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('events.create') }}" class="btn btn-primary shadow-sm px-4 py-2 rounded-pill fw-bold" 
                   style="background-color: var(--brand-accent); border: none;">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo evento
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">

    <!-- Mensajes de Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid var(--brand-accent) !important;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #dc3545 !important;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs nav-tabs-custom">
        <li class="nav-item">
            <a class="nav-link {{ request('view') !== 'archived' ? 'active' : '' }}" href="{{ route('events.index') }}">
                <i class="bi bi-grid me-2"></i>Eventos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('view') === 'archived' ? 'active' : '' }}" href="{{ route('events.index', ['view' => 'archived']) }}">
                <i class="bi bi-archive me-2"></i>Archivados
            </a>
        </li>
    </ul>

    @if($events->count() > 0)
        <div class="row g-4">
            @foreach($events as $event)
            <div class="col-lg-6 col-xl-4">
                <div class="manage-card h-100 d-flex flex-column">
                    
                    <div class="position-relative" style="height: 160px; overflow: hidden;">
                        @if($event->cover_image)
                            <img src="{{ Str::startsWith($event->cover_image, 'http') ? $event->cover_image : asset('storage/' . $event->cover_image) }}" 
                                 class="w-100 h-100" 
                                 style="object-fit: cover;"
                                 alt="{{ $event->name }}">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" 
                                 style="background-color: var(--brand-deep); background-image: linear-gradient(45deg, #0c2340, #1a3a5e);">
                                <i class="bi bi-calendar2-week text-white opacity-25 display-4"></i>
                            </div>
                        @endif

                        <div class="position-absolute top-0 end-0 m-3">
                            @php
                                $statusConfig = [
                                    'active' => ['color' => '#8CC63F', 'label' => 'Activo'],
                                    'planning' => ['color' => '#ffc107', 'label' => 'Planificación'],
                                    'finished' => ['color' => '#6c757d', 'label' => 'Finalizado']
                                ];
                                $currentStatus = $statusConfig[$event->status] ?? ['color' => '#6c757d', 'label' => ucfirst($event->status)];
                            @endphp
                            <span class="badge rounded-pill text-dark shadow-sm border border-white" style="background-color: #fff;">
                                <span class="status-dot" style="background-color: {{ $currentStatus['color'] }};"></span>
                                {{ $currentStatus['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">
                                 @php
                                    $compModalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido']; 
                                    $compVisilityLabels = ['public' => 'Público', 'private' => 'Privado'];
                                @endphp
                                {{ $compModalityLabels[$event->modality] ?? ucfirst($event->modality) }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-eye{{ $event->visibility == 'public' ? '' : '-slash' }}"></i> 
                                {{ $compVisilityLabels[$event->visibility] ?? ucfirst($event->visibility) }}
                            </small>
                        </div>

                        <h5 class="fw-bold mb-2">
                            <a href="{{ route('events.manage', $event->id) }}" class="text-decoration-none text-dark hover-link">
                                {{ $event->name }}
                            </a>
                        </h5>

                        <div class="text-muted small mb-3">
                            <i class="bi bi-calendar3 me-1" style="color: #4499bb;"></i> 
                            {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') }}
                            @if($event->location)
                                <span class="mx-1">•</span> <i class="bi bi-geo-alt me-1" style="color: #4499bb;"></i> {{ Str::limit($event->location, 20) }}
                            @endif
                        </div>

                        <div class="mt-auto pt-3 border-top border-light">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-muted">
                                    <i class="bi bi-layers-fill me-1"></i> <strong>{{ $event->components->count() }}</strong> actividades
                                </span>
                                
                                <div class="d-flex gap-1">
                                    <a href="{{ route('events.manage', $event) }}" 
                                       class="btn btn-sm btn-light text-primary fw-bold border"
                                       data-bs-toggle="tooltip" title="Administrar evento">
                                        <i class="bi bi-collection" style="color: #8cc63f;"></i>
                                    </a>
                                    
                                    <a href="{{ route('events.edit', $event) }}" 
                                       class="action-btn" 
                                       data-bs-toggle="tooltip" title="Editar Evento">
                                        <i class="bi bi-pencil"></i>
                                    </a>


                                    <div class="dropdown">
                                        <button class="action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><h6 class="dropdown-header">Gestión</h6></li>
                                            
                                            <li>
                                                <a class="dropdown-item" href="{{ route('events.schedules', $event) }}">
                                                    <i class="bi bi-calendar-week me-2 text-secondary"></i> Gestionar horarios
                                                </a>
                                            </li>

                                            <li>
                                                <a class="dropdown-item" href="{{ route('components.index', $event) }}">
                                                    <i class="bi bi-columns-gap me-2 text-secondary"></i> Agregar actividades
                                                </a>
                                            </li>
                                            
                                            @can('manageTeam', $event)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('events.team.index', $event) }}">
                                                    <i class="bi bi-people me-2 text-secondary"></i> Equipo de trabajo
                                                </a>
                                            </li>
                                            @endcan

                                            <li><hr class="dropdown-divider"></li>
                                            
                                            @if(request('view') !== 'archived')
                                                <li>
                                                    @if($event->status === 'active')
                                                        <button type="button" 
                                                                class="dropdown-item text-muted disabled" 
                                                                style="cursor: not-allowed;"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="left"
                                                                title="No puedes archivar un evento activo. Cámbialo a Planificación o Finalizado primero.">
                                                            <i class="bi bi-archive me-2"></i> Archivar
                                                            <small class="d-block text-muted" style="font-size: 0.75rem;">
                                                                (No puedes archivar un evento activo)
                                                            </small>
                                                        </button>
                                                    @else
                                                        <form action="{{ route('events.archive', $event) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="dropdown-item text-warning" 
                                                                    onclick="return confirm('¿Archivar este evento?\n\nPodrás restaurarlo más tarde desde la pestaña de Archivados.')">
                                                                <i class="bi bi-archive me-2"></i> Archivar evento
                                                            </button>
                                                        </form>
                                                    @endif
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('events.archive', $event) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="dropdown-item text-success"
                                                                onclick="return confirm('¿Restaurar este evento?\n\nVolverá a aparecer en tu lista de eventos activos.')">
                                                            <i class="bi bi-box-arrow-up me-2"></i> Restaurar evento
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif

                                            <li>
                                                <form action="{{ route('events.destroy', $event) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('¿Eliminar permanentemente?')">
                                                        <i class="bi bi-trash me-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-5">
            {{ $events->appends(request()->query())->links() }}
        </div>

    @else
        <div class="text-center py-5 rounded-4 bg-white border border-dashed">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary" style="width: 80px; height: 80px;">
                    @if(request('view') === 'archived')
                        <i class="bi bi-archive display-6"></i>
                    @else
                        <i class="bi bi-calendar-plus display-6"></i>
                    @endif
                </div>
            </div>
            
            @if(request('view') === 'archived')
                <h4 class="fw-bold" style="color: var(--brand-deep);">No hay eventos archivados</h4>
                <p class="text-muted mb-4">Los eventos que archives aparecerán en esta sección.</p>
            @else
                <h4 class="fw-bold" style="color: var(--brand-deep);">Comienza tu primer evento</h4>
                <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                    Aún no tienes eventos creados. Crea uno nuevo para empezar a gestionar actividades, ponentes y horarios.
                </p>
                <a href="{{ route('events.create') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-bold" style="background-color: var(--brand-accent); border: none;">
                    <i class="bi bi-plus-lg me-2"></i> Crear Evento
                </a>
            @endif
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection