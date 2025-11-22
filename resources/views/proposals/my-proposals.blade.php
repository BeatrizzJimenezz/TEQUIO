@extends('layouts.app')

@section('header', 'Mis Propuestas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-send-fill me-2"></i>Mis Propuestas
            </h2>
            <p class="text-muted">Historial de propuestas que has enviado a diferentes eventos.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('proposals.index') }}" class="btn" style="background-color: #8CC63F; color: white;">
                <i class="bi bi-plus-lg me-2"></i>Nueva Propuesta
            </a>
        </div>
    </div>

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
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary active" onclick="filterBy('all', this)">
                    Todas
                </button>
                <button type="button" class="btn btn-outline-warning" onclick="filterBy('proposed', this)">
                    <i class="bi bi-clock me-1"></i>Pendientes
                </button>
                <button type="button" class="btn btn-outline-success" onclick="filterBy('approved', this)">
                    <i class="bi bi-check-circle me-1"></i>Aprobadas
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="filterBy('rejected', this)">
                    <i class="bi bi-x-circle me-1"></i>Rechazadas
                </button>
            </div>
        </div>
    </div>

    @forelse($proposals as $proposal)
        <div class="card shadow-sm border-0 mb-3 proposal-card" data-status="{{ $proposal->proposal_status }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="mb-0 me-3 fw-bold">{{ $proposal->name }}</h5>
                            @if($proposal->proposal_status == 'proposed')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i>En revisión
                                </span>
                            @elseif($proposal->proposal_status == 'approved')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Aprobada
                                </span>
                            @elseif($proposal->proposal_status == 'rejected')
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>Rechazada
                                </span>
                            @endif
                        </div>

                        <p class="mb-2">
                            <i class="bi bi-calendar-event me-1" style="color: #4499BB;"></i>
                            <strong>Evento:</strong> {{ $proposal->event->name }}
                        </p>

                        <div class="mb-2">
                            @php
                                $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
                                $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                $levelLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
                            @endphp
                            <span class="badge" style="background-color: #0C2340;">
                                {{ $typeLabels[$proposal->type] ?? ucfirst($proposal->type) }}
                            </span>
                            <span class="badge" style="background-color: #4499BB;">
                                {{ $modalityLabels[$proposal->modality] ?? ucfirst($proposal->modality) }}
                            </span>
                            @if($proposal->level)
                                <span class="badge bg-secondary">
                                    {{ $levelLabels[$proposal->level] ?? ucfirst($proposal->level) }}
                                </span>
                            @endif
                            @if($proposal->capacity)
                                <span class="badge bg-info">
                                    <i class="bi bi-people me-1"></i>{{ $proposal->capacity }} cupos
                                </span>
                            @endif
                        </div>

                        <p class="card-text mb-2 text-muted">{{ Str::limit($proposal->description, 200) }}</p>

                        @if($proposal->schedules->count() > 0)
                            <div class="mt-3">
                                <strong class="small" style="color: #0C2340;">
                                    <i class="bi bi-calendar-week me-1"></i>Horarios propuestos:
                                </strong>
                                <ul class="list-unstyled ms-3 mb-0 mt-1">
                                    @foreach($proposal->schedules as $schedule)
                                        <li class="small text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $schedule->date->format('d/m/Y') }} -
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} a
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <p class="text-muted mb-0 mt-3">
                            <small>
                                <i class="bi bi-send me-1"></i>
                                Enviada el {{ $proposal->created_at->format('d/m/Y') }} a las {{ $proposal->created_at->format('H:i') }}
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-2" style="color: #0C2340;">No has enviado propuestas</h5>
                <p class="text-muted mb-3">Envía tu primera propuesta a un evento público.</p>
                <a href="{{ route('proposals.index') }}" class="btn" style="background-color: #8CC63F; color: white;">
                    <i class="bi bi-plus-lg me-2"></i>Enviar Propuesta
                </a>
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
function filterBy(status, btn) {
    // Actualizar botones activos
    document.querySelectorAll('.btn-group button').forEach(b => {
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
