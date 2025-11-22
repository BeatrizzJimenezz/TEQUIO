@extends('layouts.app')

@section('header', 'Mis Solicitudes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-clock-history me-2"></i>Mis Solicitudes de Rol
            </h2>
        </div>
        <div class="col-auto">
            @if(!auth()->user()->hasRole('Organizador'))
                @php
                    $hasPending = $requests->where('status', 'pending')->count() > 0;
                @endphp
                @if(!$hasPending)
                    <a href="{{ route('role-requests.create') }}" class="btn" style="background-color: #8CC63F; color: white;">
                        <i class="bi bi-plus-lg me-2"></i>Nueva Solicitud
                    </a>
                @endif
            @endif
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($requests->isEmpty())
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No has enviado ninguna solicitud.</p>
                @if(!auth()->user()->hasRole('Organizador'))
                    <a href="{{ route('role-requests.create') }}" class="btn" style="background-color: #4499BB; color: white;">
                        <i class="bi bi-plus-lg me-2"></i>Solicitar ser Organizador
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="row">
            @foreach($requests as $request)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header d-flex justify-content-between align-items-center
                            @if($request->status === 'pending') bg-warning text-dark
                            @elseif($request->status === 'approved') bg-success text-white
                            @else bg-danger text-white
                            @endif">
                            <span>
                                @if($request->status === 'pending')
                                    <i class="bi bi-hourglass-split me-2"></i>Pendiente
                                @elseif($request->status === 'approved')
                                    <i class="bi bi-check-circle-fill me-2"></i>Aprobada
                                @else
                                    <i class="bi bi-x-circle-fill me-2"></i>Rechazada
                                @endif
                            </span>
                            <small>{{ $request->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-person-badge me-2"></i>Solicitud para: {{ $request->requested_role }}
                            </h6>

                            <div class="mb-3">
                                <strong class="text-muted small">Mi motivo:</strong>
                                <p class="mb-0 small">{{ $request->reason }}</p>
                            </div>

                            @if($request->admin_notes)
                                <div class="alert {{ $request->status === 'approved' ? 'alert-success' : 'alert-danger' }} py-2 mb-0">
                                    <strong class="small">Respuesta del administrador:</strong>
                                    <p class="mb-0 small">{{ $request->admin_notes }}</p>
                                </div>
                            @endif

                            @if($request->reviewed_at)
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Revisada: {{ $request->reviewed_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
