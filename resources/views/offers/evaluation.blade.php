@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <!-- Event Information -->
            <div class="card-admin mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="mb-1 fw-bold text-brand-deep">{{ $event->name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3 me-1 text-brand-main"></i>
                                {{ $event->start_date->format('m/d/Y') }} - {{ $event->end_date->format('m/d/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('components.index', $event) }}" class="btn btn-white shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to Event
                        </a>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="fw-bold text-brand-deep mb-0">Evaluation Panel</h2>
                <div>
                    @php
                        // English logic: sum of proposals + applications count
                        $totalPending = $proposals->count() + $offersWithApplications->sum(fn($o) => $o->applications->count());
                    @endphp
                    <span class="badge bg-warning text-dark shadow-sm px-3 py-2 rounded-pill" style="font-size: 1rem;">
                        <i class="bi bi-hourglass-split me-1"></i> {{ $totalPending }} Pending
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Section: Applications to Open Offers -->
            @if($offersWithApplications->count() > 0)
                <h4 class="mb-3 fw-bold text-brand-deep">
                    <i class="bi bi-megaphone-fill me-2 text-brand-accent"></i> Applications to Open Offers
                </h4>

                @foreach($offersWithApplications as $offer)
                    <div class="card-admin mb-4 border-start border-4 border-brand-main">
                        <div class="card-header-admin bg-light">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h5 class="mb-0 fw-bold text-brand-deep">
                                    <i class="bi bi-megaphone me-2"></i> {{ $offer->name }}
                                </h5>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-white text-dark border shadow-sm">
                                        {{ $offer->applications->count() }} Application(s)
                                    </span>
                                    {{-- Route updated to offers.close --}}
                                    <form action="{{ route('offers.close', [$event, $offer]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm shadow-sm text-dark"
                                                onclick="return confirm('Close this offer? All pending applications will be rejected.')">
                                            <i class="bi bi-lock-fill me-1"></i> Close Offer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4 p-3 bg-light rounded">
                                <strong class="text-brand-deep d-block mb-1">Offer Description:</strong>
                                <p class="mb-0 text-muted">{{ $offer->description }}</p>
                            </div>

                            <!-- Applications List -->
                            <h6 class="border-bottom pb-2 mb-3 fw-bold text-brand-main">Candidates:</h6>

                            @foreach($offer->applications as $application)
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            <!-- Candidate Info -->
                                            <div class="col-md-4 border-end">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-brand-main text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                                         style="width: 48px; height: 48px; font-size: 1.2rem;">
                                                        {{ strtoupper(substr($application->professionalProfile->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <strong class="d-block text-brand-deep">{{ $application->professionalProfile->user->name }}</strong>
                                                        <small class="text-muted">{{ $application->professionalProfile->user->email }}</small>
                                                    </div>
                                                </div>

                                                {{-- Property updated to current_workplace --}}
                                                @if($application->professionalProfile->current_workplace)
                                                    <p class="mb-2 text-muted small">
                                                        <i class="bi bi-briefcase me-1 text-brand-accent"></i>
                                                        {{ $application->professionalProfile->current_workplace }}
                                                    </p>
                                                @endif

                                                @if($application->professionalProfile->skills)
                                                    <div class="mb-2">
                                                        <small class="fw-bold text-brand-deep d-block mb-1">Skills:</small>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach(array_slice(explode(',', $application->professionalProfile->skills), 0, 5) as $skill)
                                                                <span class="badge bg-light text-dark border">
                                                                    {{ trim($skill) }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Relationship updated to academicBackground (or education) --}}
                                                @if($application->professionalProfile->academicTrainings?->count() > 0)
                                                    <div class="mt-2">
                                                        <small class="fw-bold text-brand-deep d-block mb-1">Education:</small>
                                                        @foreach($application->professionalProfile->academicTrainings->take(2) as $education)
                                                            <div class="mt-1 small text-muted">
                                                                <strong>{{ $education->degree }}</strong><br>
                                                                {{ $education->institution }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Message and Actions -->
                                            <div class="col-md-8">
                                                @if($application->message)
                                                    <div class="mb-3">
                                                        <strong class="text-brand-deep">Candidate's message:</strong>
                                                        <p class="mb-0 mt-1 p-3 bg-light rounded text-muted small fst-italic border-start border-3 border-brand-accent">
                                                            "{{ $application->message }}"
                                                        </p>
                                                    </div>
                                                @endif

                                                <p class="text-muted mb-3 small">
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    Applied on {{ $application->created_at->format('m/d/Y H:i') }}
                                                </p>

                                                <div class="d-flex gap-2 justify-content-end">
                                                    {{-- Route updated to offers.applications.reject --}}
                                                    <form action="{{ route('offers.applications.reject', [$event, $offer, $application->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm"
                                                                onclick="return confirm('Reject this application?')">
                                                            <i class="bi bi-x-circle me-1"></i> Reject
                                                        </button>
                                                    </form>

                                                    {{-- Route updated to offers.applications.accept --}}
                                                    <form action="{{ route('offers.applications.accept', [$event, $offer, $application->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-brand-main btn-sm shadow-sm"
                                                                onclick="return confirm('Accept this candidate? Other applications will be rejected and this user will be assigned as speaker.')">
                                                            <i class="bi bi-check-circle me-1"></i> Accept & Assign
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
                <h4 class="mb-3 fw-bold text-brand-deep {{ $offersWithApplications->count() > 0 ? 'mt-5' : '' }}">
                    <i class="bi bi-send-fill me-2 text-brand-accent"></i> Spontaneous Proposals
                </h4>
            @endif

            @forelse($proposals as $proposal)
                <div class="card-admin mb-4">
                    <div class="card-header-admin bg-light">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5 class="mb-0 fw-bold text-brand-deep">{{ $proposal->name }}</h5>
                            <span class="badge bg-warning text-dark shadow-sm">
                                <i class="bi bi-clock me-1"></i> Pending Review
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Speaker Information -->
                            <div class="col-md-4 border-end">
                                <h6 class="mb-3 fw-bold text-brand-main">
                                    <i class="bi bi-person-badge me-2"></i> Speaker Information
                                </h6>

                                {{-- Relationship updated to speaker --}}
                                @if($proposal->speaker)
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-brand-deep text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                                 style="width: 48px; height: 48px; font-size: 1.2rem;">
                                                {{ strtoupper(substr($proposal->speaker->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="d-block text-brand-deep">{{ $proposal->speaker->user->name }}</strong>
                                                <small class="text-muted">{{ $proposal->speaker->user->email }}</small>
                                            </div>
                                        </div>

                                        @if($proposal->speaker->current_workplace)
                                            <p class="mb-2 text-muted small">
                                                <i class="bi bi-briefcase me-1 text-brand-accent"></i>
                                                {{ $proposal->speaker->current_workplace }}
                                            </p>
                                        @endif

                                        @if($proposal->speaker->skills)
                                            <div class="mb-2">
                                                <small class="fw-bold text-brand-deep d-block mb-1">Skills:</small>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach(array_slice(explode(',', $proposal->speaker->skills), 0, 5) as $skill)
                                                        <span class="badge bg-light text-dark border">
                                                            {{ trim($skill) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($proposal->speaker->academicBackground->count() > 0)
                                            <div class="mt-3">
                                                <small class="fw-bold text-brand-deep d-block mb-1">Education:</small>
                                                @foreach($proposal->speaker->academicBackground->take(2) as $education)
                                                    <div class="mt-1 small text-muted">
                                                        <strong>{{ $education->degree }}</strong><br>
                                                        {{ $education->institution }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Proposal Details -->
                            <div class="col-md-8">
                                <h6 class="mb-3 fw-bold text-brand-main">
                                    <i class="bi bi-file-text me-2"></i> Proposal Details
                                </h6>

                                <div class="mb-3 d-flex flex-wrap gap-2">
                                    {{-- Enums assumed to be English --}}
                                    <span class="badge bg-brand-deep text-white">{{ ucfirst($proposal->type) }}</span>
                                    <span class="badge bg-brand-main text-white">{{ ucfirst($proposal->modality) }}</span>
                                    @if($proposal->level)
                                        <span class="badge bg-secondary">{{ ucfirst($proposal->level) }}</span>
                                    @endif
                                    @if($proposal->slots)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-people me-1"></i> {{ $proposal->slots }} slots
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <strong class="text-brand-deep">Description:</strong>
                                    <p class="mt-1 text-muted" style="white-space: pre-wrap;">{{ $proposal->description }}</p>
                                </div>

                                @if($proposal->location)
                                    <div class="mb-3">
                                        <strong class="text-brand-deep">Preferred Location:</strong>
                                        <p class="mb-0 text-muted">{{ $proposal->location }}</p>
                                    </div>
                                @endif

                                {{-- Attribute updated to participant_requirements --}}
                                @if($proposal->participant_requirements)
                                    <div class="mb-3">
                                        <strong class="text-brand-deep">Participant Requirements:</strong>
                                        <p class="mb-0 text-muted">{{ $proposal->participant_requirements }}</p>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <strong class="text-brand-deep">Proposed Schedules:</strong>
                                    <ul class="mb-0 list-unstyled mt-1">
                                        {{-- Relationship updated to schedules --}}
                                        @foreach($proposal->schedules as $schedule)
                                            <li class="text-muted small">
                                                <i class="bi bi-calendar-event me-1 text-brand-accent"></i>
                                                {{ $schedule->date->format('m/d/Y') }}
                                                from {{ $schedule->start_time }} to {{ $schedule->end_time }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <p class="text-muted mb-0 mt-3 small">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Submitted on {{ $proposal->created_at->format('m/d/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top-0 p-3">
                        <div class="d-flex justify-content-end gap-2">
                            {{-- Route updated to proposals.reject --}}
                            <form action="{{ route('proposals.reject', [$event, $proposal]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger shadow-sm"
                                        onclick="return confirm('Are you sure you want to reject this proposal?')">
                                    <i class="bi bi-x-circle me-1"></i> Reject
                                </button>
                            </form>

                            {{-- Route updated to proposals.approve --}}
                            <form action="{{ route('proposals.approve', [$event, $proposal]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-brand-main shadow-sm"
                                        onclick="return confirm('Approve this proposal? It will be added as an event component.')">
                                    <i class="bi bi-check-circle me-1"></i> Approve Proposal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                @if($offersWithApplications->count() == 0)
                    <div class="card-admin border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="mt-3 mb-2 text-brand-deep">No pending proposals or applications</h5>
                            <p class="text-muted">All proposals and applications have been reviewed.</p>
                        </div>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
</div>
@endsection