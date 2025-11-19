@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $event->name }}</h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar"></i> {{ $event->start_date->format('m/d/Y') }} - {{ $event->end_date->format('m/d/Y') }}
                            </p>
                        </div>
                        {{-- Assumes route is now 'events.index' --}}
                        <a href="{{ route('events.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to My Events
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    {{-- Assumes route is now 'components.create' --}}
                    <a href="{{ route('components.create', $event) }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-plus-circle"></i> Add Component
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- Assumes route is now 'offers.index' --}}
                    <a href="{{ route('offers.index', $event) }}" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-megaphone"></i> Manage Offers
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- Assumes route is now 'offers.evaluation' --}}
                    <a href="{{ route('offers.evaluation', $event) }}" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-clipboard-check"></i> Evaluate Proposals
                        @php
                            // Logic updated to English: components() -> where('proposal_status', 'proposed')
                            $pendingProposals = $event->components()->where('proposal_status', 'proposed')->count();
                        @endphp
                        @if($pendingProposals > 0)
                            <span class="badge bg-danger ms-1">{{ $pendingProposals }}</span>
                        @endif
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- Assumes route is now 'events.public.show' --}}
                    <a href="{{ route('events.public.show', $event->id) }}" class="btn btn-outline-info w-100 mb-2" target="_blank">
                        <i class="bi bi-eye"></i> View Public Event
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Event Components</h4>
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

                    @forelse($components as $component)
                        {{-- Logic: proposal_status == 'open_offer' --}}
                        <div class="card mb-3 {{ $component->proposal_status == 'open_offer' ? 'border-success' : '' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <h5 class="mb-0 me-2">{{ $component->name }}</h5>
                                            
                                            @if($component->proposal_status == 'open_offer')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-megaphone"></i> Open Offer
                                                </span>
                                            @elseif($component->proposal_status == 'proposed')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock"></i> External Proposal
                                                </span>
                                            @else
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-check-circle"></i> Approved
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="mb-2">
                                            {{-- Assumes type is capitalized or handled by an accessor --}}
                                            <span class="badge bg-primary">{{ ucfirst($component->type) }}</span>
                                            <span class="badge bg-info">{{ ucfirst($component->modality) }}</span>
                                            
                                            @if($component->level)
                                                <span class="badge bg-secondary">{{ ucfirst($component->level) }}</span>
                                            @endif
                                            
                                            @if($component->slots)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-people"></i> {{ $component->slots }} slots
                                                </span>
                                            @endif
                                            
                                            @if($component->attendee_price > 0)
                                                <span class="badge bg-success">
                                                    ${{ number_format($component->attendee_price, 2) }}
                                                </span>
                                            @else
                                                <span class="badge bg-success">Free</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Logic: relationship 'ponente' changed to 'speaker' --}}
                                        @if($component->speaker)
                                            <p class="mb-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-person"></i> Speaker: 
                                                    <strong>{{ $component->speaker->user->name }}</strong>
                                                </small>
                                            </p>
                                        @endif
                                        
                                        <p class="card-text">{{ Str::limit($component->description, 150) }}</p>
                                        
                                        @if($component->location)
                                        <p class="mb-2">
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i> {{ $component->location }}
                                            </small>
                                        </p>
                                        @endif

                                        <div class="mt-2">
                                            <strong>Schedules:</strong>
                                            <ul class="list-unstyled ms-3 mb-0">
                                                {{-- Logic: relationship 'horarios' changed to 'schedules' --}}
                                                @foreach($component->schedules as $schedule)
                                                    <li>
                                                        <i class="bi bi-clock"></i>
                                                        {{-- Logic: 'fecha' -> 'date', 'hora_inicio' -> 'start_time' --}}
                                                        {{ $schedule->date->format('m/d/Y') }} 
                                                        from {{ $schedule->start_time }} to {{ $schedule->end_time }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-3">
                                        <div class="btn-group-vertical" role="group">
                                            @if($component->proposal_status != 'open_offer')
                                                <a href="{{ route('components.edit', [$event, $component]) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            @endif
                                            
                                            <form action="{{ route('components.destroy', [$event, $component]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger w-100" 
                                                        onclick="return confirm('Are you sure you want to delete this component?')">
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
                            <i class="bi bi-calendar-event" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No components found for this event</p>
                            <a href="{{ route('components.create', $event) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add the first component
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection