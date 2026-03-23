@extends('layouts.dashboard')

@section('dashboard-content')
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket de venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background:#f4efff;
            font-family:Arial, Helvetica, sans-serif;
        }
        .ticket-wrapper{
            max-width:420px;
            margin:40px auto;
        }
        .ticket-card{
            background:#ffffff;
            border:none;
            border-radius:24px;
            box-shadow:0 12px 30px rgba(75,29,149,0.12);
            overflow:hidden;
        }
        .ticket-header{
            background:#5b21b6;
            color:#ffffff;
            text-align:center;
            padding:24px 20px;
        }
        .ticket-header h2{
            margin:0;
            font-size:28px;
            font-weight:700;
        }
        .ticket-header p{
            margin:6px 0 0;
            font-size:14px;
        }
        .ticket-body{
            padding:24px;
        }
        .ticket-label{
            font-size:13px;
            color:#6b7280;
            margin-bottom:2px;
        }
        .ticket-value{
            font-size:15px;
            font-weight:600;
            color:#1f2937;
        }
        .divider{
            border-top:2px dashed #d8c7ff;
            margin:18px 0;
        }
        .items-table{
            width:100%;
            font-size:14px;
        }
        .items-table th{
            color:#5b21b6;
            font-weight:700;
            padding-bottom:10px;
        }
        .items-table td{
            padding:8px 0;
            vertical-align:top;
        }
        .item-title{
            font-weight:600;
            color:#1f2937;
        }
        .item-code{
            font-size:12px;
            color:#7c7c7c;
        }
        .totals-box{
            background:#f8f5ff;
            border-radius:16px;
            padding:16px;
        }
        .total-final{
            color:#4b1d95;
            font-size:24px;
            font-weight:700;
        }
        .btn-custom{
            background:#5b21b6;
            border:none;
            color:#ffffff;
            border-radius:999px;
            padding:10px 22px;
        }
        .btn-custom:hover{
            background:#4b1d95;
            color:#ffffff;
        }
        .footer-text{
            text-align:center;
            color:#6b7280;
            font-size:13px;
            margin-top:18px;
        }
        @media print{
            body{
                background:#ffffff;
            }
            .no-print{
                display:none !important;
            }
            .ticket-wrapper{
                max-width:100%;
                margin:0;
            }
            .ticket-card{
                box-shadow:none;
                border-radius:0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket-wrapper">
            <div class="d-flex justify-content-between mb-3 no-print">
                <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-outline-secondary rounded-pill px-4">Regresar</a>
                <button type="button" onclick="window.print()" class="btn btn-custom">Imprimir</button>
            </div>

            <div class="ticket-card">
                <div class="ticket-header">
                    <h2>Zapoteca Librería</h2>
                    <p>Ticket de compra</p>
                </div>

                <div class="ticket-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="ticket-label">Folio</div>
                            <div class="ticket-value">{{ $venta->folio }}</div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="ticket-label">Venta</div>
                            <div class="ticket-value">#{{ $venta->id }}</div>
                        </div>
                        <div class="col-12">
                            <div class="ticket-label">Fecha</div>
                            <div class="ticket-value">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-12">
                            <div class="ticket-label">Atendió</div>
                            <div class="ticket-value">{{ $venta->usuario->name ?? $venta->usuario->nombre ?? 'Sin usuario' }}</div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($venta->detallesVentas as $detalle)
                                <tr>
                                    <td>
                                        <div class="item-title">{{ $detalle->lote->edicion->libro->titulo ?? 'Sin libro' }}</div>
                                        <div class="item-code">Lote: {{ $detalle->lote->codigo ?? 'Sin código' }}</div>
                                    </td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3">No hay productos registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="divider"></div>

                    <div class="totals-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total de productos</span>
                            <span>{{ $venta->detallesVentas->sum('cantidad') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Monto recibido</span>
                            <span>${{ number_format($venta->monto_recibido, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Cambio</span>
                            <span>${{ number_format($venta->cambio, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total</span>
                            <span class="total-final">${{ number_format($venta->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="footer-text">
                        Gracias por tu compra
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
@endsection