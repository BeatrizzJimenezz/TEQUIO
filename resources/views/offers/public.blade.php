@extends('layouts.app')

@section('header', 'Ofertas Abiertas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold" style="color: #0C2340;">
                <i class="bi bi-megaphone-fill me-2"></i>Ofertas Abiertas
            </h2>
            <p class="text-muted">Eventos que buscan ponentes y talleristas</p>
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

    @forelse($offers as $offer)
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="mb-0 me-3 fw-bold">{{ $offer->name }}</h5>
                            <span class="badge" style="background-color: #8CC63F;">
                                <i class="bi bi-megaphone me-1"></i>Oferta Abierta
                            </span>
                        </div>

                        <p class="mb-2">
                            <i class="bi bi-calendar-event me-1" style="color: #4499BB;"></i>
                            <strong>Evento:</strong> {{ $offer->event->name }}
                        </p>

                        <div class="mb-2">
                            @php
                                $typeLabels = ['activity' => 'Actividad', 'talk' => 'Charla', 'workshop' => 'Taller'];
                                $modalityLabels = ['virtual' => 'Virtual', 'in_person' => 'Presencial', 'hybrid' => 'Híbrido'];
                                $levelLabels = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
                            @endphp
                            <span class="badge" style="background-color: #0C2340;">
                                {{ $typeLabels[$offer->type] ?? ucfirst($offer->type) }}
                            </span>
                            <span class="badge" style="background-color: #4499BB;">
                                {{ $modalityLabels[$offer->modality] ?? ucfirst($offer->modality) }}
                            </span>

                            @if($offer->level)
                                <span class="badge bg-secondary">
                                    {{ $levelLabels[$offer->level] ?? ucfirst($offer->level) }}
                                </span>
                            @endif

                            @if($offer->capacity)
                                <span class="badge bg-info">
                                    <i class="bi bi-people me-1"></i>{{ $offer->capacity }} cupos
                                </span>
                            @endif

                            @if($offer->organizer_cost)
                                <span class="badge bg-success">
                                    <i class="bi bi-cash me-1"></i>Remunerado
                                </span>
                            @endif

                            @php
                                $myApplications = $offer->applications()
                                    ->where('professional_profile_id', auth()->user()->professionalProfile?->id)
                                    ->count();

                                $totalApplications = $offer->applications()->count();
                            @endphp

                            @if($totalApplications > 0)
                                <span class="badge bg-info">
                                    <i class="bi bi-people me-1"></i>{{ $totalApplications }} postulante(s)
                                </span>
                            @endif

                            @if($myApplications > 0)
                                <span class="badge bg-secondary">
                                    <i class="bi bi-check me-1"></i>Ya postulaste
                                </span>
                            @endif
                        </div>

                        <p class="card-text text-muted mb-2">{{ Str::limit($offer->description, 200) }}</p>

                        @if($offer->location)
                            <p class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $offer->location }}
                                </small>
                            </p>
                        @endif

                        @if($offer->instructor_requirements)
                            <div class="mt-2">
                                <strong class="small" style="color: #0C2340;">Requisitos del ponente:</strong>
                                <p class="mb-0 small text-muted">{{ $offer->instructor_requirements }}</p>
                            </div>
                        @endif

                        {{-- Horarios --}}
                        @if($offer->schedules->count() > 0)
                            <div class="mt-3">
                                <strong class="small" style="color: #0C2340;">
                                    <i class="bi bi-calendar-week me-1"></i>Horarios disponibles:
                                </strong>
                                <ul class="list-unstyled ms-3 mb-0 mt-1">
                                    @foreach($offer->schedules as $schedule)
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
                    </div>

                    <div class="ms-3">
                        <button type="button" class="btn {{ $myApplications > 0 ? 'btn-secondary' : '' }}"
                                style="{{ $myApplications == 0 ? 'background-color: #8CC63F; color: white;' : '' }}"
                                data-bs-toggle="modal"
                                data-bs-target="#applyModal{{ $offer->id }}"
                                {{ $myApplications > 0 ? 'disabled' : '' }}>
                            <i class="bi bi-hand-thumbs-up me-1"></i>
                            {{ $myApplications > 0 ? 'Postulado' : 'Postularme' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Postulación --}}
        <div class="modal fade" id="applyModal{{ $offer->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('offers.apply', $offer) }}" method="POST">
                        @csrf
                        <div class="modal-header" style="background-color: #0C2340; color: white;">
                            <h5 class="modal-title">
                                <i class="bi bi-hand-thumbs-up me-2"></i>Postularme a: {{ $offer->name }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info border-0">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Al postularte, tu perfil profesional será revisado por el organizador del evento.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mensaje al organizador (opcional)</label>
                                <textarea class="form-control" name="message" rows="4"
                                          placeholder="Cuéntale al organizador por qué eres la persona indicada para esta actividad..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn" style="background-color: #8CC63F; color: white;">
                                <i class="bi bi-send me-2"></i>Enviar Postulación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-2" style="color: #0C2340;">No hay ofertas disponibles</h5>
                <p class="text-muted mb-0">Actualmente no hay eventos buscando ponentes o talleristas.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
