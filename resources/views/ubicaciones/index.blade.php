@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold">Ubicaciones</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
                    data-bs-toggle="modal" data-bs-target="#modalCreateUbicacion"
                    title="Nueva Ubicación">
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

        <div class="table-responsive">
            <table class="table table-bordered table-striped mi-datatable" style="width:100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Pasillo</th>
                    <th>Estante</th>
                    <th>Nivel</th>
                    <th>Código</th>
                    <th>Género</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ubicaciones as $ubicacion)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $ubicacion->pasillo }}</td>
                        <td>{{ $ubicacion->estante }}</td>
                        <td>{{ $ubicacion->nivel }}</td>
                        <td class="fw-semibold">{{ $ubicacion->codigo }}</td>
                        <td>{{ $ubicacion->nombre_genero }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                                    data-bs-toggle="modal" data-bs-target="#modalEditUbicacion{{ $ubicacion->id }}"
                                    title="Editar Ubicación">
                                <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                            </button>

                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5"
                                    data-bs-toggle="modal" data-bs-target="#modalDeleteUbicacion{{ $ubicacion->id }}"
                                    title="Eliminar Ubicación">
                                <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalCreateUbicacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4">
                        <i class="fa-solid fa-circle-plus me-2"></i> Nueva Ubicación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('ubicaciones.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Pasillo</label>
                                <input type="text" name="pasillo" class="form-control" placeholder="Ej. A" required maxlength="10">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Estante</label>
                                <input type="number" name="estante" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Nivel</label>
                                <input type="number" name="nivel" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Código</label>
                                <input type="text" name="codigo" class="form-control" placeholder="Ej. A-1-1" required maxlength="50">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Género</label>
                                <select name="genero_id" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    @foreach($generosCatalogo as $genero)
                                        <option value="{{ $genero->id }}">{{ $genero->nombre }}</option>
                                    @endforeach
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

    @foreach($ubicaciones as $ubicacion)
        <div class="modal fade" id="modalEditUbicacion{{ $ubicacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-pen-to-square me-2"></i> Editar Ubicación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('ubicaciones.update', $ubicacion->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Pasillo</label>
                                    <input type="text" name="pasillo" class="form-control" value="{{ $ubicacion->pasillo }}" required maxlength="10">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Estante</label>
                                    <input type="number" name="estante" class="form-control" value="{{ $ubicacion->estante }}" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Nivel</label>
                                    <input type="number" name="nivel" class="form-control" value="{{ $ubicacion->nivel }}" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Código</label>
                                    <input type="text" name="codigo" class="form-control" value="{{ $ubicacion->codigo }}" required maxlength="50">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Género</label>
                                    <select name="genero_id" class="form-select" required>
                                        <option value="">Seleccione</option>
                                        @foreach($generosCatalogo as $genero)
                                            <option value="{{ $genero->id }}" {{ $ubicacion->genero_id == $genero->id ? 'selected' : '' }}>
                                                {{ $genero->nombre }}
                                            </option>
                                        @endforeach
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

        <div class="modal fade" id="modalDeleteUbicacion{{ $ubicacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Estás seguro de eliminar la ubicación <br><strong>"{{ $ubicacion->codigo }}"</strong>?</p>
                        <p class="text-muted small mb-0 mt-2">Esta acción no se puede deshacer.</p>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>

                        <form action="{{ route('ubicaciones.destroy', $ubicacion->id) }}" method="post" class="d-inline">
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
