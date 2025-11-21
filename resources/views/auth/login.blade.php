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
                        <img src="{{ asset('img/10.svg') }}" alt="Logo EVAi" class="sidebar-logo">
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
                
                <div class="w-100 z-index-1" style="max-width: 450px;">
                    
                    <div class="login-card card">
                        <div class="card-body p-4 p-md-5">
                            
                            <div class="d-lg-none text-center mb-4">
                                <img src="{{ asset('img/11.svg') }}" alt="Logo" style="height: 80px;" class="mb-3">
                            </div>

                            <div class="d-none d-lg-block mb-4">
                                <h2 class="fw-bold text-center text-brand-deep">Iniciar Sesión</h2>
                                <p class="text-secondary text-center">Ingresa tus credenciales para acceder</p>
                            </div>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <x-auth-session-status class="mb-3 alert alert-success" :status="session('status')" />

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold ms-1 text-brand-deep">Correo</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                                           class="form-control form-control-lg"
                                           placeholder="usuario@ues.edu.sv">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold ms-1 text-brand-deep">Contraseña</label>
                                    <div class="input-group">
                                        <input id="password" type="password" name="password" required 
                                               class="form-control form-control-lg border-end-0"
                                               placeholder="••••••••">
                                        <button class="btn btn-password-toggle" type="button" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                        <label for="remember_me" class="form-check-label text-secondary small">Recordar sesión</label>
                                    </div>
                                    
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="link-evai small">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    @endif
                                </div>

                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-login btn-lg">
                                        INGRESAR
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="text-secondary mb-0">
                                        ¿No tienes cuenta? 
                                        <a href="{{ route('register') }}" class="link-evai ms-1">
                                            Regístrate aquí
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