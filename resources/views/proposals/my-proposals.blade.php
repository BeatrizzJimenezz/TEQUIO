@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Proposals</h2>
                {{-- Route updated to proposals.index (or the event selection page) --}}
                <a href="{{ route('proposals.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Proposal
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <div class="btn-group" role="group">
                        {{-- Filter values updated to English logic --}}
                        <button type="button" class="btn btn-outline-secondary active" onclick="filterBy('all')">
                            All
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="filterBy('proposed')">
                            Pending
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="filterBy('approved')">
                            Approved
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="filterBy('rejected')">
                            Rejected
                        </button>
                    </div>
                </div>
            </div>

            {{-- Variable updated to $proposals --}}
            @forelse($proposals as $proposal)
                {{-- data-status attribute updated --}}
                <div class="card mb-3 proposal-card" data-status="{{ $proposal->proposal_status }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-2">{{ $proposal->name }}</h5>
                                    {{-- Logic updated to English status values --}}
                                    @if($proposal->proposal_status == 'proposed')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> Pending Review
                                        </span>
                                    @elseif($proposal->proposal_status == 'approved')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Approved
                                        </span>
                                    @elseif($proposal->proposal_status == 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Rejected
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar-event"></i> Event: <strong>{{ $proposal->event->name }}</strong>
                                </p>
                                
                                <div class="mb-2">
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
                                
                                <p class="card-text mb-2">{{ Str::limit($proposal->description, 150) }}</p>
                                
                                <div class="mt-2">
                                    <strong class="text-muted">Proposed schedules:</strong>
                                    <ul class="list-unstyled ms-3 mb-0">
                                        {{-- Relationship updated to schedules --}}
                                        @foreach($proposal->schedules as $schedule)
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
                                
                                <p class="text-muted mb-0 mt-2">
                                    <small>Submitted on {{ $proposal->created_at->format('m/d/Y H:i') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 mb-2">You haven't submitted any proposals</h5>
                        <p class="text-muted mb-3">Submit your first proposal to a public event.</p>
                        <a href="{{ route('proposals.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Submit Proposal
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function filterBy(status) {
    // Update active buttons
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Filter cards
    const cards = document.querySelectorAll('.proposal-card');
    cards.forEach(card => {
        // Logic updated to check dataset.status and 'all'
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection