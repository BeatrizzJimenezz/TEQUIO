<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Custom -->
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
<div id="wrapper">

    <!-- SIDEBAR -->
    <div id="sidebar-wrapper">

        <div class="sidebar-heading text-white">
            <div class="d-flex align-items-center" id="logo-tequio">
                <img src="{{ asset('img/10.svg') }}" alt="TEQUIO Logo" style="max-height: 50px;">
            </div>

            <button id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <div class="list-group list-group-flush mt-2">

            <div class="sidebar-category">General</div>

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                <span class="link-text ms-6">Eventos</span>
            </a>

            @auth
                <a href="#" class="sidebar-link {{ request()->routeIs('mis-inscripciones') ? 'active' : '' }}">
                    <i class="bi bi-ticket"></i>
                    <span class="link-text ms-6">Inscripciones</span>
                </a>

                <!-- ORGANIZADOR / ADMIN -->
                @if(auth()->user()->hasAnyRole(['Administrador', 'Organizador']))
                    <div class="sidebar-category">Organizar</div>

                    <a class="sidebar-link" href="#">
                        <i class="bi bi-calendar3"></i>
                        <span class="link-text ms-6">Mis Eventos</span>
                    </a>

                    <a class="sidebar-link" href="#">
                        <i class="bi bi-grid-fill"></i>
                        <span class="link-text ms-6">Reportes</span>
                    </a>
                @endif

                    <div class="sidebar-category">Contribuciones</div>
                    <a class="sidebar-link" href="">
                        <i class="bi bi-search"></i>
                        <span class="link-text ms-6">Ofertas</span>
                    </a>
                    <a class="sidebar-link" href="">
                        <i class="bi bi-send"></i>
                        <span class="link-text ms-6">Propuestas</span>
                    </a>
                
                    <div class="sidebar-category">Mi Cuenta</div>
                    <a href="" class="sidebar-link {{ request()->routeIs('perfil-profesional.show') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        <span class="link-text ms-6">Perfil</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        <span class="link-text ms-6">Configuración</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                            <button type="submit" class="sidebar-link bg-transparent border-0 w-100">
                                <i class="bi bi-box-arrow-right"></i>
                                <span class="link-text ms-6">Cerrar Sesión</span>
                            </button>
                    </form>
                @endauth
            </div>
            
            @auth
            <div class="mt-auto p-3 ">
                <div class="user-card bg-white rounded-3 p-2 d-flex align-items-center gap-3 shadow-sm">
                    <div class="flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0d6efd&color=fff" alt="user" class="rounded-circle border border-2 border-light" width="42" height="42">
                    </div>
                    <div class="flex-grow-1 overflow-hidden user-info-text lh-1">
                        <div class="text-dark fw-bold small text-truncate mb-1">{{ Auth::user()->name }}</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">
                            <i class="bi bi-shield-lock-fill me-1"></i>{{ Auth::user()->roles->first()->name ?? 'Usuario' }}
                        </div>
                    </div>
                </div>
            </div>
            @endauth

    </div>



       <!-- Contenido Principal -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3" style="height: 70px;">
                
                <button class="btn btn-outline-secondary d-md-none me-3" id="mobileToggle">
                    <i class="bi bi-list"></i>
                </button>

                <div class="fw-bold text-uppercase text-center text-dark h5 mb-0">
                    @yield('header') 
                </div>
            </nav>

            <main class="container-fluid p-4">
                {{ $slot }}
            </main>


</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>

@stack('scripts')
</body>
</html>
