@extends('layouts.app')

@section('header', 'Gestión de Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users-index.css') }}">
@endpush

@section('content')
<div class="container py-4">

    <div class="hero-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-4">
            <div class="d-none d-md-block p-3 rounded-circle" style="background: rgba(255,255,255,0.1);">
                <i class="bi bi-people-fill fs-2"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0">Usuarios</h2>
                <p class="mb-0 opacity-75">Administra roles, accesos y permisos del sistema.</p>
            </div>
        </div>
        
        <div class="d-flex gap-4 border-start border-white border-opacity-25 ps-4">
            <div class="stat-item text-center text-md-start">
                <span class="stat-label">Total Usuarios</span>
                <span class="stat-value">{{ $users->total() }}</span>
            </div>
        </div>
        <i class="bi bi-grid-3x3-gap-fill hero-pattern text-white"></i>
    </div>

    <div class="main-content-card">
        
        <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-center">
                
                <div class="col-md-5 col-lg-4">
                    <div class="input-group filter-input-group">
                        <span class="input-group-text border-end-0 ps-3 rounded-pill-start bg-light">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 rounded-pill-end bg-light" 
                               name="search" value="{{ request('search') }}" 
                               placeholder="Buscar por nombre o email...">
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <select class="form-select filter-input-group bg-light" name="role" onchange="this.form.submit()">
                        <option value="">Todos los roles</option>
                        @php
                            // Definimos los roles permitidos en el filtro
                            $allowedRoles = ['Administrador', 'Organizador', 'Participante'];
                        @endphp
                        @foreach($roles as $role)
                            @if(in_array($role->name, $allowedRoles))
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-5 d-flex justify-content-end gap-2 ms-auto">
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-pill px-3" title="Limpiar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                    
                    <a href="{{ route('admin.users.create') }}" class="btn-create-floating text-decoration-none">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Usuario
                    </a>
                </div>
            </div>
        </form>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tabla de Usuarios --}}
        <div class="table-responsive">
            <table class="modern-table align-middle">
                <thead>
                    <tr>
                        <th>USUARIO</th>
                        <th>ROL / PERFIL</th>
                        <th>FECHA REGISTRO</th>
                        <th class="text-end">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="modern-row">
                            {{-- Columna Usuario --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/' . $user->profile_photo) }}" class="avatar-circle">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=45&background=0C2340&color=fff" class="avatar-circle">
                                        @endif
                                        
                                        @if($user->id === auth()->id())
                                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                                                <span class="visually-hidden">Tú</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ms-3">
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Columna Roles --}}
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        @php
                                            $badgeClass = match($role->name) {
                                                'Administrador' => 'role-admin',
                                                'Organizador'   => 'role-organizer',
                                                'Participante'  => 'role-participant',
                                                default         => 'role-participant'
                                            };
                                        @endphp
                                        <span class="role-badge {{ $badgeClass }}">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted small fst-italic">Sin rol asignado</span>
                                    @endforelse
                                </div>
                            </td>
                            {{-- Columna Fecha --}}
                            <td class="text-secondary small fw-bold">
                                {{ $user->created_at->format('d M, Y') }}
                            </td>

                            {{-- Columna Acciones --}}
                            <td class="text-end">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn-action-icon view" title="Ver Detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-action-icon edit" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <button type="button" class="btn-action-icon delete" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $user->id }}" 
                                            title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="opacity-50 mb-3">
                                    <i class="bi bi-people display-4"></i>
                                </div>
                                <h5 class="fw-bold text-muted">No se encontraron usuarios</h5>
                                <p class="text-muted small">Intenta ajustar los filtros de búsqueda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-4 pt-3 border-top">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@foreach($users as $user)
    @if($user->id !== auth()->id())
        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3 text-danger">
                            <i class="bi bi-person-x-fill display-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">¿Eliminar usuario?</h5>
                        <p class="text-muted small mb-4">
                            Se eliminará a <strong>{{ $user->name }}</strong> y todos sus datos. Esta acción es irreversible.
                        </p>
                        
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">
                                    Sí, eliminar usuario
                                </button>
                            </form>
                            <button type="button" class="btn btn-light w-100 rounded-pill text-muted" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection