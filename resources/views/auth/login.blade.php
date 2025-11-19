<x-guest-layout>
    <div class="container-fluid p-0 overflow-hidden">
        <div class="row g-0 min-vh-100">
            
            <!-- SECCIÓN IZQUIERDA-->
            <div class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center position-relative text-white"
                 style="background-color: #002855;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); z-index: 0;"></div>

                <div class="position-relative text-center px-5" style="z-index: 1;">
                    <div class="mb-4">
                        <img src="{{ asset('img/10.svg') }}" alt="EVAi Logo" style="height: 200px; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5));">
                    </div>

                    <div class="mx-auto mb-4 rounded-pill" style="width: 100px; height: 5px; background-color: #4499BB;"></div>

                    <div class="p-4 rounded-4 shadow-lg" 
                         style="background-color: rgba(0, 61, 115, 0.5); backdrop-filter: blur(5px); border: 1px solid rgba(68, 153, 187, 0.3);">
                        <h1 class="fw-bold mb-1" style="letter-spacing: 2px;">FMO UES</h1>
                        <h3 class="h5 text-light mb-3">Facultad Multidisciplinaria Oriental</h3>
                        <p class="lead mb-4" style="color: #cfe2ff;">San Miguel, El Salvador</p>
                        
                        <div class="pt-3 border-top border-light border-opacity-25">
                            <small style="color: #9ec5fe;">Plataforma oficial de gestión de eventos académicos</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN DERECHA-->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4"
                 style="background-color: #002855;">
                 <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); z-index: 0;"></div>
                
                <div class="w-100" style="max-width: 450px;">
                    
                    <div class="card border border-secondary rounded-4 shadow-lg">
                        <div class="card-body p-4 p-md-5">
                            
                            <div class="d-lg-none text-center mb-4">
                                <img src="{{ asset('img/11.svg') }}" alt="Logo" style="height: 80px;" class="mb-3">
                            </div>

                            <div class="d-none d-lg-block mb-4">
                                <h2 class="fw-bold text-center" style="color: #002855;">Iniciar Sesión</h2>
                                <p class="text-secondary">Ingresa tus credenciales para acceder</p>
                            </div>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <x-auth-session-status class="mb-3 alert alert-success" :status="session('status')" />

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold ms-1" style="color: #002855;">Correo</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                                           class="form-control form-control-lg"
                                           placeholder="usuario@ues.edu.sv">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold input-label" style="color: #002855;">Contraseña</label>
                                    <div class="input-group">
                                        <input id="password" type="password" name="password" required 
                                               class="form-control form-control-lg border-end-0"
                                               placeholder="••••••••">
                                        <button class="btn btn-light border border-start-0" type="button" id="togglePassword" 
                                                style="background-color: #fff; border-color: #ced4da;">
                                            <i class="bi bi-eye text-secondary"></i>
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
                                        <a href="{{ route('password.request') }}" class="text-decoration-none small fw-bold" style="color: #4499BB;">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    @endif
                                </div>

                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-lg fw-bold py-3 rounded-3"
                                            style="background-color: #8CC63F; border: none; color: #0a0a0a;">
                                        INGRESAR
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="text-secondary mb-0">
                                        ¿No tienes cuenta? 
                                        <a href="{{ route('register') }}" class="text-decoration-none fw-bold ms-1" style="color: #4499BB;">
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

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
    
</x-guest-layout>