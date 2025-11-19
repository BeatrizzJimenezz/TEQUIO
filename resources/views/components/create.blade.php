@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $evento->name }}</h5> {{-- Asumo que $evento->nombre ahora es ->name --}}
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar"></i> {{ $evento->start_date->format('m/d/Y') }} - {{ $evento->end_date->format('m/d/Y') }}
                            </p>
                        </div>
                        {{-- Nota: Si cambiaste la ruta a inglés, actualiza 'componentes.index' por 'components.index' --}}
                        <a href="{{ route('componentes.index', $evento) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add New Component</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Nota: Verifica si la ruta cambió a 'components.store' --}}
                    <form action="{{ route('componentes.store', $evento) }}" method="POST" id="componentForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Basic Information</h5>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Component Name *</label>
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
                                    {{-- Values updated to lowercase/English conventions --}}
                                    <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Activity</option>
                                    <option value="presentation" {{ old('type') == 'presentation' ? 'selected' : '' }}>Presentation</option>
                                    <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cover_url" class="form-label">Cover Image URL</label>
                                <input type="url" class="form-control @error('cover_url') is-invalid @enderror" 
                                       id="cover_url" name="cover_url" value="{{ old('cover_url') }}"
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
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                       id="slots" name="slots" value="{{ old('slots') }}" min="1"
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
                                           value="{{ old('attendee_price', 0) }}" min="0" step="0.01">
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
                                           value="{{ old('organizer_cost') }}" min="0" step="0.01">
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
                                          placeholder="Ex: Laptop, basic programming knowledge, etc.">{{ old('participant_requirements') }}</textarea>
                                @error('participant_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="instructor_requirements" class="form-label">Instructor Requirements</label>
                                <textarea class="form-control @error('instructor_requirements') is-invalid @enderror" 
                                          id="instructor_requirements" name="instructor_requirements" rows="3"
                                          placeholder="Ex: Projector, microphone, internet connection, etc.">{{ old('instructor_requirements') }}</textarea>
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
                                    <div class="schedule-item card mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-end">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Date *</label>
                                                    {{-- Attention: updated name to schedules[0][date] --}}
                                                    <input type="date" class="form-control @error('schedules.0.date') is-invalid @enderror" 
                                                           name="schedules[0][date]" 
                                                           value="{{ old('schedules.0.date') }}"
                                                           {{-- Asumo que las fechas del objeto $evento también se llaman start_date y end_date ahora --}}
                                                           min="{{ $evento->start_date ?? $evento->fecha_inicio->format('Y-m-d') }}"
                                                           max="{{ $evento->end_date ?? $evento->fecha_fin->format('Y-m-d') }}"
                                                           required>
                                                    @error('schedules.0.date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">Start Time *</label>
                                                    <input type="time" class="form-control @error('schedules.0.start_time') is-invalid @enderror" 
                                                           name="schedules[0][start_time]"
                                                           value="{{ old('schedules.0.start_time') }}"
                                                           required>
                                                    @error('schedules.0.start_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">End Time *</label>
                                                    <input type="time" class="form-control @error('schedules.0.end_time') is-invalid @enderror" 
                                                           name="schedules[0][end_time]"
                                                           value="{{ old('schedules.0.end_time') }}"
                                                           required>
                                                    @error('schedules.0.end_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeSchedule(this)" disabled>
                                                        <i class="bi bi-trash"></i> Remove
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

                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('componentes.index', $evento) }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Save Component
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
    // Asegúrate de que las propiedades de fecha en $evento coincidan (start_date vs fecha_inicio)
    const minDate = '{{ $evento->start_date ?? $evento->fecha_inicio->format("Y-m-d") }}';
    const maxDate = '{{ $evento->end_date ?? $evento->fecha_fin->format("Y-m-d") }}';
    
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
        // Disable remove button if it's the only item remaining
        if (schedules.length === 1) {
            btnRemove.disabled = true;
        } else {
            btnRemove.disabled = false;
        }
    });
}

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