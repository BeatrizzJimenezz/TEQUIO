@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Professional Profile</h2>
        <a href="{{ route('professional-profile.edit') }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit Profile
        </a>
    </div>

    <!-- Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- User Information Card -->
    <div class="card mb-4">
        <div class="card-body d-flex align-items-start">
            
            <!-- Profile Photo -->
            <div class="me-4">
                @if(auth()->user()->profile_photo)
                   <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                        alt="Profile photo"
                        class="rounded-circle border border-primary"
                        style="width: 110px; height: 110px; object-fit: cover;">

                @else
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 110px; height: 110px; font-size: 3rem;">
                        <i class="bi bi-person"></i>
                    </div>
                @endif
            </div>

            <!-- User Info -->
            <div class="flex-grow-1">
                <h3 class="mb-1">{{ auth()->user()->name }}</h3>
                <p class="text-muted mb-2">
                    <i class="bi bi-envelope"></i> {{ auth()->user()->email }}
                </p>

                @if($profile->current_workplace)
                    <p class="mb-2 text-muted">
                        <i class="bi bi-briefcase"></i> {{ $profile->current_workplace }}
                    </p>
                @endif

                @if($profile->skills)
                    <div class="mt-2">
                        <strong>Skills:</strong><br>
                        <div class="mt-1 d-flex flex-wrap gap-2">
                            @foreach(explode(',', $profile->skills) as $skill)
                                <span class="badge bg-primary">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- About me -->
    @if($profile->about_me)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">About me</h5>
            </div>
            <div class="card-body">
                <p style="white-space: pre-wrap;">{{ $profile->about_me }}</p>
            </div>
        </div>
    @endif

    <!-- Academic Training -->
    @if($profile->academicTrainings->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-mortarboard-fill"></i> Academic Training</h5>
            </div>
            <div class="card-body">
                @foreach($profile->academicTrainings as $training)
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-1">{{ $training->degree }}</h6>
                        <p class="mb-1 text-muted">{{ $training->institution }}</p>
                        <p class="mb-1 text-muted">
                            <i class="bi bi-calendar"></i>
                            {{ \Carbon\Carbon::parse($training->start_date)->format('M Y') }}
                            -
                            {{ $training->end_date ? \Carbon\Carbon::parse($training->end_date)->format('M Y') : 'Present' }}
                        </p>

                        @if($training->description)
                            <p class="mb-0">{{ $training->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Social Networks -->
    @if($profile->socialNetworks->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-share"></i> Social Networks</h5>
            </div>
            <div class="card-body d-flex flex-wrap gap-3">
                @foreach($profile->socialNetworks as $network)
                    <a href="{{ $network->link }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-link-45deg"></i> {{ $network->platform }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Talks / Workshops -->
    @if($activities->count() > 0)
        <div class="card mb-5">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Talks / Workshops Delivered</h5>
            </div>
            <div class="card-body">
                @foreach($activities as $activity)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1">{{ $activity->name }}</h6>
                                <small class="text-muted">{{ $activity->event->name }}</small><br>

                                <span class="badge 
                                    @if($activity->type == 'talk') bg-primary
                                    @elseif($activity->type == 'workshop') bg-success
                                    @else bg-info
                                    @endif">
                                    {{ ucfirst($activity->type) }}
                                </span>
                            </div>

                            @if($activity->schedules->first())
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($activity->schedules->first()->date)->format('m/d/Y') }}
                                </small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
