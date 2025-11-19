<x-guest-layout>
    <div class="container-fluid p-0 overflow-hidden">
        <div class="row g-0 min-vh-100">
            
            <!-- SECCIÓN IZQUIERDA -->
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
                
                <div class="w-100" style="max-width: 450px; position: relative; z-index: 1;">
                    
                    <div class="card border border-secondary rounded-4 shadow-lg">
                        <div class="card-body p-4 p-md-5">
                            
                            <div class="d-lg-none text-center mb-4">
                                <img src="{{ asset('img/11.svg') }}" alt="Logo" style="height: 80px;" class="mb-3">
                            </div>

                            <div class="mb-4 text-center">
                                <h2 class="fw-bold" style="color: #002855;">Registrate</h2>
                                <p class="text-secondary">Únete a la comunidad académica</p>
                            </div>

                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold ms-1" style="color: #002855;">Nombre completo</label>
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                           class="form-control form-control-lg"
                                           placeholder="Juan Pérez">
                                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-danger small" />
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold ms-1" style="color: #002855;">Correo</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                           class="form-control form-control-lg"
                                           placeholder="usuario@ues.edu.sv">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="password" class="form-label fw-bold input-label" style="color: #002855;">Contraseña</label>
                                        <div class="input-group">
                                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                                   class="form-control form-control-lg border-end-0"
                                                   placeholder="••••••••">
                                            <button class="btn btn-light border border-start-0" type="button" id="togglePassword"
                                                    style="background-color: #fff; border-color: #ced4da;">
                                                <i class="bi bi-eye text-secondary"></i>
                                            </button>
                                        </div>
                                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-bold input-label" style="color: #002855;">Confirmar</label>
                                        <div class="input-group">
                                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                                   class="form-control form-control-lg border-end-0"
                                                   placeholder="••••••••">
                                            <button class="btn btn-light border border-start-0" type="button" id="toggleConfirmPassword"
                                                    style="background-color: #fff; border-color: #ced4da;">
                                                <i class="bi bi-eye text-secondary"></i>
                                            </button>
                                        </div>
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-danger small" />
                                    </div>
                                </div>

                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-lg fw-bold py-3 rounded-3"
                                            style="background-color: #8CC63F; border: none; color: #0a0a0a;">
                                        REGISTRARSE
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="text-secondary mb-0">
                                        ¿Ya tienes cuenta? 
                                        <a href="{{ route('login') }}" class="text-decoration-none fw-bold ms-1" style="color: #4499BB;">
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

    <script>
        function setupPasswordToggle(inputId, toggleId) {
            const toggleBtn = document.querySelector('#' + toggleId);
            const passwordInput = document.querySelector('#' + inputId);

            toggleBtn.addEventListener('click', function (e) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        }
        setupPasswordToggle('password', 'togglePassword');
        setupPasswordToggle('password_confirmation', 'toggleConfirmPassword');
    </script>
</x-guest-layout>