@extends('layouts.dashboard')

@section('dashboard-content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold">Mermas</h3>
        <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
            data-bs-toggle="modal" data-bs-target="#modalCreateMerma"
            title="Nueva Merma">
            <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
        </button>
    </div>

    @if(session('status'))
    <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
        <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i>
        <span class="fw-semibold">{{ session('status') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
            <form id="formReporte" method="GET" action="{{ route('mermas.reporte.general') }}">
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

    {{-- ── RESUMEN FINANCIERO ────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Total registrado --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border-left: 5px solid #4b1c71 !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px; height:52px; background: linear-gradient(135deg, #4b1c71, #7f4ca5);">
                        <i class="fa-solid fa-coins text-white fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted small fw-semibold">Total registrado</p>
                        <h4 class="mb-0 fw-bold" style="color: #4b1c71;">${{ number_format($totalMermas, 2) }}</h4>
                        <small class="text-muted">Valor total en mermas</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monto recuperado --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border-left: 5px solid #198754 !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px; height:52px; background: linear-gradient(135deg, #198754, #28a870);">
                        <i class="fa-solid fa-hand-holding-dollar text-white fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted small fw-semibold">Monto recuperado</p>
                        <h4 class="mb-0 fw-bold text-success">${{ number_format($totalRecuperado, 2) }}</h4>
                        <small class="text-muted">Devolucion a proveedor</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monto perdido --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border-left: 5px solid #dc3545 !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px; height:52px; background: linear-gradient(135deg, #dc3545, #e8606d);">
                        <i class="fa-solid fa-fire-flame-curved text-white fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted small fw-semibold">Monto perdido</p>
                        <h4 class="mb-0 fw-bold text-danger">${{ number_format($totalPerdido, 2) }}</h4>
                        <small class="text-muted">Destrucción</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Balance neto --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border-left: 5px solid {{ $balanceNeto >= 0 ? '#198754' : '#dc3545' }} !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px; height:52px; background: linear-gradient(135deg, {{ $balanceNeto >= 0 ? '#198754, #28a870' : '#dc3545, #e8606d' }});">
                        <i class="fa-solid fa-scale-balanced text-white fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted small fw-semibold">Balance neto</p>
                        <h4 class="mb-0 fw-bold {{ $balanceNeto >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $balanceNeto >= 0 ? '' : '-' }}${{ number_format(abs($balanceNeto), 2) }}
                        </h4>
                        <small class="text-muted">Recuperado − Perdido</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mi-datatable" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>Lote</th>
                    <th>Tipo de Merma</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total Merma</th>
                    <th>Monto Recuperado</th>
                    <th>Monto Perdido</th>
                    <th>Usuario</th>
                    <th>Destino</th>
                    <th>Estatus</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mermas as $merma)
                @php
                    $persona = $merma->usuario->persona ?? null;
                    $nombreUsuario = $persona ? trim($persona->nombre . ' ' . $persona->apellido_paterno) : ($merma->usuario->correo ?? 'N/A');
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $merma->lote->compra->proveedor->nombre ?? 'Sin proveedor' }}</td>
                    <td>
                        Lote #{{ $merma->lote_id }}<br>
                        <small class="text-muted">{{ $merma->lote->edicion->libro->titulo ?? 'N/A' }}</small>
                    </td>
                    <td class="fw-semibold">{{ $merma->tipo_merma }}</td>
                    <td>{{ $merma->cantidad }}</td>
                    <td>${{ number_format($merma->precio_unitario, 2) }}</td>
                    <td class="fw-bold">${{ number_format($merma->total_merma, 2) }}</td>
                    <td class="text-success fw-bold">${{ number_format($merma->monto_recuperado, 2) }}</td>
                    <td class="text-danger fw-bold">${{ number_format($merma->monto_perdido, 2) }}</td>
                    <td>{{ $nombreUsuario }}</td>
                    <td>{{ str_replace('_', ' ', $merma->destino) }}</td>
                    <td>
                        @if($merma->estatus === 'PROCESADO')
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success-subtle">PROCESADO</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill border border-warning-subtle">PENDIENTE</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                            data-bs-toggle="modal" data-bs-target="#modalEditMerma{{ $merma->id }}"
                            title="Editar Merma">
                            <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                        </button>

                        <button type="button" class="btn btn-link p-0 text-decoration-none fs-5"
                            data-bs-toggle="modal" data-bs-target="#modalDeleteMerma{{ $merma->id }}"
                            title="Eliminar Merma">
                            <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                        </button>

                        <button type="button"
                            class="btn btn-link p-0 text-decoration-none fs-5"
                            onclick="window.location.href='{{ route('mermas.pdf', $merma->id) }}'"
                            title="Descargar PDF">

                            <i class="fa-solid fa-file-pdf" style="color: #dc3545;"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- modal crear --}}
<div class="modal fade" id="modalCreateMerma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4">
                    <i class="fa-solid fa-circle-plus me-2"></i> Nueva Merma
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('mermas.store') }}" method="post">
                @csrf

                <div class="modal-body p-4">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Lote</label>
                            <select name="lote_id" class="form-select select2" required>
                                <option value="">Seleccione un lote...</option>
                                @foreach($lotesDisponibles as $loteDisp)
                                <option value="{{ $loteDisp->id }}">
                                    Lote #{{ $loteDisp->id }} - {{ $loteDisp->edicion->libro->titulo ?? 'N/A' }} - Proveedor: {{ $loteDisp->compra->proveedor->nombre ?? 'N/A' }} - Precio: ${{ number_format($loteDisp->edicion->precio_venta ?? 0, 2) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Tipo de Merma</label>
                            <select name="tipo_merma" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option value="Portada dañada">Portada dañada</option>
                                <option value="Hojas rasgadas">Hojas rasgadas</option>
                                <option value="Hojas arrugadas">Hojas arrugadas</option>
                                <option value="Faltan hojas">Faltan hojas</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Fecha de Reporte</label>
                            <div class="input-group">
                                <input type="text" name="fecha_reporte" class="form-control selector-fecha-merma bg-light border-end-0" placeholder="Seleccione fecha y hora">
                                <span class="input-group-text bg-light"><i class="fa-regular fa-calendar" style="color: #7a6a88;"></i></span>
                            </div>
                            <small class="text-muted">Si se deja vacío, se usará la fecha actual.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Usuario</label>
                            <input type="text" class="form-control bg-light" value="{{ auth()->user()->persona ? trim(auth()->user()->persona->nombre . ' ' . auth()->user()->persona->apellido_paterno) : auth()->user()->correo }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Destino</label>
                            <select name="destino" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option value="Devolucion_Proveedor">Devolución a proveedor</option>
                                <option value="Destruccion">Destrucción</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Estatus</label>
                            <select name="estatus" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option value="PENDIENTE">PENDIENTE</option>
                                <option value="PROCESADO">PROCESADO</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- modales editar y eliminar --}}
@foreach($mermas as $merma)
<div class="modal fade" id="modalEditMerma{{ $merma->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Editar Merma
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('mermas.update', $merma->id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-body p-4">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Lote</label>
                            <select name="lote_id" class="form-select select2" required>
                                <option value="">Seleccione un lote...</option>
                                @foreach($lotesDisponibles as $loteDisp)
                                <option value="{{ $loteDisp->id }}" {{ $merma->lote_id == $loteDisp->id ? 'selected' : '' }}>
                                    Lote #{{ $loteDisp->id }} - {{ $loteDisp->edicion->libro->titulo ?? 'N/A' }} - Proveedor: {{ $loteDisp->compra->proveedor->nombre ?? 'N/A' }} - Precio: ${{ number_format($loteDisp->edicion->precio_venta ?? 0, 2) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Tipo de Merma</label>
                            <select name="tipo_merma" class="form-select" required>
                                <option value="Portada dañada" {{ $merma->tipo_merma == 'Portada dañada' ? 'selected' : '' }}>Portada dañada</option>
                                <option value="Hojas rasgadas" {{ $merma->tipo_merma == 'Hojas rasgadas' ? 'selected' : '' }}>Hojas rasgadas</option>
                                <option value="Hojas arrugadas" {{ $merma->tipo_merma == 'Hojas arrugadas' ? 'selected' : '' }}>Hojas arrugadas</option>
                                <option value="Faltan hojas" {{ $merma->tipo_merma == 'Faltan hojas' ? 'selected' : '' }}>Faltan hojas</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Fecha de Reporte</label>
                            <div class="input-group">
                                <input type="text" name="fecha_reporte" class="form-control selector-fecha-merma bg-light border-end-0" value="{{ $merma->fecha_reporte ? $merma->fecha_reporte->format('Y-m-d H:i') : '' }}" required>
                                <span class="input-group-text bg-light"><i class="fa-regular fa-calendar" style="color: #7a6a88;"></i></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" value="{{ $merma->cantidad }}" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Usuario que Reportó</label>
                            <input type="text" class="form-control bg-light" value="{{ $nombreUsuario }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Destino</label>
                            <select name="destino" class="form-select" required>
                                <option value="Devolucion_Proveedor" {{ $merma->destino == 'Devolucion_Proveedor' ? 'selected' : '' }}>Devolución a proveedor</option>
                                <option value="Destruccion" {{ $merma->destino == 'Destruccion' ? 'selected' : '' }}>Destrucción</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Estatus</label>
                            <select name="estatus" class="form-select" required>
                                <option value="PENDIENTE" {{ $merma->estatus == 'PENDIENTE' ? 'selected' : '' }}>PENDIENTE</option>
                                <option value="PROCESADO" {{ $merma->estatus == 'PROCESADO' ? 'selected' : '' }}>PROCESADO</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeleteMerma{{ $merma->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-center">
                <p class="fs-5 mb-1">¿Estás seguro de eliminar la merma <br><strong>"{{ $merma->tipo_merma }}"</strong>?</p>
                <p class="text-muted small mb-0 mt-2">Esta acción no se puede deshacer.</p>
            </div>

            <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>

                <form action="{{ route('mermas.destroy', $merma->id) }}" method="post" class="d-inline">
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
            flatpickr(".selector-fecha-merma", {
                locale: "es",
                dateFormat: "Y-m-d H:i",
                enableTime: true,
                time_24hr: true,
                disableMobile: true,
                allowInput: true
            });

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