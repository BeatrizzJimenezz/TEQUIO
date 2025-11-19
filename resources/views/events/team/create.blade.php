@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">My Events</a></li>
                    {{-- Route updated to components.index --}}
                    <li class="breadcrumb-item"><a href="{{ route('components.index', $event) }}">{{ $event->name }}</a></li>
                    {{-- Route updated to events.team.index --}}
                    <li class="breadcrumb-item"><a href="{{ route('events.team.index', $event) }}">Team</a></li>
                    <li class="breadcrumb-item active">Add Organizer</li>
                </ol>
            </nav>

            <div class="card shadow-sm">
                <div class="card-header" style="background-color: #0C2340; color: white;">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus-fill"></i> Add Organizer to Team
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <p class="text-muted mb-4">
                        <strong>Event:</strong> {{ $event->name }}
                    </p>

                    {{-- Route updated to events.team.store --}}
                    <form action="{{ route('events.team.store', $event) }}" method="POST" id="addOrganizerForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Select an option:</label>
                            <div class="btn-group w-100" role="group">
                                {{-- name="type", values in English --}}
                                <input type="radio" class="btn-check" name="type" id="typeExisting" value="existing" checked>
                                <label class="btn btn-outline-primary" for="typeExisting">
                                    <i class="bi bi-person-check"></i> Existing User
                                </label>

                                <input type="radio" class="btn-check" name="type" id="typeNew" value="new">
                                <label class="btn btn-outline-success" for="typeNew">
                                    <i class="bi bi-person-plus"></i> Create New User
                                </label>
                            </div>
                        </div>

                        <div id="existingUserForm">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">
                                    <i class="bi bi-search"></i> Search User
                                </label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" style="width: 100%;">
                                    <option value="">-- Search by name or email --</option>
                                    {{-- Variable updated to availableUsers --}}
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if($availableUsers->count() == 0)
                                <div class="alert alert-info mt-2">
                                    <i class="bi bi-info-circle"></i> No users available. Create a new user.
                                </div>
                                @endif
                            </div>
                        </div>

                        <div id="newUserForm" style="display: none;">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill"></i> A new account with <strong>Organizer</strong> role will be created.
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ex: John Doe">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimum 8 characters">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">The new user must change this password upon first login.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('events.team.index', $event) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Add to Team
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    .btn-primary {
        background-color: #4499BB;
        border-color: #4499BB;
    }
    .btn-primary:hover {
        background-color: #357A99;
        border-color: #357A99;
    }
    .btn-outline-primary {
        color: #4499BB;
        border-color: #4499BB;
    }
    .btn-outline-primary:hover,
    .btn-check:checked + .btn-outline-primary {
        background-color: #4499BB;
        border-color: #4499BB;
        color: white;
    }
    
    /* Customize Select2 */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .select2-container--bootstrap-5 .select2-selection--single {
        padding: 0.375rem 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeExisting = document.getElementById('typeExisting');
    const typeNew = document.getElementById('typeNew');
    const existingUserForm = document.getElementById('existingUserForm');
    const newUserForm = document.getElementById('newUserForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    // Initialize Select2
    $('#user_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Search by name or email --',
        allowClear: true,
        language: {
            noResults: function() {
                return "No results found";
            },
            searching: function() {
                return "Searching...";
            }
        }
    });

    // Toggle Forms
    typeExisting.addEventListener('change', function() {
        if (this.checked) {
            existingUserForm.style.display = 'block';
            newUserForm.style.display = 'none';
            // Clear new user fields
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
        }
    });

    typeNew.addEventListener('change', function() {
        if (this.checked) {
            existingUserForm.style.display = 'none';
            newUserForm.style.display = 'block';
            // Clear existing user selection
            $('#user_id').val(null).trigger('change');
        }
    });

    // Toggle Password Visibility
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    }

    // Check errors on load to show correct form
    @if($errors->has('name') || $errors->has('email') || $errors->has('password'))
        typeNew.checked = true;
        existingUserForm.style.display = 'none';
        newUserForm.style.display = 'block';
    @endif

    @if($errors->has('user_id'))
        typeExisting.checked = true;
        existingUserForm.style.display = 'block';
        newUserForm.style.display = 'none';
    @endif
});
</script>
@endpush
@endsection