@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <!-- Event Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $event->name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar"></i> {{ $event->start_date->format('m/d/Y') }} - {{ $event->end_date->format('m/d/Y') }}
                            </p>
                        </div>
                        {{-- Route updated to components.index --}}
                        <a href="{{ route('components.index', $event) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Event
                        </a>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Evaluation Panel</h2>
                <div>
                    @php
                        // English logic: sum of proposals + applications count
                        $totalPending = $proposals->count() + $offersWithApplications->sum(fn($o) => $o->applications->count());
                    @endphp
                    <span class="badge bg-warning text-dark" style="font-size: 1.2rem;">
                        {{ $totalPending }} Pending
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Section: Applications to Open Offers -->
            @if($offersWithApplications->count() > 0)
                <h4 class="mb-3">
                    <i class="bi bi-megaphone"></i> Applications to Open Offers
                </h4>

                @foreach($offersWithApplications as $offer)
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-megaphone"></i> {{ $offer->name }}
                                </h5>
                                <div>
                                    <span class="badge bg-light text-dark me-2">
                                        {{ $offer->applications->count() }} Application(s)
                                    </span>
                                    {{-- Route updated to offers.close --}}
                                    <form action="{{ route('offers.close', [$event, $offer]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm" 
                                                onclick="return confirm('Close this offer? All pending applications will be rejected.')">
                                            <i class="bi bi-lock"></i> Close Offer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Offer Description:</strong>
                                <p>{{ $offer->description }}</p>
                            </div>

                            <!-- Applications List -->
                            <h6 class="border-top pt-3 mb-3">Candidates:</h6>
                            
                            @foreach($offer->applications as $application)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Candidate Info -->
                                            <div class="col-md-4 border-end">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 40px; height: 40px;">
                                                        {{-- Accessing user through professionalProfile --}}
                                                        {{ strtoupper(substr($application->professionalProfile->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $application->professionalProfile->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $application->professionalProfile->user->email }}</small>
                                                    </div>
                                                </div>
                                                
                                                {{-- Property updated to current_workplace --}}
                                                @if($application->professionalProfile->current_workplace)
                                                    <p class="mb-2">
                                                        <i class="bi bi-briefcase"></i> 
                                                        <small>{{ $application->professionalProfile->current_workplace }}</small>
                                                    </p>
                                                @endif
                                                
                                                @if($application->professionalProfile->skills)
                                                    <div class="mb-2">
                                                        <small><strong>Skills:</strong></small>
                                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                                            @foreach(array_slice(explode(',', $application->professionalProfile->skills), 0, 5) as $skill)
                                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                                                    {{ trim($skill) }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                {{-- Relationship updated to academicBackground (or education) --}}
                                                @if($application->professionalProfile->academicBackground->count() > 0)
                                                    <div class="mt-2">
                                                        <small><strong>Education:</strong></small>
                                                        @foreach($application->professionalProfile->academicBackground->take(2) as $education)
                                                            <div class="mt-1">
                                                                <small>
                                                                    {{-- Properties updated to degree and institution --}}
                                                                    <strong>{{ $education->degree }}</strong><br>
                                                                    {{ $education->institution }}
                                                                </small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Message and Actions -->
                                            <div class="col-md-8">
                                                @if($application->message)
                                                    <div class="mb-3">
                                                        <strong>Candidate's message:</strong>
                                                        <p class="mb-0 mt-1 p-2 bg-light rounded">{{ $application->message }}</p>
                                                    </div>
                                                @endif
                                                
                                                <p class="text-muted mb-3">
                                                    <small>Applied on {{ $application->created_at->format('m/d/Y H:i') }}</small>
                                                </p>
                                                
                                                <div class="d-flex gap-2">
                                                    {{-- Route updated to offers.applications.reject --}}
                                                    <form action="{{ route('offers.applications.reject', [$event, $offer, $application->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-danger" 
                                                                onclick="return confirm('Reject this application?')">
                                                            <i class="bi bi-x-circle"></i> Reject
                                                        </button>
                                                    </form>
                                                    
                                                    {{-- Route updated to offers.applications.accept --}}
                                                    <form action="{{ route('offers.applications.accept', [$event, $offer, $application->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success" 
                                                                onclick="return confirm('Accept this candidate? Other applications will be rejected and this user will be assigned as speaker.')">
                                                            <i class="bi bi-check-circle"></i> Accept & Assign
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Section: Spontaneous Proposals -->
            @if($proposals->count() > 0)
                <h4 class="mb-3 {{ $offersWithApplications->count() > 0 ? 'mt-5' : '' }}">
                    <i class="bi bi-send"></i> Spontaneous Proposals
                </h4>
            @endif

            @forelse($proposals as $proposal)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $proposal->name }}</h5>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock"></i> Pending Review
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Speaker Information -->
                            <div class="col-md-4 border-end">
                                <h6 class="mb-3">
                                    <i class="bi bi-person-badge"></i> Speaker Information
                                </h6>
                                
                                {{-- Relationship updated to speaker --}}
                                @if($proposal->speaker)
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($proposal->speaker->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $proposal->speaker->user->name }}</strong><br>
                                                <small class="text-muted">{{ $proposal->speaker->user->email }}</small>
                                            </div>
                                        </div>
                                        
                                        @if($proposal->speaker->current_workplace)
                                            <p class="mb-2">
                                                <i class="bi bi-briefcase"></i> 
                                                <small>{{ $proposal->speaker->current_workplace }}</small>
                                            </p>
                                        @endif
                                        
                                        @if($proposal->speaker->skills)
                                            <div class="mb-2">
                                                <small><strong>Skills:</strong></small>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach(array_slice(explode(',', $proposal->speaker->skills), 0, 5) as $skill)
                                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                                            {{ trim($skill) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($proposal->speaker->academicBackground->count() > 0)
                                            <div class="mt-3">
                                                <small><strong>Education:</strong></small>
                                                @foreach($proposal->speaker->academicBackground->take(2) as $education)
                                                    <div class="mt-1">
                                                        <small>
                                                            <strong>{{ $education->degree }}</strong><br>
                                                            {{ $education->institution }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Proposal Details -->
                            <div class="col-md-8">
                                <h6 class="mb-3">
                                    <i class="bi bi-file-text"></i> Proposal Details
                                </h6>
                                
                                <div class="mb-3">
                                    {{-- Enums assumed to be English --}}
                                    <span class="badge bg-primary">{{ ucfirst($proposal->type) }}</span>
                                    <span class="badge bg-info">{{ ucfirst($proposal->modality) }}</span>
                                    @if($proposal->level)
                                        <span class="badge bg-secondary">{{ ucfirst($proposal->level) }}</span>
                                    @endif
                                    @if($proposal->slots)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-people"></i> {{ $proposal->slots }} slots
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Description:</strong>
                                    <p class="mt-1" style="white-space: pre-wrap;">{{ $proposal->description }}</p>
                                </div>
                                
                                @if($proposal->location)
                                    <div class="mb-3">
                                        <strong>Preferred Location:</strong>
                                        <p class="mb-0">{{ $proposal->location }}</p>
                                    </div>
                                @endif
                                
                                {{-- Attribute updated to participant_requirements --}}
                                @if($proposal->participant_requirements)
                                    <div class="mb-3">
                                        <strong>Participant Requirements:</strong>
                                        <p class="mb-0">{{ $proposal->participant_requirements }}</p>
                                    </div>
                                @endif
                                
                                <div class="mb-3">
                                    <strong>Proposed Schedules:</strong>
                                    <ul class="mb-0">
                                        {{-- Relationship updated to schedules --}}
                                        @foreach($proposal->schedules as $schedule)
                                            <li>
                                                {{ $schedule->date->format('m/d/Y') }} 
                                                from {{ $schedule->start_time }} to {{ $schedule->end_time }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                <p class="text-muted mb-0">
                                    <small>
                                        <i class="bi bi-clock"></i> 
                                        Submitted on {{ $proposal->created_at->format('m/d/Y H:i') }}
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-end gap-2">
                            {{-- Route updated to proposals.reject --}}
                            <form action="{{ route('proposals.reject', [$event, $proposal]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to reject this proposal?')">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            </form>
                            
                            {{-- Route updated to proposals.approve --}}
                            <form action="{{ route('proposals.approve', [$event, $proposal]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success" 
                                        onclick="return confirm('Approve this proposal? It will be added as an event component.')">
                                    <i class="bi bi-check-circle"></i> Approve Proposal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                @if($offersWithApplications->count() == 0)
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="mt-3 mb-2">No pending proposals or applications</h5>
                            <p class="text-muted">All proposals and applications have been reviewed.</p>
                        </div>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
</div>
@endsection