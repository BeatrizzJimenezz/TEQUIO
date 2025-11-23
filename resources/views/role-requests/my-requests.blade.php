@extends('layouts.app')

@section('header', 'Mis Solicitudes')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-brand-deep">
                <i class="bi bi-clock-history me-2 text-brand-accent"></i>Mis Solicitudes de Rol
            </h2>
        </div>
        <div class="col-auto">
            @if(!auth()->user()->hasRole('Organizador'))
                @php
                    $hasPending = $requests->where('status', 'pending')->count() > 0;
                @endphp
                @if(!$hasPending)
                    <a href="{{ route('role-requests.create') }}" class="btn btn-brand-primary shadow-sm">
                        <i class="bi bi-plus-lg me-2"></i>Nueva Solicitud
                    </a>
                @endif
            @endif
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($requests->isEmpty())
        <div class="card-admin">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                <p class="text-muted mt-3 mb-4">No has enviado ninguna solicitud.</p>
                @if(!auth()->user()->hasRole('Organizador'))
                    <a href="{{ route('role-requests.create') }}" class="btn btn-brand-primary shadow-sm">
                        <i class="bi bi-plus-lg me-2"></i>Solicitar ser Organizador
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($requests as $request)
                <div class="col-md-6">
                    <div class="card-admin h-100">
                        <div class="card-header-admin d-flex justify-content-between align-items-center">
                            <span class="badge rounded-pill px-3 py-2
                                @if($request->status === 'pending') bg-warning text-dark
                                @elseif($request->status === 'approved') bg-success
                                @else bg-danger
                                @endif">
                                @if($request->status === 'pending')
                                    <i class="bi bi-hourglass-split me-1"></i>Pendiente
                                @elseif($request->status === 'approved')
                                    <i class="bi bi-check-circle-fill me-1"></i>Aprobada
                                @else
                                    <i class="bi bi-x-circle-fill me-1"></i>Rechazada
                                @endif
                            </span>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>{{ $request->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 text-brand-deep">
                                <i class="bi bi-person-badge me-2 text-brand-main"></i>Solicitud para: {{ $request->requested_role }}
                            </h6>

                            <div class="mb-3">
                                <strong class="text-muted small text-uppercase">Mi motivo:</strong>
                                <p class="mb-0 mt-1 text-dark bg-light p-3 rounded border-0">{{ $request->reason }}</p>
                            </div>

                            @if($request->admin_notes)
                                <div class="alert {{ $request->status === 'approved' ? 'alert-success' : 'alert-danger' }} border-0 mb-0">
                                    <strong class="small d-block mb-1">
                                        <i class="bi bi-chat-quote-fill me-1"></i>Respuesta del administrador:
                                    </strong>
                                    <p class="mb-0 small">{{ $request->admin_notes }}</p>
                                </div>
                            @endif

                            @if($request->reviewed_at)
                                <div class="text-end mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-check-all me-1"></i>
                                        Revisada: {{ $request->reviewed_at->format('d/m/Y H:i') }}
                                    </small>
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
