@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">My Events</a></li>
                    {{-- Route updated to events.public.show --}}
                    <li class="breadcrumb-item"><a href="{{ route('dashboard', $event->id) }}">{{ $event->name }}</a></li>
                    <li class="breadcrumb-item active">Organizing Team</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1" style="color: #0C2340;">
                        <i class="bi bi-people-fill" style="color: #4499BB;"></i> Organizing Team
                    </h2>
                    <p class="text-muted mb-0">{{ $event->name }}</p>
                </div>
                {{-- Route updated to events.team.create --}}
                <a href="{{ route('events.team.create', $event) }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Add Organizer
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: #0C2340; color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-star-fill" style="color: #FFD700;"></i> Lead Organizer
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3" style="width: 60px; height: 60px; background-color: #4499BB; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 24px; font-weight: bold;">
                            {{-- Variable updated to leadOrganizer --}}
                            {{ strtoupper(substr($leadOrganizer->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $leadOrganizer->user->name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-envelope"></i> {{ $leadOrganizer->user->email }}
                            </p>
                            {{-- Attribute updated to current_workplace --}}
                            @if($leadOrganizer->current_workplace)
                            <p class="text-muted mb-0">
                                <i class="bi bi-briefcase"></i> {{ $leadOrganizer->current_workplace }}
                            </p>
                            @endif
                        </div>
                        <span class="badge bg-warning text-dark">Creator</span>
                    </div>
                </div>
            </div>

            {{-- Variable updated to collaborators --}}
            @if($collaborators->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: #0C2340; color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-people"></i> Co-Organizers ({{ $collaborators->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #F8F9FA;">
                                <tr>
                                    <th>Organizer</th>
                                    <th>Email</th>
                                    <th>Workplace</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collaborators as $collaborator)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2" style="width: 40px; height: 40px; background-color: #4499BB; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 16px; font-weight: bold;">
                                                {{ strtoupper(substr($collaborator->user->name, 0, 1)) }}
                                            </div>
                                            <strong>{{ $collaborator->user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $collaborator->user->email }}</td>
                                    <td>
                                        @if($collaborator->current_workplace)
                                            {{ $collaborator->current_workplace }}
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- ID updated to deleteModal --}}
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $collaborator->id }}">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>

                                        <div class="modal fade" id="deleteModal{{ $collaborator->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #0C2340; color: white;">
                                                        <h5 class="modal-title">Confirm Removal</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Are you sure you want to remove <strong>{{ $collaborator->user->name }}</strong> from the organizing team?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        {{-- Route updated to events.team.destroy --}}
                                                        <form action="{{ route('events.team.destroy', [$event, $collaborator->id]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Yes, Remove</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-people" style="font-size: 4rem; color: #CCC;"></i>
                    <h4 class="mt-3 text-muted">No co-organizers</h4>
                    <p class="text-muted">Add additional organizers to manage this event as a team.</p>
                    <a href="{{ route('events.team.create', $event) }}" class="btn btn-primary mt-2">
                        <i class="bi bi-person-plus-fill"></i> Add First Co-Organizer
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-primary {
        background-color: #4499BB;
        border-color: #4499BB;
    }
    .btn-primary:hover {
        background-color: #357A99;
        border-color: #357A99;
    }
</style>
@endpush
@endsection