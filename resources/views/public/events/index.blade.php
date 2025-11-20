@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3">Event Catalog</h1>
            <p class="lead text-muted">Discover events, workshops, and activities</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            {{-- Route updated to events.public.index --}}
            <form action="{{ route('dashboard') }}" method="GET" id="filtersForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">
                            <i class="bi bi-search"></i> Search
                        </label>
                        {{-- Input name updated to 'search' --}}
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               placeholder="Name or description..."
                               value="{{ request('search') }}">
                    </div>

                    <!-- Modality -->
                    <div class="col-md-3">
                        <label for="modality" class="form-label">
                            <i class="bi bi-laptop"></i> Modality
                        </label>
                        <select class="form-select" id="modality" name="modality">
                            <option value="">All</option>
                            {{-- Values updated to English Enums --}}
                            <option value="virtual" {{ request('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="in_person" {{ request('modality') == 'in_person' ? 'selected' : '' }}>In-Person</option>
                            <option value="hybrid" {{ request('modality') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">
                            <i class="bi bi-calendar"></i> From
                        </label>
                        {{-- Input name updated to 'date_from' --}}
                        <input type="date" 
                               class="form-control" 
                               id="date_from" 
                               name="date_from"
                               value="{{ request('date_from') }}">
                    </div>

                    <!-- Date To -->
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">
                            <i class="bi bi-calendar"></i> To
                        </label>
                        {{-- Input name updated to 'date_to' --}}
                        <input type="date" 
                               class="form-control" 
                               id="date_to" 
                               name="date_to"
                               value="{{ request('date_to') }}">
                    </div>

                    <!-- Filter Button -->
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i>
                        </button>
                    </div>
                </div>

                <!-- Tag Filter -->
                {{-- Variable updated to $tags --}}
                @if($tags->count() > 0)
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label">
                            <i class="bi bi-tags"></i> Tags
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <div class="form-check">
                                    {{-- Input name updated to tags[] --}}
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
                {{-- Check for English input names --}}
                @if(request()->hasAny(['search', 'modality', 'date_from', 'date_to', 'tags']))
                <div class="row mt-3">
                    <div class="col-12">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Clear Filters
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
            <p class="text-muted">
                <i class="bi bi-info-circle"></i> 
                Found <strong>{{ $events->total() }}</strong> event(s)
            </p>
        </div>
    </div>

    <!-- Events Grid -->
    {{-- Variable updated to $events --}}
    @if($events->count() > 0)
    <div class="row g-4 mb-4">
        @foreach($events as $event)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow">
                {{-- Attribute updated to cover_image --}}
                @if($event->cover_image)
                <img src="{{ $event->cover_image }}" 
                     class="card-img-top" 
                     alt="{{ $event->name }}"
                     style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                     style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-calendar-event text-white" style="font-size: 4rem;"></i>
                </div>
                @endif

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $event->name }}</h5>
                    
                    <!-- Badges -->
                    <div class="mb-2">
                        <span class="badge bg-info">{{ ucfirst($event->modality) }}</span>
                        {{-- Relationship updated to approvedComponents (or just components based on your logic) --}}
                        @if($event->approvedComponents->count() > 0)
                        <span class="badge bg-success">
                            {{ $event->approvedComponents->count() }} component(s)
                        </span>
                        @endif
                    </div>

                    <p class="card-text text-muted small">
                        {{ Str::limit($event->description, 120) }}
                    </p>

                    <!-- Event Info -->
                    <div class="mt-auto">
                        <p class="small mb-2">
                            <i class="bi bi-calendar"></i>
                            {{ $event->start_date->format('m/d/Y') }} - {{ $event->end_date->format('m/d/Y') }}
                        </p>

                        @if($event->location)
                        <p class="small mb-2">
                            <i class="bi bi-geo-alt"></i>
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

                        {{-- Route updated to events.public.show --}}
                        <a href="{{ route('event.show', $event->id) }}" 
                           class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12">
            {{ $events->links() }}
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-calendar-x" style="font-size: 5rem; color: #ccc;"></i>
        <h3 class="mt-4 text-muted">No events found</h3>
        <p class="text-muted">Try adjusting your search filters</p>
        @if(request()->hasAny(['search', 'modality', 'date_from', 'date_to', 'tags']))
        <a href="{{ route('events.index') }}" class="btn btn-primary mt-3">
            <i class="bi bi-arrow-counterclockwise"></i> View All Events
        </a>
        @endif
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush