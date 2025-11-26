@extends('layouts.app')

@section('header', 'Solicitar ser Organizador')

@push('styles')
    <link href="{{ asset('css/role-requests.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="organizer-card">
                {{-- Header --}}
                <div class="organizer-card-header">
                    <h5>
                        <span class="header-icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </span>
                        Solicitud para ser Organizador
                    </h5>
                </div>

                <div class="organizer-card-body">
                    {{-- Beneficios --}}
                    <div class="benefits-box">
                        <div class="d-flex align-items-start gap-3">
                            <div class="benefits-icon">
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="benefits-title">Como Organizador podrás:</div>
                                <ul>
                                    <li>Crear y gestionar eventos</li>
                                    <li>Publicar ofertas para ponentes</li>
                                    <li>Administrar componentes y horarios</li>
                                    <li>Ver reportes de asistencia</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Errores de validación --}}
                    @if($errors->any())
                        <div class="error-alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Formulario --}}
                    <form action="{{ route('role-requests.store') }}" method="POST">
                        @csrf

                        <div class="form-section">
                            <label for="reason" class="form-label">
                                ¿Por qué deseas ser Organizador? <span class="required">*</span>
                            </label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                      id="reason"
                                      name="reason"
                                      rows="6"
                                      placeholder="Describe tu experiencia organizando eventos, tu motivación y qué tipo de eventos te gustaría crear"
                                      required>{{ old('reason') }}</textarea>
                            <div class="form-hint">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>Mínimo 50 caracteres. Sé específico sobre tu experiencia y planes.</span>
                            </div>
                            @error('reason')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="btn-actions">
                            <a href="{{ route('dashboard') }}" class="btn-cancel">
                                <i class="bi bi-x-lg"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-send-fill"></i>
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection