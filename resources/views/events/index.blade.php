@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Events</h4>
                    {{-- Route updated to events.create --}}
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Event
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

                    @forelse($events as $event)
                        <div class="card mb-3">
                            <div class="row g-0">
                                {{-- Property updated to cover_image --}}
                                @if($event->cover_image)
                                <div class="col-md-3">
                                    <img src="{{ $event->cover_image }}" class="img-fluid rounded-start" alt="{{ $event->name }}" style="height: 100%; object-fit: cover;">
                                </div>
                                @endif
                                
                                <div class="col-md-{{ $event->cover_image ? '9' : '12' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title">{{ $event->name }}</h5>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar"></i> 
                                                        {{-- Properties updated to start_date and end_date --}}
                                                        {{ $event->start_date->format('m/d/Y') }} - {{ $event->end_date->format('m/d/Y') }}
                                                        <span class="ms-2">
                                                            {{-- Property updated to start_time --}}
                                                            <i class="bi bi-clock"></i> {{ $event->start_time }}
                                                        </span>
                                                    </small>
                                                </p>
                                            </div>
                                            <div>
                                                {{-- Logic updated to English status values: active, planning --}}
                                                <span class="badge bg-{{ $event->status === 'active' ? 'success' : ($event->status === 'planning' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($event->status) }}
                                                </span>
                                                <span class="badge bg-info ms-1">
                                                    {{ ucfirst($event->modality) }}
                                                </span>
                                                {{-- Logic updated to English visibility: public --}}
                                                <span class="badge bg-{{ $event->visibility === 'public' ? 'primary' : 'dark' }} ms-1">
                                                    {{ ucfirst($event->visibility) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <p class="card-text mt-2">{{ Str::limit($event->description, 150) }}</p>
                                        
                                        @if($event->location)
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                            </small>
                                        </p>
                                        @endif

                                        {{-- Relationship updated to tags --}}
                                        @if($event->tags->count() > 0)
                                        <div class="mb-2">
                                            @foreach($event->tags as $tag)
                                                <span class="badge bg-secondary">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                        @endif
                                        
                                        <!-- Component Count -->
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="bi bi-collection"></i> 
                                                {{-- Relationship updated to components --}}
                                                <strong>{{ $event->components->count() }}</strong> component(s)
                                            </small>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="btn-group" role="group">
                                            <!-- Manage Team Button -->
                                            {{-- Policy updated to manageTeam --}}
                                            @can('manageTeam', $event)
                                            <a href="{{ route('events.team.index', $event) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Manage organizing team">
                                                <i class="bi bi-people-fill"></i> Team
                                            </a>
                                            @endcan
                                            
                                            <!-- View Components Button -->
                                            {{-- Route updated to components.index --}}
                                            <a href="{{ route('components.index', $event) }}" 
                                               class="btn btn-sm btn-info text-white"
                                               title="Manage Components (Workshops, Presentations, Activities)">
                                                <i class="bi bi-collection"></i> Components
                                            </a>
                                            
                                            {{-- Route updated to events.edit --}}
                                            <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            
                                            {{-- Status updated to finished --}}
                                            @if($event->status !== 'finished')
                                            {{-- Route updated to events.archive --}}
                                            <form action="{{ route('events.archive', $event) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-secondary" 
                                                        onclick="return confirm('Archive this event?')">
                                                    <i class="bi bi-archive"></i> Archive
                                                </button>
                                            </form>
                                            @endif
                                            
                                            {{-- Route updated to events.destroy --}}
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this event? This will also delete all its components.')">
                                                    <i class="bi bi-trash"></i> Delete
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
                            <p class="text-muted mt-3">You have no created events</p>
                            <a href="{{ route('events.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create your first event
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection