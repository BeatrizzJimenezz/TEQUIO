@extends('layouts.app')

@section('header', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-people-fill me-2"></i>Gestión de Usuarios
            </h2>
            <p class="text-muted">Administra los usuarios del sistema y sus roles.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.users.create') }}" class="btn" style="background-color: #8CC63F; color: white;">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Usuario
            </a>
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

    {{-- Filtros --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Nombre o correo...">
                </div>
                <div class="col-md-4">
                    <label for="role" class="form-label">Filtrar por rol</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn" style="background-color: #4499BB; color: white;">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de usuarios --}}
    <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background-color: #0C2340;">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>Usuarios
                <span class="badge bg-light text-dark ms-2">{{ $users->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No se encontraron usuarios.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Registro</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            @if($user->profile_photo)
                                                <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                                     class="rounded-circle me-2" width="32" height="32"
                                                     style="object-fit: cover;">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=32&background=0C2340&color=fff"
                                                     class="rounded-circle me-2" width="32" height="32">
                                            @endif
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->id === auth()->id())
                                                    <span class="badge bg-info ms-1">Tú</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        {{ $user->email }}
                                    </td>
                                    <td class="align-middle">
                                        @forelse($user->roles as $role)
                                            <span class="badge
                                                @if($role->name === 'Administrador') bg-danger
                                                @elseif($role->name === 'Organizador') bg-primary
                                                @else bg-secondary
                                                @endif">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Sin rol
                                            </span>
                                        @endforelse
                                        @if($user->professionalProfile && $user->professionalProfile->about_me && $user->professionalProfile->skills)
                                            <span class="badge" style="background-color: #8CC63F;">
                                                <i class="bi bi-mic-fill me-1"></i>Ponente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted">
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td class="text-end align-middle">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                           class="btn btn-sm btn-outline-info me-1" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $user->id }}"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Modal Eliminar --}}
                                @if($user->id !== auth()->id())
                                    <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-exclamation-triangle me-2"></i>Eliminar Usuario
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de eliminar al usuario <strong>{{ $user->name }}</strong>?</p>
                                                    <p class="text-danger small mb-0">
                                                        <i class="bi bi-exclamation-circle me-1"></i>
                                                        Esta acción eliminará todos sus datos asociados y no se puede deshacer.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
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
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($users->hasPages())
                    <div class="card-footer bg-white">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
