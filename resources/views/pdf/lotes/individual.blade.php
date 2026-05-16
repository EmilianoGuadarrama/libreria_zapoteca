@extends('pdf.layout')

@section('contenido')

{{-- ── INFORMACIÓN GENERAL DEL LOTE ─────────────────────────────────── --}}
<div class="pdf-info-box">
    <table>
        <tr>
            <td class="info-label">Código de Lote:</td>
            <td class="fw-bold text-purple">{{ $lote->codigo }}</td>
            <td class="info-label">Fecha Entrada:</td>
            <td>
                {{ $lote->fecha_entrada
                    ? \Carbon\Carbon::parse($lote->fecha_entrada)->format('d/m/Y')
                    : '—' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Libro:</td>
            <td>{{ $lote->edicion->libro->titulo ?? 'N/A' }}</td>
            <td class="info-label">ISBN:</td>
            <td>{{ $lote->edicion->isbn ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Proveedor:</td>
            <td>{{ $lote->compra->proveedor->nombre ?? 'N/A' }}</td>
            <td class="info-label">Folio Compra:</td>
            <td>{{ $lote->compra->folio_factura ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Ubicación:</td>
            <td>{{ $lote->ubicacion->nombre ?? 'N/A' }}</td>
            <td class="info-label">Registrado por:</td>
            <td>{{ $registradoPor }}</td>
        </tr>
    </table>
</div>

{{-- ── ESTADÍSTICAS ──────────────────────────────────────────────────── --}}
<p class="stats-title">Detalle del Lote</p>
<table class="stats-grid">
    <tr>
        <td>
            <span class="stat-value">{{ $lote->codigo }}</span>
            <span class="stat-label">Código</span>
        </td>
        <td>
            <span class="stat-value">{{ $lote->cantidad }}</span>
            <span class="stat-label">Unidades en lote</span>
        </td>
        <td>
            <span class="stat-value">{{ $lote->edicion->isbn ?? '—' }}</span>
            <span class="stat-label">ISBN edición</span>
        </td>
        <td>
            <span class="stat-value">{{ $lote->ubicacion->nombre ?? '—' }}</span>
            <span class="stat-label">Ubicación</span>
        </td>
        <td>
            <span class="stat-value">
                {{ $lote->fecha_entrada
                    ? \Carbon\Carbon::parse($lote->fecha_entrada)->format('d/m/Y')
                    : '—' }}
            </span>
            <span class="stat-label">Fecha entrada</span>
        </td>
    </tr>
</table>

{{-- ── TABLA INFORMATIVA COMPLETA ────────────────────────────────────── --}}
<p class="section-title">Información Completa del Lote</p>
<table class="pdf-table">
    <thead>
        <tr>
            <th>Campo</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-left fw-bold">Código de Lote</td>
            <td>{{ $lote->codigo }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Libro</td>
            <td>{{ $lote->edicion->libro->titulo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">ISBN Edición</td>
            <td>{{ $lote->edicion->isbn ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Año Publicación</td>
            <td>{{ $lote->edicion->anio_publicacion ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Cantidad</td>
            <td>{{ $lote->cantidad }} unidades</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Proveedor</td>
            <td>{{ $lote->compra->proveedor->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Folio de Compra</td>
            <td>{{ $lote->compra->folio_factura ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Ubicación en almacén</td>
            <td>{{ $lote->ubicacion->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Registrado por</td>
            <td>{{ $registradoPor }}</td>
        </tr>
        <tr>
            <td class="text-left fw-bold">Fecha de Entrada</td>
            <td>
                {{ $lote->fecha_entrada
                    ? \Carbon\Carbon::parse($lote->fecha_entrada)->format('d/m/Y')
                    : '—' }}
            </td>
        </tr>
    </tbody>
</table>

@endsection
