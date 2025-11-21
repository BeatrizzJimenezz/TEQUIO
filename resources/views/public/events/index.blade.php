@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/public-events-index.css') }}" rel="stylesheet">
@endpush

@section('content')
    
    {{-- BANNER --}}
    <div class="hero-catalog">
        <div class="hero-pattern"></div>
        <div class="container position-relative z-1 d-flex align-items-center justify-content-center">
            <div class="row w-100">
                <div class="col-12 text-center">
                    <h1 class="display-5 fw-bold text-white mb-2">Catálogo de Eventos</h1>
                    <p class="text-white-50 lead mb-0">Descubre las próximas actividades, conferencias y talleres.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- TARJETA DE FILTROS --}}
        <div class="card filters-card mb-5 rounded-3 overflow-hidden">
            <div class="card-body p-4">
                <form action="{{ route('dashboard') }}" method="GET" id="filtersForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label form-label-custom">Búsqueda</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control form-control-filter border-start-0 ps-0" 
                                       name="search" value="{{ request('search') }}" placeholder="Nombre del evento...">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label form-label-custom">Modalidad</label>
                            <select class="form-select form-select-filter" name="modality">
                                <option value="">Todas</option>
                                <option value="virtual" {{ request('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                <option value="in-person" {{ request('modality') == 'in-person' ? 'selected' : '' }}>Presencial</option>
                                <option value="hybrid" {{ request('modality') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label form-label-custom">Fechas</label>
                            <div class="input-group">
                                <input type="date" class="form-control form-control-filter" name="date_from" value="{{ request('date_from') }}">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-arrow-right-short"></i></span>
                                <input type="date" class="form-control form-control-filter" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>

                    @if($tags->count() > 0)
                    <div class="mt-4 pt-3 border-top border-light">
                        <label class="form-label form-label-custom d-block mb-2">Etiquetas</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <input type="checkbox" class="btn-check btn-tag-check" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}" 
                                       {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}
                                       onchange="document.getElementById('filtersForm').submit()">
                                <label class="btn btn-outline-light text-secondary border btn-sm rounded-pill px-3 btn-tag-label" for="tag_{{ $tag->id }}">
                                    {{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        @if(request()->hasAny(['search', 'modality', 'date_from', 'date_to', 'tags']))
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-evai btn-sm fw-bold d-flex align-items-center">
                                <i class="bi bi-x-lg me-1"></i> Limpiar
                            </a>
                        @endif
                        <button type="submit" class="btn btn-sm px-4 fw-bold shadow-sm btn-evai-green">
                            Aplicar Filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. LISTADO DE EVENTOS --}}
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-brand-deep">Eventos Disponibles</h5>
            <span class="badge bg-light text-secondary border rounded-pill px-3">
                {{ $events->total() }} resultados
            </span>
        </div>

        @if($events->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
            @foreach($events as $event)
            <div class="col">
                <div class="card h-100 border-0 event-card bg-white rounded-4 overflow-hidden">
                    
                    <div class="position-relative">
                        @if($event->cover_image)
                            <img src="{{ Str::startsWith($event->cover_image, 'http') ? $event->cover_image : asset('storage/' . $event->cover_image) }}" 
                                 class="card-img-top event-img-container" 
                                 alt="{{ $event->name }}"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'event-img-fallback\'><i class=\'bi bi-image-alt text-white opacity-25 display-4\'></i></div>'">
                        @else
                            <div class="event-img-fallback">
                                <i class="bi bi-calendar2-event text-white opacity-25 display-4"></i>
                            </div>
                        @endif
                        
                        <span class="position-absolute top-0 end-0 m-3 badge rounded-pill shadow-sm modality-badge">
                            @if($event->modality == 'virtual') <i class="bi bi-laptop me-1 text-primary"></i>
                            @elseif($event->modality == 'in-person') <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                            @else <i class="bi bi-hdd-network me-1 text-success"></i> @endif
                            {{ ucfirst($event->modality) }}
                        </span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="d-flex align-items-center text-muted small me-3">
                                <i class="bi bi-calendar3 me-2 text-brand-accent"></i>
                                <span class="fw-medium">{{ \Carbon\Carbon::parse($event->start_date)->format('d M') }}</span>
                            </div>
                            @if($event->start_time)
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-clock me-2 text-brand-main"></i>
                                <span>{{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</span>
                            </div>
                            @endif
                        </div>

                        <h5 class="card-title fw-bold mb-2 text-truncate" title="{{ $event->name }}">
                            <a href="{{ route('event.show', $event->id) }}" class="text-decoration-none text-brand-deep">
                                {{ $event->name }}
                            </a>
                        </h5>
                        
                        @if($event->location)
                        <p class="card-text small text-muted mb-3 text-truncate">
                            <i class="bi bi-geo-alt me-1"></i> {{ $event->location }}
                        </p>
                        @endif

                        <p class="card-text text-secondary small mb-4 flex-grow-1 event-description">
                            {{ $event->description }}
                        </p>

                        <div class="pt-3 border-top border-light d-flex justify-content-between align-items-center">
                            <div class="avatars">
                                <span class="small text-muted">
                                    <i class="bi bi-layers me-1"></i> {{ $event->components->count() }} Actividades
                                </span>
                            </div>
                            <a href="{{ route('event.show', $event->id) }}" 
                               class="btn btn-sm rounded-pill px-3 fw-bold shadow-sm btn-details">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mb-5">
            {{ $events->links() }}
        </div>

        @else
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 empty-state-icon">
                <i class="bi bi-search text-secondary display-6"></i>
            </div>
            <h4 class="fw-bold text-secondary">No se encontraron resultados</h4>
            <p class="text-muted">Intenta ajustar los filtros para ver más eventos.</p>
            @if(request()->hasAny(['search', 'modality', 'date_from', 'date_to', 'tags']))
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary rounded-pill px-4">
                Ver todos los eventos
            </a>
            @endif
        </div>
        @endif

    </div>
@endsection