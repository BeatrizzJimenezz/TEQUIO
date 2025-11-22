@extends('layouts.app')

@section('header', 'Solicitar ser Organizador')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background-color: #0C2340;">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i>Solicitud para ser Organizador
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Como Organizador podrás:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Crear y gestionar eventos</li>
                            <li>Publicar ofertas para ponentes</li>
                            <li>Administrar componentes y horarios</li>
                            <li>Ver reportes de asistencia</li>
                        </ul>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('role-requests.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="reason" class="form-label fw-semibold">
                                ¿Por qué deseas ser Organizador? <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                      id="reason"
                                      name="reason"
                                      rows="6"
                                      placeholder="Describe tu experiencia organizando eventos, tu motivación y qué tipo de eventos te gustaría crear..."
                                      required>{{ old('reason') }}</textarea>
                            <div class="form-text">
                                Mínimo 50 caracteres. Sé específico sobre tu experiencia y planes.
                            </div>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn" style="background-color: #8CC63F; color: white;">
                                <i class="bi bi-send me-2"></i>Enviar Solicitud
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
