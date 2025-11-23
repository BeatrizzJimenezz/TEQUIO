@extends('layouts.app')

@section('header', 'Gestión de Solicitudes')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-brand-deep mb-1">
                <i class="bi bi-person-check-fill me-2"></i>Solicitudes de Organizador
            </h2>
            <p class="text-muted mb-0">Revisa y procesa las solicitudes de usuarios que desean ser organizadores.</p>
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

    {{-- Solicitudes Pendientes --}}
    <div class="card-admin mb-5">
        <div class="card-header-admin d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-hourglass-split me-2"></i>Solicitudes Pendientes
            </h5>
            @if($pendingRequests->isNotEmpty())
                <span class="badge bg-warning text-dark rounded-pill px-3">{{ $pendingRequests->count() }} pendientes</span>
            @endif
        </div>
        <div class="card-body p-0">
            @if($pendingRequests->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-check-circle text-success opacity-25 display-4"></i>
                    </div>
                    <h5 class="text-muted">¡Todo al día!</h5>
                    <p class="text-muted small mb-0">No hay solicitudes pendientes de revisión.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-admin table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Usuario</th>
                                <th>Motivo de Solicitud</th>
                                <th>Fecha</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRequests as $request)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-brand-main text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-brand-deep">{{ $request->user->name }}</div>
                                                <div class="small text-muted">{{ $request->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="max-width: 350px;">
                                        <div class="text-secondary text-truncate" style="max-width: 300px;">
                                            {{ $request->reason }}
                                        </div>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-brand-main text-decoration-none small fw-bold"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reasonModal{{ $request->id }}">
                                            Leer completo
                                        </button>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-medium">{{ $request->created_at->format('d M, Y') }}</div>
                                        <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-success fw-bold me-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#approveModal{{ $request->id }}"
                                                title="Aprobar">
                                            <i class="bi bi-check-lg me-1"></i>Aprobar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger fw-bold"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectModal{{ $request->id }}"
                                                title="Rechazar">
                                            <i class="bi bi-x-lg me-1"></i>Rechazar
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Ver Motivo Completo --}}
                                <div class="modal fade" id="reasonModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-brand-main text-white border-0">
                                                <h5 class="modal-title fw-bold">Motivo de Solicitud</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="rounded-circle bg-light p-2 me-2 text-brand-main">
                                                        <i class="bi bi-quote fs-4"></i>
                                                    </div>
                                                    <h6 class="mb-0 fw-bold text-brand-deep">{{ $request->user->name }} escribe:</h6>
                                                </div>
                                                <p class="text-secondary bg-light p-3 rounded-3 fst-italic mb-0">
                                                    "{{ $request->reason }}"
                                                </p>
                                            </div>
                                            <div class="modal-footer border-0 bg-light">
                                                <button type="button" class="btn btn-evai-outline" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Aprobar --}}
                                <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-success text-white border-0">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-check-circle-fill me-2"></i>Aprobar Solicitud
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('role-requests.approve', $request) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <p class="mb-3">¿Estás seguro de aprobar a <strong>{{ $request->user->name }}</strong>?</p>
                                                    <div class="alert alert-success bg-opacity-10 border-0 d-flex align-items-center">
                                                        <i class="bi bi-info-circle-fill text-success me-2 fs-5"></i>
                                                        <small class="text-success fw-bold">El usuario recibirá el rol de Organizador inmediatamente.</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="approveNotes{{ $request->id }}" class="form-label-admin">
                                                            Nota para el usuario (opcional)
                                                        </label>
                                                        <textarea class="form-control form-control-admin"
                                                                  id="approveNotes{{ $request->id }}"
                                                                  name="admin_notes"
                                                                  rows="2"
                                                                  placeholder="Ej: ¡Bienvenido al equipo!"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 bg-light">
                                                    <button type="button" class="btn btn-light border fw-bold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success fw-bold px-4">
                                                        <i class="bi bi-check-lg me-2"></i>Confirmar Aprobación
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Rechazar --}}
                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white border-0">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-x-circle-fill me-2"></i>Rechazar Solicitud
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('role-requests.reject', $request) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <p class="mb-3">Estás a punto de rechazar la solicitud de <strong>{{ $request->user->name }}</strong>.</p>
                                                    
                                                    <div class="mb-3">
                                                        <label for="rejectNotes{{ $request->id }}" class="form-label-admin">
                                                            Motivo del rechazo <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control form-control-admin"
                                                                  id="rejectNotes{{ $request->id }}"
                                                                  name="admin_notes"
                                                                  rows="3"
                                                                  placeholder="Explica brevemente por qué no se puede aprobar en este momento..."
                                                                  required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 bg-light">
                                                    <button type="button" class="btn btn-light border fw-bold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger fw-bold px-4">
                                                        <i class="bi bi-x-lg me-2"></i>Confirmar Rechazo
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
        <div class="card-admin">
            <div class="card-header-admin accent">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Historial Reciente
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-admin table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Usuario</th>
                                <th>Estado</th>
                                <th>Revisado por</th>
                                <th>Fecha de Revisión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($processedRequests as $request)
                                <tr>
                                    <td class="ps-4 fw-medium text-brand-deep">{{ $request->user->name }}</td>
                                    <td>
                                        @if($request->status === 'approved')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                                                <i class="bi bi-check-circle-fill me-1"></i>Aprobada
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill">
                                                <i class="bi bi-x-circle-fill me-1"></i>Rechazada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-secondary">{{ $request->reviewer->name ?? 'Sistema' }}</td>
                                    <td class="text-muted small">{{ $request->reviewed_at?->format('d/m/Y H:i') }}</td>
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
