@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Tag Management</h4>
                </div>
                <div class="card-body">
                    <!-- Feedback Messages -->
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

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Create Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">New Tag</h5>
                            {{-- Route updated to tags.store --}}
                            <form action="{{ route('tags.store') }}" method="POST" class="row g-3">
                                @csrf
                                <div class="col-md-9">
                                    {{-- Input name updated to 'name' --}}
                                    <input type="text" name="name" class="form-control" 
                                           placeholder="Tag name" 
                                           value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-plus-circle"></i> Create
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tags Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Name</th>
                                    <th width="180">Created Date</th>
                                    <th width="180" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Variable updated to $tags --}}
                                @forelse($tags as $tag)
                                    <tr>
                                        <td>{{ $tag->id }}</td>
                                        <td>
                                            {{-- IDs updated to English conventions --}}
                                            <span id="name-{{ $tag->id }}">{{ $tag->name }}</span>
                                            
                                            {{-- Route updated to tags.update --}}
                                            <form id="edit-form-{{ $tag->id }}" 
                                                  action="{{ route('tags.update', $tag) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group">
                                                    <input type="text" name="name" class="form-control" 
                                                           value="{{ $tag->name }}" required>
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm" 
                                                            onclick="cancelEdit({{ $tag->id }})">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td>{{ $tag->created_at->format('m/d/Y') }}</td>
                                        <td class="text-end">
                                            <button onclick="editTag({{ $tag->id }})" 
                                                    class="btn btn-sm btn-warning" id="edit-btn-{{ $tag->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            
                                            {{-- Route updated to tags.destroy --}}
                                            <form action="{{ route('tags.destroy', $tag) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this tag?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No tags registered
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editTag(id) {
    // IDs updated to match HTML changes
    document.getElementById('name-' + id).style.display = 'none';
    document.getElementById('edit-form-' + id).style.display = 'block';
    document.getElementById('edit-btn-' + id).style.display = 'none';
}

function cancelEdit(id) {
    document.getElementById('name-' + id).style.display = 'inline';
    document.getElementById('edit-form-' + id).style.display = 'none';
    document.getElementById('edit-btn-' + id).style.display = 'inline-block';
}
</script>
@endpush
@endsection