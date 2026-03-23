@extends('layouts.dashboard')

@section('dashboard-content')
<style>
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
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(75, 28, 113, 0.2);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--white);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .data-table th {
        background: var(--purple-100);
        color: var(--purple-900);
        text-align: left;
        padding: 15px;
        font-family: var(--font-display);
        letter-spacing: 0.5px;
    }
    .data-table td {
        padding: 15px;
        border-bottom: 1px solid var(--border);
        color: var(--text-dark);
    }

    /* Estilos del Modal */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(45, 31, 58, 0.6);
        backdrop-filter: blur(4px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .modal-content {
        background: var(--white);
        border-radius: 20px;
        width: 100%;
        max-width: 500px;
        padding: 24px;
        transform: translateY(-20px);
        transition: transform 0.3s ease;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    .modal-overlay.active .modal-content {
        transform: translateY(0);
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: bold;
        color: var(--purple-900);
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: var(--font-body);
    }
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
    }
    .btn-secondary {
        background: #f1f1f1;
        color: #333;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
    }
</style>

<div class="page-header">
    <h2 class="bebas" style="font-size: 2.2rem; color: var(--purple-900); margin: 0;">Gestión de Lotes</h2>
    <button class="btn-primary" onclick="abrirModal()">
        <i class="fa-solid fa-plus"></i> NUEVO LOTE
    </button>
</div>

@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
@endif

<table class="data-table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Libro / Edición</th>
            <th>Entrada</th>
            <th>Stock Actual</th>
            <th>Ubicación</th>
        </tr>
    </thead>
    <tbody>
        @forelse($lotes as $lote)
            <tr>
                <td><strong>{{ $lote->codigo }}</strong></td>
                <td>
                    {{ $lote->edicion->libro->titulo ?? 'N/A' }}<br>
                    <small style="color: var(--text-muted);">ISBN: {{ $lote->edicion->isbn ?? 'N/A' }}</small>
                </td>
                <td>{{ \Carbon\Carbon::parse($lote->fecha_entrada)->format('d/m/Y H:i') }}</td>
                <td>
                    <span style="background: {{ $lote->cantidad > 0 ? 'var(--purple-100)' : '#f8d7da' }}; color: {{ $lote->cantidad > 0 ? 'var(--purple-900)' : '#721c24' }}; padding: 4px 8px; border-radius: 6px; font-weight: bold;">
                        {{ $lote->cantidad }}
                    </span>
                </td>
                <td>{{ $lote->ubicacion->codigo ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; color: var(--text-muted);">No hay lotes registrados aún.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20px;">
    {{ $lotes->links() }}
</div>

<div class="modal-overlay" id="modalLote">
    <div class="modal-content">
        <h3 class="bebas" style="color: var(--purple-900); font-size: 1.8rem; margin-top: 0; border-bottom: 2px solid var(--purple-100); padding-bottom: 10px;">Registrar Ingreso de Lote</h3>
        
        <form action="{{ route('lotes.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Código del Lote (Único):</label>
                <input type="text" name="codigo" class="form-control" placeholder="Ej. LTB-001" required maxlength="16" style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label>Libro (Edición):</label>
                <select name="edicion_id" class="form-control" required>
                    <option value="">Seleccione una edición...</option>
                    @foreach($ediciones as $edicion)
                        <option value="{{ $edicion->id }}">{{ $edicion->isbn }} - {{ $edicion->libro->titulo ?? 'Sin título' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Compra Origen:</label>
                <select name="compra_id" class="form-control" required>
                    <option value="">Seleccione la factura de compra...</option>
                    @foreach($compras as $compra)
                        <option value="{{ $compra->id }}">Folio: {{ $compra->folio_factura }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Cantidad Ingresada:</label>
                    <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label>Ubicación Fija:</label>
                    <select name="ubicacion_id" class="form-control" required>
                        <option value="">Seleccione...</option>
                        @foreach($ubicaciones as $ubicacion)
                            <option value="{{ $ubicacion->id }}">P:{{ $ubicacion->pasillo }} E:{{ $ubicacion->estante }} N:{{ $ubicacion->nivel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Lote</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModal() {
        document.getElementById('modalLote').classList.add('active');
    }

    function cerrarModal() {
        document.getElementById('modalLote').classList.remove('active');
    }

    // Cerrar modal al hacer clic fuera del contenido
    document.getElementById('modalLote').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
</script>
@endsection