@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
                        {{-- Route updated to proposals.index or wherever the list of events is --}}
                        <a href="{{ route('proposals.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Proposal Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Submit Presentation/Workshop Proposal</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Your proposal will be reviewed by the event organizer. You will be notified when it is evaluated.
                    </div>

                    {{-- Route updated to proposals.store --}}
                    <form action="{{ route('proposals.store', $event) }}" method="POST" id="proposalForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Proposal Information</h5>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Title of Presentation/Workshop *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Select...</option>
                                    {{-- Values updated to English Enums --}}
                                    <option value="presentation" {{ old('type') == 'presentation' ? 'selected' : '' }}>Presentation</option>
                                    <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                    <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Activity</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="5" required 
                                          placeholder="Describe what your presentation/workshop is about, what participants will learn, etc.">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Level</label>
                                <select class="form-select @error('level') is-invalid @enderror" 
                                        id="level" name="level">
                                    <option value="">Select...</option>
                                    {{-- Values updated to English Enums --}}
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="slots" class="form-label">Suggested Number of Slots</label>
                                <input type="number" class="form-control @error('slots') is-invalid @enderror" 
                                       id="slots" name="slots" value="{{ old('slots') }}" min="1"
                                       placeholder="Ex: 30">
                                <small class="text-muted">Optional - the organizer can adjust this</small>
                                @error('slots')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Modality and Location -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Modality</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modality" class="form-label">Modality *</label>
                                <select class="form-select @error('modality') is-invalid @enderror" 
                                        id="modality" name="modality" required>
                                    <option value="">Select...</option>
                                    {{-- Values updated to English Enums --}}
                                    <option value="virtual" {{ old('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality') == 'in_person' ? 'selected' : '' }}>In-Person</option>
                                    <option value="hybrid" {{ old('modality') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                </select>
                                @error('modality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Preferred Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location') }}"
                                       placeholder="Ex: Main Auditorium, Zoom, etc.">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Requirements</h5>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="participant_requirements" class="form-label">Participant Requirements</label>
                                <textarea class="form-control @error('participant_requirements') is-invalid @enderror" 
                                          id="participant_requirements" name="participant_requirements" rows="3"
                                          placeholder="Ex: Laptop, basic programming knowledge, etc.">{{ old('participant_requirements') }}</textarea>
                                @error('participant_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Proposed Schedules -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Proposed Schedules *</h5>
                                <p class="text-muted">Propose the times when you could teach your presentation/workshop</p>
                            </div>

                            <div class="col-12">
                                <div id="schedulesContainer">
                                    <!-- Initial Schedule -->
                                    <div class="schedule-item card mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-end">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Date *</label>
                                                    <input type="date" class="form-control" 
                                                           name="schedules[0][date]" 
                                                           min="{{ $event->start_date->format('Y-m-d') }}"
                                                           max="{{ $event->end_date->format('Y-m-d') }}"
                                                           required>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">Start Time *</label>
                                                    <input type="time" class="form-control" 
                                                           name="schedules[0][start_time]" required>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">End Time *</label>
                                                    <input type="time" class="form-control" 
                                                           name="schedules[0][end_time]" required>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSchedule(this)" disabled>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-primary" onclick="addSchedule()">
                                    <i class="bi bi-plus-circle"></i> Add Another Schedule
                                </button>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    {{-- Route updated to proposals.index --}}
                                    <a href="{{ route('proposals.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Submit Proposal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let scheduleIndex = 1;

function addSchedule() {
    const container = document.getElementById('schedulesContainer');
    const minDate = '{{ $event->start_date->format("Y-m-d") }}';
    const maxDate = '{{ $event->end_date->format("Y-m-d") }}';
    
    const newSchedule = `
        <div class="schedule-item card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control" 
                               name="schedules[${scheduleIndex}][date]"
                               min="${minDate}"
                               max="${maxDate}"
                               required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Start Time *</label>
                        <input type="time" class="form-control" 
                               name="schedules[${scheduleIndex}][start_time]" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">End Time *</label>
                        <input type="time" class="form-control" 
                               name="schedules[${scheduleIndex}][end_time]" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSchedule(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newSchedule);
    scheduleIndex++;
    updateRemoveButtons();
}

function removeSchedule(button) {
    button.closest('.schedule-item').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const schedules = document.querySelectorAll('.schedule-item');
    schedules.forEach((item) => {
        const btnRemove = item.querySelector('button[onclick*="removeSchedule"]');
        btnRemove.disabled = schedules.length === 1;
    });
}
</script>
@endsection