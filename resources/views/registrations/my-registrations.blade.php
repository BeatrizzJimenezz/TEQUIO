@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">My Registrations</h4>
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

                    {{-- Variable updated to $registrations --}}
                    @forelse($registrations as $registration)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        {{-- Relationships updated to English --}}
                                        <h5 class="card-title">{{ $registration->component->name }}</h5>
                                        <h6 class="text-muted">{{ $registration->component->event->name }}</h6>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-primary">{{ ucfirst($registration->component->type) }}</span>
                                            <span class="badge bg-info">{{ ucfirst($registration->component->modality) }}</span>
                                            @if($registration->component->level)
                                                <span class="badge bg-secondary">{{ ucfirst($registration->component->level) }}</span>
                                            @endif
                                        </div>

                                        <p class="card-text">{{ Str::limit($registration->component->description, 120) }}</p>

                                        @if($registration->component->location)
                                            <p class="mb-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-geo-alt"></i> {{ $registration->component->location }}
                                                </small>
                                            </p>
                                        @endif

                                        <div class="mt-2">
                                            <strong>Schedules:</strong>
                                            <ul class="list-unstyled ms-3 mb-2">
                                                @foreach($registration->component->schedules as $schedule)
                                                    <li>
                                                        <i class="bi bi-clock"></i>
                                                        {{ $schedule->date->format('m/d/Y') }} 
                                                        from {{ $schedule->start_time }} to {{ $schedule->end_time }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <p class="mb-0">
                                            <small class="text-muted">
                                                {{-- Assumes created_at is the registration timestamp --}}
                                                Registered on: {{ $registration->created_at->format('m/d/Y H:i') }}
                                            </small>
                                        </p>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="mb-3">
                                            <strong>Your Ticket</strong>
                                            <div id="qr-{{ $registration->id }}" class="my-2 d-flex justify-content-center"></div>
                                            {{-- Attribute updated to ticket_code --}}
                                            <p class="mb-0"><small>{{ $registration->ticket_code }}</small></p>
                                        </div>

                                        {{-- Route updated to registrations.destroy (REST standard) --}}
                                        <form action="{{ route('registrations.destroy', $registration->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to cancel this registration?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                <i class="bi bi-x-circle"></i> Cancel Registration
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-ticket-perforated" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">You have no active registrations</p>
                            {{-- Route updated to events.public.index --}}
                            <a href="{{ route('events.public.index') }}" class="btn btn-primary">
                                <i class="bi bi-search"></i> Explore Events
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR codes for each registration
    @foreach($registrations as $registration)
        new QRCode(document.getElementById("qr-{{ $registration->id }}"), {
            text: "{{ $registration->ticket_code }}",
            width: 150,
            height: 150
        });
    @endforeach
</script>
@endpush
@endsection