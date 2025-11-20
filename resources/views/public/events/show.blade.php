@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" style="color: #4499BB;">Eventos</a>
            </li>
            <li class="breadcrumb-item active" style="color: #0C2340;">{{ $event->name }}</li>
        </ol>
    </nav>

    <!-- Feedback Messages Container -->
    <div id="feedback-message"></div>

    <!-- Event Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            @if($event->cover_image)
            <img src="{{ $event->cover_image }}"
                 class="img-fluid rounded shadow-sm mb-4"
                 alt="{{ $event->name }}"
                 style="width: 100%; max-height: 400px; object-fit: cover;">
            @endif

            <h1 class="fw-bold mb-3" style="color: #0C2340;">{{ $event->name }}</h1>

            <div class="mb-3">
                @php
                    $modalityLabels = [
                        'virtual' => 'Virtual',
                        'in_person' => 'Presencial',
                        'hybrid' => 'Híbrido'
                    ];
                    $statusLabels = [
                        'planning' => 'En planificación',
                        'active' => 'Activo',
                        'finished' => 'Finalizado'
                    ];
                @endphp
                <span class="badge" style="background-color: #8CC63F;">
                    {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                </span>
                <span class="badge" style="background-color: #4499BB;">
                    {{ $modalityLabels[$event->modality] ?? ucfirst($event->modality) }}
                </span>
                @foreach($event->tags as $tag)
                    <span class="badge bg-secondary">{{ $tag->name }}</span>
                @endforeach
            </div>

            <p class="lead text-muted">{{ $event->description }}</p>
        </div>

        <!-- Sidebar with Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-info-circle me-1" style="color: #4499BB;"></i> Información del Evento
                    </h5>

                    <!-- Dates -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-calendar-event me-1" style="color: #4499BB;"></i> Fechas
                        </h6>
                        <p class="mb-0">
                            <strong>Inicio:</strong> {{ $event->start_date->format('d/m/Y') }}<br>
                            <strong>Fin:</strong> {{ $event->end_date->format('d/m/Y') }}<br>
                            <strong>Hora:</strong> {{ $event->start_time }}
                        </p>
                    </div>

                    <hr>

                    <!-- Modality and Location -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-laptop me-1" style="color: #4499BB;"></i> Modalidad
                        </h6>
                        <p class="mb-0">{{ $modalityLabels[$event->modality] ?? ucfirst($event->modality) }}</p>
                    </div>

                    @if($event->location)
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-geo-alt me-1" style="color: #8CC63F;"></i> Ubicación
                        </h6>
                        <p class="mb-0">{{ $event->location }}</p>
                    </div>
                    @endif

                    <hr>

                    <!-- Organizer -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-person-badge me-1" style="color: #4499BB;"></i> Organizador
                        </h6>
                        <p class="mb-0">
                            {{ $event->professionalProfile->full_name ?? $event->professionalProfile->user->name }}
                        </p>
                    </div>

                    <hr>

                    <!-- Statistics -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-graph-up me-1" style="color: #8CC63F;"></i> Estadísticas
                        </h6>
                        <p class="mb-0">
                            <strong>{{ $event->approvedComponents->count() }}</strong> componente(s)<br>
                            <strong>{{ $event->approvedComponents->sum('capacity') ?: 'Ilimitados' }}</strong> cupos totales
                        </p>
                    </div>

                    @auth
                    <div class="d-grid mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver al Catálogo
                        </a>
                    </div>
                    @else
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('login') }}" class="btn" style="background-color: #0C2340; color: white;">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión para Inscribirse
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Event Components -->
    @if($event->approvedComponents->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card mb-4 border-0 shadow-sm" style="border-left: 4px solid #4499BB !important;">
                <div class="card-body py-3">
                    <h4 class="mb-0 fw-bold" style="color: #0C2340;">
                        <i class="bi bi-collection me-2" style="color: #4499BB;"></i> Componentes del Evento
                    </h4>
                </div>
            </div>

            <!-- Tabs by Component Type -->
            @if($componentsByType->count() > 1)
            <ul class="nav nav-tabs mb-4" role="tablist">
                @foreach($componentsByType as $type => $components)
                @php
                    $typeLabels = [
                        'workshop' => 'Talleres',
                        'talk' => 'Charlas',
                        'activity' => 'Actividades'
                    ];
                @endphp
                <li class="nav-item">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            data-bs-target="#{{ Str::slug($type) }}"
                            type="button"
                            style="{{ $loop->first ? 'color: #0C2340; border-bottom-color: #4499BB;' : '' }}">
                        {{ $typeLabels[$type] ?? ucfirst($type) }} ({{ $components->count() }})
                    </button>
                </li>
                @endforeach
            </ul>
            @endif

            <!-- Tab Content -->
            <div class="tab-content">
                @foreach($componentsByType as $type => $components)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="{{ Str::slug($type) }}">

                    <div class="row g-4">
                        @foreach($components as $component)
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                @if($component->cover_image)
                                <img src="{{ $component->cover_image }}"
                                     class="card-img-top"
                                     alt="{{ $component->name }}"
                                     style="height: 180px; object-fit: cover;">
                                @endif

                                <div class="card-body">
                                    <h5 class="card-title fw-bold" style="color: #0C2340;">{{ $component->name }}</h5>

                                    <!-- Badges -->
                                    <div class="mb-3">
                                        @php
                                            $typeLabels = [
                                                'workshop' => 'Taller',
                                                'talk' => 'Charla',
                                                'activity' => 'Actividad'
                                            ];
                                            $levelLabels = [
                                                'beginner' => 'Principiante',
                                                'intermediate' => 'Intermedio',
                                                'advanced' => 'Avanzado'
                                            ];
                                            $componentModalityLabels = [
                                                'virtual' => 'Virtual',
                                                'in_person' => 'Presencial',
                                                'hybrid' => 'Híbrido'
                                            ];
                                        @endphp
                                        <span class="badge" style="background-color: #0C2340;">
                                            {{ $typeLabels[$component->type] ?? ucfirst($component->type) }}
                                        </span>
                                        <span class="badge" style="background-color: #4499BB;">
                                            {{ $componentModalityLabels[$component->modality] ?? ucfirst($component->modality) }}
                                        </span>
                                        @if($component->level)
                                            <span class="badge bg-secondary">
                                                {{ $levelLabels[$component->level] ?? ucfirst($component->level) }}
                                            </span>
                                        @endif
                                        @if($component->attendee_price > 0)
                                            <span class="badge" style="background-color: #8CC63F;">
                                                ${{ number_format($component->attendee_price, 2) }}
                                            </span>
                                        @else
                                            <span class="badge" style="background-color: #8CC63F;">Gratis</span>
                                        @endif
                                    </div>

                                    <p class="card-text text-muted">{{ $component->description }}</p>

                                    @if($component->location)
                                    <p class="small text-muted mb-2">
                                        <i class="bi bi-geo-alt me-1" style="color: #8CC63F;"></i> {{ $component->location }}
                                    </p>
                                    @endif

                                    <!-- Available Slots -->
                                    @if($component->capacity)
                                    <div class="alert py-2 mb-3" style="background-color: rgba(68, 153, 187, 0.1); border: 1px solid #4499BB;">
                                        <i class="bi bi-people me-1" style="color: #4499BB;"></i>
                                        <strong>Cupos:</strong>
                                        <span id="slots-{{ $component->id }}">{{ $component->available_seats }}</span>
                                        disponibles de {{ $component->capacity }}

                                        @if($component->available_seats <= 0)
                                            <span class="badge bg-danger ms-2">Lleno</span>
                                        @elseif($component->available_seats <= 5)
                                            <span class="badge bg-warning text-dark ms-2">Últimos cupos</span>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Schedules -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2">
                                            <i class="bi bi-clock me-1" style="color: #4499BB;"></i> Horarios
                                        </h6>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($component->schedules as $schedule)
                                            <li class="small mb-1">
                                                <i class="bi bi-calendar-check me-1" style="color: #8CC63F;"></i>
                                                {{ $schedule->date->format('d/m/Y') }}
                                                de {{ substr($schedule->start_time, 0, 5) }} a {{ substr($schedule->end_time, 0, 5) }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <!-- Requirements -->
                                    @if($component->participant_requirements)
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2">
                                            <i class="bi bi-check2-square me-1" style="color: #4499BB;"></i> Requisitos
                                        </h6>
                                        <p class="small mb-0">{{ $component->participant_requirements }}</p>
                                    </div>
                                    @endif

                                    <!-- Registration Button -->
                                    @auth
                                    <div id="btn-container-{{ $component->id }}">
                                        @if($component->capacity && $component->available_seats <= 0)
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="bi bi-x-circle me-1"></i> Sin Cupos Disponibles
                                        </button>
                                        @else
                                        <button class="btn w-100 btn-register"
                                                style="background-color: #8CC63F; color: white;"
                                                data-component-id="{{ $component->id }}"
                                                onclick="register({{ $component->id }})">
                                            <i class="bi bi-pencil-square me-1"></i> Inscribirse
                                        </button>
                                        @endif
                                    </div>
                                    @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión para Inscribirse
                                    </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-collection" style="font-size: 3rem; color: #C8CCC9;"></i>
                    </div>
                    <p class="text-muted mb-0">Este evento aún no tiene componentes publicados.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // CSRF Token for requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Function to show messages
    function showMessage(message, type = 'success') {
        const feedbackDiv = document.getElementById('feedback-message');
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';

        feedbackDiv.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        // Scroll to top to see message
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Auto-close after 5 seconds
        setTimeout(() => {
            const alert = feedbackDiv.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }

    // Function to register
    async function register(componentId) {
        const button = document.querySelector(`button[data-component-id="${componentId}"]`);
        const btnContainer = document.getElementById(`btn-container-${componentId}`);

        // Disable button while processing
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Procesando...';

        try {
            const response = await fetch('{{ route("registrations.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    component_id: componentId
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Success
                showMessage(data.message, 'success');

                // Change button to "Already registered"
                btnContainer.innerHTML = `
                    <button class="btn btn-success w-100" disabled>
                        <i class="bi bi-check-circle me-1"></i> Ya inscrito
                    </button>
                `;

                // Update slots if they exist
                const slotsElement = document.getElementById(`slots-${componentId}`);
                if (slotsElement) {
                    const currentSlots = parseInt(slotsElement.textContent);
                    slotsElement.textContent = currentSlots - 1;
                }

            } else {
                // Validation error
                showMessage(data.message || 'Error al procesar la inscripción', 'error');
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-pencil-square me-1"></i> Inscribirse';
                button.style.backgroundColor = '#8CC63F';
            }

        } catch (error) {
            console.error('Error:', error);
            showMessage('Error de conexión. Por favor intenta de nuevo.', 'error');
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-pencil-square me-1"></i> Inscribirse';
            button.style.backgroundColor = '#8CC63F';
        }
    }

    // Verify registrations on page load
    document.addEventListener('DOMContentLoaded', async function() {
        @auth
        const registerButtons = document.querySelectorAll('.btn-register');

        for (const button of registerButtons) {
            const componentId = button.dataset.componentId;

            try {
                const response = await fetch(`{{ url('/registrations/check') }}/${componentId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.registered) {
                    const btnContainer = document.getElementById(`btn-container-${componentId}`);
                    btnContainer.innerHTML = `
                        <button class="btn btn-success w-100" disabled>
                            <i class="bi bi-check-circle me-1"></i> Ya inscrito
                        </button>
                    `;
                }
            } catch (error) {
                console.error('Error verificando inscripción:', error);
            }
        }
        @endauth
    });
</script>
@endpush
