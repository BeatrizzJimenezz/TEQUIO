@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Header --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #8CC63F !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #0C2340;">
                                <i class="bi bi-plus-circle me-2" style="color: #8CC63F;"></i>
                                Crear Nuevo Evento
                            </h3>
                            <p class="text-muted mb-0">Completa la información para crear tu evento</p>
                        </div>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="alert border-0 shadow-sm mb-4" style="background-color: #fff5f5; border-left: 4px solid #dc3545 !important;">
                    <strong class="text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Error de validación:
                    </strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('events.store') }}" method="POST">
                @csrf

                {{-- Información Básica --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-info-circle me-2" style="color: #4499BB;"></i>
                            Información Básica
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold" style="color: #0C2340;">
                                Nombre del Evento <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="Ej: Congreso de Tecnología 2025">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold" style="color: #0C2340;">
                                Descripción <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4" required
                                      placeholder="Describe tu evento, objetivos, público objetivo, etc.">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Fecha y Hora --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-calendar3 me-2" style="color: #8CC63F;"></i>
                            Fecha y Hora
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label fw-semibold" style="color: #0C2340;">
                                    Fecha de Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label fw-semibold" style="color: #0C2340;">
                                    Fecha de Fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="start_time" class="form-label fw-semibold" style="color: #0C2340;">
                                    Hora de Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                       id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Configuración --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-gear me-2" style="color: #4499BB;"></i>
                            Configuración
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="modality" class="form-label fw-semibold" style="color: #0C2340;">
                                    Modalidad <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('modality') is-invalid @enderror"
                                        id="modality" name="modality" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="virtual" {{ old('modality') === 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality') === 'in_person' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hybrid" {{ old('modality') === 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                                @error('modality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="visibility" class="form-label fw-semibold" style="color: #0C2340;">
                                    Visibilidad <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('visibility') is-invalid @enderror"
                                        id="visibility" name="visibility" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Público</option>
                                    <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>Privado</option>
                                </select>
                                @error('visibility')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Los eventos públicos aparecen en el catálogo general
                                </small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label fw-semibold" style="color: #0C2340;">
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>Planificando</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Activo</option>
                                    <option value="finished" {{ old('status') === 'finished' ? 'selected' : '' }}>Finalizado</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label fw-semibold" style="color: #0C2340;">
                                Ubicación
                            </label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                   id="location" name="location" value="{{ old('location') }}"
                                   placeholder="Dirección física o enlace para eventos virtuales">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Imágenes --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-image me-2" style="color: #8CC63F;"></i>
                            Imágenes
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cover_image" class="form-label fw-semibold" style="color: #0C2340;">
                                    Imagen de Portada (URL)
                                </label>
                                <input type="url" class="form-control @error('cover_image') is-invalid @enderror"
                                       id="cover_image" name="cover_image" value="{{ old('cover_image') }}"
                                       placeholder="https://ejemplo.com/imagen.jpg">
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label fw-semibold" style="color: #0C2340;">
                                    Logo del Evento (URL)
                                </label>
                                <input type="url" class="form-control @error('logo') is-invalid @enderror"
                                       id="logo" name="logo" value="{{ old('logo') }}"
                                       placeholder="https://ejemplo.com/logo.jpg">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Etiquetas --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold" style="color: #0C2340;">
                            <i class="bi bi-tags me-2" style="color: #4499BB;"></i>
                            Etiquetas / Categorías
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            @foreach($tags as $tag)
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="tags[]" value="{{ $tag->id }}"
                                               id="tag_{{ $tag->id }}"
                                               {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tag_{{ $tag->id }}">
                                            {{ $tag->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-lg text-white" style="background-color: #8CC63F;">
                        <i class="bi bi-check-lg me-1"></i> Crear Evento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
