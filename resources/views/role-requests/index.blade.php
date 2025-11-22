@extends('layouts.app')

@section('header', 'Gestión de Solicitudes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-person-check-fill me-2"></i>Solicitudes de Organizador
            </h2>
            <p class="text-muted">Revisa y procesa las solicitudes de usuarios que desean ser organizadores.</p>
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

    {{-- Solicitudes Pendientes --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header text-white" style="background-color: #0C2340;">
            <h5 class="mb-0">
                <i class="bi bi-hourglass-split me-2"></i>Solicitudes Pendientes
                <span class="badge bg-warning text-dark ms-2">{{ $pendingRequests->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($pendingRequests->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-check-all text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No hay solicitudes pendientes.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Fecha</th>
                                <th>Motivo</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRequests as $request)
                                <tr>
                                    <td class="align-middle">
                                        <strong>{{ $request->user->name }}</strong>
                                    </td>
                                    <td class="align-middle">
                                        {{ $request->user->email }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $request->created_at->format('d/m/Y') }}
                                        <br>
                                        <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="align-middle" style="max-width: 300px;">
                                        <small>{{ Str::limit($request->reason, 100) }}</small>
                                        @if(strlen($request->reason) > 100)
                                            <button type="button" class="btn btn-link btn-sm p-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reasonModal{{ $request->id }}">
                                                Ver más
                                            </button>
                                        @endif
                                    </td>
                                    <td class="text-end align-middle">
                                        <button type="button" class="btn btn-sm btn-success me-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#approveModal{{ $request->id }}"
                                                title="Aprobar">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectModal{{ $request->id }}"
                                                title="Rechazar">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Ver Motivo Completo --}}
                                <div class="modal fade" id="reasonModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #4499BB; color: white;">
                                                <h5 class="modal-title">Motivo de {{ $request->user->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ $request->reason }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Aprobar --}}
                                <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-check-circle me-2"></i>Aprobar Solicitud
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('role-requests.approve', $request) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <p>¿Aprobar la solicitud de <strong>{{ $request->user->name }}</strong>?</p>
                                                    <p class="text-muted small">El usuario recibirá el rol de Organizador y será notificado por correo.</p>

                                                    <div class="mb-3">
                                                        <label for="approveNotes{{ $request->id }}" class="form-label">
                                                            Nota para el usuario (opcional)
                                                        </label>
                                                        <textarea class="form-control"
                                                                  id="approveNotes{{ $request->id }}"
                                                                  name="admin_notes"
                                                                  rows="2"
                                                                  placeholder="Ej: Bienvenido al equipo de organizadores..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-check-lg me-2"></i>Aprobar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Rechazar --}}
                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-x-circle me-2"></i>Rechazar Solicitud
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('role-requests.reject', $request) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <p>¿Rechazar la solicitud de <strong>{{ $request->user->name }}</strong>?</p>

                                                    <div class="mb-3">
                                                        <label for="rejectNotes{{ $request->id }}" class="form-label">
                                                            Motivo del rechazo <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control"
                                                                  id="rejectNotes{{ $request->id }}"
                                                                  name="admin_notes"
                                                                  rows="3"
                                                                  placeholder="Explica al usuario por qué se rechaza su solicitud..."
                                                                  required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-x-lg me-2"></i>Rechazar
                                                    </button>
                                                </div>
                                            </form>
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

    {{-- Historial de Solicitudes Procesadas --}}
    @if($processedRequests->isNotEmpty())
        <div class="card shadow-sm border-0">
            <div class="card-header" style="background-color: #4499BB; color: white;">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Historial Reciente
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Estado</th>
                                <th>Revisado por</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($processedRequests as $request)
                                <tr>
                                    <td>{{ $request->user->name }}</td>
                                    <td>
                                        @if($request->status === 'approved')
                                            <span class="badge bg-success">Aprobada</span>
                                        @else
                                            <span class="badge bg-danger">Rechazada</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->reviewer->name ?? 'N/A' }}</td>
                                    <td>{{ $request->reviewed_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
