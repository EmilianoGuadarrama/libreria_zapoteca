@extends('pdf.layout')

@section('contenido')

{{-- ── ESTADÍSTICAS GENERALES ────────────────────────────────────────── --}}
<p class="stats-title">Estadísticas Generales</p>
<table class="stats-grid">
    <tr>
        <td>
            <span class="stat-value">{{ $totalMermas }}</span>
            <span class="stat-label">Total registros</span>
        </td>
        <td>
            <span class="stat-value">{{ $totalUnidades }}</span>
            <span class="stat-label">Unidades afectadas</span>
        </td>
        <td>
            <span class="stat-value">{{ number_format($promedioUnid, 1) }}</span>
            <span class="stat-label">Promedio por merma</span>
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

{{-- ── DISTRIBUCIÓN POR TIPO Y DESTINO ──────────────────────────────── --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:16px;">
    <tr>
        {{-- Por tipo --}}
        <td style="width:48%; vertical-align:top; padding-right:8px;">
            <p class="stats-title">Por Tipo de Merma</p>
            <table class="pdf-table">
                <thead>
                    <tr>
                        <th class="text-left">Tipo</th>
                        <th>Registros</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($porTipo as $tipo => $cantidad)
                    <tr>
                        <td class="text-left">{{ $tipo }}</td>
                        <td>{{ $cantidad }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
        {{-- Por destino --}}
        <td style="width:48%; vertical-align:top; padding-left:8px;">
            <p class="stats-title">Por Destino</p>
            <table class="pdf-table">
                <thead>
                    <tr>
                        <th class="text-left">Destino</th>
                        <th>Registros</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($porDestino as $destino => $cantidad)
                    <tr>
                        <td class="text-left">{{ str_replace('_', ' ', $destino) }}</td>
                        <td>{{ $cantidad }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
</table>

{{-- ── GRÁFICA ───────────────────────────────────────────────────────── --}}
<p class="stats-title">Distribución por Tipo de Merma</p>
<div class="pdf-chart-wrap">
    <img src="{{ $chartBase64 }}" alt="Gráfica mermas por tipo">
</div>

{{-- ── TABLA DE MERMAS ───────────────────────────────────────────────── --}}
<p class="section-title">Listado de Mermas Procesadas</p>
<table class="pdf-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Lote</th>
            <th class="text-left">Libro</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Destino</th>
            <th>Reportado por</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $i => $fila)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="fw-bold text-purple">{{ $fila['lote_codigo'] }}</td>
            <td class="text-left">{{ $fila['libro'] }}</td>
            <td>{{ $fila['tipo'] }}</td>
            <td>{{ $fila['cantidad'] }}</td>
            <td>{{ str_replace('_', ' ', $fila['destino']) }}</td>
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
        <td style="width:20%;">Total de registros: {{ $totalMermas }}</td>
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
