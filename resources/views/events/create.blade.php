@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/events-edit-create.css') }}">

@endpush

@section('content')
<div class="container py-4">
    
    <form action="{{ route('events.store') }}" method="POST">
        @csrf

        <div class="card shadow mb-4" style="background-color: #0c2340;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    
                    <div>
                        <div class="mb-2">
                            <a href="{{ route('events.index') }}" class="text-decoration-none d-inline-flex align-items-center" style="color: #ffffffff; font-size: 0.75rem;">
                                <i class="bi bi-arrow-left me-1"></i> Regresar
                            </a>
                        </div>
                        
                        <h2 class="fw-bold mb-1 text-white">
                            Crear nuevo evento
                        </h2>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('events.index') }}" class="btn btn-outline-evai px-4" style="border-color: white; color: white;">
                            Cancelar
                        </a>
                        <style>.btn-outline-evai:hover { background-color: white; color: #0C2340 !important; }</style>

                        <button type="submit" class="btn btn-evai-green px-4">
                            <i class="bi bi-check-lg me-1"></i> Crear Evento
                        </button>
                    </div>

                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4 border-start border-4 border-danger">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Hay errores en el formulario:</strong>
                        <ul class="mb-0 ps-3 mt-1 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            
            <div class="col-lg-8">
                
                <div class="edit-card border-top border-4" style="border-top-color: #8CC63F !important;">
                    <div class="edit-card-header">
                        <i class="bi bi-pencil-square me-2 fs-5" style="color: #4499BB;"></i>
                        <h5 class="mb-0 fw-bold text-muted small uppercase" style="color: #0C2340;">DETALLES DEL EVENTO</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="name" class="form-label">Nombre del Evento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg text-muted" id="name" name="name" 
                                   value="{{ old('name') }}" required placeholder="Ej. Congreso de Innovación 2025"
                                   style="color: #0C2340;">
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-0">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="8" required
                                      placeholder="Describe de qué trata el evento, sus objetivos y público meta...">{{ old('description') }}</textarea>
                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Una descripción detallada ayuda a los participantes a entender el valor de tu evento.
                            </div>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="edit-card">
                    <div class="edit-card-header">
                        <i class="bi bi-images me-2 fs-5" style="color: #8CC63F;"></i>
                        <h5 class="mb-0 fw-bold text-muted small uppercase" style="color: #0C2340;">MULTIMEDIA</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label for="cover_image" class="form-label">Imagen de Portada (URL)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-image"></i></span>
                                    <input type="url" class="form-control border-start-0 ps-0" id="cover_image" name="cover_image" 
                                           value="{{ old('cover_image') }}" placeholder="https://ejemplo.com/portada.jpg">
                                </div>
                                @error('cover_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="logo" class="form-label">Logo del Evento (URL) <span class="text-muted fw-normal ms-1">(Opcional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-box"></i></span>
                                    <input type="url" class="form-control border-start-0 ps-0" id="logo" name="logo" 
                                           value="{{ old('logo') }}" placeholder="https://ejemplo.com/logo.png">
                                </div>
                                @error('logo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="edit-card">
                    <div class="edit-card-header">
                        <i class="bi bi-tags-fill me-2 fs-5" style="color: #4499BB;"></i>
                       <h5 class="mb-0 fw-bold text-muted small uppercase" style="color: #0C2340;">CATEGORIAS</h5>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label mb-3">Selecciona las etiquetas relacionadas:</label>
                        <div class="d-flex flex-wrap">
                            @foreach($tags as $tag)
                                <input type="checkbox" class="btn-check tag-check-input" 
                                       name="tags[]" id="tag_{{ $tag->id }}" value="{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                <label class="tag-check-label shadow-sm" for="tag_{{ $tag->id }}">
                                    {{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    
                    <div class="edit-card border-top border-4" style="border-top-color: #8CC63F !important;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">Publicación</h6>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Estado inicial <span class="text-danger">*</span></label>
                                <select class="form-select fw-medium" id="status" name="status" required>
                                    <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>En planificación</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Activo</option>
                                    <option value="finished" {{ old('status') === 'finished' ? 'selected' : '' }}>Finalizado</option>
                                </select>
                                @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibilidad <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="visibility" id="vis_public" value="public" {{ old('visibility', 'public') === 'public' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="vis_public">Público</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="visibility" id="vis_private" value="private" {{ old('visibility') === 'private' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="vis_private">Privado</label>
                                    </div>
                                </div>
                                @error('visibility') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="edit-card">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">Logística</h6>

                            <div class="mb-3">
                                <label for="modality" class="form-label">Modalidad <span class="text-danger">*</span></label>
                                <select class="form-select" id="modality" name="modality" required>
                                    <option value="" disabled {{ old('modality') ? '' : 'selected' }}>Seleccionar...</option>
                                    <option value="virtual" {{ old('modality') === 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality') === 'in_person' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hybrid" {{ old('modality') === 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                                @error('modality') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Ubicación / Enlace</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="location" name="location" 
                                           value="{{ old('location') }}" placeholder="Dirección o link">
                                </div>
                                @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label for="start_date" class="form-label small">Fecha Inicio <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ old('start_date') }}" required>
                                </div>
                                <div class="col-6">
                                    <label for="end_date" class="form-label small">Fecha Fin <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ old('end_date') }}" required>
                                </div>
                            </div>
                            @error('start_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @error('end_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                            <div class="mb-0">
                                <label for="start_time" class="form-label small">Hora de Inicio <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="start_time" name="start_time"
                                       value="{{ old('start_time') }}" required>
                                @error('start_time') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>
@endsection