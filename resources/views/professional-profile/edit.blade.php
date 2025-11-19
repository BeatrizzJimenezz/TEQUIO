@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Edit Professional Profile</h2>
        <a href="{{ route('professional-profile.show') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- PROFILE PHOTO -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3">Profile Photo</h4>
            <div class="d-flex gap-4">
                <!-- Preview -->
                <div id="photo-preview-container">
                    @if(auth()->user()->profile_photo)
                       <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                            class="rounded-circle border border-primary"
                            width="120" height="120" 
                            id="preview-image"
                            style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                             style="width:120px; height:120px;" id="preview-placeholder">
                            <i class="bi bi-person text-white" style="font-size:50px;"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Upload form -->
                <div class="flex-grow-1">
                    <form action="{{ route('professional-profile.photo.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="profile_photo" id="profile_photo"
                                   class="form-control" accept="image/*">
                            @error('profile_photo')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Upload
                        </button>
                    </form>
                    
                    <!-- Delete button (outside upload form) -->
                    @if(auth()->user()->profile_photo)
                        <button type="button"
                                class="btn btn-danger mt-2"
                                id="delete-photo-btn">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PERSONAL INFORMATION -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3">Personal Information</h4>
            <form action="{{ route('professional-profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">About me</label>
                    <textarea name="about_me" class="form-control" rows="4">{{ old('about_me', $profile->about_me) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Current workplace</label>
                    <input type="text" name="current_workplace" class="form-control"
                           value="{{ old('current_workplace', $profile->current_workplace) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Skills (comma separated)</label>
                    <input type="text" name="skills" class="form-control"
                           value="{{ old('skills', $profile->skills) }}">
                </div>
                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- ACADEMIC TRAINING -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h4 class="card-title">Academic Training</h4>
                <button class="btn btn-success" onclick="document.getElementById('training-form').classList.toggle('d-none')">
                    <i class="bi bi-plus-circle"></i> Add
                </button>
            </div>

            <!-- Add form -->
            <div id="training-form" class="d-none mt-3">
                <form action="{{ route('professional-profile.academic-training.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Institution *</label>
                            <input type="text" name="institution" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Degree *</label>
                            <input type="text" name="degree" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3">Save</button>
                </form>
            </div>

            <!-- Training list -->
            <div class="mt-4">
                @forelse($profile->academicTrainings as $training)
                    <div class="border rounded p-3 mb-2 d-flex justify-content-between">
                        <div>
                            <strong>{{ $training->degree }}</strong>
                            <p class="mb-0">{{ $training->institution }}</p>
                        </div>
                        <form action="{{ route('professional-profile.academic-training.destroy', $training->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete training?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">No training registered.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
// Preview image on file select
document.getElementById('profile_photo')?.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const container = document.getElementById('photo-preview-container');
            
            // Replace content with new image
            container.innerHTML = `
                <img src="${e.target.result}"
                     class="rounded-circle border border-primary"
                     width="120" height="120" 
                     id="preview-image"
                     style="object-fit: cover;">
            `;
            
            // Show delete button if it doesn't exist
            if (!document.getElementById('delete-photo-btn')) {
                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'btn btn-danger mt-2';
                deleteBtn.id = 'delete-photo-btn';
                deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Delete';
                document.querySelector('.flex-grow-1').appendChild(deleteBtn);
                attachDeleteHandler();
            }
        };
        
        reader.readAsDataURL(this.files[0]);
    }
});

// Delete photo with AJAX
function attachDeleteHandler() {
    const deleteBtn = document.getElementById('delete-photo-btn');
    if (deleteBtn && !deleteBtn.hasAttribute('data-handler-attached')) {
        deleteBtn.setAttribute('data-handler-attached', 'true');
        deleteBtn.addEventListener('click', handleDelete);
    }
}

function handleDelete(e) {
    e.preventDefault();
    
    if (!confirm('¿Eliminar foto de perfil?')) {
        return;
    }
    
    const btn = this;
    const originalHTML = btn.innerHTML;
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Eliminando...';
    
    // Make AJAX request
    fetch('{{ route("professional-profile.photo.delete") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update preview to placeholder
            document.getElementById('photo-preview-container').innerHTML = `
                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                     style="width:120px; height:120px;" id="preview-placeholder">
                    <i class="bi bi-person text-white" style="font-size:50px;"></i>
                </div>
            `;
            
            // Remove delete button
            btn.remove();
            
            // Clear file input
            document.getElementById('profile_photo').value = '';
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                ${data.message || 'Foto eliminada correctamente'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
            
            // Auto dismiss after 4 seconds
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 4000);
        } else {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            alert(data.message || 'Error al eliminar la foto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        alert('Error al eliminar la foto. Por favor intenta de nuevo.');
    });

    
}

// Función para actualizar el avatar del sidebar
function updateSidebarAvatar(imageUrl) {
    const sidebarAvatar = document.getElementById('sidebar-avatar');
    if (sidebarAvatar) {
        sidebarAvatar.src = imageUrl;
    }
}

// Modifica la función handleDelete para actualizar el sidebar
function handleDelete(e) {
    e.preventDefault();
    
    if (!confirm('¿Eliminar foto de perfil?')) {
        return;
    }
    
    const btn = this;
    const originalHTML = btn.innerHTML;
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Eliminando...';
    
    // Make AJAX request
    fetch('{{ route("professional-profile.photo.delete") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update preview to placeholder
            document.getElementById('photo-preview-container').innerHTML = `
                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                     style="width:120px; height:120px;" id="preview-placeholder">
                    <i class="bi bi-person text-white" style="font-size:50px;"></i>
                </div>
            `;
            
            // Update sidebar avatar
            const defaultAvatar = 'https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0d6efd&color=fff';
            updateSidebarAvatar(defaultAvatar);
            
            // Remove delete button
            btn.remove();
            
            // Clear file input
            document.getElementById('profile_photo').value = '';
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                ${data.message || 'Foto eliminada correctamente'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
            
            // Auto dismiss after 4 seconds
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 4000);
        } else {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            alert(data.message || 'Error al eliminar la foto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        alert('Error al eliminar la foto. Por favor intenta de nuevo.');
    });
}

// Attach handler on page load
attachDeleteHandler();
</script>

@endsection