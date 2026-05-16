@extends('pdf.layout')

@section('contenido')

{{-- ── ESTADÍSTICAS ──────────────────────────────────────────────────── --}}
<p class="stats-title">Estadísticas Generales</p>
<table class="stats-grid">
    <tr>
        <td>
            <span class="stat-value">${{ number_format($totalInversion, 2) }}</span>
            <span class="stat-label">Total invertido</span>
        </td>
        <td>
            <span class="stat-value">{{ $cantidadCompras }}</span>
            <span class="stat-label">Compras realizadas</span>
        </td>
        <td>
            <span class="stat-value">${{ number_format($promedioCompra, 2) }}</span>
            <span class="stat-label">Promedio por compra</span>
        </td>
        <td>
            <span class="stat-value">${{ number_format($maxCompra, 2) }}</span>
            <span class="stat-label">Compra máxima</span>
        </td>
        <td>
            <span class="stat-value">${{ number_format($minCompra, 2) }}</span>
            <span class="stat-label">Compra mínima</span>
        </td>
    </tr>
</table>

{{-- ── GRÁFICA ───────────────────────────────────────────────────────── --}}
<p class="stats-title">Inversión por Mes</p>
<div class="pdf-chart-wrap">
    <img src="{{ $chartBase64 }}" alt="Gráfica de compras por mes">
</div>

{{-- ── TABLA DE COMPRAS ──────────────────────────────────────────────── --}}
<p class="section-title">Listado de Compras</p>
<table class="pdf-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Folio Factura</th>
            <th>Proveedor</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $i => $fila)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="fw-bold text-purple">{{ $fila['folio'] }}</td>
            <td class="text-left">{{ $fila['proveedor'] }}</td>
            <td>
                @if($fila['estado'] === 'Recibida')
                    <span class="badge-procesado">{{ $fila['estado'] }}</span>
                @elseif($fila['estado'] === 'Pendiente')
                    <span class="badge-pendiente">{{ $fila['estado'] }}</span>
                @else
                    <span class="badge-cancelada">{{ $fila['estado'] }}</span>
                @endif
            </td>
            <td class="fw-bold">{{ $fila['total'] }}</td>
            <td>{{ $fila['fecha'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ── TOTAL FINAL ───────────────────────────────────────────────────── --}}
<table class="pdf-totals">
    <tr>
        <td style="width:65%;"></td>
        <td style="width:20%;">Total de registros: {{ $cantidadCompras }}</td>
        <td style="width:15%;"></td>
    </tr>
    <tr class="total-row">
        <td style="width:65%; background:transparent;"></td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px;">TOTAL INVERTIDO:</td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px; font-size:13px;">
            ${{ number_format($totalInversion, 2) }}
        </td>
    </tr>
</table>

@endsection
