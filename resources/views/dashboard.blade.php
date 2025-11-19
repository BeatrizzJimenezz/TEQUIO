@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="container">
            
            <div class="mb-4">
                <h1 class="h2 fw-bold text-dark">Events Catalog</h1>
            </div>
            
            <!-- Filtros -->
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <form action="{{ route('dashboard') }}" method="GET" id="filtersForm">
                        <div class="row g-3">
                            
                            <div class="col-md-3">
                                <label for="search" class="form-label fw-medium text-secondary small mb-1">
                                    🔍 Search
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Name or description..."
                                       value="{{ request('search') }}">
                            </div>

                            <!-- Modality -->
                            <div class="col-md-3">
                                <label for="modality" class="form-label fw-medium text-secondary small mb-1">
                                    💻 Modality
                                </label>
                                <select class="form-select" 
                                        id="modality" 
                                        name="modality">
                                    <option value="">All</option>
                                    <option value="virtual" {{ request('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in-person" {{ request('modality') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                                    <option value="hybrid" {{ request('modality') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                </select>
                            </div>

                            <!-- Date From -->
                            <div class="col-md-3">
                                <label for="date_from" class="form-label fw-medium text-secondary small mb-1">
                                    📅 From
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="date_from" 
                                       name="date_from"
                                       value="{{ request('date_from') }}">
                            </div>

                            <!-- Date To -->
                            <div class="col-md-3">
                                <label for="date_to" class="form-label fw-medium text-secondary small mb-1">
                                    📅 To
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="date_to" 
                                       name="date_to"
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>

                        <!-- Tags Filter -->
                        @if($tags->count() > 0)
                        <div class="mt-4">
                            <label class="form-label fw-medium text-secondary small mb-2">
                                🏷️ Tags
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($tags as $tag)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="tags[]" 
                                               value="{{ $tag->id }}"
                                               id="tag_{{ $tag->id }}"
                                               {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}
                                               onchange="document.getElementById('filtersForm').submit()">
                                        <label class="form-check-label text-secondary small" for="tag_{{ $tag->id }}">
                                            {{ $tag->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Buttons -->
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                🔎 Filter
                            </button>
                            
                            @if(request()->hasAny(['search', 'modality', 'date_from', 'date_to', 'tags']))
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm px-3 border text-secondary">
                                ❌ Clear Filters
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            <div class="mb-4">
                <p class="text-muted small">
                    ℹ️ Found <strong>{{ $events->total() }}</strong> event(s)
                </p>
            </div>

            <!-- Events Grid -->
            @if($events->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                @foreach($events as $event)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 hover-shadow transition-all">
                        
                        <!-- Cover Image -->
                        @if($event->cover_image)
                        <img src="{{ asset('storage/' . $event->cover_image) }}" 
                             class="card-img-top" 
                             alt="{{ $event->name }}"
                             style="height: 200px; object-fit: cover;">
                        @else
                        <div class="card-img-top d-flex align-items-center justify-content-center text-white" 
                             style="height: 200px; background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%);">
                            <span class="display-4">📅</span>
                        </div>
                        @endif

                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold text-dark mb-2">{{ $event->name }}</h5>
                            
                            <!-- Badges -->
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                    {{ ucfirst($event->modality) }}
                                </span>
                                @if($event->components->count() > 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                    {{ $event->components->count() }} component(s)
                                </span>
                                @endif
                            </div>

                            <p class="card-text text-secondary small mb-4">
                                {{ Str::limit($event->description, 120) }}
                            </p>

                            <!-- Event Information -->
                            <div class="mb-4">
                                <p class="small text-secondary mb-1">
                                    📅 {{ \Carbon\Carbon::parse($event->start_date)->format('m/d/Y') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('m/d/Y') }}
                                </p>

                                @if($event->location)
                                <p class="small text-secondary mb-1">
                                    📍 {{ Str::limit($event->location, 30) }}
                                </p>
                                @endif

                                @if($event->start_time)
                                <p class="small text-secondary mb-0">
                                    🕐 {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
                                </p>
                                @endif
                            </div>

                            <!-- Tags -->
                            @if($event->tags->count() > 0)
                            <div class="mb-4 d-flex flex-wrap gap-1">
                                @foreach($event->tags->take(3) as $tag)
                                    <span class="badge bg-light text-dark border border-light-subtle rounded-1 fw-normal">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                                @if($event->tags->count() > 3)
                                    <span class="badge bg-light text-dark border border-light-subtle rounded-1 fw-normal">
                                        +{{ $event->tags->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                            @endif

                            <a href="{{ route('events.show', $event->id) }}" 
                               class="btn btn-primary w-100 btn-sm py-2">
                                👁️ View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-3 d-flex justify-content-center">
                    {{-- 
                        NOTA: Si la paginación se ve rara, asegúrate de agregar 
                        Paginator::useBootstrapFive(); 
                        en el método boot() de tu AppServiceProvider.php 
                    --}}
                    {{ $events->links() }}
                </div>
            </div>

            @else
            <!-- Empty State -->
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <div class="display-1 text-muted mb-3 opacity-25">📅</div>
                    <h3 class="h4 fw-bold text-secondary mb-2">No events found</h3>
                    <p class="text-muted mb-4">Try adjusting your search filters</p>
                    
                    @if(request()->hasAny(['search', 'modality', 'date_from', 'date_to', 'tags']))
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        🔄 View All Events
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Estilo adicional para efecto hover en cards -->
    <style>
        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
    </style>
@endsection