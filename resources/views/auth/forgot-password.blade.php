<x-guest-layout>
    <div class="container-fluid p-0 overflow-hidden">
        <div class="row g-0 min-vh-100">
            
            <div class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center position-relative text-white"
                 style="background-color: #002855;">
                 
                 <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); z-index: 0;"></div>

                <div class="position-relative text-center px-5" style="z-index: 1;">
                    <div class="mb-4">
                        <img src="{{ asset('img/10.svg') }}" alt="EVAi Logo" style="height: 200px; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5));">
                    </div>
                    <div class="mx-auto mb-4 rounded-pill" style="width: 100px; height: 5px; background-color: #4499BB;"></div>
                    <h2 class="fw-bold">Recuperación de cuenta</h2>
                    <p class="lead text-light opacity-75">Te ayudaremos a restablecer tu acceso de forma segura.</p>
                </div>
            </div>

            <div class="col-lg-6 login-right-section p-4">
                <div class="w-100" style="max-width: 450px;">
                    
                    <div class="card login-card">
                        <div class="card-body p-4 p-md-5">
                            
                            <div class="d-lg-none text-center mb-4">
                                <img src="{{ asset('img/11.svg') }}" alt="Logo" style="height: 60px;" class="mb-3">
                            </div>

                            <div class="mb-4">
                                <h3 class="login-title mb-2">¿Olvidaste tu contraseña?</h3>
                                <p class="text-secondary small mb-0">
                                    No te preocupes. Ingresa tu correo electrónico y te enviaremos un enlace para que elijas una nueva contraseña.
                                </p>
                            </div>

                            <!-- Mensaje de éxito -->
                            <x-auth-session-status class="mb-4 alert alert-success" :status="session('status')" />

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label input-label">Correo</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                           class="form-control form-control-lg"
                                           placeholder="usuario@gmail.com">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                                </div>

                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-lg btn-login">
                                        ENVIAR ENLACE
                                    </button>
                                </div>

                                <div class="text-center">
                                    <a href="{{ route('login') }}" class="text-link small">
                                        <i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>