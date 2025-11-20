@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #4499BB !important;">
        <div class="card-body">
            <div class="text-center">
                <h2 class="fw-bold mb-2" style="color: #0C2340;">
                    <i class="bi bi-calendar-event me-2" style="color: #4499BB;"></i>
                    Catálogo de Eventos
                </h2>
                <p class="text-muted mb-0">Descubre eventos, talleres y actividades disponibles</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('dashboard') }}" method="GET" id="filtersForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label for="search" class="form-label fw-semibold" style="color: #0C2340;">
                            <i class="bi bi-search me-1" style="color: #4499BB;"></i> Buscar
                        </label>
                        <input type="text"
                               class="form-control"
                               id="search"
                               name="search"
                               placeholder="Nombre o descripción..."
                               value="{{ request('search') }}">
                    </div>

                    <!-- Modality -->
                    <div class="col-md-3">
                        <label for="modality" class="form-label fw-semibold" style="color: #0C2340;">
                            <i class="bi bi-laptop me-1" style="color: #4499BB;"></i> Modalidad
                        </label>
                        <select class="form-select" id="modality" name="modality">
                            <option value="">Todas</option>
                            <option value="virtual" {{ request('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="in_person" {{ request('modality') == 'in_person' ? 'selected' : '' }}>Presencial</option>
                            <option value="hybrid" {{ request('modality') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div class="col-md-2">
                        <label for="date_from" class="form-label fw-semibold" style="color: #0C2340;">
                            <i class="bi bi-calendar me-1" style="color: #4499BB;"></i> Desde
                        </label>
                        <input type="date"
                               class="form-control"
                               id="date_from"
                               name="date_from"
                               value="{{ request('date_from') }}">
                    </div>

                    <!-- Date To -->
                    <div class="col-md-2">
                        <label for="date_to" class="form-label fw-semibold" style="color: #0C2340;">
                            <i class="bi bi-calendar me-1" style="color: #4499BB;"></i> Hasta
                        </label>
                        <input type="date"
                               class="form-control"
                               id="date_to"
                               name="date_to"
                               value="{{ request('date_to') }}">
                    </div>

                    <!-- Filter Button -->
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn w-100" style="background-color: #4499BB; color: white;">
                            <i class="bi bi-funnel"></i>
                        </button>
                    </div>
                </div>

                <!-- Tag Filter -->
                @if($tags->count() > 0)
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: #0C2340;">
                            <i class="bi bi-tags me-1" style="color: #4499BB;"></i> Etiquetas
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="tags[]"
                                           value="{{ $tag->id }}"
                                           id="tag{{ $tag->id }}"
                                           {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}
                                           onchange="document.getElementById('filtersForm').submit()">
                                    <label class="form-check-label" for="tag{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Clear Filters -->
                @if(request()->hasAny(['search', 'modality', 'date_from', 'date_to', 'tags']))
                <div class="row mt-3">
                    <div class="col-12">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Results Count -->
    <div class="row mb-3">
        <div class="col-12">
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle me-1" style="color: #4499BB;"></i>
                Se encontraron <strong style="color: #0C2340;">{{ $events->total() }}</strong> evento(s)
            </p>
        </div>
    </div>

    <!-- Events Grid -->
    @if($events->count() > 0)
    <div class="row g-4 mb-4">
        @foreach($events as $event)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                @if($event->cover_image)
                <img src="{{ $event->cover_image }}"
                     class="card-img-top"
                     alt="{{ $event->name }}"
                     style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top d-flex align-items-center justify-content-center"
                     style="height: 200px; background: linear-gradient(135deg, #0C2340 0%, #4499BB 100%);">
                    <i class="bi bi-calendar-event text-white" style="font-size: 4rem;"></i>
                </div>
                @endif

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold" style="color: #0C2340;">{{ $event->name }}</h5>

                    <!-- Badges -->
                    <div class="mb-2">
                        @php
                            $modalityLabels = [
                                'virtual' => 'Virtual',
                                'in_person' => 'Presencial',
                                'hybrid' => 'Híbrido'
                            ];
                        @endphp
                        <span class="badge" style="background-color: #4499BB;">
                            {{ $modalityLabels[$event->modality] ?? ucfirst($event->modality) }}
                        </span>
                        @if($event->approvedComponents->count() > 0)
                        <span class="badge" style="background-color: #8CC63F;">
                            {{ $event->approvedComponents->count() }} componente(s)
                        </span>
                        @endif
                    </div>

                    <p class="card-text text-muted small flex-grow-1">
                        {{ Str::limit($event->description, 120) }}
                    </p>

                    <!-- Event Info -->
                    <div class="mt-auto">
                        <p class="small mb-2">
                            <i class="bi bi-calendar me-1" style="color: #4499BB;"></i>
                            {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                        </p>

                        @if($event->location)
                        <p class="small mb-2">
                            <i class="bi bi-geo-alt me-1" style="color: #8CC63F;"></i>
                            {{ Str::limit($event->location, 30) }}
                        </p>
                        @endif

                        <!-- Tags -->
                        @if($event->tags->count() > 0)
                        <div class="mb-3">
                            @foreach($event->tags->take(3) as $tag)
                                <span class="badge bg-secondary small">{{ $tag->name }}</span>
                            @endforeach
                            @if($event->tags->count() > 3)
                                <span class="badge bg-secondary small">+{{ $event->tags->count() - 3 }}</span>
                            @endif
                        </div>
                        @endif

                        <a href="{{ route('event.show', $event->id) }}"
                           class="btn btn-sm w-100" style="background-color: #0C2340; color: white;">
                            <i class="bi bi-eye me-1"></i> Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12 d-flex justify-content-center">
            {{ $events->links() }}
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="bi bi-calendar-x" style="font-size: 4rem; color: #C8CCC9;"></i>
            </div>
            <h5 class="text-muted mb-3">No se encontraron eventos</h5>
            <p class="text-muted mb-4">Intenta ajustar los filtros de búsqueda</p>
            @if(request()->hasAny(['search', 'modality', 'date_from', 'date_to', 'tags']))
            <a href="{{ route('dashboard') }}" class="btn" style="background-color: #8CC63F; color: white;">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Ver Todos los Eventos
            </a>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush
