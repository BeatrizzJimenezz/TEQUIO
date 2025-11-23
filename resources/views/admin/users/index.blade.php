@extends('layouts.app')

@section('header', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-brand-deep mb-1">
                <i class="bi bi-people-fill me-2"></i>Gestión de Usuarios
            </h2>
            <p class="text-muted mb-0">Administra los usuarios del sistema, sus roles y permisos.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-evai shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Usuario
        </a>
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

    {{-- Filtros --}}
    <div class="card-admin mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label-admin">Buscar Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control form-control-admin border-start-0 ps-0" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Nombre o correo electrónico...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="role" class="form-label-admin">Filtrar por Rol</label>
                    <select class="form-select form-select-admin form-control-admin" id="role" name="role">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-evai-outline w-100">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light border w-auto" title="Limpiar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de usuarios --}}
    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin align-middle">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Fecha Registro</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                             class="avatar-circle me-3" width="40" height="40">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=40&background=0C2340&color=fff"
                                             class="avatar-circle me-3" width="40" height="40">
                                    @endif
                                    <div>
                                        <div class="fw-bold text-brand-deep">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill" style="font-size: 0.65rem;">Tú</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-secondary">{{ $user->email }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        @php
                                            $badgeClass = match($role->name) {
                                                'Administrador' => 'admin',
                                                'Organizador' => 'organizer',
                                                default => 'user'
                                            };
                                        @endphp
                                        <span class="badge-role {{ $badgeClass }}">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="badge-role user text-muted">Sin rol</span>
                                    @endforelse
                                    
                                    @if($user->professionalProfile && $user->professionalProfile->about_me)
                                        <span class="badge-role speaker">
                                            <i class="bi bi-mic-fill me-1"></i>Ponente
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-secondary">
                                <i class="bi bi-calendar3 me-1"></i>{{ $user->created_at->format('d M, Y') }}
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="btn btn-sm btn-light text-brand-main" title="Ver detalles">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="btn btn-sm btn-light text-brand-main" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-sm btn-light text-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $user->id }}"
                                                title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Eliminar --}}
                        @if($user->id !== auth()->id())
                            <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-danger text-white border-0">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Usuario
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 text-center">
                                            <div class="mb-3">
                                                <div class="avatar-circle d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-person-x-fill display-4"></i>
                                                </div>
                                            </div>
                                            <h5 class="mb-3">¿Estás seguro?</h5>
                                            <p class="text-muted mb-0">
                                                Estás a punto de eliminar al usuario <strong>{{ $user->name }}</strong>.
                                                Esta acción eliminará todos sus datos asociados y <span class="text-danger fw-bold">no se puede deshacer</span>.
                                            </p>
                                        </div>
                                        <div class="modal-footer border-0 bg-light justify-content-center">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger px-4 fw-bold">
                                                    Sí, Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-search display-4 mb-3 d-block opacity-25"></i>
                                    <h5>No se encontraron usuarios</h5>
                                    <p class="mb-0">Intenta ajustar los filtros de búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
