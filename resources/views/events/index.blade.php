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
                            <h3 class="mb-1 fw-bold" style="color: #0C2340;">
                                <i class="bi bi-calendar-event me-2" style="color: #4499BB;"></i>
                                Mis Eventos
                            </h3>
                            <p class="text-muted mb-0">Administra y organiza tus eventos</p>
                        </div>
                        <a href="{{ route('events.create') }}" class="btn text-white" style="background-color: #8CC63F;">
                            <i class="bi bi-plus-lg"></i> Crear Evento
                        </a>
                    </div>
                </div>
            </div>

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

            @forelse($events as $event)
                <div class="card mb-4 border-0 shadow-sm" style="transition: transform 0.2s ease;">
                    <div class="row g-0">
                        @if($event->cover_image)
                        <div class="col-md-3">
                            <img src="{{ $event->cover_image }}" class="img-fluid rounded-start h-100" alt="{{ $event->name }}" style="object-fit: cover;">
                        </div>
                        @endif

                        <div class="col-md-{{ $event->cover_image ? '9' : '12' }}">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="fw-bold mb-2" style="color: #0C2340;">{{ $event->name }}</h4>
                                        <p class="mb-0">
                                            <span class="me-3">
                                                <i class="bi bi-calendar3 me-1" style="color: #4499BB;"></i>
                                                {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                                            </span>
                                            <span>
                                                <i class="bi bi-clock me-1" style="color: #8CC63F;"></i>
                                                {{ $event->start_time }}
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        @php
                                            $statusColors = [
                                                'active' => '#8CC63F',
                                                'planning' => '#ffc107',
                                                'finished' => '#6c757d'
                                            ];
                                            $statusLabels = [
                                                'active' => 'Activo',
                                                'planning' => 'Planificando',
                                                'finished' => 'Finalizado'
                                            ];
                                        @endphp
                                        <span class="badge text-white" style="background-color: {{ $statusColors[$event->status] ?? '#6c757d' }};">
                                            {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                                        </span>
                                        <span class="badge ms-1" style="background-color: #4499BB;">
                                            {{ ucfirst($event->modality) }}
                                        </span>
                                        <span class="badge ms-1" style="background-color: {{ $event->visibility === 'public' ? '#0C2340' : '#495057' }};">
                                            {{ $event->visibility === 'public' ? 'Público' : 'Privado' }}
                                        </span>
                                    </div>
                                </div>

                                <p class="text-muted mb-3">{{ Str::limit($event->description, 150) }}</p>

                                @if($event->location)
                                <p class="mb-2">
                                    <i class="bi bi-geo-alt me-1" style="color: #8CC63F;"></i>
                                    <span class="text-muted">{{ $event->location }}</span>
                                </p>
                                @endif

                                @if($event->tags->count() > 0)
                                <div class="mb-3">
                                    @foreach($event->tags as $tag)
                                        <span class="badge bg-light text-dark border me-1">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-3">
                                            <i class="bi bi-collection me-1" style="color: #4499BB;"></i>
                                            <strong>{{ $event->components->count() }}</strong> componente(s)
                                        </span>
                                    </div>

                                    <div class="btn-group" role="group">
                                        @can('manageTeam', $event)
                                        <a href="{{ route('events.team.index', $event) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Administrar equipo organizador">
                                            <i class="bi bi-people-fill"></i>
                                        </a>
                                        @endcan

                                        <a href="{{ route('components.index', $event) }}"
                                           class="btn btn-sm"
                                           style="background-color: #4499BB; color: white;"
                                           title="Administrar Componentes">
                                            <i class="bi bi-collection"></i> Componentes
                                        </a>

                                        <a href="{{ route('events.schedules', $event) }}"
                                           class="btn btn-sm"
                                           style="background-color: #0C2340; color: white;"
                                           title="Ver Agenda del Evento">
                                            <i class="bi bi-calendar-week"></i> Agenda
                                        </a>

                                        <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        @if($event->status !== 'finished')
                                        <form action="{{ route('events.archive', $event) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    onclick="return confirm('¿Archivar este evento?')"
                                                    title="Archivar">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </form>
                                        @endif

                                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar este evento? Esto también eliminará todos sus componentes.')"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-calendar-x" style="font-size: 4rem; color: #C8CCC9;"></i>
                        </div>
                        <h5 class="text-muted mb-3">No tienes eventos creados</h5>
                        <p class="text-muted mb-4">Comienza creando tu primer evento para gestionar actividades, componentes y horarios.</p>
                        <a href="{{ route('events.create') }}" class="btn btn-lg text-white" style="background-color: #8CC63F;">
                            <i class="bi bi-plus-lg me-1"></i> Crear tu primer evento
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
