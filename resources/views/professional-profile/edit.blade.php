@extends('layouts.app')

@section('header', 'Editar Perfil Profesional')

@push('styles')
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <link href="{{ asset('css/profile-edit.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">

    <!-- Volver -->
    <div class="mb-4">
        <a href="{{ route('professional-profile.show') }}" class="btn btn-link text-decoration-none p-0 text-secondary hover-scale">
            <i class="bi bi-arrow-left me-1"></i> Volver al perfil
        </a>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" style="background-color: #f8d7da; color: #842029;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        
        <!-- COLUMNA IZQUIERDA -->
        <div class="col-lg-4 col-xl-3">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-white border-bottom-0 pt-4 text-center">
                    <h5 class="fw-bold mb-0" style="color: #0c2340;">Foto de perfil</h5>
                </div>
                <div class="card-body text-center p-4">
                    
                    <div id="photo-preview-container" class="mb-4 position-relative d-inline-block">
                        @if(auth()->user()->profile_photo)
                           <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                class="rounded-circle border border-4 shadow-sm"
                                style="width: 160px; height: 160px; object-fit: cover; border-color: #e9ecef;"
                                id="preview-image">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold border border-4 shadow-sm"
                                 style="width: 160px; height: 160px; background-color: #0c2340; font-size: 3.5rem; border-color: #e9ecef;" 
                                 id="preview-placeholder">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        
                        @if(auth()->user()->profile_photo)
                            <button type="button" 
                                    id="delete-photo-btn"
                                    class="btn btn-danger btn-sm position-absolute bottom-0 end-0 rounded-circle shadow"
                                    style="width: 35px; height: 35px;"
                                    title="Eliminar foto">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </div>

                    <form action="{{ route('professional-profile.photo.upload') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        
                        <div class="d-grid gap-2">
                            <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*">
                            
                            <label for="profile_photo" class="btn btn-outline-secondary border-dashed">
                                <i class="bi bi-camera me-1"></i> Seleccionar nueva imagen
                            </label>
                            
                            @error('profile_photo')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                            <button type="submit" id="upload-btn" class="btn text-white fw-bold d-none" style="background-color: #4499bb;">
                                <i class="bi bi-cloud-upload me-1"></i> Actualizar Foto
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block fst-italic" style="font-size: 0.8rem;">
                            Formatos: JPG, PNG. Máx: 2MB.
                        </small>
                    </form>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA -->
        <div class="col-lg-8 col-xl-9">
            
            <!-- Informacion general -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4">
                    <h5 class="fw-bold mb-0" style="color: #0c2340;">
                        <i class="bi bi-person-vcard me-2" style="color: #4499bb;"></i>Información personal
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('professional-profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-medium text-secondary">Lugar de trabajo actual</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-briefcase text-muted"></i></span>
                                    <input type="text" name="current_workplace" class="form-control border-start-0 ps-0 bg-light"
                                           value="{{ old('current_workplace', $profile->current_workplace) }}"
                                           placeholder="Ej. Freelance, Empresa X...">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-medium text-secondary">Habilidades <small class="text-muted">(Separadas por comas)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lightning text-muted"></i></span>
                                    <input type="text" name="skills" class="form-control border-start-0 ps-0 bg-light"
                                           value="{{ old('skills', $profile->skills) }}"
                                           placeholder="Ej. PHP, Laravel, Liderazgo, Diseño UX">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium text-secondary">Sobre mí</label>
                                <textarea name="about_me" class="form-control bg-light" rows="5" 
                                          placeholder="Escribe una breve biografía profesional...">{{ old('about_me', $profile->about_me) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button class="btn text-white px-4 py-2 fw-bold shadow-sm hover-scale" style="background-color: #8cc63f; border: none;">
                                <i class="bi bi-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SECCIÓN DE REDES SOCIALES -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0" style="color: #0c2340;">
                        <i class="bi bi-share me-2" style="color: #4499bb;"></i>Redes Sociales
                    </h5>
                    <button class="btn btn-sm btn-outline-evai rounded-pill px-3" id="toggle-social-btn">
                        <i class="bi bi-plus-lg me-1"></i> Agregar
                    </button>
                </div>
                <div class="card-body p-4">
            
                <!-- Formulario para agregar -->
                <div id="social-form" class="mb-4 p-4 rounded-3 bg-light border border-dashed d-none">
                    <h6 class="fw-bold mb-3" style="color: #002855;">Nueva red social</h6>
                    <form action="{{ route('professional-profile.social-networks.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Plataforma *</label>
                                <select name="platform" class="form-select" required>
                                    <option value="">Selecciona una plataforma</option>
                                    <option value="LinkedIn">LinkedIn</option>
                                    <option value="GitHub">GitHub</option>
                                    <option value="Twitter">Twitter (X)</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Instagram">Instagram</option>
                                    <option value="YouTube">YouTube</option>
                                    <option value="TikTok">TikTok</option>
                                    <option value="Behance">Behance</option>
                                    <option value="Dribbble">Dribbble</option>
                                    <option value="Portfolio">Sitio Web / Portfolio</option>
                                    <option value="Otra">Otra</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Enlace *</label>
                                <input type="url" name="link" class="form-control" required 
                                    placeholder="https://ejemplo.com/tu-perfil">
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-link text-secondary text-decoration-none" 
                                    onclick="toggleSocialForm()">Cancelar</button>
                            <button class="btn text-white px-4 py-2 fw-bold shadow-sm hover-scale" 
                                    style="background-color: #8cc63f; border: none;">Agregar
                            </button>
                        </div>
                    </form>
            </div>

            <!-- Lista de Redes Sociales -->
            <div class="d-flex flex-column gap-3">
                @forelse($profile->socialNetworks as $social)
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded hover-shadow bg-white">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <!-- Icono según plataforma -->
                            <div class="social-icon-wrapper">
                                @switch($social->platform)
                                    @case('LinkedIn')
                                        <i class="bi bi-linkedin" style="font-size: 1.5rem; color: #0077B5;"></i>
                                        @break
                                    @case('GitHub')
                                        <i class="bi bi-github" style="font-size: 1.5rem; color: #181717;"></i>
                                        @break
                                    @case('Twitter')
                                        <i class="bi bi-twitter-x" style="font-size: 1.5rem; color: #000000;"></i>
                                        @break
                                    @case('Facebook')
                                        <i class="bi bi-facebook" style="font-size: 1.5rem; color: #1877F2;"></i>
                                        @break
                                    @case('Instagram')
                                        <i class="bi bi-instagram" style="font-size: 1.5rem; color: #E4405F;"></i>
                                        @break
                                    @case('YouTube')
                                        <i class="bi bi-youtube" style="font-size: 1.5rem; color: #FF0000;"></i>
                                        @break
                                    @case('TikTok')
                                        <i class="bi bi-tiktok" style="font-size: 1.5rem; color: #000000;"></i>
                                        @break
                                    @case('Portfolio')
                                        <i class="bi bi-globe" style="font-size: 1.5rem; color: #4499bb;"></i>
                                        @break
                                    @default
                                        <i class="bi bi-link-45deg" style="font-size: 1.5rem; color: #6c757d;"></i>
                                @endswitch
                            </div>
                            
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-dark">{{ $social->platform }}</h6>
                                <a href="{{ $social->link }}" target="_blank" 
                                class="small text-decoration-none" style="color: #4499bb;">
                                    {{ Str::limit($social->link, 50) }}
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                        
                        <form action="{{ route('professional-profile.social-networks.destroy', $social->id) }}" 
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm border-0" 
                                    onclick="return confirm('¿Estás seguro de eliminar esta red social?')"
                                    data-bs-toggle="tooltip" title="Eliminar">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-share display-6 mb-2 d-block opacity-50"></i>
                        No hay redes sociales registradas.
                    </div>
                @endforelse
            </div>
        </div>

        </div>
        <!-- Formacion academica -->
        <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0" style="color: #4499bb;">
                        <i class="bi bi-mortarboard me-2" style="color: #4499bb;"></i>Formación académica
                    </h5>
                    <button class="btn btn-sm btn-outline-evai rounded-pill px-3" id="toggle-training-btn">
                        <i class="bi bi-plus-lg me-1"></i> Agregar
                    </button>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Formulario agregar formacion -->
                    <div id="training-form" class="mb-5 p-4 rounded-3 bg-light border border-dashed d-none">
                        <h6 class="fw-bold mb-3" style="color: #002855;">Nueva formación</h6>
                        <form action="{{ route('professional-profile.academic-training.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Institución *</label>
                                    <input type="text" name="institution" class="form-control" required placeholder="Nombre de la universidad o escuela">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Título / Grado / Certificación*</label>
                                    <input type="text" name="degree" class="form-control" required placeholder="Ej. Licenciatura en...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Fecha inicio *</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Fecha fin</label>
                                    <input type="date" name="end_date" class="form-control">
                                    <div class="form-text">Dejar vacío si actualmente cursas esto.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted">Descripción (Opcional)</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Detalles adicionales..."></textarea>
                                </div>
                            </div>
                            <div class="mt-3 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none" onclick="toggleTrainingForm()">Cancelar</button>
                                <button class="btn text-white px-4 py-2 fw-bold shadow-sm hover-scale" style="background-color: #8cc63f; border: none;">Guardar</button>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        @forelse($profile->academicTrainings as $training)
                            <div class="d-flex justify-content-between align-items-start p-3 border rounded hover-shadow bg-white">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $training->degree }}</h6>
                                    <div class="small mb-1" style="color: #8cc63f;">{{ $training->institution }}</div>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($training->start_date)->format('M Y') }} - 
                                        {{ $training->end_date ? \Carbon\Carbon::parse($training->end_date)->format('M Y') : 'Presente' }}
                                    </div>
                                    @if($training->description)
                                        <p class="text-secondary small mt-2 mb-0 fst-italic">
                                            "{{ Str::limit($training->description, 100) }}"
                                        </p>
                                    @endif
                                </div>
                                <form action="{{ route('professional-profile.academic-training.destroy', $training->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm border-0" 
                                            onclick="return confirm('¿Estás seguro de eliminar este registro?')"
                                            data-bs-toggle="tooltip" title="Eliminar">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x display-6 mb-2 d-block opacity-50"></i>
                                No hay formación registrada.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/profile-edit.js') }}"></script>
@endpush

@endsection