@extends('layouts.dashboard')

@section('dashboard-content')
<style>
    .kpi-card {
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid rgba(181, 126, 220, 0.2);
        box-shadow: 0 10px 30px rgba(75, 28, 113, 0.05);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        opacity: 0; /* Para animación con anime.js */
        transform: translateY(20px);
    }

    .kpi-card:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 15px 35px rgba(75, 28, 113, 0.12);
    }

    .kpi-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.8rem;
        flex-shrink: 0;
    }

    .kpi-icon.purple { background: linear-gradient(135deg, var(--purple-700), var(--purple-900)); color: white; }
    .kpi-icon.pink { background: linear-gradient(135deg, #d38ec4, #b57edc); color: white; }
    .kpi-icon.blue { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
    .kpi-icon.orange { background: linear-gradient(135deg, #f6d365, #fda085); color: white; }
    .kpi-icon.red { background: linear-gradient(135deg, #ff0844, #ffb199); color: white; }
    .kpi-icon.green { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }

    .kpi-details h4 {
        margin: 0;
        font-size: 0.95rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
    }

    .kpi-details .kpi-number {
        margin: 0;
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1.2;
    }

    .dashboard-section {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(181, 126, 220, 0.2);
        box-shadow: 0 10px 30px rgba(75, 28, 113, 0.05);
        padding: 24px;
        margin-bottom: 24px;
        opacity: 0;
    }

    .section-title {
        font-family: var(--font-display);
        color: var(--purple-900);
        font-size: 1.8rem;
        margin-bottom: 20px;
        letter-spacing: 1px;
    }

    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table-custom th {
        border: none;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 0 15px 10px;
    }

    .table-custom td {
        background: #fdf9ff;
        border: none;
        padding: 15px;
        vertical-align: middle;
    }

    .table-custom td:first-child { border-radius: 12px 0 0 12px; }
    .table-custom td:last-child { border-radius: 0 12px 12px 0; }

    .table-custom tbody tr {
        transition: transform 0.2s;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .table-custom tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(75, 28, 113, 0.08);
    }

    .badge-stock {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    
    .badge-stock.danger { background: #ffe2e5; color: #f64e60; }
    .badge-stock.success { background: #c9f7f5; color: #1bc5bd; }

</style>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4 dashboard-header" style="opacity:0;">
        <h2 class="bebas" style="font-size: 2.5rem; color: var(--purple-900);">Panel de Control</h2>
        <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</p>
    </div>

    <!-- KPIs Row 1 -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="kpi-card anim-card">
                <div class="kpi-icon purple">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="kpi-details">
                    <h4>Ventas Totales</h4>
                    <p class="kpi-number counter" data-count="{{ $total_ventas }}">0</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="kpi-card anim-card">
                <div class="kpi-icon green">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="kpi-details">
                    <h4>Ingresos</h4>
                    <p class="kpi-number">$<span class="counter" data-count="{{ $monto_vendido }}">0</span></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="kpi-card anim-card">
                <div class="kpi-icon blue">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div class="kpi-details">
                    <h4>Catálogo Libros</h4>
                    <p class="kpi-number counter" data-count="{{ $total_libros }}">0</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="kpi-card anim-card">
                <div class="kpi-icon orange">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div class="kpi-details">
                    <h4>Stock Global</h4>
                    <p class="kpi-number counter" data-count="{{ $stock_total }}">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Row 2 -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="kpi-card anim-card">
                <div class="kpi-icon pink">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="kpi-details">
                    <h4>Clientes</h4>
                    <p class="kpi-number counter" data-count="{{ $total_clientes }}">0</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="kpi-card anim-card">
                <div class="kpi-icon purple">
                    <i class="fa-solid fa-feather-pointed"></i>
                </div>
                <div class="kpi-details">
                    <h4>Autores</h4>
                    <p class="kpi-number counter" data-count="{{ $total_autores }}">0</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="kpi-card anim-card">
                <div class="kpi-icon blue">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div class="kpi-details">
                    <h4>Categorías</h4>
                    <p class="kpi-number counter" data-count="{{ $total_categorias }}">0</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="kpi-card anim-card" style="border-color: #ff0844; background: #fffafb;">
                <div class="kpi-icon red">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="kpi-details">
                    <h4 style="color: #ff0844;">Bajo Stock</h4>
                    <p class="kpi-number counter" style="color: #ff0844;" data-count="{{ $conteo_bajo_stock }}">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="dashboard-section anim-section">
                <h3 class="section-title"><i class="fa-solid fa-chart-line me-2"></i>Ventas (Últimos 7 días)</h3>
                <div style="height: 300px;">
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="dashboard-section anim-section">
                <h3 class="section-title"><i class="fa-solid fa-ranking-star me-2"></i>Top 5 Libros</h3>
                <div style="height: 300px;">
                    <canvas id="topLibrosChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="dashboard-section anim-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="section-title mb-0"><i class="fa-solid fa-clock-rotate-left me-2"></i>Últimas Ventas</h3>
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm" style="background: var(--purple-100); color: var(--purple-900); border-radius: 10px; font-weight: 600;">Ver Todas</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Cajero</th>
                                <th>Fecha</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimas_ventas as $venta)
                            <tr>
                                <td><span class="fw-bold" style="color: var(--purple-700)">#{{ $venta->folio }}</span></td>
                                <td>{{ $venta->usuario ? $venta->usuario->correo : 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                                <td class="text-end fw-bold text-success">${{ number_format($venta->total, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay ventas registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="dashboard-section anim-section" style="border-top: 4px solid #ff0844;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="section-title mb-0" style="color: #ff0844;"><i class="fa-solid fa-triangle-exclamation me-2"></i>Alertas de Inventario</h3>
                    <a href="#" class="btn btn-sm" style="background: #ffe2e5; color: #f64e60; border-radius: 10px; font-weight: 600;">Reabastecer</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Libro (Edición)</th>
                                <th>ISBN</th>
                                <th class="text-center">Existencias</th>
                                <th class="text-center">Stock Mín.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($libros_bajo_stock as $edicion)
                            <tr>
                                <td class="fw-bold text-dark">{{ $edicion->libro ? $edicion->libro->titulo : 'Desconocido' }}</td>
                                <td>{{ $edicion->isbn }}</td>
                                <td class="text-center">
                                    <span class="badge-stock danger">{{ $edicion->existencias }}</span>
                                </td>
                                <td class="text-center text-muted">{{ $edicion->stock_minimo }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Todo el inventario está en niveles óptimos.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. ANIMACIONES CON ANIME.JS
    
    // Header
    anime({
        targets: '.dashboard-header',
        opacity: [0, 1],
        translateY: [-20, 0],
        duration: 800,
        easing: 'easeOutExpo'
    });

    // KPI Cards stagger
    anime({
        targets: '.anim-card',
        opacity: [0, 1],
        translateY: [30, 0],
        delay: anime.stagger(100, {start: 200}),
        duration: 800,
        easing: 'easeOutElastic(1, .8)'
    });

    // Sections stagger
    anime({
        targets: '.anim-section',
        opacity: [0, 1],
        translateY: [40, 0],
        delay: anime.stagger(150, {start: 600}),
        duration: 800,
        easing: 'easeOutExpo'
    });

    // Counters animation
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        let finalValue = parseFloat(counter.getAttribute('data-count'));
        
        anime({
            targets: counter,
            innerHTML: [0, finalValue],
            round: 1, // Redondea a entero
            easing: 'easeOutExpo',
            duration: 2000,
            delay: 400,
            update: function(a) {
                // Formatear con comas para millares si es necesario
                if(finalValue > 999) {
                    counter.innerHTML = Number(counter.innerHTML).toLocaleString('es-MX');
                }
            }
        });
    });

    // Table rows stagger
    anime({
        targets: '.table-custom tbody tr',
        opacity: [0, 1],
        translateX: [-20, 0],
        delay: anime.stagger(100, {start: 1000}),
        duration: 600,
        easing: 'easeOutExpo'
    });

    // 2. CHART.JS INTEGRATION
    
    // Ventas Chart
    const ctxVentas = document.getElementById('ventasChart').getContext('2d');
    
    // Gradiente para el área
    let gradientVentas = ctxVentas.createLinearGradient(0, 0, 0, 400);
    gradientVentas.addColorStop(0, 'rgba(127, 76, 165, 0.5)');   
    gradientVentas.addColorStop(1, 'rgba(127, 76, 165, 0.0)');

    new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_reverse($ultimos_dias)) !!},
            datasets: [{
                label: 'Ingresos ($)',
                data: {!! json_encode(array_reverse($ventas_por_dia)) !!},
                borderColor: '#7f4ca5',
                backgroundColor: gradientVentas,
                borderWidth: 3,
                pointBackgroundColor: '#b57edc',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#4b1c71',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: { callback: function(value) { return '$' + value; } }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            },
            animation: {
                y: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        }
    });

    // Top Libros Chart
    const ctxTop = document.getElementById('topLibrosChart').getContext('2d');
    new Chart(ctxTop, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($nombres_top_libros) !!},
            datasets: [{
                data: {!! json_encode($cantidades_top_libros) !!},
                backgroundColor: [
                    '#4b1c71', '#7f4ca5', '#b57edc', '#dbb6ee', '#f6eefb'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 20 }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1500
            }
        }
    });

});
</script>
@endpush
@endsection
