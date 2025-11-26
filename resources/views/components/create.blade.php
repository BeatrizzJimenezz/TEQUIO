@extends('layouts.app')

@push('styles')
    {{-- Estilos específicos basados en tu CSS proporcionado --}}
    <style>
        :root {
            --brand-deep: #0C2340;
            --brand-main: #4499BB;
            --brand-accent: #8CC63F;
            --evai-blue-main: #4499BB;
            --evai-blue-deep: #0C2340;
            --evai-green-accent: #8CC63F;
            --evai-gray-light: #C8CCC9;
            --evai-white: #FFFFFF;
        }

        .edit-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            background-color: #fff;
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }

        .edit-card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.25rem;
            display: flex;
            align-items: center;
        }

        .border-top-accent {
            border-top: 4px solid var(--evai-green-accent) !important;
        }

        .form-label {
            font-weight: 600;
            color: var(--brand-deep);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #dee2e6;
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            color: #0C2340;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand-main);
            box-shadow: 0 0 0 0.25rem rgba(68, 153, 187, 0.15);
        }

        .sticky-sidebar {
            position: sticky;
            top: 1.5rem;
            z-index: 10;
        }

        .btn-evai-green {
            background-color: var(--evai-green-accent);
            border-color: var(--evai-green-accent);
            color: white;
            font-weight: bold;
            border-radius: 50rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .btn-evai-green:hover {
            background-color: #72a933;
            border-color: #72a933;
            color: white;
        }

        .btn-outline-evai {
            border: 1px solid var(--evai-blue-main);
            color: var(--evai-blue-main);
            border-radius: 50rem;
            font-weight: bold;
            background-color: transparent;
        }
        
        .btn-outline-evai:hover {
            background-color: var(--evai-blue-main);
            color: var(--evai-white);
        }
        
        /* Estilos adicionales para radio buttons personalizados */
        .custom-radio-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .form-check-input:checked + .custom-radio-card {
            border-color: var(--brand-main);
            background-color: rgba(68, 153, 187, 0.05);
        }

        #speaker_results .list-group-item {
            cursor: pointer;
            border-left: none;
            border-right: none;
        }

        #speaker_results .list-group-item:first-child {
            border-top: none;
        }

        #speaker_results .list-group-item:hover {
            background-color: #f8f9fa;
        }

        #speaker_results mark {
            padding: 0;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
<div class="container py-4">
    
    <form action="{{ route('components.store', $event) }}" method="POST" id="componentForm">
        @csrf

        {{-- HEADER AZUL OSCURO (Igual que create.blade de Eventos) --}}
        <div class="card shadow mb-4" style="background-color: #0c2340;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    
                    <div>
                        <div class="mb-2">
                            <a href="{{ route('components.index', $event) }}" class="text-decoration-none d-inline-flex align-items-center" style="color: #ffffffff; font-size: 0.75rem;">
                                <i class="bi bi-arrow-left me-1"></i> Volver a componentes
                            </a>
                        </div>
                        
                        <h2 class="fw-bold mb-1 text-white">
                            Nuevo Componente
                        </h2>
                        <p class="mb-0 text-white-50 small">
                            <i class="bi bi-calendar3 me-1"></i> {{ $event->name }}
                        </p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('components.index', $event) }}" class="btn btn-outline-evai px-4" style="border-color: white; color: white;">
                            Cancelar
                        </a>
                        <style>.btn-outline-evai:hover { background-color: white; color: #0C2340 !important; }</style>

                        <button type="submit" class="btn btn-evai-green px-4">
                            <i class="bi bi-check-lg me-1"></i> Guardar Componente
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- ALERTAS DE ERROR --}}
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4 border-start border-4 border-danger">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Hay errores en el formulario:</strong>
                        <ul class="mb-0 ps-3 mt-1 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            
            {{-- COLUMNA PRINCIPAL (IZQUIERDA) --}}
            <div class="col-lg-8">
                
                {{-- TARJETA 1: DETALLES GENERALES --}}
                <div class="edit-card border-top border-4" style="border-top-color: #8CC63F !important;">
                    <div class="edit-card-header">
                        <i class="bi bi-pencil-square me-2 fs-5" style="color: #4499BB;"></i>
                        <h5 class="mb-0 fw-bold text-muted small uppercase" style="color: #0C2340;">DETALLES BÁSICOS</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="name" class="form-label">Nombre del Componente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                   value="{{ old('name') }}" required placeholder="Ej. Conferencia Magistral de IA">
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" required
                                      placeholder="Describe el contenido, objetivos y detalles del componente...">{{ old('description') }}</textarea>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-0">
                            <label for="cover_image" class="form-label">Imagen de Portada (URL)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-image"></i></span>
                                <input type="url" class="form-control border-start-0 ps-0" id="cover_image" name="cover_image" 
                                       value="{{ old('cover_image') }}" placeholder="https://ejemplo.com/imagen.jpg">
                            </div>
                            @error('cover_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- TARJETA 2: PONENTE / TALLERISTA --}}
                <div class="edit-card">
                    <div class="edit-card-header">
                        <i class="bi bi-person-badge-fill me-2 fs-5" style="color: #8CC63F;"></i>
                        <h5 class="mb-0 fw-bold text-muted small uppercase" style="color: #0C2340;">PONENTE / TALLERISTA</h5>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label mb-3">¿Cómo deseas asignar el ponente?</label>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="w-100 position-relative">
                                    <input class="form-check-input position-absolute top-0 end-0 m-2" type="radio" name="speaker_type" value="existing" 
                                           {{ old('speaker_type', 'existing') == 'existing' ? 'checked' : '' }} onchange="toggleSpeakerSection()">
                                    <div class="custom-radio-card text-center h-100 py-3">
                                        <i class="bi bi-person-check fs-3 text-primary mb-2 d-block"></i>
                                        <span class="small fw-bold">Seleccionar Existente</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="w-100 position-relative">
                                    <input class="form-check-input position-absolute top-0 end-0 m-2" type="radio" name="speaker_type" value="new" 
                                           {{ old('speaker_type') == 'new' ? 'checked' : '' }} onchange="toggleSpeakerSection()">
                                    <div class="custom-radio-card text-center h-100 py-3">
                                        <i class="bi bi-person-plus fs-3 text-success mb-2 d-block"></i>
                                        <span class="small fw-bold">Crear Perfil Temporal</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="w-100 position-relative">
                                    <input class="form-check-input position-absolute top-0 end-0 m-2" type="radio" name="speaker_type" value="none" 
                                           {{ old('speaker_type') == 'none' ? 'checked' : '' }} onchange="toggleSpeakerSection()">
                                    <div class="custom-radio-card text-center h-100 py-3">
                                        <i class="bi bi-person-x fs-3 text-muted mb-2 d-block"></i>
                                        <span class="small fw-bold">Sin Asignar</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Sección de ponente existente --}}
                        <div id="existingSpeakerSection" class="{{ old('speaker_type', 'existing') == 'existing' ? '' : 'd-none' }}">
                            <label for="speaker_search" class="form-label">Buscar Ponente</label>
                            
                            <div class="position-relative">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control border-start-0" 
                                        id="speaker_search" 
                                        placeholder="Buscar por nombre o email..."
                                        autocomplete="off"
                                    >
                                </div>
                                
                                {{-- Input oculto para enviar el ID seleccionado --}}
                                <input type="hidden" id="speaker_id" name="speaker_id" value="{{ old('speaker_id') }}">
                                
                                {{-- Dropdown de resultados --}}
                                <div id="speaker_results" class="list-group position-absolute w-100 shadow-lg" style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000; margin-top: 2px;">
                                    {{-- Los resultados se cargan aquí dinámicamente --}}
                                </div>
                                
                                {{-- Ponente seleccionado --}}
                                <div id="selected_speaker" class="mt-2" style="display: none;">
                                    <div class="alert alert-info d-flex justify-content-between align-items-center py-2 px-3 mb-0">
                                        <div>
                                            <i class="bi bi-person-check-fill me-2"></i>
                                            <strong id="selected_speaker_name"></strong>
                                            <small class="text-muted ms-2" id="selected_speaker_detail"></small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="clearSpeakerSelection()">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            @error('speaker_id') 
                                <div class="text-danger small mt-1">{{ $message }}</div> 
                            @enderror
                        </div>

                        {{-- Sección de nuevo ponente temporal --}}
                        <div id="newSpeakerSection" class="{{ old('speaker_type') == 'new' ? '' : 'd-none' }}">
                            <div class="alert alert-light border mb-3 small">
                                <i class="bi bi-info-circle me-1"></i> Creas un perfil temporal. Si el ponente se registra luego con este email, podrá vincularse.
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="temp_name" name="temp_name" value="{{ old('temp_name') }}">
                                    @error('temp_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="temp_email" name="temp_email" value="{{ old('temp_email') }}">
                                    @error('temp_email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Profesión / Ocupación</label>
                                    <input type="text" class="form-control" id="temp_profession" name="temp_profession" value="{{ old('temp_profession') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TARJETA 3: HORARIOS --}}
                <div class="edit-card">
                    <div class="edit-card-header d-flex justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history me-2 fs-5" style="color: #4499BB;"></i>
                            <h5 class="mb-0 fw-bold text-muted small uppercase" style="color: #0C2340;">HORARIOS Y FECHAS</h5>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-evai" onclick="addSchedule()">
                            <i class="bi bi-plus-lg"></i> Agregar Horario
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div id="schedulesContainer">
                            {{-- Los items se renderizan aquí --}}
                            <div class="schedule-item border rounded p-3 mb-3 bg-light position-relative">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label small">Fecha</label>
                                        <input type="date" class="form-control form-control-sm" name="schedules[0][date]"
                                               value="{{ old('schedules.0.date') }}"
                                               min="{{ $event->start_date->format('Y-m-d') }}"
                                               max="{{ $event->end_date->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Inicio</label>
                                        <input type="time" class="form-control form-control-sm" name="schedules[0][start_time]"
                                               value="{{ old('schedules.0.start_time') }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Fin</label>
                                        <input type="time" class="form-control form-control-sm" name="schedules[0][end_time]"
                                               value="{{ old('schedules.0.end_time') }}" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeSchedule(this)" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('schedules.0.date') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TARJETA 4: REQUISITOS (Opcional) --}}
                <div class="edit-card">
                    <div class="edit-card-header">
                        <i class="bi bi-list-check me-2 fs-5" style="color: #8CC63F;"></i>
                        <h5 class="mb-0 fw-bold text-muted small uppercase" style="color: #0C2340;">REQUISITOS</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Para el Participante</label>
                                <textarea class="form-control" name="participant_requirements" rows="2"
                                          placeholder="Ej: Laptop, conocimientos básicos...">{{ old('participant_requirements') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Para el Instructor (Logística)</label>
                                <textarea class="form-control" name="instructor_requirements" rows="2"
                                          placeholder="Ej: Proyector, Audio...">{{ old('instructor_requirements') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- COLUMNA LATERAL (DERECHA) --}}
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    
                    {{-- SIDEBAR 1: CONFIGURACIÓN --}}
                    <div class="edit-card border-top border-4" style="border-top-color: #8CC63F !important;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">Configuración</h6>
                            
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipo de Componente <span class="text-danger">*</span></label>
                                <select class="form-select fw-medium" id="type" name="type" required>
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Actividad</option>
                                    <option value="talk" {{ old('type') == 'talk' ? 'selected' : '' }}>Conferencia</option>
                                    <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Taller</option>
                                </select>
                                @error('type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="level" class="form-label">Nivel de Dificultad</label>
                                <select class="form-select" id="level" name="level">
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Principiante</option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Avanzado</option>
                                </select>
                                @error('level') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SIDEBAR 2: LOGÍSTICA --}}
                    <div class="edit-card">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">Ubicación</h6>

                            <div class="mb-3">
                                <label for="modality" class="form-label">Modalidad <span class="text-danger">*</span></label>
                                <select class="form-select" id="modality" name="modality" required>
                                    <option value="" disabled {{ old('modality') ? '' : 'selected' }}>Seleccionar...</option>
                                    <option value="virtual" {{ old('modality') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    <option value="in_person" {{ old('modality') == 'in_person' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hybrid" {{ old('modality') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                                @error('modality') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-0">
                                <label for="location" class="form-label">Lugar / Enlace</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="location" name="location" 
                                           value="{{ old('location') }}" placeholder="Sala A / Zoom Link">
                                </div>
                                @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SIDEBAR 3: FINANZAS --}}
                    <div class="edit-card">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3 border-bottom pb-2">Capacidad y Costos</h6>

                            <div class="mb-3">
                                <label for="capacity" class="form-label">Cupos Disponibles</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" 
                                       value="{{ old('capacity') }}" min="1" placeholder="Ilimitado si se deja vacío">
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Precio al Público</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">$</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           value="{{ old('price', 0) }}" min="0" step="0.01" onchange="togglePaymentMethods()">
                                </div>
                                <div class="form-text small">0.00 para gratuito</div>
                            </div>

                            <div class="mb-3">
                                <label for="organizer_cost" class="form-label">Costo Interno</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">$</span>
                                    <input type="number" class="form-control" id="organizer_cost" name="organizer_cost" 
                                           value="{{ old('organizer_cost') }}" min="0" step="0.01">
                                </div>
                            </div>

                            {{-- Métodos de pago (oculto por defecto si es gratis) --}}
                            <div id="paymentMethodsSection" style="display: none;">
                                <div class="border-top pt-3 mt-3">
                                    <label class="form-label small mb-2">Métodos de Pago Aceptados</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[]" value="online" id="payOnline"
                                            {{ is_array(old('payment_methods')) && in_array('online', old('payment_methods')) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="payOnline">
                                            Online (PayPal/Tarjeta)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="payment_methods[]" value="in_person" id="payPerson"
                                            {{ is_array(old('payment_methods')) && in_array('in_person', old('payment_methods')) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="payPerson">
                                            Efectivo / En sitio
                                        </label>
                                    </div>
                                    @error('payment_methods') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ============================================================================
// VARIABLES GLOBALES
// ============================================================================
let scheduleIndex = 1;
let searchTimeout;

// Data de ponentes (pasada desde el backend)
const speakers = {!! json_encode($speakers->map(function($profile) {
    return [
        'id' => $profile->id,
        'name' => $profile->is_temporary ? $profile->temp_name : $profile->user->name,
        'email' => $profile->is_temporary ? $profile->temp_email : $profile->user->email,
        'detail' => $profile->is_temporary ? 'Temporal' : ($profile->skills ?? 'Sin especialidad'),
        'is_temporary' => $profile->is_temporary
    ];
})) !!};

// ============================================================================
// GESTIÓN DE PONENTES - BUSCADOR
// ============================================================================
const searchInput = document.getElementById('speaker_search');
const resultsContainer = document.getElementById('speaker_results');
const selectedSpeakerDiv = document.getElementById('selected_speaker');
const speakerIdInput = document.getElementById('speaker_id');

// Búsqueda en tiempo real
searchInput?.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value.toLowerCase().trim();
    
    if (query.length < 2) {
        resultsContainer.style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        const filtered = speakers.filter(speaker => 
            speaker.name.toLowerCase().includes(query) || 
            speaker.email.toLowerCase().includes(query)
        );
        
        displayResults(filtered);
    }, 300);
});

// Mostrar resultados de búsqueda
function displayResults(results) {
    if (results.length === 0) {
        resultsContainer.innerHTML = `
            <div class="list-group-item text-muted text-center py-3">
                <i class="bi bi-inbox"></i> No se encontraron ponentes
            </div>
        `;
        resultsContainer.style.display = 'block';
        return;
    }
    
    resultsContainer.innerHTML = results.map(speaker => `
        <button 
            type="button" 
            class="list-group-item list-group-item-action" 
            onclick="selectSpeaker(${speaker.id}, '${escapeHtml(speaker.name)}', '${escapeHtml(speaker.email)}', '${escapeHtml(speaker.detail)}', ${speaker.is_temporary})"
        >
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>${highlightText(speaker.name, searchInput.value)}</strong>
                    <br>
                    <small class="text-muted">${highlightText(speaker.email, searchInput.value)}</small>
                </div>
                <span class="badge ${speaker.is_temporary ? 'bg-warning' : 'bg-primary'} ms-2">
                    ${speaker.detail}
                </span>
            </div>
        </button>
    `).join('');
    
    resultsContainer.style.display = 'block';
}

// Seleccionar un ponente
function selectSpeaker(id, name, email, detail, isTemporary) {
    speakerIdInput.value = id;
    searchInput.value = name;
    resultsContainer.style.display = 'none';
    
    document.getElementById('selected_speaker_name').textContent = name;
    document.getElementById('selected_speaker_detail').textContent = `${email} - ${detail}`;
    selectedSpeakerDiv.style.display = 'block';
    searchInput.disabled = true;
}

// Limpiar selección de ponente
function clearSpeakerSelection() {
    speakerIdInput.value = '';
    searchInput.value = '';
    searchInput.disabled = false;
    selectedSpeakerDiv.style.display = 'none';
    searchInput.focus();
}

// Resaltar texto de búsqueda
function highlightText(text, query) {
    if (!query) return text;
    const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
    return text.replace(regex, '<mark class="bg-warning">$1</mark>');
}

// Escape HTML para prevenir XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/'/g, "\\'");
}

// Escape regex
function escapeRegex(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// ============================================================================
// GESTIÓN DE TIPO DE PONENTE (Existente/Nuevo/Ninguno)
// ============================================================================
function toggleSpeakerSection() {
    const speakerType = document.querySelector('input[name="speaker_type"]:checked').value;
    const existingSection = document.getElementById('existingSpeakerSection');
    const newSection = document.getElementById('newSpeakerSection');

    existingSection.classList.add('d-none');
    newSection.classList.add('d-none');

    if (speakerType === 'existing') {
        existingSection.classList.remove('d-none');
        // Limpiar campos de temporal
        document.getElementById('temp_name').value = '';
        document.getElementById('temp_email').value = '';
        document.getElementById('temp_profession').value = '';
    } else if (speakerType === 'new') {
        newSection.classList.remove('d-none');
        // Limpiar selección de ponente existente
        clearSpeakerSelection();
    } else {
        // Limpiar ambos
        clearSpeakerSelection();
        document.getElementById('temp_name').value = '';
        document.getElementById('temp_email').value = '';
        document.getElementById('temp_profession').value = '';
    }
}

// ============================================================================
// GESTIÓN DE MÉTODOS DE PAGO
// ============================================================================
function togglePaymentMethods() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const section = document.getElementById('paymentMethodsSection');
    
    if (price > 0) {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
        document.getElementById('payOnline').checked = false;
        document.getElementById('payPerson').checked = false;
    }
}

// ============================================================================
// GESTIÓN DE HORARIOS
// ============================================================================
function addSchedule() {
    const container = document.getElementById('schedulesContainer');
    const minDate = '{{ $event->start_date->format("Y-m-d") }}';
    const maxDate = '{{ $event->end_date->format("Y-m-d") }}';

    const newSchedule = `
        <div class="schedule-item border rounded p-3 mb-3 bg-light position-relative">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small">Fecha</label>
                    <input type="date" class="form-control form-control-sm" 
                           name="schedules[${scheduleIndex}][date]"
                           min="${minDate}" max="${maxDate}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Inicio</label>
                    <input type="time" class="form-control form-control-sm" 
                           name="schedules[${scheduleIndex}][start_time]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Fin</label>
                    <input type="time" class="form-control form-control-sm" 
                           name="schedules[${scheduleIndex}][end_time]" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeSchedule(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', newSchedule);
    scheduleIndex++;
    updateRemoveButtons();
    
    // Aplicar restricciones de fecha al nuevo campo agregado
    setDateConstraints();
}

function removeSchedule(button) {
    button.closest('.schedule-item').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const schedules = document.querySelectorAll('.schedule-item');
    schedules.forEach(item => {
        const btn = item.querySelector('button[onclick*="removeSchedule"]');
        btn.disabled = schedules.length === 1;
    });
}

// ============================================================================
// VALIDACIÓN DE FORMULARIO
// ============================================================================
document.getElementById('componentForm')?.addEventListener('submit', function(e) {
    if (document.querySelectorAll('.schedule-item').length === 0) {
        e.preventDefault();
        alert('Debes agregar al menos un horario');
    }
});

// ============================================================================
// CERRAR RESULTADOS DE BÚSQUEDA AL HACER CLIC FUERA
// ============================================================================
document.addEventListener('click', function(e) {
    if (!searchInput?.contains(e.target) && !resultsContainer?.contains(e.target)) {
        resultsContainer.style.display = 'none';
    }
});

// ============================================================================
// VALIDACIÓN DE FECHAS - LIMITAR AL RANGO DEL EVENTO
// ============================================================================
function setDateConstraints() {
    const minDate = '{{ $event->start_date->format("Y-m-d") }}';
    const maxDate = '{{ $event->end_date->format("Y-m-d") }}';
    
    // Aplicar restricciones a todos los inputs de fecha existentes y futuros
    document.querySelectorAll('input[type="date"][name^="schedules"]').forEach(input => {
        input.setAttribute('min', minDate);
        input.setAttribute('max', maxDate);
        
        // Validar si la fecha actual está fuera del rango
        if (input.value) {
            const selectedDate = new Date(input.value);
            const min = new Date(minDate);
            const max = new Date(maxDate);
            
            if (selectedDate < min || selectedDate > max) {
                input.value = ''; // Limpiar si está fuera del rango
                input.classList.add('is-invalid');
            }
        }
        
        // Agregar validación en tiempo real
        input.addEventListener('change', function() {
            validateDateRange(this, minDate, maxDate);
        });
    });
}

function validateDateRange(input, minDate, maxDate) {
    const selectedDate = new Date(input.value);
    const min = new Date(minDate);
    const max = new Date(maxDate);
    
    if (selectedDate < min || selectedDate > max) {
        input.classList.add('is-invalid');
        
        // Crear mensaje de error si no existe
        let errorMsg = input.parentElement.querySelector('.invalid-feedback');
        if (!errorMsg) {
            errorMsg = document.createElement('div');
            errorMsg.className = 'invalid-feedback';
            input.parentElement.appendChild(errorMsg);
        }
        
        const eventStart = new Date(minDate).toLocaleDateString('es-ES');
        const eventEnd = new Date(maxDate).toLocaleDateString('es-ES');
        errorMsg.textContent = `La fecha debe estar entre ${eventStart} y ${eventEnd}`;
        
        // Limpiar el valor inválido después de mostrar el mensaje
        setTimeout(() => {
            input.value = '';
            input.classList.remove('is-invalid');
        }, 3000);
    } else {
        input.classList.remove('is-invalid');
        const errorMsg = input.parentElement.querySelector('.invalid-feedback');
        if (errorMsg) {
            errorMsg.remove();
        }
    }
}

// ============================================================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar sección de ponente
    toggleSpeakerSection();
    
    // Inicializar métodos de pago
    togglePaymentMethods();
    
    // Aplicar restricciones de fecha
    setDateConstraints();
    
    // Cargar selección previa de ponente si existe (para old values)
    const preselectedId = speakerIdInput?.value;
    if (preselectedId) {
        const speaker = speakers.find(s => s.id == preselectedId);
        if (speaker) {
            selectSpeaker(speaker.id, speaker.name, speaker.email, speaker.detail, speaker.is_temporary);
        }
    }
});
</script>
@endpush