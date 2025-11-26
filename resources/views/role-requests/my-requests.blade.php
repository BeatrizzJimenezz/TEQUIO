@extends('layouts.app')

@section('header', 'Mis Solicitudes')

@push('styles')
    <link href="{{ asset('css/role-requests.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="page-header">
        <h2 class="page-title">
            <i class="bi bi-clock-history"></i>
            Mis solicitudes de Rol
        </h2>
        @if(!auth()->user()->hasRole('Organizador'))
            @php
                $hasPending = $requests->where('status', 'pending')->count() > 0;
            @endphp
            @if(!$hasPending)
                <a href="{{ route('role-requests.create') }}" class="btn-new-request">
                    <i class="bi bi-plus-lg"></i>
                    Nueva Solicitud
                </a>
            @endif
        @endif
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-custom alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill alert-icon"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-custom alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill alert-icon"></i>
            <span>{{ session('info') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-custom alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Content --}}
    @if($requests->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h5 class="empty-state-title">No tienes solicitudes</h5>
            <p class="empty-state-text">No has enviado ninguna solicitud de rol todavía.</p>
            @if(!auth()->user()->hasRole('Organizador'))
                <a href="{{ route('role-requests.create') }}" class="btn-new-request">
                    <i class="bi bi-plus-lg"></i>
                    Solicitar ser Organizador
                </a>
            @endif
        </div>
    @else
        <div class="row g-4">
            @foreach($requests as $request)
                <div class="col-md-6 col-12">
                    <div class="request-card">
                        <div class="request-card-header">
                            <span class="status-badge {{ $request->status }}">
                                @if($request->status === 'pending')
                                    <i class="bi bi-hourglass-split"></i>Pendiente
                                @elseif($request->status === 'approved')
                                    <i class="bi bi-check-circle-fill"></i>Aprobada
                                @else
                                    <i class="bi bi-x-circle-fill"></i>Rechazada
                                @endif
                            </span>
                            <span class="request-date">
                                <i class="bi bi-calendar-event"></i>
                                {{ $request->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="request-card-body">
                            <h6 class="request-role">
                                <i class="bi bi-person-badge"></i>
                                Solicitud para: {{ $request->requested_role }}
                            </h6>

                            <div class="mb-3">
                                <div class="request-section-label">Mi motivo:</div>
                                <div class="request-reason">{{ $request->reason }}</div>
                            </div>

                            @if($request->admin_notes)
                                <div class="admin-response {{ $request->status === 'approved' ? 'approved' : 'rejected' }}">
                                    <div class="admin-response-label">
                                        <i class="bi bi-chat-quote-fill"></i>
                                        Respuesta del administrador:
                                    </div>
                                    <p class="admin-response-text">{{ $request->admin_notes }}</p>
                                </div>
                            @endif

                            @if($request->reviewed_at)
                                <div class="reviewed-date">
                                    <i class="bi bi-check-all"></i>
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