@extends('layouts.app')

@section('header', 'Solicitar ser Organizador')

@push('styles')
    <link href="{{ asset('css/management.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-admin">
                <div class="card-header-admin">
                    <h5 class="mb-0 fw-bold text-brand-deep">
                        <i class="bi bi-person-plus-fill me-2 text-brand-accent"></i>Solicitud para ser Organizador
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert bg-brand-light border-0 mb-4">
                        <div class="d-flex">
                            <i class="bi bi-info-circle-fill text-brand-main fs-4 me-3"></i>
                            <div>
                                <strong class="text-brand-deep">Como Organizador podrás:</strong>
                                <ul class="mb-0 mt-2 text-muted">
                                    <li>Crear y gestionar eventos</li>
                                    <li>Publicar ofertas para ponentes</li>
                                    <li>Administrar componentes y horarios</li>
                                    <li>Ver reportes de asistencia</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
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
                            <label for="reason" class="form-label fw-bold text-brand-deep">
                                ¿Por qué deseas ser Organizador? <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                      id="reason"
                                      name="reason"
                                      rows="6"
                                      placeholder="Describe tu experiencia organizando eventos, tu motivación y qué tipo de eventos te gustaría crear..."
                                      required>{{ old('reason') }}</textarea>
                            <div class="form-text text-muted">
                                <i class="bi bi-pencil-fill me-1"></i>Mínimo 50 caracteres. Sé específico sobre tu experiencia y planes.
                            </div>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-white shadow-sm">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-brand-primary shadow-sm">
                                <i class="bi bi-send me-2"></i>Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
