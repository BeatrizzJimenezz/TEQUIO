@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Create New Event</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Route updated to events.store --}}
                    <form action="{{ route('events.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Event Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Start Date *</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ old('start_date') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">End Date *</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ old('end_date') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" 
                                       value="{{ old('start_time') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="modality" class="form-label">Modality *</label>
                                <select class="form-select" id="modality" name="modality" required>
                                    <option value="">Select...</option>
                                    {{-- Values updated to English Enums --}}
                                    <option value="virtual" {{ old('modality') === 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality') === 'in_person' ? 'selected' : '' }}>In-Person</option>
                                    <option value="hybrid" {{ old('modality') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="visibility" class="form-label">Visibility *</label>
                                <select class="form-select" id="visibility" name="visibility" required>
                                    <option value="">Select...</option>
                                    <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select...</option>
                                    <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="finished" {{ old('status') === 'finished' ? 'selected' : '' }}>Finished</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="{{ old('location') }}" placeholder="Address or link for virtual events">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="4" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cover_image" class="form-label">Cover Image (URL)</label>
                                <input type="url" class="form-control" id="cover_image" name="cover_image" 
                                       value="{{ old('cover_image') }}" placeholder="https://example.com/image.jpg">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label">Event Logo (URL)</label>
                                <input type="url" class="form-control" id="logo" name="logo" 
                                       value="{{ old('logo') }}" placeholder="https://example.com/logo.jpg">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags/Categories</label>
                            <div class="row">
                                {{-- Variable updated to $tags --}}
                                @foreach($tags as $tag)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            {{-- Input name updated to tags[] --}}
                                            <input class="form-check-input" type="checkbox" 
                                                   name="tags[]" value="{{ $tag->id }}" 
                                                   id="tag_{{ $tag->id }}"
                                                   {{-- Check if in old input array --}}
                                                   {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tag_{{ $tag->id }}">
                                                {{ $tag->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('events.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection