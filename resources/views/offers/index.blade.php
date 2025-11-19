@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
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

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Open Event Offers</h4>
                    {{-- Route updated to offers.create --}}
                    <a href="{{ route('offers.create', $event) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Publish Offer
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <p class="text-muted mb-0">
                        Open offers are components that you are looking for other users (speakers/workshop leaders) to teach. 
                        They will appear publicly and users will be able to apply.
                    </p>
                </div>
            </div>

            {{-- Variable updated to $offers --}}
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
                                
                                <div class="mb-2">
                                    {{-- Assumes 'type' is stored in English or handled by accessor --}}
                                    <span class="badge bg-primary">{{ ucfirst($offer->type) }}</span>
                                    <span class="badge bg-info">{{ ucfirst($offer->modality) }}</span>
                                    
                                    @if($offer->level)
                                        <span class="badge bg-secondary">{{ ucfirst($offer->level) }}</span>
                                    @endif
                                    
                                    {{-- Attribute updated to slots --}}
                                    @if($offer->slots)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-people"></i> {{ $offer->slots }} slots
                                        </span>
                                    @endif
                                    
                                    {{-- Attribute updated to organizer_cost --}}
                                    @if($offer->organizer_cost && $offer->organizer_cost > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-cash"></i> ${{ number_format($offer->organizer_cost, 2) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="card-text mb-2">{{ Str::limit($offer->description, 200) }}</p>
                                
                                @if($offer->location)
                                    <p class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $offer->location }}
                                        </small>
                                    </p>
                                @endif

                                <div class="mt-2">
                                    <strong class="text-muted">Available schedules:</strong>
                                    <ul class="list-unstyled ms-3 mb-0">
                                        {{-- Relationship updated to schedules --}}
                                        @foreach($offer->schedules as $schedule)
                                            <li>
                                                <small>
                                                    <i class="bi bi-clock"></i>
                                                    {{-- Properties updated to date, start_time, end_time --}}
                                                    {{ $schedule->date->format('m/d/Y') }} 
                                                    from {{ $schedule->start_time }} to {{ $schedule->end_time }}
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                <p class="text-muted mb-0 mt-2">
                                    <small>Published on {{ $offer->created_at->format('m/d/Y H:i') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-megaphone" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 mb-2">You haven't published any offers</h5>
                        <p class="text-muted mb-3">Publish offers so speakers and workshop leaders can apply.</p>
                        <a href="{{ route('offers.create', $event) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Publish First Offer
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection