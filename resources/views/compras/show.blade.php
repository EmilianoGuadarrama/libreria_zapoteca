@extends('layouts.dashboard')

@section('dashboard-content')
<style>
    .invoice-container {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid var(--border);
        max-width: 900px;
        margin: 0 auto;
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        border-bottom: 2px solid var(--purple-100);
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 30px;
    }

    .info-block h4 {
        font-family: 'Bebas Neue', sans-serif;
        color: var(--purple-700);
        font-size: 1.2rem;
        margin-bottom: 10px;
        text-transform: uppercase;
        border-left: 4px solid var(--purple-500);
        padding-left: 10px;
    }

    .info-block p {
        margin: 5px 0;
        color: var(--text-dark);
        line-height: 1.5;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .items-table th {
        background: var(--purple-900);
        color: white;
        text-align: left;
        padding: 12px;
        font-family: 'Bebas Neue', sans-serif;
    }

    .items-table td {
        padding: 15px 12px;
        border-bottom: 1px solid var(--border);
    }

    .total-section {
        margin-top: 30px;
        text-align: right;
    }

    .total-box {
        display: inline-block;
        background: var(--purple-100);
        padding: 20px;
        border-radius: 12px;
        min-width: 200px;
    }

    .total-box h2 {
        font-family: 'Bebas Neue', sans-serif;
        color: var(--purple-900);
        font-size: 2rem;
        margin: 0;
    }

    @media print {
        .no-print { display: none; }
        .invoice-container { box-shadow: none; border: none; padding: 0; }
        body { background: white; }
    }
</style>

<div class="no-print" style="margin-bottom: 20px; display: flex; gap: 10px;">
    <a href="{{ route('compras.index') }}" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; border-radius: 8px;">
        <i class="fa-solid fa-arrow-left"></i> Volver al listado
    </a>
    <button onclick="window.print()" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; cursor: pointer;">
        <i class="fa-solid fa-print"></i> Imprimir Factura
    </button>
</div>

<div class="invoice-container">
    <div class="invoice-header">
        <div>
            <h1 class="bebas" style="color: var(--purple-900); font-size: 2.5rem; margin: 0;">DETALLE DE COMPRA</h1>
            <p style="color: var(--text-muted);">Folio Interno: #{{ str_pad($compra->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div style="text-align: right;">
            <div class="badge {{ $compra->estado == 'Recibida' ? 'badge-recibida' : 'badge-pendiente' }}" style="font-size: 1rem; padding: 8px 15px;">
                {{ strtoupper($compra->estado) }}
            </div>
            <p style="margin-top: 10px;"><strong>Fecha:</strong> {{ $compra->fecha_compra->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-block">
            <h4>Datos del Proveedor</h4>
            <p><strong>Empresa:</strong> {{ $compra->proveedor->nombre }}</p>
            <p><strong>Contacto:</strong> {{ $compra->proveedor->personaContacto->nombre ?? 'N/A' }}</p>
            <p><strong>Teléfono:</strong> {{ $compra->proveedor->telefono }}</p>
            <p><strong>Correo:</strong> {{ $compra->proveedor->correo }}</p>
        </div>
        <div class="info-block">
            <h4>Información de Factura</h4>
            <p><strong>Folio Fiscal/Factura:</strong> {{ $compra->folio_factura }}</p>
            <p><strong>Registrado por:</strong> {{ $compra->usuario->name ?? $compra->usuario->correo }}</p>
            <p><strong>Fecha Registro:</strong> {{ $compra->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Descripción del Libro (Edición)</th>
                <th style="text-align: center;">Cantidad</th>
                <th style="text-align: right;">Precio Unit.</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compra->detalles as $detalle)
            <tr>
                <td><small>{{ $detalle->edicion->isbn }}</small></td>
                <td>
                    <strong>{{ $detalle->edicion->libro->titulo }}</strong><br>
                    <small style="color: var(--text-muted)">{{ $detalle->edicion->editorial }} - {{ $detalle->edicion->año }}</small>
                </td>
                <td style="text-align: center;">{{ $detalle->cantidad }}</td>
                <td style="text-align: right;">${{ number_format($detalle->subtotal / $detalle->cantidad, 2) }}</td>
                <td style="text-align: right; font-weight: bold;">${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-box">
            <p style="margin: 0; color: var(--purple-700); font-weight: bold;">TOTAL DE LA COMPRA</p>
            <h2>${{ number_format($compra->total_compra, 2) }}</h2>
        </div>
    </div>

    <div style="margin-top: 50px; border-top: 1px solid var(--border); padding-top: 20px; font-size: 0.8rem; color: var(--text-muted); text-align: center;">
        Esta es una representación de entrada de almacén para el sistema Librería Zapoteca.
    </div>
</div>
@endsection