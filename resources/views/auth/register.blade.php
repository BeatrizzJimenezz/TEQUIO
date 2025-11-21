<x-guest-layout>
    @push('styles')
        <link href="{{ asset('css/login.css') }}" rel="stylesheet">
    @endpush

    <div class="container-fluid p-0 overflow-hidden">
        <div class="row g-0 min-vh-100">
            
            <div class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center position-relative text-white bg-deep-blue">
                <div class="bg-pattern-overlay"></div>

                <div class="z-index-1 text-center px-5">
                    <div class="mb-4">
                        <img src="{{ asset('img/10.svg') }}" alt="EVAi Logo" class="sidebar-logo">
                    </div>

                    <div class="sidebar-divider"></div>

                    <div class="glass-card">
                        <h1 class="fw-bold mb-1" style="letter-spacing: 2px;">FMO UES</h1>
                        <h3 class="h5 text-light mb-3">Facultad Multidisciplinaria Oriental</h3>
                        <p class="lead mb-4 text-blue-light">San Miguel, El Salvador</p>
                        
                        <div class="pt-3 border-top border-light border-opacity-25">
                            <small class="text-blue-lighter">Plataforma oficial de gestión de eventos académicos</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 bg-deep-blue position-relative">
                 <div class="bg-pattern-overlay"></div>
                
                <div class="w-100 z-index-1" style="max-width: 550px;"> <div class="login-card card"> <div class="card-body p-4 p-md-5">
                            
                            <div class="d-lg-none text-center mb-4">
                                <img src="{{ asset('img/11.svg') }}" alt="Logo" style="height: 80px;" class="mb-3">
                            </div>

                            <div class="mb-4 text-center">
                                <h2 class="fw-bold text-brand-deep">Regístrate</h2>
                                <p class="text-secondary">Únete a la comunidad académica</p>
                            </div>

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold ms-1 text-brand-deep">Nombre completo</label>
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                           class="form-control form-control-lg"
                                           placeholder="Juan Pérez">
                                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-danger small" />
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold ms-1 text-brand-deep">Correo</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                           class="form-control form-control-lg"
                                           placeholder="usuario@ues.edu.sv">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="password" class="form-label fw-bold ms-1 text-brand-deep">Contraseña</label>
                                        <div class="input-group">
                                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                                   class="form-control form-control-lg border-end-0"
                                                   placeholder="••••••••">
                                            <button class="btn btn-password-toggle" type="button">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-bold ms-1 text-brand-deep">Confirmar</label>
                                        <div class="input-group">
                                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                                   class="form-control form-control-lg border-end-0"
                                                   placeholder="••••••••">
                                            <button class="btn btn-password-toggle" type="button">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-danger small" />
                                    </div>
                                </div>

                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-login btn-lg">
                                        REGISTRARSE
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="text-secondary mb-0">
                                        ¿Ya tienes cuenta? 
                                        <a href="{{ route('login') }}" class="link-evai ms-1">
                                            Inicia sesión aquí
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/login.js') }}"></script>
    @endpush
</x-guest-layout>