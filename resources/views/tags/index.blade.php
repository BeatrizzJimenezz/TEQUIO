@extends('layouts.app')

@section('header', 'Gestión de Etiquetas')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-brand-deep mb-1">
                <i class="bi bi-tags-fill me-2"></i>Gestión de Etiquetas
            </h2>
            <p class="text-muted mb-0">Administra las etiquetas disponibles para clasificar eventos.</p>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Formulario para crear nueva etiqueta --}}
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Nueva Etiqueta
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('tags.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="form-label-admin">Nombre de la etiqueta</label>
                            <input type="text"
                                   class="form-control form-control-admin @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Ej: Tecnología, Cultura..."
                                   required>
                            <div class="form-text text-muted small mt-2">
                                <i class="bi bi-info-circle me-1"></i>El nombre debe ser único.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-evai w-100">
                            <i class="bi bi-plus-lg me-2"></i>Crear Etiqueta
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Lista de etiquetas existentes --}}
        <div class="col-lg-8">
            <div class="card-admin h-100">
                <div class="card-header-admin accent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-list-ul me-2"></i>Etiquetas Existentes
                    </h5>
                    <span class="badge bg-white text-brand-main rounded-pill px-3">{{ $tags->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($tags->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-tags text-muted opacity-25 display-4"></i>
                            </div>
                            <h5 class="text-muted">Sin etiquetas</h5>
                            <p class="text-muted small mb-0">Usa el formulario de la izquierda para crear la primera.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-admin table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Nombre</th>
                                        <th class="text-center">Eventos Asociados</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tags as $tag)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge-role user fw-bold text-dark" style="font-size: 0.9rem;">
                                                    {{ $tag->name }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-secondary border">
                                                    {{ $tag->events_count ?? $tag->events()->count() }} eventos
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-sm btn-light text-brand-main"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editModal{{ $tag->id }}"
                                                            title="Editar">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-light text-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal{{ $tag->id }}"
                                                            title="Eliminar">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- Modal Editar --}}
                                        <div class="modal fade" id="editModal{{ $tag->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-brand-deep text-white border-0">
                                                        <h5 class="modal-title fw-bold">
                                                            <i class="bi bi-pencil-square me-2"></i>Editar Etiqueta
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('tags.update', $tag) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label for="editName{{ $tag->id }}" class="form-label-admin">
                                                                    Nombre de la etiqueta
                                                                </label>
                                                                <input type="text"
                                                                       class="form-control form-control-admin"
                                                                       id="editName{{ $tag->id }}"
                                                                       name="name"
                                                                       value="{{ $tag->name }}"
                                                                       required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 bg-light">
                                                            <button type="button" class="btn btn-light border fw-bold text-secondary" data-bs-dismiss="modal">
                                                                Cancelar
                                                            </button>
                                                            <button type="submit" class="btn btn-evai fw-bold">
                                                                Guardar Cambios
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Modal Eliminar --}}
                                        <div class="modal fade" id="deleteModal{{ $tag->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white border-0">
                                                        <h5 class="modal-title fw-bold">
                                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Etiqueta
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-center">
                                                        <div class="mb-3">
                                                            <div class="avatar-circle d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width: 60px; height: 60px;">
                                                                <i class="bi bi-trash display-6"></i>
                                                            </div>
                                                        </div>
                                                        <h5 class="mb-2">¿Confirmar eliminación?</h5>
                                                        <p class="text-muted mb-0">
                                                            Estás a punto de eliminar la etiqueta <strong>"{{ $tag->name }}"</strong>.
                                                        </p>
                                                        <p class="text-danger small mt-2 fw-bold">
                                                            <i class="bi bi-info-circle me-1"></i>Esta acción no se puede deshacer.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer border-0 bg-light justify-content-center">
                                                        <button type="button" class="btn btn-light border fw-bold text-secondary px-4" data-bs-dismiss="modal">
                                                            Cancelar
                                                        </button>
                                                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger fw-bold px-4">
                                                                Sí, Eliminar
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
