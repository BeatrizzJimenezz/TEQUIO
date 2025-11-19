@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Editar Evento</h4>
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

                    <form action="{{ route('eventos.update', $evento) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre" class="form-label">Nombre del Evento *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="{{ old('nombre', $evento->nombre) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                       value="{{ old('fecha_inicio', $evento->fecha_inicio->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                       value="{{ old('fecha_fin', $evento->fecha_fin->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="hora_inicio" class="form-label">Hora de Inicio *</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" 
                                       value="{{ old('hora_inicio', $evento->hora_inicio) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="modalidad" class="form-label">Modalidad *</label>
                                <select class="form-select" id="modalidad" name="modalidad" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="virtual" {{ old('modalidad', $evento->modalidad) === 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="presencial" {{ old('modalidad', $evento->modalidad) === 'presencial' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hibrido" {{ old('modalidad', $evento->modalidad) === 'hibrido' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="visibilidad" class="form-label">Visibilidad *</label>
                                <select class="form-select" id="visibilidad" name="visibilidad" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="publico" {{ old('visibilidad', $evento->visibilidad) === 'publico' ? 'selected' : '' }}>Público</option>
                                    <option value="privado" {{ old('visibilidad', $evento->visibilidad) === 'privado' ? 'selected' : '' }}>Privado</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado *</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="planificacion" {{ old('estado', $evento->estado) === 'planificacion' ? 'selected' : '' }}>Planificación</option>
                                    <option value="activo" {{ old('estado', $evento->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="finalizado" {{ old('estado', $evento->estado) === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="ubicacion" name="ubicacion" 
                                   value="{{ old('ubicacion', $evento->ubicacion) }}" placeholder="Dirección o enlace para eventos virtuales">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción *</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="4" required>{{ old('descripcion', $evento->descripcion) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="imagen_portada" class="form-label">Imagen de Portada (URL)</label>
                                <input type="url" class="form-control" id="imagen_portada" name="imagen_portada" 
                                       value="{{ old('imagen_portada', $evento->imagen_portada) }}" placeholder="https://ejemplo.com/imagen.jpg">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label">Logo del Evento (URL)</label>
                                <input type="url" class="form-control" id="logo" name="logo" 
                                       value="{{ old('logo', $evento->logo) }}" placeholder="https://ejemplo.com/logo.jpg">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Etiquetas/Categorías</label>
                            <div class="row">
                                @foreach($etiquetas as $etiqueta)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="etiquetas[]" value="{{ $etiqueta->id }}" 
                                                   id="etiqueta_{{ $etiqueta->id }}"
                                                   {{ in_array($etiqueta->id, old('etiquetas', $etiquetasSeleccionadas)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="etiqueta_{{ $etiqueta->id }}">
                                                {{ $etiqueta->nombre }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('eventos.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Actualizar Evento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection