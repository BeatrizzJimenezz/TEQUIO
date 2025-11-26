<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TEQUIO') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

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

            <!-- CATEGORIA GENERAL -->
            <div class="sidebar-category">General</div>
            <hr class="sidebar-divider">

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                <span class="link-text ms-6">Eventos</span>
            </a>

            @auth
                @if (auth()->user()->must_change_password == false)
                    <a href="{{ route('registrations.index') }}" class="sidebar-link {{ request()->routeIs('registrations.index') ? 'active' : '' }}">
                        <i class="bi bi-ticket"></i>
                        <span class="link-text ms-6">Mis Inscripciones</span>
                    </a>
                    
                    <!-- ORGANIZADOR / ADMIN -->
                    @if(auth()->user()->hasAnyRole(['Administrador', 'Organizador']))

                        <!-- CATEGORIA ORGANIZAR -->
                        <div class="sidebar-category">Organizar</div>
                        <hr class="sidebar-divider">

                        <a href="{{ route('events.index') }}" class="sidebar-link {{ request()->routeIs('events.index') ? 'active' : '' }}">
                            <i class="bi bi-calendar3"></i>
                            <span class="link-text ms-6">Mis Eventos</span>
                        </a>

                        <a href="{{ route('events.reports.index') }}" class="sidebar-link {{ request()->routeIs('events.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-fill"></i>
                            <span class="link-text ms-6">Reportes</span>
                        </a>

                        <a href="{{ route('organizer.funds.index') }}" class="sidebar-link {{ request()->routeIs('organizer.funds.*') ? 'active' : '' }}">
                            <i class="bi bi-wallet2"></i>
                            <span class="link-text ms-6">Mis Fondos</span>
                            @php
                                $balance = auth()->user()->organizerBalance;
                                $availableBalance = $balance ? $balance->available_balance : 0;
                            @endphp
                            @if($availableBalance > 0)
                                <span class="badge bg-success rounded-pill ms-auto">${{ number_format($availableBalance, 0) }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- SOLO ADMIN -->
                    @if(auth()->user()->hasRole('Administrador'))
                        
                        <!-- CATEGORIA ADMINISTRACIÓN -->
                        <div class="sidebar-category">Administración</div>
                        <hr class="sidebar-divider">

                        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span class="link-text ms-6">Usuarios</span>
                        </a>

                        <a href="{{ route('tags.index') }}" class="sidebar-link {{ request()->routeIs('tags.*') ? 'active' : '' }}">
                            <i class="bi bi-tags-fill"></i>
                            <span class="link-text ms-6">Etiquetas</span>
                        </a>

                        @php
                            $pendingRoleRequests = \App\Models\RoleRequest::where('status', 'pending')->count();
                        @endphp
                        <a href="{{ route('role-requests.index') }}" class="sidebar-link {{ request()->routeIs('role-requests.index') ? 'active' : '' }}">
                            <i class="bi bi-person-check-fill"></i>
                            <span class="link-text ms-6">Solicitudes de Rol</span>
                            @if($pendingRoleRequests > 0)
                                <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingRoleRequests }}</span>
                            @endif
                        </a>

                        @php
                            $pendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
                        @endphp
                        <a href="{{ route('admin.withdrawals.index') }}" class="sidebar-link {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                            <i class="bi bi-cash-stack"></i>
                            <span class="link-text ms-6">Retiros</span>
                            @if($pendingWithdrawals > 0)
                                <span class="badge bg-warning rounded-pill ms-auto">{{ $pendingWithdrawals }}</span>
                            @endif
                        </a>

                    @endif

                    <!-- OPORTUNIDADES (Para participantes) -->
                    @if(!auth()->user()->hasAnyRole(['Administrador', 'Organizador']))
                        <div class="sidebar-category">Oportunidades</div>
                        <hr class="sidebar-divider">

                        <a href="{{ route('role-requests.create') }}" class="sidebar-link {{ request()->routeIs('role-requests.create') ? 'active' : '' }}">
                            <i class="bi bi-person-plus-fill"></i>
                            <span class="link-text ms-6">Ser Organizador</span>
                        </a>

                        <a href="{{ route('role-requests.my-requests') }}" class="sidebar-link {{ request()->routeIs('role-requests.my-requests') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i>
                            <span class="link-text ms-6">Mis Solicitudes</span>
                        </a>
                    @endif

                    <!-- CONTRIBUCIONES -->
                    <div class="sidebar-category">Contribuciones</div>
                    <hr class="sidebar-divider">

                    <a href="{{ route('offers.public') }}" class="sidebar-link {{ request()->routeIs('offers.public') ? 'active' : '' }}">
                        <i class="bi bi-search"></i>
                        <span class="link-text ms-6">Ofertas</span>
                    </a>

                    <a href="{{ route('proposals.my_proposals') }}" class="sidebar-link {{ request()->routeIs('proposals.my_proposals') ? 'active' : '' }}">
                        <i class="bi bi-send"></i>
                        <span class="link-text ms-6">Mis Propuestas</span>
                    </a>

                    <!-- MI CUENTA -->
                    <div class="sidebar-category">Mi Cuenta</div>
                    <hr class="sidebar-divider">

                    <a href="{{ route('professional-profile.show') }}" class="sidebar-link {{ request()->routeIs('professional-profile.show') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        <span class="link-text ms-6">Perfil Profesional</span>
                    </a>

                    @if(auth()->user()->hasRole('Organizador'))
                        <a href="{{ route('paypal.index') }}" class="sidebar-link {{ request()->routeIs('paypal.*') ? 'active' : '' }}">
                            <i class="bi bi-star-fill"></i>
                            <span class="link-text ms-6">
                                @if(auth()->user()->hasActiveSubscription())
                                    Mi Suscripción
                                @else
                                    Suscríbete
                                @endif
                            </span>
                            @if(!auth()->user()->hasActiveSubscription())
                                <span class="badge bg-warning rounded-pill ms-auto">Pro</span>
                            @endif
                        </a>
                    @endif

                    <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        <span class="link-text ms-6">Configuración</span>
                    </a>
                    @else
                        <a href="{{ route('password.force-change') }}" class="sidebar-link {{ request()->routeIs('password.force-change') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span class="link-text ms-6">Actualizar contraseña</span>
                        </a>
                    @endif

                    
                <form method="POST" action="{{ route('logout') }}" class="m-0" id="logout-form">
                    @csrf
                </form>

                <a href="#" class="sidebar-link bg-transparent border-0 w-100" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="link-text ms-6">Cerrar Sesión</span>
                </a>

            @endauth
        </div>

        <!-- USER CARD -->
        @auth
        <div class="mt-auto p-3">
            <div class="user-card bg-white rounded-3 p-2 d-flex align-items-center gap-3 shadow-sm">
                @if(Auth::user()->profile_photo)
                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                        class="rounded-circle border border-2 border-light"
                        width="42" height="42"
                        style="object-fit: cover;"
                        alt="Profile photo">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0d6efd&color=fff"
                        class="rounded-circle border border-2 border-light"
                        width="42" height="42"
                        alt="Default avatar">
                @endif
                <div class="overflow-hidden user-info-text lh-1">
                    <div class="text-dark fw-bold small text-truncate">{{ Auth::user()->name }}</div>
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
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3 d-md-none text-center" style="height: 70px;">
            <button class="btn btn-outline-secondary me-3" id="mobileToggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="fw-bold text-uppercase text-center text-dark h5 mb-0">
                @yield('header') 
            </div>
        </nav>

        <main class="container-fluid p-4">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>

@stack('scripts')
</body>
</html>