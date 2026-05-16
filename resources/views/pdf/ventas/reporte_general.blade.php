@extends('pdf.layout')

@section('contenido')

{{-- ── ESTADÍSTICAS ──────────────────────────────────────────────────── --}}
<p class="stats-title">Estadísticas Generales</p>
<table class="stats-grid">
    <tr>
        <td>
            <span class="stat-value">${{ number_format($totalVentas, 2) }}</span>
            <span class="stat-label">Total facturado</span>
        </td>
        <td>
            <span class="stat-value">{{ $cantidadVentas }}</span>
            <span class="stat-label">Ventas realizadas</span>
        </td>
        <td>
            <span class="stat-value">${{ number_format($promedioVentas, 2) }}</span>
            <span class="stat-label">Promedio por venta</span>
        </td>
        <td>
            <span class="stat-value">${{ number_format($maxVenta, 2) }}</span>
            <span class="stat-label">Venta máxima</span>
        </td>
        <td>
            <span class="stat-value">${{ number_format($minVenta, 2) }}</span>
            <span class="stat-label">Venta mínima</span>
        </td>
    </tr>
</table>

{{-- ── GRÁFICA ───────────────────────────────────────────────────────── --}}
<p class="stats-title">Ventas por Mes</p>
<div class="pdf-chart-wrap">
    <img src="{{ $chartBase64 }}" alt="Gráfica de ventas por mes">
</div>

{{-- ── TABLA DE VENTAS ───────────────────────────────────────────────── --}}
<p class="section-title">Listado de Ventas</p>
<table class="pdf-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Folio</th>
            <th>Vendedor</th>
            <th>Total</th>
            <th>Monto Recibido</th>
            <th>Cambio</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $i => $fila)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="fw-bold text-purple">{{ $fila['folio'] }}</td>
            <td class="text-left">{{ $fila['vendedor'] }}</td>
            <td class="fw-bold">{{ $fila['total'] }}</td>
            <td>{{ $fila['recibido'] }}</td>
            <td>{{ $fila['cambio'] }}</td>
            <td>{{ $fila['fecha'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ── TOTAL FINAL ───────────────────────────────────────────────────── --}}
<table class="pdf-totals">
    <tr>
        <td style="width:65%;"></td>
        <td style="width:20%;">Total de registros: {{ $cantidadVentas }}</td>
        <td style="width:15%;"></td>
    </tr>
    <tr class="total-row">
        <td style="width:65%; background:transparent;"></td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px;">TOTAL FACTURADO:</td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px; font-size:13px;">
            ${{ number_format($totalVentas, 2) }}
        </td>
    </tr>
</table>

@endsection
