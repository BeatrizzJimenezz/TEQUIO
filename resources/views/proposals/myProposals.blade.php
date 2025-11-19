@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/proposals/main.css') }}">
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="glass-card p-4 p-md-5 rounded-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-bold text-dark mb-2">Mis Propuestas</h1>
                        <p class="lead text-muted mb-0">Gestiona las propuestas enviadas a eventos</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('proposals.index') }}" class="btn btn-accent btn-lg px-4 py-3 rounded-pill shadow-lg hover-lift">
                            <i class="bi bi-plus-circle-fill me-2"></i>Nueva Propuesta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div class="row mb-4">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center rounded-4" role="alert">
                    <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                    <div class="flex-grow-1 fw-semibold">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Filtros por Estado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="mb-0 text-center">
                        <i class="bi bi-funnel me-2"></i>Filtrar por Estado
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-2 justify-content-center">
                        <div class="col-auto">
                            <button type="button" class="btn btn-filter btn-primary active rounded-pill" onclick="filterBy('all')">
                                <i class="bi bi-collection me-2"></i>Todas
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-filter btn-warning rounded-pill" onclick="filterBy('proposed')">
                                <i class="bi bi-clock me-2"></i>Pendientes
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-filter btn-success rounded-pill" onclick="filterBy('approved')">
                                <i class="bi bi-check-circle me-2"></i>Aprobadas
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-filter btn-danger rounded-pill" onclick="filterBy('rejected')">
                                <i class="bi bi-x-circle me-2"></i>Rechazadas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista -->
    <div class="row">
        <div class="col-12">
            @forelse($proposals as $proposal)
                <div class="event-card mb-4 proposal-card" data-status="{{ $proposal->proposal_status }}">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-lg">
                        <div class="card-body p-4 p-xl-5">

                            <!-- Header -->
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1 me-3">
                                    <h3 class="h2 fw-bold text-dark mb-2">{{ $proposal->name }}</h3>
                                    <div class="d-flex flex-wrap align-items-center text-muted mb-2">
                                        <div class="d-flex align-items-center me-4 mb-2">
                                            <i class="bi bi-calendar-event me-2 text-primary"></i>
                                            <span class="fw-semibold">{{ $proposal->event->name }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-clock me-2 text-primary"></i>
                                            <span class="fw-semibold">Enviada el {{ $proposal->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-2 mt-lg-0">

                                    @if($proposal->proposal_status == 'proposed')
                                        <span class="badge status-badge bg-warning text-dark">
                                            <i class="bi bi-clock me-1"></i>Pendiente de Revisión
                                        </span>
                                    @elseif($proposal->proposal_status == 'approved')
                                        <span class="badge status-badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Aprobada
                                        </span>
                                    @elseif($proposal->proposal_status == 'rejected')
                                        <span class="badge status-badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Rechazada
                                        </span>
                                    @endif

                                    <span class="badge status-badge bg-primary">
                                        <i class="bi bi-layers me-1"></i>{{ $proposal->type }}
                                    </span>

                                    <span class="badge status-badge bg-teal">
                                        <i class="bi bi-laptop me-1"></i>{{ ucfirst($proposal->modality) }}
                                    </span>

                                    @if($proposal->level)
                                        <span class="badge status-badge bg-brown">
                                            <i class="bi bi-bar-chart me-1"></i>{{ $proposal->level }}
                                        </span>
                                    @endif

                                </div>
                            </div>

                            <!-- Descripción -->
                            <p class="text-muted mb-4 fs-6">{{ $proposal->description }}</p>

                            <!-- Info -->
                            <div class="row mb-4">
                                @if($proposal->capacity)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center text-dark">
                                        <i class="bi bi-people me-3 fs-5 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Cupos Propuestos</small>
                                            <span class="fw-semibold">{{ $proposal->capacity }} participantes</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center text-dark">
                                        <i class="bi bi-calendar-check me-3 fs-5 text-primary"></i>
                                        <div>
                                            <small class="text-muted d-block">Horarios Propuestos</small>
                                            <span class="fw-semibold">{{ $proposal->schedules->count() }} horario(s)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Horarios -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="bi bi-clock me-2 text-primary"></i>Horarios Propuestos
                                </h6>
                                <div class="row g-3">
                                    @foreach($proposal->schedules as $schedule)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="glass-card p-3 rounded-4 text-center">
                                            <div class="fw-bold text-dark mb-1">{{ $schedule->date->format('d/m/Y') }}</div>
                                            <div class="text-muted small">{{ $schedule->start_time }} - {{ $schedule->end_time }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex flex-wrap gap-3 pt-3 border-top">
                                <a href="#" class="btn btn-outline-primary btn-lg px-4 rounded-pill hover-lift">
                                    <i class="bi bi-eye me-2"></i>Ver Detalles
                                </a>
                                
                                @if($proposal->proposal_status == 'proposed')
                                <a href="#" class="btn btn-warning btn-lg px-4 rounded-pill text-white hover-lift">
                                    <i class="bi bi-pencil me-2"></i>Editar Propuesta
                                </a>
                                @endif
                                
                                <button type="button" 
                                        class="btn btn-danger btn-lg px-4 rounded-pill hover-lift" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal{{ $proposal->id }}">
                                    <i class="bi bi-trash me-2"></i>Eliminar
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="deleteModal{{ $proposal->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow-lg">
                                            <div class="modal-header bg-primary text-white rounded-top-4">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-center">
                                                <i class="bi bi-trash display-4 text-danger mb-3"></i>
                                                <h4 class="fw-bold text-dark mb-3">¿Eliminar propuesta?</h4>
                                                <p class="text-muted mb-0">
                                                    ¿Estás seguro de que deseas eliminar la propuesta 
                                                    <strong class="text-dark">"{{ $proposal->name }}"</strong>?
                                                </p>
                                                <p class="text-warning mt-2 mb-0">
                                                    <i class="bi bi-exclamation-circle me-1"></i>
                                                    Esta acción no se puede deshacer.
                                                </p>
                                            </div>
                                            <div class="modal-footer border-0 rounded-bottom-4">
                                                <button type="button" class="btn btn-outline-secondary btn-lg px-4 rounded-pill hover-lift" data-bs-dismiss="modal">
                                                    <i class="bi bi-x-circle me-2"></i>Cancelar
                                                </button>
                                                <form action="#" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-lg px-4 rounded-pill hover-lift">
                                                        <i class="bi bi-trash me-2"></i>Sí, Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            @empty

                <!-- Estado vacío -->
                <div class="text-center py-5">
                    <div class="card border-0 rounded-4 shadow-lg overflow-hidden">
                        <div class="card-body p-5">
                            <i class="bi bi-inbox display-1 text-muted mb-4"></i>
                            <h2 class="h1 fw-bold text-dark mb-3">No has enviado propuestas</h2>
                            <p class="lead text-muted mb-4">Envía tu primera propuesta a un evento público.</p>
                            <a href="{{ route('proposals.index') }}" class="btn btn-accent btn-lg px-5 py-3 rounded-pill shadow hover-lift">
                                <i class="bi bi-plus-circle-fill me-2"></i>Enviar Primera Propuesta
                            </a>
                        </div>
                    </div>
                </div>

            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function filterBy(state) {
    document.querySelectorAll('.btn-filter').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    const cards = document.querySelectorAll('.proposal-card');
    cards.forEach(card => {
        if (state === 'all' || card.dataset.status === state) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection
