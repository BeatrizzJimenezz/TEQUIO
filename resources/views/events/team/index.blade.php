@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Breadcrumb al estilo Tequio --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Mis Eventos</a></li>
                    {{-- Usaremos el color primario para los enlaces --}}
                    <li class="breadcrumb-item"><a href="{{ route('components.index', $event->id) }}" style="color: #4499BB;">{{ $event->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Equipo Organizador</li>
                </ol>
            </nav>

            {{-- Encabezado de la Sección --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    {{-- Título principal con estilo corporativo --}}
                    <h1 class="mb-0" style="color: #0C2340; font-weight: 600;">
                        <i class="bi bi-people-fill me-2" style="color: #4499BB;"></i> Equipo Organizador
                    </h1>
                    <p class="text-muted mt-1 mb-0">{{ $event->name }}</p>
                </div>
                
                {{-- Botón de Acción Positiva con Color de Acento (similar al verde/lima de la imagen) --}}
                <a href="{{ route('events.team.create', $event) }}" class="btn btn-accent-positive shadow-sm">
                    <i class="bi bi-person-plus-fill me-1"></i> Invitar Organizador
                </a>
            </div>

            {{-- Mensajes de Sesión --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif

            {{-- 1. Tarjeta del Organizador Principal (Lead Organizer) --}}
            <div class="card shadow-sm mb-4 rounded-3 border-accent-left">
                <div class="card-header bg-primary-dark text-white rounded-top-3">
                    <h5 class="mb-0">
                        <i class="bi bi-star-fill me-2" style="color: #FFD700;"></i> Organizador Principal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3 flex-shrink-0" style="background-color: #4499BB; color: white;">
                            {{ strtoupper(substr($leadOrganizer->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $leadOrganizer->user->name }}</h5>
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-envelope me-1"></i> Email: {{ $leadOrganizer->user->email }}
                            </p>
                            @if($leadOrganizer->current_workplace)
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-briefcase me-1"></i> Trabajo: {{ $leadOrganizer->current_workplace }}
                            </p>
                            @endif
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 fw-bold flex-shrink-0">Creador</span>
                    </div>
                </div>
            </div>

            {{-- 2. Tarjeta de Co-Organizadores --}}
            @if($collaborators->count() > 0)
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-primary-dark text-white rounded-top-3">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i> Co-Organizadores ({{ $collaborators->count() }})
                    </h5>
                </div>
                
                {{-- Se cambia la tabla por una lista de "cards" para un estilo más moderno y consistente --}}
                <div class="list-group list-group-flush">
                    @foreach($collaborators as $collaborator)
                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle-sm me-3 flex-shrink-0" style="background-color: #92B4CD; color: #0C2340;">
                                {{ strtoupper(substr($collaborator->user->name, 0, 1)) }}
                            </div>
                            <div class="me-4">
                                <strong class="d-block">{{ $collaborator->user->name }}</strong>
                                <span class="text-muted small d-block">
                                    <i class="bi bi-envelope me-1"></i> Email: {{ $collaborator->user->email }}
                                </span>
                            </div>
                            <div class="d-none d-lg-block">
                                @if($collaborator->current_workplace)
                                <span class="text-muted small">
                                    <i class="bi bi-briefcase me-1"></i> Trabajo: {{ $collaborator->current_workplace }}
                                </span>
                                @else
                                <span class="text-muted small fst-italic">Lugar de trabajo no especificado</span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Botón de Acción --}}
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal{{ $collaborator->id }}"
                                title="Eliminar Colaborador">
                            <i class="bi bi-trash"></i> 
                            <span class="d-none d-lg-inline-block ms-1">Eliminar</span>
                        </button>

                        {{-- Modal de Confirmación --}}
                        <div class="modal fade" id="deleteModal{{ $collaborator->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $collaborator->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-3 shadow-lg">
                                    <div class="modal-header bg-primary-dark text-white">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $collaborator->id }}">Confirmar Eliminación</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        ¿Estás seguro de que quieres eliminar a <strong>{{ $collaborator->user->name }}</strong> del equipo organizador?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <form action="{{ route('events.team.destroy', [$event, $collaborator->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            {{-- Estado vacío, mejorado --}}
            <div class="card shadow-sm rounded-3">
                <div class="card-body text-center py-5">
                    <i class="bi bi-people" style="font-size: 4rem; color: #E0E0E0;"></i>
                    <h4 class="mt-3 text-muted">No hay co-organizadores</h4>
                    <p class="text-muted">Añade organizadores adicionales para gestionar este evento en equipo.</p>
                    <a href="{{ route('events.team.create', $event) }}" class="btn btn-accent-positive mt-3 shadow-sm">
                        <i class="bi bi-person-plus-fill me-1"></i> Invitar al Primer Co-Organizador
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Colores base para la vista */
    .bg-primary-dark {
        background-color: #0C2340 !important;
    }
    .text-primary-light {
        color: #4499BB !important;
    }

    /* Estilo del botón de acción positiva (similares a 'Guardar Cambios' de la imagen) */
    .btn-accent-positive {
        background-color: #00B050; /* Un verde vibrante */
        border-color: #00B050;
        color: white;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        transition: all 0.2s;
    }
    .btn-accent-positive:hover {
        background-color: #009343;
        border-color: #009343;
        color: white;
    }

    /* Estilo de los avatares circulares */
    .avatar-circle {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 24px;
        font-weight: bold;
    }
    .avatar-circle-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 16px;
        font-weight: bold;
    }

    /* Estilo para la tarjeta principal para darle un toque diferenciador */
    .border-accent-left {
        border-left: 5px solid #4499BB;
    }

    /* Asegurar que las esquinas superiores del header estén redondeadas si el card lo está */
    .card-header.bg-primary-dark {
        border-bottom: 0; /* Eliminar el borde feo entre header y body */
    }

    /* Estilo hover para los elementos de la lista de colaboradores */
    .list-group-item:hover {
        background-color: #F8F9FA;
    }
</style>
@endpush
@endsection