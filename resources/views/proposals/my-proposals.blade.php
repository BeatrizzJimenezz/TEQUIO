@extends('layouts.app')

@section('header', 'Mis Propuestas')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-brand-deep mb-1">
                <i class="bi bi-send-fill me-2"></i>Mis Propuestas
            </h2>
            <p class="text-muted mb-0">Historial de propuestas que has enviado a diferentes eventos.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('proposals.index') }}" class="btn btn-brand-accent shadow-sm">
                <i class="bi bi-plus-lg me-2"></i>Nueva Propuesta
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="card-admin mb-4">
        <div class="card-body p-3">
            <div class="d-flex gap-2 flex-wrap" role="group">
                <button type="button" class="btn btn-white active px-3" onclick="filterBy('all', this)">
                    Todas
                </button>
                <button type="button" class="btn btn-white text-warning px-3" onclick="filterBy('proposed', this)">
                    <i class="bi bi-clock-fill me-1"></i>Pendientes
                </button>
                <button type="button" class="btn btn-white text-success px-3" onclick="filterBy('approved', this)">
                    <i class="bi bi-check-circle-fill me-1"></i>Aprobadas
                </button>
                <button type="button" class="btn btn-white text-danger px-3" onclick="filterBy('rejected', this)">
                    <i class="bi bi-x-circle-fill me-1"></i>Rechazadas
                </button>
            </div>
        </div>
    </div>

    {{-- Lista de Propuestas --}}
    <div class="row g-4">
        @forelse($proposals as $proposal)
            <div class="col-12 proposal-card" data-status="{{ $proposal->proposal_status }}">
                <div class="card-admin h-100">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-3">
                                    <h5 class="mb-0 fw-bold text-brand-deep me-3">{{ $proposal->name }}</h5>
                                    @if($proposal->proposal_status == 'proposed')
                                        <span class="badge bg-warning text-dark rounded-pill px-3">
                                            <i class="bi bi-clock-fill me-1"></i>En revisión
                                        </span>
                                    @elseif($proposal->proposal_status == 'approved')
                                        <span class="badge bg-success rounded-pill px-3">
                                            <i class="bi bi-check-circle-fill me-1"></i>Aprobada
                                        </span>
                                    @elseif($proposal->proposal_status == 'rejected')
                                        <span class="badge bg-danger rounded-pill px-3">
                                            <i class="bi bi-x-circle-fill me-1"></i>Rechazada
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="bi bi-calendar-event-fill text-brand-main me-2"></i>
                                        Evento: <span class="fw-bold text-dark">{{ $proposal->event->name }}</span>
                                    </h6>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @php
                                        $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
                                        $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                        $levelLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
                                    @endphp
                                    
                                    <span class="badge bg-brand-deep text-white px-3 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-tag-fill me-1"></i>{{ $typeLabels[$proposal->type] ?? ucfirst($proposal->type) }}
                                    </span>
                                    
                                    <span class="badge bg-brand-main text-white px-3 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-laptop me-1"></i>{{ $modalityLabels[$proposal->modality] ?? ucfirst($proposal->modality) }}
                                    </span>

                                    @if($proposal->level)
                                        <span class="badge bg-secondary text-white px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-bar-chart-fill me-1"></i>{{ $levelLabels[$proposal->level] ?? ucfirst($proposal->level) }}
                                        </span>
                                    @endif

                                    @if($proposal->capacity)
                                        <span class="badge bg-info text-white px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-people-fill me-1"></i>{{ $proposal->capacity }} cupos
                                        </span>
                                    @endif
                                </div>

                                <div class="bg-light p-3 rounded-3 mb-3">
                                    <p class="text-secondary mb-0 small">{{ Str::limit($proposal->description, 200) }}</p>
                                </div>
                            </div>

                            <div class="col-lg-4 border-start-lg ps-lg-4 mt-4 mt-lg-0">
                                @if($proposal->schedules->count() > 0)
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-brand-deep mb-3">
                                            <i class="bi bi-clock-fill me-2"></i>Horarios Propuestos
                                        </h6>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($proposal->schedules as $schedule)
                                                <li class="mb-2 d-flex align-items-center text-muted small">
                                                    <i class="bi bi-calendar-check me-2 text-brand-main"></i>
                                                    <span>
                                                        {{ $schedule->date->format('d/m/Y') }} <br>
                                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="text-end mt-auto">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-send-fill me-1"></i>
                                        Enviada el {{ $proposal->created_at->format('d/m/Y') }}
                                    </small>
                                    <small class="text-muted">
                                        a las {{ $proposal->created_at->format('H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-admin">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-inbox-fill text-muted opacity-25 display-1"></i>
                        </div>
                        <h4 class="fw-bold text-brand-deep">No has enviado propuestas</h4>
                        <p class="text-muted mb-4">Envía tu primera propuesta a un evento público.</p>
                        <a href="{{ route('proposals.index') }}" class="btn btn-brand-accent text-white fw-bold px-4 py-2 shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i>Enviar Propuesta
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterBy(status, btn) {
    // Actualizar botones activos
    document.querySelectorAll('.d-flex button').forEach(b => {
        b.classList.remove('active');
    });
    btn.classList.add('active');

    // Filtrar tarjetas
    const cards = document.querySelectorAll('.proposal-card');
    cards.forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endpush
