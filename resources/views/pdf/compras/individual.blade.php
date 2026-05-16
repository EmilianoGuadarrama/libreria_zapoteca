@extends('pdf.layout')

@section('contenido')

{{-- ── INFORMACIÓN GENERAL DE LA COMPRA ─────────────────────────────── --}}
<div class="pdf-info-box">
    <table>
        <tr>
            <td class="info-label">Folio Factura:</td>
            <td>{{ $compra->folio_factura }}</td>
            <td class="info-label">Fecha Compra:</td>
            <td>
                {{ $compra->fecha_compra
                    ? \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y')
                    : '—' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Proveedor:</td>
            <td>{{ $compra->proveedor->nombre ?? 'N/A' }}</td>
            <td class="info-label">Estado:</td>
            <td>
                @if($compra->estado === 'Recibida')
                    <span class="badge-procesado">{{ $compra->estado }}</span>
                @elseif($compra->estado === 'Pendiente')
                    <span class="badge-pendiente">{{ $compra->estado }}</span>
                @else
                    <span class="badge-cancelada">{{ $compra->estado }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Registrado por:</td>
            <td>{{ $registradoPor }}</td>
            <td class="info-label">Total títulos:</td>
            <td>{{ $totalTitulos }}</td>
        </tr>
    </table>
</div>

{{-- ── ESTADÍSTICAS ──────────────────────────────────────────────────── --}}
<p class="stats-title">Resumen de la Compra</p>
<table class="stats-grid">
    <tr>
        <td>
            <span class="stat-value">${{ number_format($compra->total_compra, 2) }}</span>
            <span class="stat-label">Total invertido</span>
        </td>
        <td>
            <span class="stat-value">{{ $totalTitulos }}</span>
            <span class="stat-label">Títulos distintos</span>
        </td>
        <td>
            <span class="stat-value">{{ $totalItems }}</span>
            <span class="stat-label">Unidades compradas</span>
        </td>
        <td>
            <span class="stat-value">{{ $compra->estado }}</span>
            <span class="stat-label">Estado</span>
        </td>
        <td>
            <span class="stat-value">{{ $compra->proveedor->nombre ?? 'N/A' }}</span>
            <span class="stat-label">Proveedor</span>
        </td>
    </tr>
</table>

{{-- ── DETALLE DE PRODUCTOS ──────────────────────────────────────────── --}}
<p class="section-title">Detalle de Productos</p>
<table class="pdf-table">
    <thead>
        <tr>
            <th>#</th>
            <th class="text-left">Libro</th>
            <th>ISBN</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($compra->detalles as $i => $detalle)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="text-left">
                {{ $detalle->edicion->libro->titulo ?? '—' }}
            </td>
            <td>{{ $detalle->edicion->isbn ?? '—' }}</td>
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
        <td style="width:15%; border-top: 1px solid #ddd;">Total ítems: {{ $totalItems }}</td>
        <td style="width:15%; border-top: 1px solid #ddd;"></td>
    </tr>
    <tr class="total-row">
        <td style="width:70%; background:transparent;"></td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px;">TOTAL COMPRA:</td>
        <td style="background-color:#4b1c71; color:#fff; padding:8px; font-size:14px;">
            ${{ number_format($compra->total_compra, 2) }}
        </td>
    </tr>
</table>

@endsection
