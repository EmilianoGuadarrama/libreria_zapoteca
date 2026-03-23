@extends('layouts.dashboard')

@section('dashboard-content')
<style>
    /* ... Tus estilos anteriores ... */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--purple-700) 0%, var(--purple-900) 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: bold;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer; /* Añadido para el botón */
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(75, 28, 113, 0.2);
        color: white;
    }

    .data-table-container {
        background: var(--white, #ffffff);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid var(--border, #eadcf2);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background: var(--purple-100, #fff0ff);
        color: var(--purple-900, #4b1c71);
        text-align: left;
        padding: 16px;
        font-family: var(--font-display, 'Bebas Neue', sans-serif);
        letter-spacing: 0.5px;
        font-size: 1.1rem;
    }

    .data-table td {
        padding: 16px;
        border-bottom: 1px solid var(--border, #eadcf2);
        color: var(--text-dark, #2d1f3a);
        vertical-align: middle;
    }

    .data-table tr:hover { background: #fdfafc; }

    .badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: bold;
        font-size: 0.85rem;
        display: inline-block;
        text-align: center;
    }
    .badge-recibida { background: #e6f4ea; color: #1e8e3e; }
    .badge-pendiente { background: #fef7e0; color: #b06000; }
    .badge-cancelada { background: #fce8e6; color: #d93025; }

    .btn-icon {
        background: var(--purple-100, #fff0ff);
        color: var(--purple-700, #7f4ca5);
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-icon:hover { background: var(--purple-700, #7f4ca5); color: white; }

    .total-amount {
        font-weight: bold;
        color: var(--purple-900, #4b1c71);
        font-size: 1.1rem;
    }

    /* ESTILOS PARA EL MODAL (Inyectados aquí para que funcionen con create) */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(45, 31, 58, 0.6);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        max-width: 950px;
        width: 95%;
        max-height: 90vh;
        overflow-y: auto;
    }
    .search-dropdown-compra {
        position: absolute;
        width: 100%;
        background: white;
        border: 1px solid var(--border);
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        z-index: 1050;
        max-height: 200px;
        overflow-y: auto;
    }
    .search-item-compra {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    .search-item-compra:hover { background: var(--purple-50); }
</style>

<div class="page-header">
    <h2 class="bebas" style="font-size: 2.2rem; color: var(--purple-900, #4b1c71); margin: 0;">
        <i class="fa-solid fa-boxes-packing" style="margin-right: 10px;"></i> Historial de Compras
    </h2>
    <button onclick="abrirModalCompra()" class="btn-primary">
        <i class="fa-solid fa-plus"></i> REGISTRAR COMPRA
    </button>
</div>

        @if(session('success') || session('status'))
            <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i> 
                <span class="fw-semibold">{{ session('success') ?? session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> 
                {{ session('error') ?? $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <table class="table table-bordered table-striped mi-datatable mb-0" style="width:100%">
                <thead style="background-color: #fff0ff; color: #4b1c71;">
                    <tr>
                        <th class="bebas">#</th>
                        <th class="bebas">Folio / Factura</th>
                        <th class="bebas">Proveedor</th>
                        <th class="bebas">Fecha</th>
                        <th class="bebas">Total</th>
                        <th class="bebas">Estado</th>
                        <th class="bebas">Registró</th>
                        <th class="text-end bebas">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compras as $compra)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $compra->folio_factura }}</td>
                            <td>{{ $compra->proveedor->nombre ?? 'N/A' }}</td>
                            <td>{{ $compra->fecha_compra->format('d/m/Y') }}</td>
                            <td class="fw-bold" style="color: #4b1c71;">
                                ${{ number_format($compra->total_compra, 2) }}
                            </td>
                            <td>
                                @if($compra->estado === 'Recibida')
                                    <span class="badge" style="background-color: #e6f4ea; color: #1e8e3e; border-radius: 8px;">Recibida</span>
                                @else
                                    <span class="badge" style="background-color: #fef7e0; color: #b06000; border-radius: 8px;">{{ $compra->estado }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $compra->usuario->name ?? $compra->usuario->correo }}
                                </small>
                            </td>

                            <td class="text-end">
                                <a href="{{ route('compras.show', $compra->id) }}" class="text-decoration-none fs-5 me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Detalle">
                                    <i class="fa-solid fa-eye" style="color: #4b1c71;"></i>
                                </a>

                                <form action="{{ route('compras.destroy', $compra->id) }}" method="post" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 text-decoration-none fs-5" onclick="return confirm('¿Seguro que deseas eliminar esta compra? No se podrá recuperar.')" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Compra">
                                        <i class="fa-regular fa-trash-can" style="color: #000;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


@include('compras.create')

@endsection