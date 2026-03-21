@extends('layouts.app')

@section('content')

    <style>
        :root{
            --purple-900:#4b1c71;
            --purple-700:#7f4ca5;
            --purple-500:#b57edc;
            --purple-300:#dbb6ee;
            --purple-100:#fff0ff;

            --text-dark:#2d1f3a;
            --text-muted:#7a6a88;
            --white:#ffffff;
            --card:#ffffff;
            --border:#eadcf2;
            --shadow:0 12px 35px rgba(75, 28, 113, .12);

            --font-display:'Bebas Neue', sans-serif;
            --font-body:'Archivo', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        html,body{
            background:linear-gradient(180deg,#f8f2fb 0%, #fdf9ff 100%);
            font-family:var(--font-body);
            color:var(--text-dark);
        }

        .bebas{
            font-family:var(--font-display);
            letter-spacing:.5px;
        }

        .dashboard-container{
            min-height:calc(100vh - 110px);
            display:flex;
            gap:22px;
            padding:22px;
            background:
                radial-gradient(circle at top right, rgba(181,126,220,.10), transparent 28%),
                radial-gradient(circle at bottom left, rgba(127,76,165,.08), transparent 30%),
                linear-gradient(180deg, #fbf7fd 0%, #f6eefb 100%);
        }
        .sidebar
        {
            width:280px;
            flex-shrink:0;
            color:#fff;
            border-radius:24px;
            padding:22px 18px;
            background:
                linear-gradient(180deg, #a274c8 0%, #8a5db1 35%, #74449d 60%, #5e2d86 82%, #4b1c71 100%);
            box-shadow:
                0 20px 45px rgba(75, 28, 113, .30),
                inset 0 1px 0 rgba(255,255,255,.08);
            display:flex;
            flex-direction:column;
            position:relative;
            overflow:hidden;
        }

        .sidebar::before{
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(180deg, rgba(255,255,255,.08) 0%, rgba(255,255,255,.02) 35%, rgba(0,0,0,.05) 100%);
            pointer-events:none;
        }

        .sidebar > *{
            position:relative;
            z-index:1;
        }

        .sidebar .brand {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            padding: 20px 0 10px;
            width: 100%;
        }

        .sidebar .brand img {
            width: 160px;
            height: auto;
            max-height: 160px;
            object-fit: contain;

            background: transparent;
            border: none;
            padding: 0;

            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.4));

            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .sidebar .brand img:hover {
            transform: scale(1.1);
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.6)) brightness(1.1);
        }

        .sidebar .brand span{
            font-size:1.75rem;
            line-height:1;
            font-weight:800;
            color:#fff;
            text-shadow:0 3px 8px rgba(0,0,0,.18);
        }

        .nav-title{
            font-size:.78rem;
            letter-spacing:.10em;
            text-transform:uppercase;
            margin:12px 8px 8px;
            font-weight:800;
            color:rgba(255,255,255,.72);
        }

        .sidebar a,
        .sidebar button{
            color:#fff;
            text-decoration:none;
            display:flex;
            align-items:center;
            gap:11px;
            padding:12px 14px;
            border-radius:13px;
            font-weight:700;
            transition:all .25s ease;
            margin-bottom:4px;
        }


        .sidebar a i,
        .sidebar button i{
            width:18px;
            text-align:center;
            font-size:.95rem;
        }

        .sidebar a:hover,
        .sidebar button:hover {
            background:rgba(255,255,255,.12);
            transform:translateX(2px);
            color:#fff;
        }

        .sidebar a.active,
        .sidebar button.active {
            background:rgba(255,255,255,.18);
            box-shadow:inset 4px 0 0 #fff, 0 8px 20px rgba(0,0,0,.08);
        }

        .logout{
            margin-top:auto;
            border-top:1px solid rgba(255,255,255,.14);
            padding-top:14px;
        }

        .content{
            flex:1;
            padding:30px;
            border-radius:24px;
            background:linear-gradient(180deg, rgba(255,255,255,.98) 0%, rgba(255,240,255,.92) 100%);
            box-shadow:var(--shadow);
            border:1px solid rgba(181,126,220,.18);
        }

        @media (max-width:991.98px){
            .dashboard-container{
                flex-direction:column;
            }
            .sidebar{
                width:100%;
            }
            .content{
                padding:22px;
            }
        }
    </style>

    <div class="dashboard-container" style="margin-top: 20px;">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('img/logo.png') }}" alt="Logo">
            </div>

            <a href="" class="{{ request()->routeIs('') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span class="bebas">INICIO</span>
            </a>

            <div class="nav-title">Catálogo literario</div>

            <a href="{{route('promociones.index')}}" class="{{ request()->routeIs('promociones.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bookmark"></i>
                <span class="bebas">PROMOCIONES</span>
            </a>

            <a href="{{route('asigna_promociones.index')}}" class="{{ request()->routeIs('asigna_promociones.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bookmark"></i>
                <span class="bebas">ASIGNAR PROMOCIONES</span>
            </a>

            <a href="{{route('clasificaciones.index')}}" class="{{ request()->routeIs('clasificaciones.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i>
                <span class="bebas">CLASIFICACIONES</span>
            </a>

            <a href="{{route('libros.index')}}" class="{{ request()->routeIs('libros.*') ? 'active' : '' }}">
                <i class="fa-solid fa-book"></i>
                <span class="bebas">LIBROS</span>
            </a>

            <div class="nav-title">Inventario</div>

            <a href="" class="{{ request()->routeIs('') ? 'active' : '' }}">
                <i class="fa-solid fa-location-dot"></i>
                <span class="bebas">UBICACIONES</span>
            </a>

            <a href="#" class="{{ request()->routeIs('mermas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box-open"></i>
                <span class="bebas">MERMAS</span>
            </a>

            <form action="{{ route('logout') }}" method="POST" class="logout" style="margin: 0;">
                @csrf
                <button type="submit">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="bebas">SALIR</span>
                </button>
            </form>


        </aside>

        <section class="content">
            @yield('dashboard-content')
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
