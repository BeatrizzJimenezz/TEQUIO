@extends('layouts.app')

@section('header', 'Gestión de Solicitudes')

@push('styles')
    <link href="{{ asset('css/role-requests-admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="page-header-admin">
        <h2 class="page-title">
            <i class="bi bi-person-check-fill"></i>
            Solicitudes de Organizador
        </h2>
        <p class="page-subtitle">Revisa y procesa las solicitudes de usuarios que desean ser organizadores.</p>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-admin alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill alert-icon"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-admin alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Solicitudes Pendientes --}}
    <div class="card-admin-panel">
        <div class="card-admin-header pending">
            <h5>
                <i class="bi bi-hourglass-split"></i>
                Solicitudes Pendientes
            </h5>
            @if($pendingRequests->isNotEmpty())
                <span class="badge-count">{{ $pendingRequests->count() }} pendientes</span>
            @endif
        </div>

        @if($pendingRequests->isEmpty())
            <div class="empty-state-admin">
                <div class="empty-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h5>¡Todo al día!</h5>
                <p>No hay solicitudes pendientes de revisión.</p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="table-responsive desktop-table">
                <table class="table table-admin-panel mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Motivo de Solicitud</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name">{{ $request->user->name }}</div>
                                            <div class="user-email">{{ $request->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="reason-cell">
                                        <div class="reason-text">{{ $request->reason }}</div>
                                        <button type="button" class="btn-read-more"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reasonModal{{ $request->id }}">
                                            Leer completo
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-cell">
                                        <div class="date-main">{{ $request->created_at->format('d M, Y') }}</div>
                                        <div class="date-relative">{{ $request->created_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn-approve"
                                                data-bs-toggle="modal"
                                                data-bs-target="#approveModal{{ $request->id }}">
                                            <i class="bi bi-check-lg"></i>Aprobar
                                        </button>
                                        <button type="button" class="btn-reject"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectModal{{ $request->id }}">
                                            <i class="bi bi-x-lg"></i>Rechazar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="mobile-request-cards">
                @foreach($pendingRequests as $request)
                    <div class="mobile-request-card">
                        <div class="mobile-header">
                            <div class="mobile-user">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                </div>
                                <div class="user-info">
                                    <div class="user-name">{{ $request->user->name }}</div>
                                    <div class="user-email">{{ $request->user->email }}</div>
                                </div>
                            </div>
                            <div class="mobile-date">
                                <div>{{ $request->created_at->format('d/m/Y') }}</div>
                                <div>{{ $request->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="mobile-reason-label">Motivo:</div>
                        <div class="mobile-reason">{{ $request->reason }}</div>
                        <div class="mobile-actions">
                            <button type="button" class="btn-approve"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveModal{{ $request->id }}">
                                <i class="bi bi-check-lg"></i>Aprobar
                            </button>
                            <button type="button" class="btn-reject"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal{{ $request->id }}">
                                <i class="bi bi-x-lg"></i>Rechazar
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Historial de Solicitudes Procesadas --}}
    @if($processedRequests->isNotEmpty())
        <div class="card-admin-panel">
            <div class="card-admin-header history">
                <h5>
                    <i class="bi bi-clock-history"></i>
                    Historial Reciente
                </h5>
            </div>

            {{-- Desktop Table --}}
            <div class="table-responsive desktop-table">
                <table class="table table-admin-panel mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Revisado por</th>
                            <th>Fecha de Revisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processedRequests as $request)
                            <tr>
                                <td>
                                    <span class="user-name">{{ $request->user->name }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn-view-reason"
                                            data-bs-toggle="modal"
                                            data-bs-target="#historyReasonModal{{ $request->id }}">
                                        <i class="bi bi-eye"></i> Ver motivo
                                    </button>
                                </td>
                                <td>
                                    @if($request->status === 'approved')
                                        <span class="status-badge-admin approved">
                                            <i class="bi bi-check-circle-fill"></i>Aprobada
                                        </span>
                                    @else
                                        <span class="status-badge-admin rejected">
                                            <i class="bi bi-x-circle-fill"></i>Rechazada
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $request->reviewer->name ?? 'Sistema' }}</td>
                                <td class="text-muted">{{ $request->reviewed_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile History --}}
            <div class="mobile-request-cards">
                @foreach($processedRequests as $request)
                    <div class="mobile-history-card">
                        <div>
                            <div class="history-user">{{ $request->user->name }}</div>
                            <div class="history-reviewer">Por: {{ $request->reviewer->name ?? 'Sistema' }}</div>
                            <div class="history-date">{{ $request->reviewed_at?->format('d/m/Y H:i') }}</div>
                            <button type="button" class="btn-view-reason mt-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#historyReasonModal{{ $request->id }}">
                                <i class="bi bi-eye"></i> Ver motivo
                            </button>
                        </div>
                        <div>
                            @if($request->status === 'approved')
                                <span class="status-badge-admin approved">
                                    <i class="bi bi-check-circle-fill"></i>Aprobada
                                </span>
                            @else
                                <span class="status-badge-admin rejected">
                                    <i class="bi bi-x-circle-fill"></i>Rechazada
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Modales --}}
    @foreach($pendingRequests as $request)
        {{-- Modal Ver Motivo Completo --}}
        <div class="modal fade modal-admin" id="reasonModal{{ $request->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header info">
                        <h5 class="modal-title">
                            <i class="bi bi-chat-quote-fill"></i>
                            Motivo de Solicitud
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                {{ strtoupper(substr($request->user->name, 0, 1)) }}
                            </div>
                            <strong>{{ $request->user->name }} escribe:</strong>
                        </div>
                        <div class="quote-box">
                            "{{ $request->reason }}"
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Aprobar --}}
        <div class="modal fade modal-admin" id="approveModal{{ $request->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header approve">
                        <h5 class="modal-title">
                            <i class="bi bi-check-circle-fill"></i>
                            Aprobar Solicitud
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('role-requests.approve', $request) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <p class="mb-3">¿Estás seguro de aprobar a <strong>{{ $request->user->name }}</strong>?</p>
                            
                            <div class="info-box">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>El usuario recibirá el rol de Organizador inmediatamente.</span>
                            </div>

                            <div class="mb-0">
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
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-confirm-approve">
                                <i class="bi bi-check-lg"></i>Confirmar Aprobación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Rechazar --}}
        <div class="modal fade modal-admin" id="rejectModal{{ $request->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header reject">
                        <h5 class="modal-title">
                            <i class="bi bi-x-circle-fill"></i>
                            Rechazar Solicitud
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('role-requests.reject', $request) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <p class="mb-3">Estás a punto de rechazar la solicitud de <strong>{{ $request->user->name }}</strong>.</p>
                            
                            <div class="mb-0">
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
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-confirm-reject">
                                <i class="bi bi-x-lg"></i>Confirmar Rechazo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modales para ver motivo del historial --}}
    @foreach($processedRequests as $request)
        <div class="modal fade modal-admin" id="historyReasonModal{{ $request->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header info">
                        <h5 class="modal-title">
                            <i class="bi bi-chat-quote-fill"></i>
                            Motivo de Solicitud
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                {{ strtoupper(substr($request->user->name, 0, 1)) }}
                            </div>
                            <strong>{{ $request->user->name }} escribió:</strong>
                        </div>
                        <div class="quote-box">
                            "{{ $request->reason }}"
                        </div>

                        @if($request->admin_notes)
                            <div class="admin-notes-box mt-3">
                                <div class="admin-notes-label">
                                    <i class="bi bi-reply-fill"></i>
                                    Respuesta del administrador:
                                </div>
                                <div class="admin-notes-text">{{ $request->admin_notes }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection