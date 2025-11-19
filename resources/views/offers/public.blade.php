@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="mb-4">
                <h2>Open Offers</h2>
                <p class="text-muted">Events seeking speakers and workshop leaders</p>
            </div>

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

            @forelse($offers as $offer)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-2">{{ $offer->name }}</h5>
                                    <span class="badge bg-success">
                                        <i class="bi bi-megaphone"></i> Open Offer
                                    </span>
                                </div>
                                
                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar-event"></i> 
                                    {{-- Relationship updated to event --}}
                                    <strong>{{ $offer->event->name }}</strong>
                                </p>
                                
                                <div class="mb-2">
                                    <span class="badge bg-primary">{{ ucfirst($offer->type) }}</span>
                                    <span class="badge bg-info">{{ ucfirst($offer->modality) }}</span>
                                    
                                    @if($offer->level)
                                        <span class="badge bg-secondary">{{ ucfirst($offer->level) }}</span>
                                    @endif
                                    
                                    @if($offer->slots)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-people"></i> {{ $offer->slots }} slots
                                        </span>
                                    @endif
                                    
                                    @if($offer->organizer_cost)
                                        <span class="badge bg-success">
                                            <i class="bi bi-cash"></i> Paid
                                        </span>
                                    @endif

                                    @php
                                        // Logic updated to English relationships
                                        // Assumes user relationship is 'professionalProfile'
                                        $myApplications = $offer->applications()
                                            ->where('professional_profile_id', auth()->user()->professionalProfile?->id)
                                            ->count();
                                        
                                        $totalApplications = $offer->applications()->count();
                                    @endphp
                                    
                                    @if($totalApplications > 0)
                                        <span class="badge bg-info">
                                            <i class="bi bi-people"></i> {{ $totalApplications }} Applicant(s)
                                        </span>
                                    @endif
                                    
                                    @if($myApplications > 0)
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-check"></i> You Applied
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="card-text mb-2">{{ $offer->description }}</p>
                                
                                @if($offer->location)
                                    <p class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $offer->location }}
                                        </small>
                                    </p>
                                @endif
                                
                                @if($offer->instructor_requirements)
                                    <div class="mt-2">
                                        <strong class="text-muted">Requirements:</strong>
                                        <p class="mb-0"><small>{{ $offer->instructor_requirements }}</small></p>
                                    </div>
                                @endif
                                
                                <!-- Schedules -->
                                <div class="mt-2">
                                    <strong class="text-muted">Available schedules:</strong>
                                    <ul class="list-unstyled ms-3 mb-0">
                                        @foreach($offer->schedules as $schedule)
                                            <li>
                                                <small>
                                                    <i class="bi bi-clock"></i>
                                                    {{ $schedule->date->format('m/d/Y') }} 
                                                    from {{ $schedule->start_time }} to {{ $schedule->end_time }}
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="ms-3">
                                {{-- Disable button if already applied (optional logic) --}}
                                <button type="button" class="btn btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#applyModal{{ $offer->id }}"
                                        {{ $myApplications > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-hand-thumbs-up"></i> {{ $myApplications > 0 ? 'Applied' : 'Apply' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apply Modal -->
                <div class="modal fade" id="applyModal{{ $offer->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            {{-- Route updated to offers.apply --}}
                            <form action="{{ route('offers.apply', $offer) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Apply to: {{ $offer->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i>
                                        By applying, your professional profile will be reviewed by the organizer.
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Message to the organizer (optional)</label>
                                        {{-- name="mensaje" -> "message" --}}
                                        <textarea class="form-control" name="message" rows="4"
                                                  placeholder="Tell the organizer why you are the right person for this activity..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Submit Application
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 mb-2">No open offers available</h5>
                        <p class="text-muted mb-3">There are currently no events looking for speakers or workshop leaders.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection