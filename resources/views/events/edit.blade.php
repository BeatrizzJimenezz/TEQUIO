@extends('layouts.app')

@section('header', 'Editar Evento')

@push('styles')
<style>
    :root {
        --brand-deep: #0C2340;
        --brand-main: #4499BB;
        --brand-accent: #8CC63F;
            --evai-blue-main: #4499BB;
    --evai-blue-deep: #0C2340;
    --evai-green-accent: #8CC63F;
    --evai-gray-light: #C8CCC9;
    --evai-white: #FFFFFF;
    }

    /* Estilo de Tarjetas */
    .edit-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        background-color: #fff;
        margin-bottom: 1.5rem;
        transition: transform 0.2s;
    }
    .edit-card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem;
        display: flex;
        align-items: center;
    }
    
    /* Inputs Personalizados */
    .form-label {
        font-weight: 600;
        color: var(--brand-deep);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    .form-control, .form-select {
        border: 1px solid #dee2e6;
        padding: 0.6rem 1rem;
        border-radius: 0.5rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--brand-main);
        box-shadow: 0 0 0 0.25rem rgba(68, 153, 187, 0.15);
    }

    /* Checkboxes de Etiquetas como Botones */
    .tag-check-input:checked + .tag-check-label {
        background-color: var(--brand-deep);
        color: white;
        border-color: var(--brand-deep);
    }
    .tag-check-label {
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e9ecef;
        padding: 0.375rem 0.75rem;
        border-radius: 50rem;
        font-size: 0.85rem;
        color: #6c757d;
        display: inline-block;
        margin-bottom: 0.5rem;
        margin-right: 0.25rem;
        background-color: #f8f9fa;
    }
    .tag-check-label:hover {
        background-color: #e2e6ea;
        color: var(--brand-deep);
    }

    /* Barra Lateral Fija */
    .sticky-sidebar {
        position: sticky;
        top: 1.5rem;
        z-index: 10;
    }

    /* Botón principal verde (sólido) */
        .btn-evai-green {
            background-color: var(--evai-green-accent);
            border-color: var(--evai-green-accent);
            color: white;
            font-weight: bold;
            border-radius: 50rem;
        }

        .btn-evai-green:hover {
            background-color: #72a933;
            border-color: #72a933;
            color: white;
        }

        /* Botón secundario azul (borde) */
        .btn-outline-evai {
            border-color: var(--evai-blue-main);
            color: var(--evai-blue-main);
            border-radius: 50rem;
            font-weight: bold;
        }

        .btn-outline-evai:hover {
            background-color: var(--evai-blue-main);
            color: var(--evai-white);
        }
</style>
@endpush

@section('content')
<div class="container py-4">
    
    <form action="{{ route('events.update', $event) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- HEADER DE ACCIONES -->
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
                            Editar Evento
                        </h2>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('events.index') }}" class="btn btn-outline-evai px-4">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-evai-green">
                            <i class="bi bi-save me-1"></i> Guardar Cambios
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
            
            <!-- COLUMNA IZQUIERDA (Contenido Principal) -->
            <div class="col-lg-8">
                
                <!-- 1. Información General -->
                <div class="edit-card border-top border-4" style="border-top-color: #8CC63F !important;">
                    <div class="edit-card-header">
                        <i class="bi bi-pencil-square me-2 fs-5" style="color: #4499BB;"></i>
                        <h5 class="mb-0 fw-bold text-muted small uppercase" style="color: #0C2340;">DETALLES DEL EVENTO</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="name" class="form-label">Nombre del Evento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg fw-bold" id="name" name="name" 
                                   value="{{ old('name', $event->name) }}" required placeholder="Ej. Congreso de Innovación 2025"
                                   style="color: #0C2340;">
                        </div>

                        <div class="mb-0">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="8" required
                                      placeholder="Describe de qué trata el evento, sus objetivos y público meta...">{{ old('description', $event->description) }}</textarea>
                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Una descripción detallada ayuda a los participantes a entender el valor de tu evento.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Multimedia -->
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
                                           value="{{ old('cover_image', $event->cover_image) }}" placeholder="https://ejemplo.com/portada.jpg">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="logo" class="form-label">Logo del Evento (URL) <span class="text-muted fw-normal ms-1">(Opcional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-box"></i></span>
                                    <input type="url" class="form-control border-start-0 ps-0" id="logo" name="logo" 
                                           value="{{ old('logo', $event->logo) }}" placeholder="https://ejemplo.com/logo.png">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Etiquetas -->
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
                                       {{ in_array($tag->id, old('tags', $selectedTags)) ? 'checked' : '' }}>
                                <label class="tag-check-label shadow-sm" for="tag_{{ $tag->id }}">
                                    {{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- COLUMNA DERECHA (Configuración y Logística) -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    
                    <!-- 1. Estado y Publicación -->
                    <div class="edit-card border-top border-4" style="border-top-color: #8CC63F !important;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">Publicación</h6>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Estado Actual</label>
                                <select class="form-select fw-medium" id="status" name="status">
                                    <option value="planning" {{ old('status', $event->status) === 'planning' ? 'selected' : '' }}>En Planificación</option>
                                    <option value="active" {{ old('status', $event->status) === 'active' ? 'selected' : '' }}>Activo</option>
                                    <option value="finished" {{ old('status', $event->status) === 'finished' ? 'selected' : '' }}>Finalizado</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibilidad</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="visibility" id="vis_public" value="public" {{ old('visibility', $event->visibility) === 'public' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="vis_public">Público</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="visibility" id="vis_private" value="private" {{ old('visibility', $event->visibility) === 'private' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="vis_private">Privado</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Logística (Fecha y Lugar) -->
                    <div class="edit-card">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">Logística</h6>

                            <div class="mb-3">
                                <label for="modality" class="form-label">Modalidad</label>
                                <select class="form-select" id="modality" name="modality">
                                    <option value="virtual" {{ old('modality', $event->modality) === 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality', $event->modality) === 'in_person' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hybrid" {{ old('modality', $event->modality) === 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Ubicación / Enlace</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="location" name="location" 
                                           value="{{ old('location', $event->location) }}" placeholder="Dirección o link">
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label for="start_date" class="form-label small">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}">
                                </div>
                                <div class="col-6">
                                    <label for="end_date" class="form-label small">Fecha Fin</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <div class="mb-0">
                                <label for="start_time" class="form-label small">Hora de Inicio</label>
                                <input type="time" class="form-control" id="start_time" name="start_time"
                                       value="{{ old('start_time', $event->start_time) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Info Meta (Solo lectura) -->
                    <div class="text-center mt-3">
                        <p class="text-muted small mb-0">
                            Creado el {{ $event->created_at->format('d/m/Y') }}
                        </p>
                        <p class="text-muted small">
                            Última edición: {{ $event->updated_at->diffForHumans() }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>
@endsection