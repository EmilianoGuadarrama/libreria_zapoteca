@extends('layouts.dashboard')

@section('dashboard-content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold">Gestión de Lotes</h3>
        <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
            data-bs-toggle="modal" data-bs-target="#modalCreateLote"
            data-bs-placement="left" title="Nuevo Lote">
            <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
        <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i>
        <span class="fw-semibold">{{ session('success') }}</span>
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

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
            <form id="formReporte" method="GET" action="{{ route('lotes.reporte.general') }}">
                <div class="row g-3">
                    {{-- FECHA --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="color: #4b1c71;">Día</label>
                        <div class="input-group">
                            <input type="text" name="fecha" class="form-control selector-fecha-reporte bg-light border-end-0" placeholder="aaaa-mm-dd">
                            <span class="input-group-text bg-light"><i class="fa-regular fa-calendar" style="color: #7a6a88;"></i></span>
                        </div>
                    </div>

                    {{-- MES --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="color: #4b1c71;">Mes</label>
                        <select name="mes" class="form-select bg-light">
                            <option value="">Seleccione</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>

                    {{-- AÑO --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="color: #4b1c71;">Año</label>
                        <select name="anio" class="form-select bg-light">
                            <option value="">Seleccione</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>

                    {{-- BOTÓN --}}
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#modalConfirmarReporte">
                            <i class="fa-solid fa-file-pdf me-1"></i> Generar Reporte
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped mi-datatable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th class="bebas">Código</th>
                    <th class="bebas">Libro / Edición</th>
                    <th class="bebas">Entrada</th>
                    <th class="bebas">Stock Actual</th>
                    <th class="bebas">Ubicación</th>
                    <th class="text-end bebas">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lotes as $lote)
                <tr>
                    <td class="fw-bold align-middle">{{ $lote->codigo }}</td>
                    <td class="align-middle">
                        <span class="fw-semibold">{{ $lote->edicion->libro->titulo ?? 'N/A' }}</span><br>
                        <small class="text-muted">ISBN: {{ $lote->edicion->isbn ?? 'N/A' }}</small>
                    </td>
                    <td class="align-middle">{{ \Carbon\Carbon::parse($lote->fecha_entrada)->format('d/m/Y H:i') }}</td>
                    <td class="align-middle">
                        <span class="badge" style="background-color: {{ $lote->cantidad > 0 ? '#fff0ff' : '#fce8e6' }}; color: {{ $lote->cantidad > 0 ? '#4b1c71' : '#d93025' }}; border: 1px solid {{ $lote->cantidad > 0 ? '#dbb6ee' : '#f5c6cb' }}; border-radius: 8px; font-size: 0.9rem;">
                            {{ $lote->cantidad }}
                        </span>
                    </td>
                    <td class="align-middle text-muted">{{ $lote->ubicacion->codigo ?? 'N/A' }}</td>

                    <td class="text-end align-middle">
                        <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                            data-bs-toggle="modal" data-bs-target="#modalEditLote{{ $lote->id }}"
                            data-bs-placement="top" title="Editar Lote">
                            <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                        </button>

                        <button type="button" class="btn btn-link p-0 text-decoration-none fs-5"
                            data-bs-toggle="modal" data-bs-target="#modalDeleteLote{{ $lote->id }}"
                            data-bs-placement="top" title="Eliminar Lote">
                            <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                        </button>

                        <button type="button"
                            class="btn btn-link p-0 text-decoration-none fs-5"
                            onclick="window.location.href='{{ route('lotes.pdf', $lote->id) }}'"
                            title="Descargar PDF">

                            <i class="fa-solid fa-file-pdf" style="color: #dc3545;"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fa-solid fa-boxes-stacked fs-2 mb-2 opacity-50"></i><br>
                        No hay lotes registrados aún.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalCreateLote" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-box-open me-2"></i> Registrar Ingreso de Lote</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('lotes.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Código del Lote (Único):</label>
                            <input type="text" name="codigo" class="form-control rounded-3 bg-light" placeholder="Ej. LTB-001" required maxlength="16" style="text-transform: uppercase;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Libro (Edición):</label>
                                <select name="edicion_id" class="form-select rounded-3 bg-light" required>
                                    <option value="">Seleccione una edición...</option>
                                    @foreach($ediciones as $edicion)
                                    <option value="{{ $edicion->id }}">{{ $edicion->isbn }} - {{ $edicion->libro->titulo ?? 'Sin título' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Compra Origen:</label>
                                <select name="compra_id" class="form-select rounded-3 bg-light" required>
                                    <option value="">Seleccione la factura...</option>
                                    @foreach($compras as $compra)
                                    <option value="{{ $compra->id }}">Folio: {{ $compra->folio_factura }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Cantidad Ingresada:</label>
                                <input type="number" name="cantidad" class="form-control rounded-3 bg-light" value="1" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Ubicación Fija:</label>
                                <select name="ubicacion_id" class="form-select rounded-3 bg-light" required>
                                    <option value="">Seleccione ubicación...</option>
                                    @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}">P:{{ $ubicacion->pasillo }} E:{{ $ubicacion->estante }} N:{{ $ubicacion->nivel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #4b1c71;">Guardar Lote</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($lotes as $lote)

    <div class="modal fade" id="modalEditLote{{ $lote->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Lote</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('lotes.update', $lote->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Código del Lote:</label>
                            <input type="text" name="codigo" class="form-control rounded-3 bg-light" value="{{ $lote->codigo }}" required maxlength="16" style="text-transform: uppercase;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Libro (Edición):</label>
                                <select name="edicion_id" class="form-select rounded-3 bg-light" required>
                                    @foreach($ediciones as $edicion)
                                    <option value="{{ $edicion->id }}" {{ $lote->edicion_id == $edicion->id ? 'selected' : '' }}>
                                        {{ $edicion->isbn }} - {{ $edicion->libro->titulo ?? 'Sin título' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Compra Origen:</label>
                                <select name="compra_id" class="form-select rounded-3 bg-light" required>
                                    @foreach($compras as $compra)
                                    <option value="{{ $compra->id }}" {{ $lote->compra_id == $compra->id ? 'selected' : '' }}>
                                        Folio: {{ $compra->folio_factura }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Cantidad (Stock Actual):</label>
                                <input type="number" name="cantidad" class="form-control rounded-3 text-muted" style="background-color: #e9ecef; cursor: not-allowed;" value="{{ $lote->cantidad }}" readonly>
                                <small class="text-danger mt-1 d-block" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-lock me-1"></i> Por normas de control, la cantidad no es editable. Si hay un error de captura, elimine el lote y regístrelo nuevamente.
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Ubicación Fija:</label>
                                <select name="ubicacion_id" class="form-select rounded-3 bg-light" required>
                                    @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}" {{ $lote->ubicacion_id == $ubicacion->id ? 'selected' : '' }}>
                                        P:{{ $ubicacion->pasillo }} E:{{ $ubicacion->estante }} N:{{ $ubicacion->nivel }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #4b1c71;">Actualizar Lote</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDeleteLote{{ $lote->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="fs-5 mb-1">¿Estás seguro de eliminar el lote <br><strong>"{{ $lote->codigo }}"</strong>?</p>
                    <p class="text-muted small mb-0 mt-2">Esta acción descontará el stock asociado a este lote de forma definitiva.</p>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('lotes.destroy', $lote->id) }}" method="post" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endforeach

{{-- MODAL CONFIRMAR REPORTE --}}
<div class="modal fade" id="modalConfirmarReporte" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4">
                    <i class="fa-solid fa-file-pdf me-2"></i> Generar Reporte PDF
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="fs-5 mb-1">¿Estás seguro de generar el reporte PDF con los filtros seleccionados?</p>
                <p class="text-muted small mb-0 mt-2">El documento se descargará automáticamente.</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" onclick="document.getElementById('formReporte').submit();" data-bs-dismiss="modal">Sí, generar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr(".selector-fecha-reporte", {
                locale: "es",
                dateFormat: "Y-m-d",
                maxDate: "today",
                disableMobile: true
            });
        }
    });
</script>
@endsection