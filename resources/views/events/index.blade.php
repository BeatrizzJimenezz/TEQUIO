@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Mis Eventos</h4>
                    <a href="{{ route('eventos.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Crear Evento
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @forelse($eventos as $evento)
                        <div class="card mb-3">
                            <div class="row g-0">
                                @if($evento->imagen_portada)
                                <div class="col-md-3">
                                    <img src="{{ $evento->imagen_portada }}" class="img-fluid rounded-start" alt="{{ $evento->nombre }}" style="height: 100%; object-fit: cover;">
                                </div>
                                @endif
                                <div class="col-md-{{ $evento->imagen_portada ? '9' : '12' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title">{{ $evento->nombre }}</h5>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar"></i> 
                                                        {{ $evento->fecha_inicio->format('d/m/Y') }} - {{ $evento->fecha_fin->format('d/m/Y') }}
                                                        <span class="ms-2">
                                                            <i class="bi bi-clock"></i> {{ $evento->hora_inicio }}
                                                        </span>
                                                    </small>
                                                </p>
                                            </div>
                                            <div>
                                                <span class="badge bg-{{ $evento->estado === 'activo' ? 'success' : ($evento->estado === 'planificacion' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($evento->estado) }}
                                                </span>
                                                <span class="badge bg-info ms-1">
                                                    {{ ucfirst($evento->modalidad) }}
                                                </span>
                                                <span class="badge bg-{{ $evento->visibilidad === 'publico' ? 'primary' : 'dark' }} ms-1">
                                                    {{ ucfirst($evento->visibilidad) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <p class="card-text mt-2">{{ Str::limit($evento->descripcion, 150) }}</p>
                                        
                                        @if($evento->ubicacion)
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i> {{ $evento->ubicacion }}
                                            </small>
                                        </p>
                                        @endif

                                        @if($evento->etiquetas->count() > 0)
                                        <div class="mb-2">
                                            @foreach($evento->etiquetas as $etiqueta)
                                                <span class="badge bg-secondary">{{ $etiqueta->nombre }}</span>
                                            @endforeach
                                        </div>
                                        @endif
                                        
                                        <!-- Conteo de componentes -->
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="bi bi-collection"></i> 
                                                <strong>{{ $evento->componentes->count() }}</strong> componente(s)
                                            </small>
                                        </div>
                                        
                                        <!-- Botones de acción -->
                                        <div class="btn-group" role="group">
                                            <!-- Nuevo botón: Gestionar Equipo -->
                                            @can('gestionarEquipo', $evento)
                                            <a href="{{ route('eventos.equipo.index', $evento) }}" 
                                            class="btn btn-sm btn-outline-primary"
                                            title="Gestionar equipo de organizadores">
                                                <i class="bi bi-people-fill"></i> Equipo
                                            </a>
                                            @endcan
                                            
                                            <!-- Nuevo botón: Ver Componentes -->
                                            <a href="{{ route('componentes.index', $evento) }}" 
                                            class="btn btn-sm btn-info text-white"
                                            title="Gestionar Componentes (Talleres, Ponencias, Actividades)">
                                                <i class="bi bi-collection"></i> Componentes
                                            </a>
                                            
                                            <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            
                                            @if($evento->estado !== 'finalizado')
                                            <form action="{{ route('eventos.archivar', $evento) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-secondary" 
                                                        onclick="return confirm('¿Archivar este evento?')">
                                                    <i class="bi bi-archive"></i> Archivar
                                                </button>
                                            </form>
                                            @endif
                                            
                                            <form action="{{ route('eventos.destroy', $evento) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('¿Estás seguro de eliminar este evento? Esto eliminará también todos sus componentes.')">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No tienes eventos creados</p>
                            <a href="{{ route('eventos.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Crear tu primer evento
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection