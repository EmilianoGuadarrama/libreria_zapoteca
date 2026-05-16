@extends('pdf.layout')

@section('contenido')

{{-- ── ESTADÍSTICAS GENERALES ────────────────────────────────────────── --}}
<p class="stats-title">Estadísticas Generales</p>
<table class="stats-grid">
    <tr>
        <td>
            <span class="stat-value">{{ $totalLotes }}</span>
            <span class="stat-label">Total lotes</span>
        </td>
        <td>
            <span class="stat-value">{{ $totalUnidades }}</span>
            <span class="stat-label">Unidades totales</span>
        </td>
        <td>
            <span class="stat-value">{{ number_format($promedioUnid, 1) }}</span>
            <span class="stat-label">Promedio por lote</span>
        </td>
        <td>
            <span class="stat-value">{{ $maxUnidades }}</span>
            <span class="stat-label">Máx. unidades</span>
        </td>
        <td>
            <span class="stat-value">{{ $minUnidades }}</span>
            <span class="stat-label">Mín. unidades</span>
        </td>
    </tr>
</table>

{{-- ── GRÁFICA ───────────────────────────────────────────────────────── --}}
<p class="stats-title">Lotes Registrados por Mes</p>
<div class="pdf-chart-wrap">
    <img src="{{ $chartBase64 }}" alt="Gráfica de lotes por mes">
</div>

{{-- ── TABLA DE LOTES ────────────────────────────────────────────────── --}}
<p class="section-title">Listado de Lotes</p>
<table class="pdf-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Código</th>
            <th class="text-left">Libro</th>
            <th>Folio Compra</th>
            <th>Proveedor</th>
            <th>Cantidad</th>
            <th>Ubicación</th>
            <th>Responsable</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $i => $fila)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="fw-bold text-purple">{{ $fila['codigo'] }}</td>
            <td class="text-left">{{ $fila['libro'] }}</td>
            <td>{{ $fila['compra'] }}</td>
            <td>{{ $fila['proveedor'] }}</td>
            <td>{{ $fila['cantidad'] }}</td>
            <td>{{ $fila['ubicacion'] }}</td>
            <td>{{ $fila['usuario'] }}</td>
            <td>{{ $fila['fecha'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ── TOTAL FINAL ───────────────────────────────────────────────────── --}}
<table class="pdf-totals">
    <tr>
        <td style="width:65%;"></td>
        <td style="width:20%;">Total de lotes: {{ $totalLotes }}</td>
        <td style="width:15%;"></td>
    </tr>
    <tr class="total-row">
        <td style="width:65%; background:transparent;"></td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px;">TOTAL UNIDADES:</td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px; font-size:13px;">
            {{ $totalUnidades }} uds.
        </td>
    </tr>
</table>

@endsection
