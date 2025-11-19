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
                        {{-- Route updated to offers.index --}}
                        <a href="{{ route('offers.index', $event) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Publish Offer Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Publish Open Offer</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Open offers are components that you are looking for other users (speakers/workshop leaders) to teach.
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Route updated to offers.store --}}
                    <form action="{{ route('offers.store', $event) }}" method="POST" id="offerForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Offer Information</h5>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Title of Requested Activity *</label>
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
                                <label for="description" class="form-label">Description of Request *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="5" required 
                                          placeholder="Describe what kind of presentation/workshop you are looking for, topics of interest, etc.">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Expected Level</label>
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
                                <label for="slots" class="form-label">Number of Slots</label>
                                <input type="number" class="form-control @error('slots') is-invalid @enderror" 
                                       id="slots" name="slots" value="{{ old('slots') }}" min="1"
                                       placeholder="Ex: 30">
                                @error('slots')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Modality and Location -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Modality and Location</h5>
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
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location') }}"
                                       placeholder="Ex: Main Auditorium">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Compensation and Requirements -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Compensation and Requirements</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="organizer_cost" class="form-label">Speaker/Instructor Compensation</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    {{-- Name updated to organizer_cost --}}
                                    <input type="number" class="form-control @error('organizer_cost') is-invalid @enderror" 
                                           id="organizer_cost" name="organizer_cost" 
                                           value="{{ old('organizer_cost') }}" min="0" step="0.01">
                                </div>
                                <small class="text-muted">Leave at $0 if there is no financial compensation</small>
                                @error('organizer_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="instructor_requirements" class="form-label">Application Requirements</label>
                                <textarea class="form-control @error('instructor_requirements') is-invalid @enderror" 
                                          id="instructor_requirements" name="instructor_requirements" rows="3"
                                          placeholder="Ex: Experience in the topic, certifications, etc.">{{ old('instructor_requirements') }}</textarea>
                                @error('instructor_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Schedules -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Available Schedules *</h5>
                            </div>

                            <div class="col-12">
                                <div id="schedulesContainer">
                                    <div class="schedule-item card mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-end">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Date *</label>
                                                    {{-- Name updated to schedules[0][date] --}}
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
                                    <a href="{{ route('offers.index', $event) }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-megaphone"></i> Publish Offer
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