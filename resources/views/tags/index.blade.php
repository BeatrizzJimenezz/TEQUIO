@extends('layouts.app')

@section('header', 'Gestión de Etiquetas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-tags-fill me-2"></i>Gestión de Etiquetas
            </h2>
            <p class="text-muted">Administra las etiquetas disponibles para clasificar eventos.</p>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Formulario para crear nueva etiqueta --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background-color: #0C2340;">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Nueva Etiqueta
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tags.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nombre de la etiqueta</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Ej: Tecnología, Cultura, Deportes..."
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn w-100" style="background-color: #8CC63F; color: white;">
                            <i class="bi bi-plus-lg me-2"></i>Crear Etiqueta
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Lista de etiquetas existentes --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background-color: #4499BB;">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>Etiquetas Existentes
                        <span class="badge bg-light text-dark ms-2">{{ $tags->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($tags->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-tags text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No hay etiquetas creadas aún.</p>
                            <p class="text-muted small">Usa el formulario de la izquierda para crear la primera.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Nombre</th>
                                        <th class="text-center">Eventos</th>
                                        <th class="text-end pe-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tags as $tag)
                                        <tr>
                                            <td class="ps-3 align-middle">
                                                <span class="badge rounded-pill px-3 py-2" style="background-color: #4499BB;">
                                                    {{ $tag->name }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-secondary">
                                                    {{ $tag->events_count ?? $tag->events()->count() }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-3 align-middle">
                                                {{-- Botón Editar --}}
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $tag->id }}"
                                                        title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                {{-- Botón Eliminar --}}
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $tag->id }}"
                                                        title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Modal Editar --}}
                                        <div class="modal fade" id="editModal{{ $tag->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #0C2340; color: white;">
                                                        <h5 class="modal-title">
                                                            <i class="bi bi-pencil me-2"></i>Editar Etiqueta
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('tags.update', $tag) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="editName{{ $tag->id }}" class="form-label fw-semibold">
                                                                    Nombre de la etiqueta
                                                                </label>
                                                                <input type="text"
                                                                       class="form-control"
                                                                       id="editName{{ $tag->id }}"
                                                                       name="name"
                                                                       value="{{ $tag->name }}"
                                                                       required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                Cancelar
                                                            </button>
                                                            <button type="submit" class="btn" style="background-color: #8CC63F; color: white;">
                                                                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Modal Eliminar --}}
                                        <div class="modal fade" id="deleteModal{{ $tag->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>¿Estás seguro de que deseas eliminar la etiqueta <strong>"{{ $tag->name }}"</strong>?</p>
                                                        <p class="text-muted small mb-0">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            Esta acción no se puede deshacer. Si la etiqueta está en uso, no podrá ser eliminada.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            Cancelar
                                                        </button>
                                                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="bi bi-trash me-2"></i>Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
