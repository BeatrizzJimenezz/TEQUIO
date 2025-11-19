@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Submit Proposal</h2>
                    <p class="text-muted">Select an event to submit your presentation or workshop proposal</p>
                </div>
                {{-- Route updated to proposals.my_proposals --}}
                <a href="{{ route('proposals.my_proposals') }}" class="btn btn-outline-primary">
                    <i class="bi bi-list-check"></i> My Proposals
                </a>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Variable updated to $events --}}
            @forelse($events as $event)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="card-title">{{ $event->name }}</h5>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar"></i> 
                                    {{-- Properties updated to start_date/end_date --}}
                                    {{ $event->start_date->format('m/d/Y') }} - {{ $event->end_date->format('m/d/Y') }}
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-person"></i> 
                                    {{-- Relationship updated to professionalProfile --}}
                                    Organized by: {{ $event->professionalProfile->user->name }}
                                </p>
                                <div class="mb-2">
                                    {{-- Properties updated to English --}}
                                    <span class="badge bg-info">{{ ucfirst($event->modality) }}</span>
                                    <span class="badge bg-success">{{ ucfirst($event->status) }}</span>
                                    
                                    @if($event->location)
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                        </span>
                                    @endif
                                </div>
                                <p class="card-text">{{ Str::limit($event->description, 200) }}</p>
                            </div>
                            <div class="ms-3">
                                {{-- Route updated to proposals.create --}}
                                <a href="{{ route('proposals.create', $event) }}" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Submit Proposal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 mb-2">No events available</h5>
                        <p class="text-muted">Currently there are no public events accepting proposals.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection