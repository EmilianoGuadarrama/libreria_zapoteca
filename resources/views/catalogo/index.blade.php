<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Librería ZAPOTECA</title>
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
        body { background: radial-gradient(circle at top right, rgba(181,126,220,.12), transparent 40%), radial-gradient(circle at bottom left, rgba(127,76,165,.08), transparent 40%), linear-gradient(180deg, #f8f2fb 0%, #ffffff 100%); font-family: var(--font-body); color: var(--text-dark); margin: 0; }
        .bebas { font-family: var(--font-display); letter-spacing: .5px; }
        .navbar { padding: 20px 0; background: transparent; }
        .navbar-brand img { height: 65px; width: auto; transition: transform 0.3s ease; }
        .navbar-brand img:hover { transform: scale(1.05); }
        .nav-link { color: var(--zapoteca-dark); font-weight: 600; text-transform: uppercase; font-family: var(--font-display); letter-spacing: 1.5px; margin: 0 12px; transition: color 0.3s; }
        .nav-link:hover { color: var(--purple-500); }
        .btn-nav { font-family: var(--font-display); font-weight: 600; letter-spacing: 1px; padding: 10px 24px; border-radius: 50px; text-decoration: none; transition: all 0.3s ease; display: inline-block; font-size: 0.95rem; }
        .btn-login-nav { background-color: var(--zapoteca-dark); color: white !important; box-shadow: 0 4px 15px rgba(75, 28, 113, 0.2); }
        .btn-login-nav:hover { background-color: var(--zapoteca-light); transform: translateY(-2px); color: white !important; }
        .btn-signup-nav { background-color: transparent; color: var(--zapoteca-dark) !important; border: 2px solid var(--zapoteca-dark); }

        /* Hero del catálogo */
        .catalog-hero { background: linear-gradient(135deg, var(--zapoteca-dark) 0%, var(--zapoteca-light) 100%); padding: 50px 0 40px; color: white; position: relative; overflow: hidden; }
        .catalog-hero::before { content: ""; position: absolute; top: -50%; right: -10%; width: 500px; height: 500px; background: rgba(255,255,255,0.04); border-radius: 50%; }
        .catalog-hero::after { content: ""; position: absolute; bottom: -30%; left: -5%; width: 350px; height: 350px; background: rgba(255,255,255,0.03); border-radius: 50%; }
        .catalog-hero h1 { font-family: var(--font-display); font-size: 3.5rem; line-height: 0.9; position: relative; z-index: 1; }
        .catalog-hero p { opacity: 0.85; font-size: 1.1rem; position: relative; z-index: 1; }

        /* Barra de búsqueda */
        .search-bar { position: relative; z-index: 2; margin-top: -30px; margin-bottom: 30px; }
        .search-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(12px); border-radius: 20px; padding: 24px 30px; box-shadow: var(--shadow); border: 1px solid rgba(181,126,220,0.2); }
        .search-card .form-control, .search-card .form-select { border-radius: 12px; border: 1.5px solid #eadcf2; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s; }
        .search-card .form-control:focus, .search-card .form-select:focus { border-color: var(--zapoteca-light); box-shadow: 0 0 0 3px rgba(127,76,165,0.12); }
        .btn-search { background: var(--zapoteca-dark); color: white; border: none; border-radius: 12px; padding: 12px 28px; font-weight: 700; transition: all 0.3s; }
        .btn-search:hover { background: var(--zapoteca-light); transform: translateY(-2px); color: white; }
        .btn-clear { background: transparent; color: var(--zapoteca-dark); border: 1.5px solid #eadcf2; border-radius: 12px; padding: 12px 20px; font-weight: 600; transition: all 0.3s; }
        .btn-clear:hover { border-color: var(--zapoteca-light); background: var(--purple-100); }

        /* Grid de libros */
        .book-card { background: white; border-radius: 20px; overflow: hidden; border: 1px solid rgba(181,126,220,0.15); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; height: 100%; display: flex; flex-direction: column; }
        .book-card:hover { transform: translateY(-8px); box-shadow: 0 20px 45px rgba(75,28,113,0.18); border-color: var(--purple-500); }
        .book-cover-wrapper { position: relative; width: 100%; padding-top: 140%; overflow: hidden; background: linear-gradient(180deg, #f8f2fb 0%, #efe4f7 100%); }
        .book-cover-wrapper img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .book-card:hover .book-cover-wrapper img { transform: scale(1.05); }
        .book-cover-wrapper .no-cover { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); }
        .book-cover-wrapper .no-cover i { font-size: 2.5rem; margin-bottom: 8px; color: #cdb7dc; }
        .promo-badge { position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 6px 14px; border-radius: 50px; font-weight: 800; font-size: 0.85rem; box-shadow: 0 4px 12px rgba(231,76,60,0.3); z-index: 2; }
        .book-info { padding: 18px; flex: 1; display: flex; flex-direction: column; }
        .book-info .book-title { font-family: var(--font-display); font-size: 1.25rem; color: var(--zapoteca-dark); margin-bottom: 4px; line-height: 1.1; }
        .book-info .book-author { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px; }
        .book-info .book-genre { font-size: 0.75rem; }
        .book-price-row { margin-top: auto; padding-top: 12px; border-top: 1px solid #f0e6f7; display: flex; align-items: center; justify-content: space-between; }
        .price-original { text-decoration: line-through; color: #aaa; font-size: 0.85rem; }
        .price-final { font-weight: 800; color: var(--zapoteca-dark); font-size: 1.15rem; }
        .stock-badge { font-size: 0.75rem; }

        /* Modal detalle */
        .detail-cover { width: 100%; max-width: 300px; border-radius: 18px; box-shadow: 0 20px 40px rgba(75,28,113,0.18); }
        .detail-cover-empty { width: 100%; max-width: 300px; height: 420px; border-radius: 18px; border: 2px dashed #cfb3e2; background: linear-gradient(180deg, #fbf7ff 0%, #f3e9fb 100%); display: flex; align-items: center; justify-content: center; color: #7a6a88; }
        .detail-info-card { border-radius: 16px; padding: 1rem; }
        .detail-info-card.soft { background: #f8f2fb; }
        .detail-info-card.outline { border: 1px solid #eadcf3; background: #fff; }

        /* Contador de resultados */
        .results-count { color: var(--text-muted); font-weight: 600; }
        .results-count span { color: var(--zapoteca-dark); font-weight: 800; }

        /* Empty state */
        .empty-state { text-align: center; padding: 80px 20px; }
        .empty-state i { font-size: 4rem; color: #cdb7dc; margin-bottom: 20px; }
        .empty-state h4 { color: var(--zapoteca-dark); font-family: var(--font-display); font-size: 2rem; }

        @media (max-width: 767px) {
            .catalog-hero h1 { font-size: 2.5rem; }
            .search-card { padding: 18px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Zapoteca">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="{{ route('catalogo') }}">Catálogo</a></li>
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
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="border-radius: 15px;">
                            @if(in_array(auth()->user()->rol_id, [1, 2]))
                                <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a></li>
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

{{-- Hero --}}
<section class="catalog-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="bebas mb-2">NUESTRO CATÁLOGO</h1>
                <p class="mb-0">Descubre tu próxima gran lectura entre nuestra colección curada de títulos.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('home') }}" class="btn btn-outline-light rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-arrow-left me-2"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Barra de búsqueda --}}
<div class="container search-bar">
    <div class="search-card">
        <form action="{{ route('catalogo') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label fw-bold small text-uppercase" style="color: var(--zapoteca-dark); letter-spacing: 1px;">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
                    </label>
                    <input type="text" name="buscar" class="form-control" placeholder="Título o ISBN..." value="{{ request('buscar') }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label fw-bold small text-uppercase" style="color: var(--zapoteca-dark); letter-spacing: 1px;">
                        <i class="fa-solid fa-layer-group me-1"></i> Género
                    </label>
                    <select name="genero" class="form-select">
                        <option value="">Todos los géneros</option>
                        @foreach($generosCatalogo as $g)
                            <option value="{{ $g->id }}" {{ request('genero') == $g->id ? 'selected' : '' }}>{{ $g->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label fw-bold small text-uppercase" style="color: var(--zapoteca-dark); letter-spacing: 1px;">
                        <i class="fa-solid fa-tags me-1"></i> Clasificación
                    </label>
                    <select name="clasificacion" class="form-select">
                        <option value="">Todas las clasificaciones</option>
                        @foreach($clasificacionesCatalogo as $c)
                            <option value="{{ $c->id }}" {{ request('clasificacion') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-search flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Filtrar</button>
                    <a href="{{ route('catalogo') }}" class="btn btn-clear" title="Limpiar"><i class="fa-solid fa-xmark"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Resultados --}}
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="results-count mb-0"><span>{{ $ediciones->count() }}</span> ediciones encontradas</p>
    </div>

    @if($ediciones->count() > 0)
        <div class="row g-4">
            @foreach($ediciones as $edicion)
                @php
                    $portada = $edicion->edicion_portada ? asset('storage/' . $edicion->edicion_portada) : ($edicion->libro_portada ? asset('storage/' . $edicion->libro_portada) : null);
                    $precio = floatval($edicion->precio_venta);
                    $descuento = floatval($edicion->promo_descuento ?? 0);
                    $precioFinal = $descuento > 0 ? $precio - ($precio * $descuento / 100) : $precio;
                    $autores = isset($autoresPorLibro[$edicion->libro_id]) ? $autoresPorLibro[$edicion->libro_id]->pluck('nombre_completo')->implode(', ') : 'Autor desconocido';
                    $subgeneros = isset($subgenerosPorLibro[$edicion->libro_id]) ? $subgenerosPorLibro[$edicion->libro_id]->pluck('nombre')->toArray() : [];
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="book-card" data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $edicion->edicion_id }}">
                        <div class="book-cover-wrapper">
                            @if($portada)
                                <img src="{{ $portada }}" alt="{{ $edicion->titulo }}" loading="lazy">
                            @else
                                <div class="no-cover">
                                    <i class="fa-solid fa-book-open"></i>
                                    <span class="small">Sin portada</span>
                                </div>
                            @endif
                            @if($descuento > 0)
                                <div class="promo-badge"><i class="fa-solid fa-tag me-1"></i> -{{ intval($descuento) }}%</div>
                            @endif
                        </div>
                        <div class="book-info">
                            <div class="book-title">{{ $edicion->titulo }}</div>
                            <div class="book-author"><i class="fa-solid fa-feather-pointed me-1"></i> {{ Str::limit($autores, 35) }}</div>
                            <div class="book-genre">
                                <span class="badge" style="background: var(--purple-100); color: var(--zapoteca-dark); border: 1px solid #dbb6ee;">{{ $edicion->genero_nombre ?? 'N/A' }}</span>
                            </div>
                            <div class="book-price-row">
                                <div>
                                    @if($descuento > 0)
                                        <span class="price-original">${{ number_format($precio, 2) }}</span><br>
                                    @endif
                                    <span class="price-final">${{ number_format($precioFinal, 2) }}</span>
                                </div>
                                <span class="badge stock-badge {{ $edicion->existencias > 5 ? 'bg-success' : 'bg-warning text-dark' }}">
                                    <i class="fa-solid fa-box me-1"></i>{{ $edicion->existencias }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Detalle --}}
                <div class="modal fade" id="modalDetalle{{ $edicion->edicion_id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                            <div class="modal-header border-0" style="background: linear-gradient(135deg, #4b1c71 0%, #7f4ca5 100%); color: white;">
                                <h5 class="modal-title bebas fs-3"><i class="fa-solid fa-book-open me-2"></i> Detalle del Libro</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 p-lg-5">
                                <div class="row g-4">
                                    <div class="col-lg-4 text-center">
                                        @if($portada)
                                            <img src="{{ $portada }}" alt="{{ $edicion->titulo }}" class="detail-cover img-fluid">
                                        @else
                                            <div class="detail-cover-empty mx-auto">
                                                <div class="text-center">
                                                    <i class="fa-solid fa-image fs-1 mb-2"></i>
                                                    <p class="mb-0 fw-semibold">Sin Portada</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-8">
                                        <h2 class="fw-bold mb-1" style="color: var(--zapoteca-dark);">{{ $edicion->titulo }}</h2>
                                        <p class="text-muted mb-3"><i class="fa-solid fa-feather-pointed me-1"></i> {{ $autores }}</p>

                                        <div class="d-flex flex-wrap gap-2 mb-4">
                                            <span class="badge" style="background: var(--purple-100); color: var(--zapoteca-dark); border: 1px solid #dbb6ee; font-size: 0.9rem;">{{ $edicion->clasificacion_nombre ?? 'N/A' }}</span>
                                            <span class="badge" style="background: #f0e6f7; color: var(--zapoteca-dark); border: 1px solid #cdb7dc; font-size: 0.9rem;">{{ $edicion->genero_nombre ?? 'N/A' }}</span>
                                            @foreach($subgeneros as $sg)
                                                <span class="badge bg-light text-dark border" style="font-size: 0.85rem;">{{ $sg }}</span>
                                            @endforeach
                                        </div>

                                        @if($descuento > 0)
                                            <div class="p-3 rounded-4 mb-4" style="background: linear-gradient(135deg, #fff7e8 0%, #fff2cf 100%); border: 1px solid #f2ddb2;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge bg-danger fs-6 rounded-pill px-3 py-2"><i class="fa-solid fa-tag me-1"></i> -{{ intval($descuento) }}%</span>
                                                    <div>
                                                        <span class="text-muted"><del>${{ number_format($precio, 2) }}</del></span>
                                                        <span class="fw-bold text-success fs-4 ms-2">${{ number_format($precioFinal, 2) }}</span>
                                                    </div>
                                                    <span class="badge bg-success ms-auto">{{ $edicion->promo_nombre }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-4">
                                                <span class="fw-bold fs-3" style="color: var(--zapoteca-dark);">${{ number_format($precio, 2) }}</span>
                                            </div>
                                        @endif

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4"><div class="detail-info-card soft"><div class="small text-muted">Editorial</div><div class="fw-semibold">{{ $edicion->editorial_nombre ?? 'N/A' }}</div></div></div>
                                            <div class="col-md-4"><div class="detail-info-card soft"><div class="small text-muted">Idioma</div><div class="fw-semibold">{{ $edicion->idioma_nombre ?? 'N/A' }}</div></div></div>
                                            <div class="col-md-4"><div class="detail-info-card soft"><div class="small text-muted">Formato</div><div class="fw-semibold">{{ $edicion->formato_nombre ?? 'N/A' }}</div></div></div>
                                            <div class="col-md-3"><div class="detail-info-card outline"><div class="small text-muted">ISBN</div><div class="fw-semibold" style="font-size: 0.85rem;">{{ $edicion->isbn }}</div></div></div>
                                            <div class="col-md-3"><div class="detail-info-card outline"><div class="small text-muted">Edición</div><div class="fw-semibold">{{ $edicion->numero_edicion }}ª</div></div></div>
                                            <div class="col-md-3"><div class="detail-info-card outline"><div class="small text-muted">Páginas</div><div class="fw-semibold">{{ $edicion->numero_paginas }}</div></div></div>
                                            <div class="col-md-3"><div class="detail-info-card outline"><div class="small text-muted">Existencias</div><div class="fw-semibold">{{ $edicion->existencias }} uds.</div></div></div>
                                        </div>

                                        <div class="p-4 rounded-4" style="background: #fff; border: 1px solid #eadcf3;">
                                            <div class="small text-muted fw-bold mb-2 text-uppercase" style="letter-spacing: 0.5px;">Sinopsis</div>
                                            <p class="mb-0" style="line-height: 1.7;">{{ $edicion->sinopsis }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-book-open-reader d-block"></i>
            <h4>No encontramos resultados</h4>
            <p class="text-muted">Intenta con otros filtros o términos de búsqueda.</p>
            <a href="{{ route('catalogo') }}" class="btn btn-search rounded-pill px-4 mt-2">Ver todo el catálogo</a>
        </div>
    @endif
</div>

<footer class="text-center py-4" style="color: var(--zapoteca-dark); opacity: 0.7; font-size: 0.85rem;">
    <p class="bebas m-0">© {{ date('Y') }} - LIBRERÍA ZAPOTECA | SOFTWARE SOLUTIONS</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
