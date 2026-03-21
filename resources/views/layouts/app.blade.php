<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Librería Zapote'))</title>

    <style>
        @font-face{
            font-family:'Bebas Neue';
            src:url('{{ asset('fonts/bebasneue/BebasNeue-Regular.ttf') }}') format('truetype');
            font-weight:400;
            font-style:normal;
            font-display:swap;
        }

        @font-face{
            font-family:'Archivo';
            src:url('{{ asset('fonts/archivo/Archivo-Regular.ttf') }}') format('truetype');
            font-weight:400;
            font-style:normal;
            font-display:swap;
        }

        :root{
            --purple-900:#4b1c71;
            --purple-700:#7f4ca5;
            --purple-500:#b57edc;
            --purple-300:#dbb6ee;
            --purple-100:#fff0ff;

            --dark:#0b0b10;
            --dark-soft:#171320;
            --dark-purple:#241533;

            --text-light:#f5ecfb;
            --text-soft:#d9cbe6;
            --muted:#8c7a9e;
            --card:#ffffff;
            --border:#eadcf2;

            --font-display:'Bebas Neue', sans-serif;
            --font-body:'Archivo', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        html, body{
            font-family:var(--font-body);
            background:linear-gradient(180deg, #fbf7fd 0%, #f8f2fb 100%);
            color:#111;
        }

        .bebas{
            font-family:var(--font-display);
            letter-spacing:.5px;
        }

        /* ========= NAVBAR GENERAL ========= */
        .navbar-purple{
            background:linear-gradient(90deg, var(--dark) 0%, var(--dark-soft) 45%, var(--purple-900) 100%);
            box-shadow:0 8px 24px rgba(0,0,0,.18);
        }

        .navbar-brand{
            font-family:var(--font-display);
            letter-spacing:1px;
            font-weight:700;
            color:#fff !important;
            font-size:1.4rem;
        }

        .nav-link{
            color:var(--text-light)!important;
            opacity:.92;
            font-weight:500;
            transition:.2s ease;
        }

        .nav-link:hover{
            opacity:1;
            color:#fff !important;
        }

        .navbar-toggler{
            border-color:rgba(255,255,255,.35);
        }

        .navbar-toggler-icon{
            filter:invert(1);
        }

        /* ===================== BUSCADOR ===================== */
        .nav-search .search-wrap{
            background:#fff;
            border-radius:18px;
            padding:.5rem;
            box-shadow:0 10px 28px rgba(0,0,0,.10);
            max-width:720px;
            margin-inline:auto;
            border:1px solid rgba(181,126,220,.18);
        }

        .nav-search input{
            border:0;
            font-family:var(--font-body);
            color:#2d1f3a;
        }

        .nav-search input::placeholder{
            color:#8b7b99;
        }

        .nav-search .btn{
            height:42px;
            border-radius:12px;
            font-weight:700;
            background:linear-gradient(135deg, var(--purple-700), var(--purple-900));
            border:0;
            box-shadow:0 8px 18px rgba(75,28,113,.18);
        }

        .nav-search .btn:hover{
            background:linear-gradient(135deg, var(--purple-900), var(--purple-700));
        }

        @media (max-width: 991.98px){
            .nav-search .search-wrap{
                max-width:100%;
            }
        }

        /* ===================== CARRITO ===================== */
        .cart-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:.5rem;
            padding:.45rem .65rem;
            border:1px solid rgba(255,255,255,.28);
            border-radius:13px;
            color:#fff;
            background:rgba(255,255,255,.03);
            position:relative;
            transition:.2s ease;
        }

        .cart-btn:hover{
            background:rgba(255,255,255,.10);
            color:#fff;
        }

        .badge-cart{
            position:absolute;
            top:-6px;
            right:-6px;
            font-size:.70rem;
            border-radius:999px;
            padding:.15rem .38rem;
            background:var(--purple-700);
            color:#fff;
            box-shadow:0 4px 10px rgba(0,0,0,.18);
        }

        .navbar{
            z-index:1035;
        }

        .navbar .container,
        .navbar .row,
        .navbar [class*="col-"]{
            overflow:visible;
        }

        .dropdown-menu{
            z-index:1080;
            border:none;
            border-radius:14px;
            box-shadow:0 18px 40px rgba(75,28,113,.14);
            overflow:hidden;
        }

        .dropdown-item:active{
            background:var(--purple-700);
        }

        .search-wrap input:focus{
            box-shadow:none !important;
            outline:0 !important;
        }

        .navbar .row{
            --bs-gutter-x:1rem;
        }

        /* Frase central */
        .slogan-1{
            color:var(--purple-500);
            font-weight:800;
            text-transform:uppercase;
        }

        .slogan-2{
            color:var(--text-soft);
        }

        /* Botón Inicio */
        .btn-home{
            color:#fff;
            border:0;
            background:linear-gradient(135deg, var(--purple-700), var(--purple-500));
            padding:.45rem .8rem;
            border-radius:.6rem;
        }

        .btn-home:hover{
            filter:brightness(.96);
            color:#fff;
        }

        .navbar{
            z-index:1040;
        }

        .navbar .container,
        .navbar .container-fluid,
        .navbar .row,
        .navbar [class*="col-"]{
            overflow:visible;
        }

        .dropdown-menu{
            z-index:1080;
        }
        .logo-navbar-small {
            height: 32px;
            width: auto;
            object-fit: contain;

            filter: drop-shadow(0 2px 4px rgba(75, 28, 113, 0.15));
        }

        .logo-text {
            color: #dbb6ee;
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1;
        }

        /* Efecto al pasar el mouse por toda la marca */
        .navbar-brand:hover .logo-navbar-small {
            transform: rotate(-5deg) scale(1.1);
            transition: transform 0.2s ease;
        }
    </style>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<div id="app">

    {{-- NAVBAR UNIFICADO --}}
    <nav class="navbar navbar-expand-lg navbar-purple sticky-top">
        <div class="container">
            <div class="row align-items-center w-100 g-2">

                {{-- Izquierda: Marca con Logo Pequeño --}}
                <div class="col-6 col-lg-3 order-1">
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                        <img src="{{ asset('img/logo_nav.png') }}" alt="Logo Nav" class="logo-navbar-small me-2">
                        <span class="bebas logo-text">
            LIBRERÍA ZAPOTECA
        </span>
                    </a>
                </div>

                {{-- Móvil: Carrito + Hamburguesa --}}
                <div class="col-6 d-lg-none order-2 d-flex justify-content-end align-items-center gap-2">

                    <button class="position-relative cart-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#cartModal">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="badge-cart">{{ $cartCount ?? 0 }}</span>
                    </button>

                    <button class="navbar-toggler" type="button"
                            data-bs-toggle="collapse" data-bs-target="#navMain">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                {{-- Centro: Barra de búsqueda --}}
                <div class="col-12 col-lg-6 order-4 order-lg-2 nav-search">
                    <form class="search-wrap d-flex align-items-center gap-2" method="GET" action="">
                        <input type="search" name="q" class="form-control"
                               placeholder="Buscar libros (ej. Los Hornos de Hitler, It, Frankenstein)...">
                        <button class="btn btn-primary px-3" type="submit">Buscar</button>
                    </form>
                </div>

                {{-- Derecha --}}
                <div class="col-12 col-lg-3 order-3 order-lg-3">
                    <div id="navMain" class="collapse navbar-collapse justify-content-lg-end">

                        <ul class="navbar-nav align-items-lg-center gap-lg-3 ms-lg-2">

                            <li class="nav-item">
                                <a class="nav-link" href="#contacto">Contacto</a>
                            </li>

                            @auth
                                @role('cliente')
                                <li class="nav-item">
                                    <a class="nav-link" href="">Catálogo</a>
                                </li>
                                @endrole
                            @endauth

                            <li class="nav-item d-none d-lg-inline">
                                <button class="position-relative cart-btn" data-bs-toggle="modal" data-bs-target="#cartModal">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <span class="badge-cart">{{ $cartCount ?? 0 }}</span>
                                </button>
                            </li>

                            @guest
                                <li class="nav-item"><a class="nav-link" href="">Ingresar</a></li>
                                <li class="nav-item"><a class="nav-link" href="{">Registrarse</a></li>
                            @else
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                        {{ Auth::user()->name }}
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        @if(auth()->user()->rol && auth()->user()->rol->nombre == 'Administrador')
                                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                            <li><hr></li>
                                        @endif

                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                Cerrar sesión
                                            </a>
                                        </li>
                                    </ul>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            @endguest

                        </ul>

                    </div>
                </div>

            </div>
        </div>
    </nav>



    <main class="py">
        @yield('content')
    </main>

    @stack('scripts')
</div>
</body>
</html>
