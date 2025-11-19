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
                        <a href="{{ route('components.index', $event) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Component</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Route updated to 'components.update' --}}
                    <form action="{{ route('components.update', [$event, $component]) }}" method="POST" id="componentForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Basic Information</h5>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Component Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $component->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Select...</option>
                                    {{-- Values updated to lowercase English --}}
                                    <option value="activity" {{ old('type', $component->type) == 'activity' ? 'selected' : '' }}>Activity</option>
                                    <option value="presentation" {{ old('type', $component->type) == 'presentation' ? 'selected' : '' }}>Presentation</option>
                                    <option value="workshop" {{ old('type', $component->type) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" required>{{ old('description', $component->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cover_url" class="form-label">Cover Image URL</label>
                                <input type="url" class="form-control @error('cover_url') is-invalid @enderror" 
                                       id="cover_url" name="cover_url" value="{{ old('cover_url', $component->cover_url) }}"
                                       placeholder="https://example.com/image.jpg">
                                @error('cover_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Level</label>
                                <select class="form-select @error('level') is-invalid @enderror" 
                                        id="level" name="level">
                                    <option value="">Select...</option>
                                    {{-- Values updated to lowercase English --}}
                                    <option value="beginner" {{ old('level', $component->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('level', $component->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('level', $component->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Speaker / Workshop Leader</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="speaker_id" class="form-label">Select Speaker</label>
                                {{-- Assumes variable passed from controller is $speakers --}}
                                <select class="form-select @error('speaker_id') is-invalid @enderror" 
                                        id="speaker_id" name="speaker_id">
                                    <option value="">-- Select --</option>
                                    @foreach($speakers as $profile)
                                        <option value="{{ $profile->id }}" 
                                            {{ old('speaker_id', $component->speaker_id) == $profile->id ? 'selected' : '' }}>
                                            {{ $profile->user->name }} ({{ $profile->skills ?? 'No skills registered' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('speaker_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">If not selected, leave unassigned.</small>
                            </div>
                        </div>


                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Modality and Location</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modality" class="form-label">Modality *</label>
                                <select class="form-select @error('modality') is-invalid @enderror" 
                                        id="modality" name="modality" required>
                                    <option value="">Select...</option>
                                    <option value="virtual" {{ old('modality', $component->modality) == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality', $component->modality) == 'in_person' ? 'selected' : '' }}>In-Person</option>
                                    <option value="hybrid" {{ old('modality', $component->modality) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                </select>
                                @error('modality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $component->location) }}"
                                       placeholder="Ex: Conference Room A, Zoom Link, etc.">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Capacity and Pricing</h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="slots" class="form-label">Number of Slots</label>
                                <input type="number" class="form-control @error('slots') is-invalid @enderror" 
                                       id="slots" name="slots" value="{{ old('slots', $component->slots) }}" min="1"
                                       placeholder="Ex: 30">
                                <small class="text-muted">Leave empty for unlimited slots</small>
                                @error('slots')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="attendee_price" class="form-label">Attendee Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('attendee_price') is-invalid @enderror" 
                                           id="attendee_price" name="attendee_price" 
                                           value="{{ old('attendee_price', $component->attendee_price) }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted">$0.00 for free activity</small>
                                @error('attendee_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="organizer_cost" class="form-label">Organizer Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('organizer_cost') is-invalid @enderror" 
                                           id="organizer_cost" name="organizer_cost" 
                                           value="{{ old('organizer_cost', $component->organizer_cost) }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted">Internal cost (optional)</small>
                                @error('organizer_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Requirements</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="participant_requirements" class="form-label">Participant Requirements</label>
                                <textarea class="form-control @error('participant_requirements') is-invalid @enderror" 
                                          id="participant_requirements" name="participant_requirements" rows="3"
                                          placeholder="Ex: Laptop, basic programming knowledge, etc.">{{ old('participant_requirements', $component->participant_requirements) }}</textarea>
                                @error('participant_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="instructor_requirements" class="form-label">Instructor Requirements</label>
                                <textarea class="form-control @error('instructor_requirements') is-invalid @enderror" 
                                          id="instructor_requirements" name="instructor_requirements" rows="3"
                                          placeholder="Ex: Projector, microphone, internet connection, etc.">{{ old('instructor_requirements', $component->instructor_requirements) }}</textarea>
                                @error('instructor_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Schedules *</h5>
                            </div>

                            <div class="col-12">
                                <div id="schedulesContainer">
                                    {{-- Handle validation errors (old input) --}}
                                    @if(old('schedules'))
                                        @foreach(old('schedules') as $index => $schedule)
                                            <div class="schedule-item card mb-3">
                                                <div class="card-body">
                                                    <div class="row align-items-end">
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Date *</label>
                                                            <input type="date" class="form-control @error('schedules.'.$index.'.date') is-invalid @enderror" 
                                                                   name="schedules[{{ $index }}][date]" 
                                                                   value="{{ $schedule['date'] }}"
                                                                   min="{{ $event->start_date->format('Y-m-d') }}"
                                                                   max="{{ $event->end_date->format('Y-m-d') }}"
                                                                   required>
                                                            @error('schedules.'.$index.'.date')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label">Start Time *</label>
                                                            <input type="time" class="form-control @error('schedules.'.$index.'.start_time') is-invalid @enderror" 
                                                                   name="schedules[{{ $index }}][start_time]"
                                                                   value="{{ $schedule['start_time'] }}"
                                                                   required>
                                                            @error('schedules.'.$index.'.start_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label">End Time *</label>
                                                            <input type="time" class="form-control @error('schedules.'.$index.'.end_time') is-invalid @enderror" 
                                                                   name="schedules[{{ $index }}][end_time]"
                                                                   value="{{ $schedule['end_time'] }}"
                                                                   required>
                                                            @error('schedules.'.$index.'.end_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSchedule(this)">
                                                                <i class="bi bi-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        {{-- Handle existing database values --}}
                                        @foreach($component->schedules as $index => $schedule)
                                            <div class="schedule-item card mb-3">
                                                <div class="card-body">
                                                    <div class="row align-items-end">
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Date *</label>
                                                            <input type="date" class="form-control" 
                                                                   name="schedules[{{ $index }}][date]" 
                                                                   value="{{ $schedule->date->format('Y-m-d') }}"
                                                                   min="{{ $event->start_date->format('Y-m-d') }}"
                                                                   max="{{ $event->end_date->format('Y-m-d') }}"
                                                                   required>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label">Start Time *</label>
                                                            <input type="time" class="form-control" 
                                                                   name="schedules[{{ $index }}][start_time]"
                                                                   value="{{ $schedule->start_time }}"
                                                                   required>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label">End Time *</label>
                                                            <input type="time" class="form-control" 
                                                                   name="schedules[{{ $index }}][end_time]"
                                                                   value="{{ $schedule->end_time }}"
                                                                   required>
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSchedule(this)">
                                                                <i class="bi bi-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <button type="button" class="btn btn-outline-primary" onclick="addSchedule()">
                                    <i class="bi bi-plus-circle"></i> Add Another Schedule
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('components.index', $event) }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Update Component
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
// Logic updated to check old 'schedules' first, then database 'schedules'
let scheduleIndex = {{ old('schedules') ? count(old('schedules')) : $component->schedules->count() }};

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
                               name="schedules[${scheduleIndex}][start_time]"
                               required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">End Time *</label>
                        <input type="time" class="form-control" 
                               name="schedules[${scheduleIndex}][end_time]"
                               required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSchedule(this)">
                            <i class="bi bi-trash"></i> Remove
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
    const scheduleItem = button.closest('.schedule-item');
    scheduleItem.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const schedules = document.querySelectorAll('.schedule-item');
    schedules.forEach((item, index) => {
        const btnRemove = item.querySelector('button[onclick*="removeSchedule"]');
        if (schedules.length === 1) {
            btnRemove.disabled = true;
        } else {
            btnRemove.disabled = false;
        }
    });
}

// Initialize button states on load
document.addEventListener('DOMContentLoaded', function() {
    updateRemoveButtons();
});

// Real-time validation
document.getElementById('componentForm')?.addEventListener('submit', function(e) {
    const schedules = document.querySelectorAll('.schedule-item');
    if (schedules.length === 0) {
        e.preventDefault();
        alert('You must add at least one schedule');
        return false;
    }
});
</script>
@endsection