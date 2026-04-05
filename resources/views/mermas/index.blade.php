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

    <a href="{{ route('reportes.mermas') }}" class="btn btn-dark">
        Reporte de Mermas
    </a>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mi-datatable" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lote</th>
                    <th>Tipo de Merma</th>
                    <th>Fecha de Reporte</th>
                    <th>Cantidad</th>
                    <th>Usuario</th>
                    <th>Destino</th>
                    <th>Estatus</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mermas as $merma)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $merma->lote_id }}</td>
                    <td class="fw-semibold">{{ $merma->tipo_merma }}</td>
                    <td>{{ $merma->fecha_reporte }}</td>
                    <td>{{ $merma->cantidad }}</td>
                    <td>{{ $merma->usuario_id }}</td>
                    <td>{{ $merma->destino }}</td>
                    <td>{{ $merma->estatus }}</td>
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

                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Lote ID</label>
                            <input type="number" name="lote_id" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-8">
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
                            <input type="datetime-local" name="fecha_reporte" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Usuario</label>
                            <select name="usuario_id" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach($usuariosCatalogo as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->correo }}</option>
                                @endforeach
                            </select>
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

                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Lote ID</label>
                            <input type="number" name="lote_id" class="form-control" value="{{ $merma->lote_id }}" min="1" required>
                        </div>

                        <div class="col-md-8">
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
                            <input type="datetime-local" name="fecha_reporte" class="form-control" value="{{ \Carbon\Carbon::parse($merma->fecha_reporte)->format('Y-m-d\TH:i') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" value="{{ $merma->cantidad }}" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Usuario</label>
                            <select name="usuario_id" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach($usuariosCatalogo as $usuario)
                                <option value="{{ $usuario->id }}" {{ $merma->usuario_id == $usuario->id ? 'selected' : '' }}>
                                    {{ $usuario->correo }}
                                </option>
                                @endforeach
                            </select>
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
@endsection