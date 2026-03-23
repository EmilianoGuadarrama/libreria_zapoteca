@extends('layouts.dashboard')

@section('dashboard-content')
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle de venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background:#f4efff;
            font-family:Arial, Helvetica, sans-serif;
        }
        .page-title{
            color:#4b1d95;
            font-weight:700;
        }
        .card-custom{
            border:none;
            border-radius:20px;
            box-shadow:0 10px 25px rgba(75,29,149,0.10);
        }
        .card-header-custom{
            background:#5b21b6;
            color:#fff;
            border-radius:20px 20px 0 0 !important;
            padding:18px 24px;
        }
        .info-label{
            font-size:14px;
            color:#6b7280;
            margin-bottom:4px;
        }
        .info-value{
            font-size:16px;
            font-weight:600;
            color:#1f2937;
        }
        .table thead{
            background:#5b21b6;
            color:#fff;
        }
        .table thead th{
            border:none;
        }
        .table tbody tr:hover{
            background:#f3ebff;
        }
        .btn-primary-custom{
            background:#5b21b6;
            border:none;
            color:#fff;
        }
        .btn-primary-custom:hover{
            background:#4b1d95;
            color:#fff;
        }
        .summary-box{
            background:#f8f5ff;
            border-radius:16px;
            padding:20px;
        }
        .total-text{
            color:#4b1d95;
            font-size:24px;
            font-weight:700;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="page-title mb-0">Detalle de venta</h1>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Regresar</a>
                <a href="{{ route('ventas.ticket', $venta->id) }}" class="btn btn-primary-custom rounded-pill px-4">Ver ticket</a>
            </div>
        </div>

        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom">
                Información general
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="info-label">ID</div>
                        <div class="info-value">{{ $venta->id }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Folio</div>
                        <div class="info-value">{{ $venta->folio }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Fecha</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Usuario</div>
                        <div class="info-value">{{ $venta->usuario->name ?? $venta->usuario->nombre ?? 'Sin usuario' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Monto recibido</div>
                        <div class="info-value">${{ number_format($venta->monto_recibido, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Cambio</div>
                        <div class="info-value">${{ number_format($venta->cambio, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom">
                Productos vendidos
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID detalle</th>
                                <th>Lote</th>
                                <th>Libro</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($venta->detallesVentas as $detalle)
                                <tr>
                                    <td>{{ $detalle->id }}</td>
                                    <td>{{ $detalle->lote->codigo ?? 'Sin lote' }}</td>
                                    <td>{{ $detalle->lote->edicion->libro->titulo ?? 'Sin libro' }}</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No hay detalles registrados para esta venta</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total de productos</span>
                        <span>{{ $venta->detallesVentas->sum('cantidad') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total</span>
                        <span class="total-text">${{ number_format($venta->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
@endsection