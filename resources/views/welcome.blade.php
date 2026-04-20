<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librería ZAPOTECA - Tu Próxima Historia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --zapoteca-dark: #4b1c71;
            --zapoteca-light: #7f4ca5;
            --purple-500: #b57edc;
            --purple-100: #fff0ff;
            --text-dark: #2d1f3a;
            --text-muted: #7a6a88;
            --white: #ffffff;
            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'Archivo', sans-serif;
            --shadow: 0 15px 35px rgba(75, 28, 113, 0.15);
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(181,126,220,.12), transparent 40%),
                radial-gradient(circle at bottom left, rgba(127,76,165,.08), transparent 40%),
                linear-gradient(180deg, #f8f2fb 0%, #ffffff 100%);
            font-family: var(--font-body);
            color: var(--text-dark);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            padding: 20px 0;
            background: transparent;
        }

        .navbar-brand img {
            height: 65px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .nav-link {
            color: var(--zapoteca-dark);
            font-weight: 600;
            text-transform: uppercase;
            font-family: var(--font-display);
            letter-spacing: 1.5px;
            margin: 0 12px;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--purple-500);
        }

        .btn-nav {
            font-family: var(--font-display);
            font-weight: 600;
            letter-spacing: 1px;
            padding: 10px 24px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: 0.95rem;
        }

        .btn-login-nav {
            background-color: var(--zapoteca-dark);
            color: white !important;
            box-shadow: 0 4px 15px rgba(75, 28, 113, 0.2);
        }

        .btn-login-nav:hover {
            background-color: var(--zapoteca-light);
            transform: translateY(-2px);
            color: white !important;
        }

        .btn-signup-nav {
            background-color: transparent;
            color: var(--zapoteca-dark) !important;
            border: 2px solid var(--zapoteca-dark);
        }

        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 32px;
            border: 1px solid rgba(181, 126, 220, 0.3);
            padding: 45px 50px 55px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .hero-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--zapoteca-dark), var(--zapoteca-light));
        }

        /* Alerta */
        .zapoteca-alert {
            position: relative;
            z-index: 30;
            background: #f6ecfb;
            border: 2px solid var(--zapoteca-light);
            color: var(--zapoteca-dark);
            border-radius: 24px;
            padding: 24px 20px;
            margin-bottom: 45px;
            animation: slideDown 0.5s ease-out;
            box-shadow: 0 8px 18px rgba(75, 28, 113, 0.08);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content {
            position: relative;
            z-index: 10;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: 5rem;
            color: var(--zapoteca-dark);
            line-height: 0.85;
            margin-bottom: 10px;
        }

        .hero-subtitle {
            color: var(--zapoteca-light);
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 3px;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        .hero-text {
            color: var(--text-muted);
            font-size: 1.15rem;
            line-height: 1.7;
            margin-bottom: 45px;
            max-width: 90%;
        }

        .btn-zapoteca-main {
            background-color: var(--zapoteca-dark);
            color: white;
            border: none;
            padding: 16px 50px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 1px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px rgba(75, 28, 113, 0.25);
            text-decoration: none;
            display: inline-block;
        }

        .btn-zapoteca-main:hover {
            background-color: var(--zapoteca-light);
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(75, 28, 113, 0.3);
            color: white;
        }

        .img-container {
            position: relative;
            min-height: 520px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            z-index: 1;
        }

        .blob-bg {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 540px;
            height: auto;
            z-index: 0;
            opacity: 0.18;
            fill: var(--purple-500);
            pointer-events: none;
        }

        .main-logo-hero {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 390px;
            height: auto;
            filter: drop-shadow(0 20px 35px rgba(75, 28, 113, 0.16));
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-18px); }
        }

        .bebas {
            font-family: var(--font-display);
        }

        @media (max-width: 991px) {
            .hero-card {
                padding: 35px 22px 40px;
                text-align: center;
            }

            .hero-title {
                font-size: 3.5rem;
            }

            .hero-text {
                max-width: 100%;
            }

            .img-container {
                min-height: 360px;
                margin-bottom: 25px;
            }

            .blob-bg {
                max-width: 380px;
                top: 50%;
            }

            .main-logo-hero {
                max-width: 280px;
            }

            .zapoteca-alert {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Zapoteca">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="#">Catálogo</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Novedades</a></li>

                @guest
                    <li class="nav-item ms-lg-4">
                        <a href="{{ route('login') }}" class="btn-nav btn-login-nav">
                            <i class="fa-solid fa-user me-2"></i> INICIAR SESIÓN
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a href="{{ route('register') }}" class="btn-nav btn-signup-nav">CREAR CUENTA</a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item dropdown ms-lg-4">
                        <a class="nav-link dropdown-toggle btn-nav btn-login-nav text-white px-4" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user me-2"></i> HOLA, {{ explode(' ', Auth::user()->persona->nombre)[0] }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" aria-labelledby="userDropdown" style="border-radius: 15px;">
                            @if(in_array(auth()->user()->rol_id, [1, 2]))
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                        <i class="fa-solid fa-chart-line me-2"></i> Dashboard (Panel)
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger bg-transparent border-0 w-100 text-start">
                                        <i class="fa-solid fa-power-off me-2"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="hero-section">
    <div class="container">
        <div class="hero-card">

            @if (session('status') || session('info') || session('success'))
                <div class="zapoteca-alert text-center">
                    <i class="fa-solid fa-circle-check mb-2" style="font-size: 2.5rem; color: var(--zapoteca-dark);"></i>
                    <h4 class="bebas mb-2">¡Aviso de Zapoteca!</h4>
                    <p class="mb-0 fw-bold">{{ session('status') ?? session('info') ?? session('success') }}</p>
                </div>
            @endif

            <div class="row align-items-center hero-content">
                <div class="col-lg-6 order-2 order-lg-1">
                    @auth
                        <h1 class="hero-title">¡HOLA DE <br>NUEVO!</h1>
                        <h2 class="hero-subtitle">{{ Auth::user()->persona->nombre }}</h2>
                    @else
                        <h1 class="hero-title">LIBRERÍA <br>ZAPOTECA</h1>
                        <h2 class="hero-subtitle">La sabiduría de leer</h2>
                    @endauth

                    <p class="hero-text">
                        Explora una colección curada de historias que trascienden el tiempo. Desde clásicos inmortales hasta las novedades más esperadas, encuentra tu próximo libro favorito.
                    </p>

                    <a href="#" class="btn-zapoteca-main">
                        EXPLORAR CATÁLOGO <i class="fa-solid fa-arrow-right-long ms-2"></i>
                    </a>
                </div>

                <div class="col-lg-6 order-1 order-lg-2 img-container mb-4 mb-lg-0">
                    <svg class="blob-bg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <path d="M42.7,-65.3C55.9,-58.5,67.6,-48.5,74.1,-35.8C80.6,-23.1,81.9,-7.7,78.7,6.8C75.5,21.3,67.8,34.9,57.7,46.1C47.6,57.3,35,66.1,21,71.1C7,76.1,-8.3,77.3,-22.4,73.5C-36.5,69.7,-49.4,60.9,-58.5,49.2C-67.6,37.5,-72.9,22.9,-73.8,8.2C-74.7,-6.5,-71.2,-21.3,-63.3,-33.6C-55.4,-45.9,-43.1,-55.7,-30.2,-62.7C-17.3,-69.7,-3.8,-73.9,10,-75.6C23.8,-77.3,37.5,-76.5,42.7,-65.3Z" transform="translate(100 100)" />
                    </svg>

                    <img src="{{ asset('img/logo.png') }}" alt="Zapoteca Ilustración" class="main-logo-hero">
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="text-center py-4" style="color: var(--zapoteca-dark); opacity: 0.7; font-size: 0.85rem;">
    <p class="bebas m-0">© {{ date('Y') }} - LIBRERÍA ZAPOTECA | SOFTWARE SOLUTIONS</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>