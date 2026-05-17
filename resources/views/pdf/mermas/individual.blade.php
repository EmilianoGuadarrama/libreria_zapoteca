@extends('pdf.layout')

@section('contenido')

{{-- ── INFORMACIÓN GENERAL DE LA MERMA ──────────────────────────────── --}}
<div class="pdf-info-box">
    <table>
        <tr>
            <td class="info-label">ID Merma:</td>
            <td>#{{ $merma->id }}</td>
            <td class="info-label">Fecha Reporte:</td>
            <td>
                {{ $merma->fecha_reporte
                    ? \Carbon\Carbon::parse($merma->fecha_reporte)->format('d/m/Y H:i')
                    : '—' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Código de Lote:</td>
            <td>{{ $merma->lote->codigo ?? 'N/A' }}</td>
            <td class="info-label">Libro:</td>
            <td>{{ $merma->lote->edicion->libro->titulo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Proveedor:</td>
            <td>{{ $merma->lote->compra->proveedor->nombre ?? 'N/A' }}</td>
            <td class="info-label">Tipo de Merma:</td>
            <td>{{ $merma->tipo_merma }}</td>
        </tr>
        <tr>
            <td class="info-label">Destino:</td>
            <td>{{ str_replace('_', ' ', $merma->destino) }}</td>
        </tr>
        <tr>
            <td class="info-label">Reportado por:</td>
            <td>{{ $reportadoPor }}</td>
            <td class="info-label">Estatus:</td>
            <td><span class="badge-procesado">{{ $merma->estatus }}</span></td>
        </tr>
    </table>
</div>

{{-- ── ESTADÍSTICAS ──────────────────────────────────────────────────── --}}
<p class="stats-title">Detalle de la Merma</p>
<table class="stats-grid">
    <tr>
        <td>
            <span class="stat-value">{{ $merma->id }}</span>
            <span class="stat-label">ID Registro</span>
        </td>
        <td>
            <span class="stat-value">{{ $merma->cantidad }}</span>
            <span class="stat-label">Unidades afectadas</span>
        </td>
        <td>
            <span class="stat-value">{{ $merma->tipo_merma }}</span>
            <span class="stat-label">Tipo de merma</span>
        </td>
        <td>
            <span class="stat-value">{{ str_replace('_', ' ', $merma->destino) }}</span>
            <span class="stat-label">Destino</span>
        </td>
        <td>
            <span class="stat-value">{{ $merma->lote->codigo ?? '—' }}</span>
            <span class="stat-label">Código de lote</span>
        </td>
    </tr>
</table>

{{-- ── TABLA RESUMEN ─────────────────────────────────────────────────── --}}
<p class="section-title">Información Completa</p>
<table class="pdf-table">
    <thead>
        <tr>
            <th>Campo</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-left fw-bold">ID Merma</td>
            <td>#{{ $merma->id }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Código de Lote</td>
            <td>{{ $merma->lote->codigo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Libro Afectado</td>
            <td>{{ $merma->lote->edicion->libro->titulo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">ISBN</td>
            <td>{{ $merma->lote->edicion->isbn ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Tipo de Merma</td>
            <td>{{ $merma->tipo_merma }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Cantidad</td>
            <td>{{ $merma->cantidad }} unidades</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Precio Unitario</td>
            <td>${{ number_format($merma->precio_unitario, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Total Merma</td>
            <td class="fw-bold">${{ number_format($merma->total_merma, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Monto Recuperado</td>
            <td class="text-success fw-bold">${{ number_format($merma->monto_recuperado, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Monto Perdido</td>
            <td class="text-danger fw-bold">${{ number_format($merma->monto_perdido, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Destino</td>
            <td>{{ str_replace('_', ' ', $merma->destino) }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Estatus</td>
            <td><span class="badge-procesado">{{ $merma->estatus }}</span></td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Reportado por</td>
            <td>{{ $reportadoPor }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Fecha de Reporte</td>
            <td>
                {{ $merma->fecha_reporte
                    ? \Carbon\Carbon::parse($merma->fecha_reporte)->format('d/m/Y H:i')
                    : '—' }}
            </td>
        </tr>
    </tbody>
</table>

@endsection
