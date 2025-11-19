@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                {{-- Route updated to events.public.index --}}
                <a href="{{ route('events.public.index') }}">Events</a>
            </li>
            <li class="breadcrumb-item active">{{ $event->name }}</li>
        </ol>
    </nav>

    <!-- Feedback Messages Container -->
    <div id="feedback-message"></div>

    <!-- Event Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            {{-- Attribute updated to cover_image --}}
            @if($event->cover_image)
            <img src="{{ $event->cover_image }}" 
                 class="img-fluid rounded shadow-lg mb-4" 
                 alt="{{ $event->name }}"
                 style="width: 100%; max-height: 400px; object-fit: cover;">
            @endif

            <h1 class="display-5 mb-3">{{ $event->name }}</h1>

            <div class="mb-3">
                {{-- Values updated to English --}}
                <span class="badge bg-success fs-6">{{ ucfirst($event->status) }}</span>
                <span class="badge bg-info fs-6">{{ ucfirst($event->modality) }}</span>
                {{-- Variable updated to $tags --}}
                @foreach($event->tags as $tag)
                    <span class="badge bg-secondary fs-6">{{ $tag->name }}</span>
                @endforeach
            </div>

            <p class="lead">{{ $event->description }}</p>
        </div>

        <!-- Sidebar with Information -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-info-circle"></i> Event Information
                    </h5>

                    <!-- Dates -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-calendar-event"></i> Dates
                        </h6>
                        <p class="mb-0">
                            {{-- Attributes updated to start_date, end_date, start_time --}}
                            <strong>Start:</strong> {{ $event->start_date->format('m/d/Y') }}<br>
                            <strong>End:</strong> {{ $event->end_date->format('m/d/Y') }}<br>
                            <strong>Time:</strong> {{ $event->start_time }}
                        </p>
                    </div>

                    <hr>

                    <!-- Modality and Location -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-laptop"></i> Modality
                        </h6>
                        <p class="mb-0">{{ ucfirst($event->modality) }}</p>
                    </div>

                    @if($event->location)
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-geo-alt"></i> Location
                        </h6>
                        <p class="mb-0">{{ $event->location }}</p>
                    </div>
                    @endif

                    <hr>

                    <!-- Organizer -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-person-badge"></i> Organizer
                        </h6>
                        <p class="mb-0">
                            {{-- Relationships updated to professionalProfile --}}
                            {{ $event->professionalProfile->full_name ?? $event->professionalProfile->user->name }}
                        </p>
                    </div>

                    <hr>

                    <!-- Statistics -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-graph-up"></i> Statistics
                        </h6>
                        <p class="mb-0">
                            {{-- Relationship updated to approvedComponents --}}
                            <strong>{{ $event->approvedComponents->count() }}</strong> component(s)<br>
                            {{-- Attribute updated to slots --}}
                            <strong>{{ $event->approvedComponents->sum('slots') ?: 'Unlimited' }}</strong> total slots
                        </p>
                    </div>

                    @auth
                    <div class="d-grid mt-4">
                        <a href="{{ route('events.public.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Catalog
                        </a>
                    </div>
                    @else
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login to Register
                        </a>
                        <a href="{{ route('events.public.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Event Components -->
    @if($event->approvedComponents->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="bi bi-collection"></i> Event Components
            </h2>

            <!-- Tabs by Component Type -->
            {{-- Variable updated to $componentsByType --}}
            @if($componentsByType->count() > 1)
            <ul class="nav nav-tabs mb-4" role="tablist">
                @foreach($componentsByType as $type => $components)
                <li class="nav-item">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#{{ Str::slug($type) }}"
                            type="button">
                        {{ ucfirst($type) }}s ({{ $components->count() }})
                    </button>
                </li>
                @endforeach
            </ul>
            @endif

            <!-- Tab Content -->
            <div class="tab-content">
                @foreach($componentsByType as $type => $components)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                     id="{{ Str::slug($type) }}">
                    
                    <div class="row g-4">
                        @foreach($components as $component)
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                {{-- Attribute updated to cover_url --}}
                                @if($component->cover_url)
                                <img src="{{ $component->cover_url }}" 
                                     class="card-img-top" 
                                     alt="{{ $component->name }}"
                                     style="height: 180px; object-fit: cover;">
                                @endif

                                <div class="card-body">
                                    <h5 class="card-title">{{ $component->name }}</h5>

                                    <!-- Badges -->
                                    <div class="mb-3">
                                        <span class="badge bg-primary">{{ ucfirst($component->type) }}</span>
                                        <span class="badge bg-info">{{ ucfirst($component->modality) }}</span>
                                        @if($component->level)
                                            <span class="badge bg-secondary">{{ ucfirst($component->level) }}</span>
                                        @endif
                                        {{-- Attribute updated to attendee_price --}}
                                        @if($component->attendee_price > 0)
                                            <span class="badge bg-success">${{ number_format($component->attendee_price, 2) }}</span>
                                        @else
                                            <span class="badge bg-success">Free</span>
                                        @endif
                                    </div>

                                    <p class="card-text">{{ $component->description }}</p>

                                    @if($component->location)
                                    <p class="small text-muted mb-2">
                                        <i class="bi bi-geo-alt"></i> {{ $component->location }}
                                    </p>
                                    @endif

                                    <!-- Available Slots -->
                                    {{-- Attribute updated to slots / available_slots --}}
                                    @if($component->slots)
                                    <div class="alert alert-info py-2 mb-3">
                                        <i class="bi bi-people"></i>
                                        <strong>Slots:</strong> 
                                        <span id="slots-{{ $component->id }}">{{ $component->available_slots }}</span> 
                                        available of {{ $component->slots }}
                                        
                                        @if($component->available_slots <= 0)
                                            <span class="badge bg-danger ms-2">Full</span>
                                        @elseif($component->available_slots <= 5)
                                            <span class="badge bg-warning text-dark ms-2">Last slots</span>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Schedules -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2">
                                            <i class="bi bi-clock"></i> Schedule
                                        </h6>
                                        <ul class="list-unstyled mb-0">
                                            {{-- Relationship updated to schedules --}}
                                            @foreach($component->schedules as $schedule)
                                            <li class="small mb-1">
                                                <i class="bi bi-calendar-check"></i>
                                                {{-- Attributes updated to date, start_time, end_time --}}
                                                {{ $schedule->date->format('m/d/Y') }}
                                                from {{ $schedule->start_time }} to {{ $schedule->end_time }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <!-- Requirements -->
                                    {{-- Attribute updated to participant_requirements --}}
                                    @if($component->participant_requirements)
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2">
                                            <i class="bi bi-check2-square"></i> Requirements
                                        </h6>
                                        <p class="small mb-0">{{ $component->participant_requirements }}</p>
                                    </div>
                                    @endif

                                    <!-- Registration Button -->
                                    @auth
                                    <div id="btn-container-{{ $component->id }}">
                                        @if($component->slots && $component->available_slots <= 0)
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="bi bi-x-circle"></i> No Slots Available
                                        </button>
                                        @else
                                        <button class="btn btn-primary w-100 btn-register" 
                                                data-component-id="{{ $component->id }}"
                                                onclick="register({{ $component->id }})">
                                            <i class="bi bi-pencil-square"></i> Register
                                        </button>
                                        @endif
                                    </div>
                                    @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-box-arrow-in-right"></i> Login to Register
                                    </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i>
                This event has no published components yet.
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // CSRF Token for requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Function to show messages
    function showMessage(message, type = 'success') {
        const feedbackDiv = document.getElementById('feedback-message');
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        
        feedbackDiv.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Scroll to top to see message
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Auto-close after 5 seconds
        setTimeout(() => {
            const alert = feedbackDiv.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }

    // Function to register
    // JS variables updated to English (componentId, currentSlots)
    async function register(componentId) {
        const button = document.querySelector(`button[data-component-id="${componentId}"]`);
        const btnContainer = document.getElementById(`btn-container-${componentId}`);
        
        // Disable button while processing
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
        
        try {
            // Route updated to registrations.store
            const response = await fetch('{{ route("registrations.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    component_id: componentId
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Success
                showMessage(data.message, 'success');
                
                // Change button to "Already registered"
                btnContainer.innerHTML = `
                    <button class="btn btn-success w-100" disabled>
                        <i class="bi bi-check-circle"></i> Already registered
                    </button>
                `;
                
                // Update slots if they exist
                const slotsElement = document.getElementById(`slots-${componentId}`);
                if (slotsElement) {
                    const currentSlots = parseInt(slotsElement.textContent);
                    slotsElement.textContent = currentSlots - 1;
                }
                
            } else {
                // Validation error
                showMessage(data.message || 'Error processing registration', 'error');
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-pencil-square"></i> Register';
            }
            
        } catch (error) {
            console.error('Error:', error);
            showMessage('Connection error. Please try again.', 'error');
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-pencil-square"></i> Register';
        }
    }

    // Verify registrations on page load
    document.addEventListener('DOMContentLoaded', async function() {
        @auth
        const registerButtons = document.querySelectorAll('.btn-register');
        
        for (const button of registerButtons) {
            const componentId = button.dataset.componentId;
            
            try {
                // URL updated to /registrations/check
                const response = await fetch(`{{ url('/registrations/check') }}/${componentId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                
                // 'inscrito' -> 'registered' (Update your JSON response in backend)
                if (data.registered || data.inscrito) {
                    const btnContainer = document.getElementById(`btn-container-${componentId}`);
                    btnContainer.innerHTML = `
                        <button class="btn btn-success w-100" disabled>
                            <i class="bi bi-check-circle"></i> Already registered
                        </button>
                    `;
                }
            } catch (error) {
                console.error('Error verifying registration:', error);
            }
        }
        @endauth
    });
</script>
@endpush