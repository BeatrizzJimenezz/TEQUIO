<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TEQUIO</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/Welcome.css') }}" rel="stylesheet">
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-blue-deep fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <img src="{{ asset('img/10.svg') }}" alt="TEQUIO Logo" height="45" >
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link transition" href="#why-evai">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link transition" href="#services">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link transition" href="#caracteristicas">Caracteristicas</a></li>
                    @guest
                        <li class="nav-item ms-lg-3"> 
                            <a class="btn btn-outline-light btn-sm px-3 rounded-pill transition" href="{{ route('login') }}">
                                Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item ms-2"> 
                            <a class="btn btn-evai-green btn-sm px-3 rounded-pill transition" href="{{ route('register') }}">
                                Registrarse
                            </a>
                        </li>
                    @endguest

                    @auth
                        <li class="nav-item ms-lg-3 dropdown" style="color: #C8CCC9;">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle" style="color: #C8CCC9;"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end bg-blue-deep" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item text-white transition" href="{{ route('dashboard') }}">Mi Dashboard</a></li>
                                <li><hr class="dropdown-divider bg-secondary"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-white transition">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section pt-5">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="display-3 fw-bold mb-3">
                        Tu espacio para gestionar eventos y actividades de forma ágil e integrada.
                    </h1>
                    <p class="lead mb-4" style="color: #C8CCC9;">
                        La plataforma que centraliza inscripciones, pagos, ponencias y reportes, garantizando eficiencia y seguridad para tus eventos universitarios.
                    </p>
                    <div class="d-flex gap-3 mt-4">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-lg btn-evai-green">
                        <i class="bi bi-lightning-charge-fill"></i>    
                        Empezar
                        </a>
                    @endguest
                        <a  class="btn btn-lg btn-outline-evai">
                            <i class="bi bi-calendar-event"></i> Ver eventos
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <img src="{{ asset('img/7.svg') }}" alt="EVAi Illustration" class="img-fluid" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </section>

    @auth
    <section id="ask-admin" class="py-5" style="background-color: #C8CCC9;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold mb-3">
                        <span class="color-blue-main">¿Quieres ser organizador?</span> Transforma tu gestión.
                    </h2>
                    <p class="lead">
                        Deja atrás las hojas de cálculo. Centraliza usuarios, roles, eventos y transacciones en una sola plataforma segura.
                    </p>
                </div>
                <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0">
                    <a href="" class="btn btn-lg btn-evai-green me-2">
                        <i class="bi bi-lightning-charge"></i> Solicitar nivel de organizador
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endauth

    <section id="why-evai" class="py-5" style="background-color: #FFFFFF;">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 offset-lg-2">
                    <h2 class="display-5 fw-bold pb-2" style="color: #0C2340;">¿Por qué <span class="color-blue-main">TEQUIO</span>?</h2>
                    <p class="lead" style="color: #585858;">Nuestro nombre resume nuestra misión:</p>
                    <p class="lead" style="color: #585858;">Eventos y actividades academicas integradas en tu gestión.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="text-center p-2 h-100">
                        <i class="bi bi-calendar-event color-green-accent" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 fw-bold" style="color: #0C2340;">Eventos</h4>
                        <p class="text-muted small">Gestión completa y centralizada de congresos, ponencias y talleres. Control de foros, horarios y cupos.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="text-center p-2 h-100">
                        <i class="bi bi-bar-chart-line color-blue-main" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 fw-bold" style="color: #0C2340;">Visión</h4>
                        <p class="text-muted small">Reportes en tiempo real de asistencia, inscripciones y pagos, dando a los organizadores información clave.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="text-center p-2 h-100">
                        <i class="bi bi-mortarboard color-green-accent" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 fw-bold" style="color: #0C2340;">Academicos</h4>
                        <p class="text-muted small">Plataforma diseñada específicamente para las necesidades y la estructura de instituciones universitarias.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="text-center p-2 h-100">
                        <i class="bi bi-plug-fill color-blue-main" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 fw-bold" style="color: #0C2340;">Integrados</h4>
                        <p class="text-muted small">Unificamos pagos seguros, registro de usuarios y el flujo de propuestas en un solo lugar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="py-5" style="background-color: #C8CCC9;">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 offset-lg-2">
                    <h2 class="display-5 fw-bold" style="color: #0C2340;">Nuestros<span class="color-blue-main"> Servicios</span></h2>
                    <p class="lead" style="color: #585858;">Centralizamos los procesos desde la convocatoria hasta el reporte final en un entorno 100% digital, cubriendo todos los roles.</p>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-credit-card color-green-accent" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold" style="color: #0C2340;">Pasarela de Pagos Segura</h4>
                            <p class="text-muted small">Integración para transacciones seguras en línea y validación automática.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-code color-blue-main" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold" style="color: #0C2340;">Gestión de Ponencias/Talleres</h4>
                            <p class="text-muted small">Panel para envío, evaluación y notificación de propuestas de ponencias y talleres.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-graph-up color-green-accent" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold" style="color: #0C2340;">Analítica y Reportes Clave</h4>
                            <p class="text-muted small">Generación de métricas de participación, asistencia y pagos para toma de decisiones.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-person-gear color-blue-main" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold" style="color: #0C2340;">Control de Usuarios y Roles</h4>
                            <p class="text-muted small">Administración de usuarios, roles (Organizador, Ponente, Participante) y permisos específicos.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-person-check color-green-accent" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold" style="color: #0C2340;">Inscripción y Cupos en Vivo</h4>
                            <p class="text-muted small">Registro simple de participantes con validación de cupos y resolución de conflictos de horario.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-sliders color-blue-main" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold" style="color: #0C2340;">Configuración Modular</h4>
                            <p class="text-muted small">Define categorías y acceso (público/privado, gratuito/pago) de cada evento.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="caracteristicas" class="py-5 bg-blue-deep text-white">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 offset-lg-2">
                    <h2 class="display-5 fw-bold">Caracteristicas</h2>
                    <p class="lead" style="color: #C8CCC9;">EVAi se adapta a las necesidades de cada perfil: desde el ponente hasta el administrador.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 rounded border h-100 feature-card" style="border-color: #4499BB !important; background-color: #4499BB;">
                        <i class="bi bi-person-workspace color-deepblue-main" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-3 fw-bold">Gestión para Administradores</h5>
                        <p class="small" style="color: #C8CCC9;">Control total sobre usuarios, roles, permisos y la configuración global de la plataforma.</p>
                        <ul class="list-unstyled small ps-3">
                            <li style="color: #C8CCC9;"><i class="bi bi-check-circle-fill color-green-accent me-2"></i>Edición de Roles y Permisos (RF01)</li>
                            <li style="color: #C8CCC9;"><i class="bi bi-check-circle-fill color-green-accent me-2"></i>Registro de Auditorías (RF06.4)</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="p-4 rounded border h-100 feature-card" style="border-color: #4499BB !important; background-color: #4499BB;">
                        <i class="bi bi-person-video color-green-accent" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-3 fw-bold">Módulo de Ponencias y Talleres</h5>
                        <p class="small" style="color: #C8CCC9;">Simplifica el proceso de postulación y evaluación de contenidos académicos.</p>
                        <ul class="list-unstyled small ps-3">
                            <li style="color: #C8CCC9;"><i class="bi bi-check-circle-fill color-green-accent me-2"></i>Envío y Edición de Propuestas (RF02)</li>
                            <li style="color: #C8CCC9;"><i class="bi bi-check-circle-fill color-green-accent me-2"></i>Notificación de Estado Automática (RF02.7)</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-12 col-lg-4">
                    <div class="p-4 rounded border h-100 feature-card" style="border-color: #4499BB !important; background-color: #4499BB;">
                        <i class="bi bi-people color-deepblue-main" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-3 fw-bold">Experiencia del Participante</h5>
                        <p class="small" style="color: #C8CCC9;">Garantiza una inscripción fácil, segura y transparente en todas las actividades.</p>
                        <ul class="list-unstyled small ps-3">
                            <li style="color: #C8CCC9;"><i class="bi bi-check-circle-fill color-green-accent me-2"></i>Control de Cupos en Tiempo Real (RF03.6)</li>
                            <li style="color: #C8CCC9;"><i class="bi bi-check-circle-fill color-green-accent me-2"></i>Pasarela de Pagos PCI DSS (RNF02.2)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5 mt-0" style="background-color: #0D0D0D !important;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/10.svg') }}" alt="EVAi Logo" height="40" class="me-3">
                    </div>
                    <p class="text-light mb-4 opacity-75" style="color: #C8CCC9 !important;">
                        Conectamos la academia con la tecnología. Gestión eficiente de eventos en entornos universitarios.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-5 opacity-75 hover-text-accent transition">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="text-white fs-5 opacity-75 hover-text-accent transition">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="text-white fs-5 opacity-75 hover-text-accent transition">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold mb-3 color-blue-main">Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none opacity-75 hover-text-accent transition">
                                <i class="bi bi-chevron-right me-2 small color-green-accent"></i>Eventos
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none opacity-75 hover-text-accent transition">
                                <i class="bi bi-chevron-right me-2 small color-green-accent"></i>Funcionalidades
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none opacity-75 hover-text-accent transition">
                                <i class="bi bi-chevron-right me-2 small color-green-accent"></i>Contacto
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold mb-3 color-blue-main">Soporte</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none opacity-75 hover-text-accent transition">
                                <i class="bi bi-question-circle me-2 color-green-accent"></i>Centro de Ayuda
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none opacity-75 hover-text-accent transition">
                                <i class="bi bi-shield-check me-2 color-green-accent"></i>Política de Privacidad
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-light text-decoration-none opacity-75 hover-text-accent transition">
                                <i class="bi bi-file-text me-2 color-green-accent"></i>Términos de Servicio
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="my-4 border-secondary opacity-50">

            <div class="row align-items-center small">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75" style="color: #C8CCC9 !important;">
                        <i class="bi bi-code-slash color-blue-main me-1"></i>
                        Hecho por alumnos de Ingeniería de Software 2025 (FMO UES).
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-light opacity-75" style="color: #C8CCC9 !important;">
                        <i class="bi bi-c-circle me-1"></i>
                        {{ date('Y') }} TEQUIO. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>