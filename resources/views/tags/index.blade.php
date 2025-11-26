@extends('layouts.app')

@section('header', 'Gestión de Etiquetas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tags.css') }}">
    {{-- Estilos Inline de respaldo --}}
    <style>
        .hero-header { background: #0C2340; color: white; border-radius: 1rem; padding: 2rem; position: relative; margin-bottom: -2rem; }
        .modern-row { background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .modern-row:hover { transform: translateY(-3px); }
        .tag-pill { background: #eef2f6; padding: 0.4rem 1rem; border-radius: 20px; font-weight: bold; color: #0C2340; }
        .btn-create-floating { background-color: #8CC63F; color: white; border-radius: 50px; padding: 10px 25px; font-weight: bold; border:none; box-shadow: 0 4px 10px rgba(140, 198, 63, 0.3); }
    </style>
@endpush

@section('content')
<div class="container py-4">

    <div class="hero-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-4">
            <div class="d-none d-md-block p-3 rounded-circle" style="background: rgba(255,255,255,0.1);">
                <i class="bi bi-tags-fill fs-2"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0">Etiquetas</h2>
                <p class="mb-0 opacity-75">Organiza tus eventos de forma eficiente</p>
            </div>
        </div>
        
        <div class="d-flex gap-4 border-start border-white border-opacity-25 ps-4">
            <div class="stat-item text-center text-md-start">
                <span class="stat-label">Total</span>
                <span class="stat-value">{{ $tags->count() }}</span>
            </div>
            <div class="stat-item text-center text-md-start d-none d-sm-flex">
                <span class="stat-label">Activas</span>
                <span class="stat-value">
                    {{ $tags->filter(fn($t) => ($t->events_count ?? $t->events()->count()) > 0)->count() }}
                </span>
            </div>
        </div>
        <i class="bi bi-hash hero-pattern text-white"></i>
    </div>

    <div class="main-content-card">
        
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-pill">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="Buscar etiqueta...">
            </div>

            <button type="button" class="btn-create-floating" data-bs-toggle="modal" data-bs-target="#createTagModal">
                <i class="bi bi-plus-lg"></i> Nueva Etiqueta
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($tags->isEmpty())
            <div class="text-center py-5">
                <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                    <i class="bi bi-inbox text-muted display-6"></i>
                </div>
                <h5 class="text-muted fw-bold">No hay etiquetas aún</h5>
                <p class="text-muted small">Comienza creando tu primera categoría para los eventos.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="modern-table" id="tagsTable">
                    <thead>
                        <tr>
                            <th>NOMBRE DE ETIQUETA</th>
                            <th class="text-center">EVENTOS VINCULADOS</th>
                            <th class="text-end">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                            <tr class="modern-row">
                                {{-- Columna Nombre --}}
                                <td class="name-cell">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="tag-pill shadow-sm">
                                            <i class="bi bi-hash me-1 opacity-50"></i>
                                            {{ $tag->name }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Columna Conteo --}}
                                <td class="text-center">
                                    @php $count = $tag->events_count ?? $tag->events()->count(); @endphp
                                    @if($count > 0)
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                            <i class="bi bi-calendar-event me-1 text-primary opacity-50"></i>
                                            <strong>{{ $count }}</strong> eventos
                                        </span>
                                    @else
                                        <span class="text-muted small fst-italic">Sin uso</span>
                                    @endif
                                </td>

                                {{-- Columna Acciones --}}
                                <td class="text-end">
                                    <button class="btn-action-icon edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $tag->id }}" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn-action-icon delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $tag->id }}" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="createTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white border-0 px-4 pt-4" style="background-color: #0C2340;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2 text-success"></i> Nueva Etiqueta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tags.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-4 bg-white">
                    <div class="mb-3">
                        <label for="newName" class="form-label fw-bold text-muted small">NOMBRE DE LA ETIQUETA</label>
                        <input type="text" class="form-control form-control-lg border-2" 
                               id="newName" name="name" placeholder="Ej. Tecnología, Finanzas..." required
                               style="border-color: #f0f0f0;">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #8CC63F;">
                        <i class="bi bi-check-lg me-1"></i> Crear Etiqueta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($tags as $tag)
    {{-- MODAL EDITAR --}}
    <div class="modal fade" id="editModal{{ $tag->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white border-0 px-4 pt-4" style="background-color: #0C2340;">
                    <h5 class="modal-title fw-bold">Editar Etiqueta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('tags.update', $tag) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body px-4 py-3">
                        <label class="form-label text-muted small fw-bold">NOMBRE</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0 fw-bold text-tequio-deep" name="name" value="{{ $tag->name }}" required>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold" style="background-color: #4499BB; border:none;">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ELIMINAR --}}
    <div class="modal fade" id="deleteModal{{ $tag->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-danger">
                        <i class="bi bi-exclamation-circle display-4"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Eliminar etiqueta?</h5>
                    <p class="text-muted small mb-4">Esta acción no se puede deshacer.</p>
                    
                    <div class="d-grid gap-2">
                        <form action="{{ route('tags.destroy', $tag) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">Sí, eliminar</button>
                        </form>
                        <button type="button" class="btn btn-light w-100 rounded-pill text-muted" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
    // Buscador en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.modern-row');

        searchInput.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();

            rows.forEach(row => {
                const nameText = row.querySelector('.name-cell').textContent.toLowerCase();
                
                if(nameText.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush

@endsection