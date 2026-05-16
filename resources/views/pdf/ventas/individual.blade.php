@extends('pdf.layout')

@section('contenido')

{{-- ── INFORMACIÓN GENERAL DE LA VENTA ──────────────────────────────── --}}
<div class="pdf-info-box">
    <table>
        <tr>
            <td class="info-label">Folio:</td>
            <td>{{ $venta->folio }}</td>
            <td class="info-label">Fecha:</td>
            <td>
                {{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') : '—' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Atendido por:</td>
            <td>{{ $vendedor }}</td>
            <td class="info-label">Monto recibido:</td>
            <td>${{ number_format($venta->monto_recibido ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="info-label">Cambio:</td>
            <td>${{ number_format($venta->cambio ?? 0, 2) }}</td>
            <td class="info-label">Total títulos:</td>
            <td>{{ $totalTitulos }}</td>
        </tr>
    </table>
</div>

{{-- ── ESTADÍSTICAS ──────────────────────────────────────────────────── --}}
<p class="stats-title">Resumen de la Venta</p>
<table class="stats-grid">
    <tr>
        <td>
            <span class="stat-value">${{ number_format($venta->total, 2) }}</span>
            <span class="stat-label">Total cobrado</span>
        </td>
        <td>
            <span class="stat-value">{{ $totalTitulos }}</span>
            <span class="stat-label">Títulos distintos</span>
        </td>
        <td>
            <span class="stat-value">{{ $totalItems }}</span>
            <span class="stat-label">Unidades vendidas</span>
        </td>
        <td>
            <span class="stat-value">${{ number_format($venta->monto_recibido ?? 0, 2) }}</span>
            <span class="stat-label">Monto recibido</span>
        </td>
        <td>
            <span class="stat-value">${{ number_format($venta->cambio ?? 0, 2) }}</span>
            <span class="stat-label">Cambio</span>
        </td>
    </tr>
</table>

{{-- ── DETALLE DE PRODUCTOS ──────────────────────────────────────────── --}}
<p class="section-title">Detalle de Productos</p>
<table class="pdf-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Código Lote</th>
            <th class="text-left">Libro</th>
            <th>ISBN</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($venta->detallesVentas as $i => $detalle)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $detalle->lote->codigo ?? '—' }}</td>
            <td class="text-left">
                {{ $detalle->lote->edicion->libro->titulo ?? '—' }}
            </td>
            <td>{{ $detalle->lote->edicion->isbn ?? '—' }}</td>
            <td>{{ $detalle->cantidad }}</td>
            <td>${{ number_format($detalle->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ── TOTAL FINAL ───────────────────────────────────────────────────── --}}
<table class="pdf-totals">
    <tr>
        <td style="width:70%;"></td>
        <td style="width:15%; border-top: 1px solid #ddd;">Subtotal:</td>
        <td style="width:15%; border-top: 1px solid #ddd; color:#333;">
            ${{ number_format($venta->total, 2) }}
        </td>
    </tr>
    <tr class="total-row">
        <td style="width:70%; background:transparent;"></td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px;">TOTAL:</td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px; font-size:14px;">
            ${{ number_format($venta->total, 2) }}
        </td>
    </tr>
</table>

@endsection
