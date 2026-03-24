@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold">Libros</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
                    data-bs-toggle="modal" data-bs-target="#modalCreateLibro"
                    title="Nuevo Libro">
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
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Sinopsis</th>
                    <th>Clasificación</th>
                    <th>Año de Publicación</th>
                    <th>Género</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($libros as $libro)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $libro->titulo }}</td>
                        <td>{{ Str::limit($libro->sinopsis, 50) }}</td>
                        <td>{{ $libro->nombre_clasificacion }}</td>
                        <td>{{ $libro->anio_publicacion_original }}</td>
                        <td>{{ $libro->nombre_genero }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                                    data-bs-toggle="modal" data-bs-target="#modalEditLibro{{ $libro->id }}"
                                    title="Editar Libro">
                                <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                            </button>

                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5"
                                    data-bs-toggle="modal" data-bs-target="#modalDeleteLibro{{ $libro->id }}"
                                    title="Eliminar Libro">
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
    <div class="modal fade" id="modalCreateLibro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4">
                        <i class="fa-solid fa-circle-plus me-2"></i> Nuevo Libro
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('libros.store') }}" method="post">
                    @csrf

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Título</label>
                                <input type="text" name="titulo" class="form-control" required maxlength="255">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Año de Publicación</label>
                                <input type="number" name="anio_publicacion_original" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Clasificación</label>
                                <select name="clasificacion_id" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    @foreach($clasificacionesCatalogo as $clasificacion)
                                        <option value="{{ $clasificacion->id }}">{{ $clasificacion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Género</label>
                                <select name="genero_principal_id" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    @foreach($generosCatalogo as $genero)
                                        <option value="{{ $genero->id }}">{{ $genero->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Sinopsis</label>
                                <textarea name="sinopsis" class="form-control" rows="5" required></textarea>
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
    @foreach($libros as $libro)
        <div class="modal fade" id="modalEditLibro{{ $libro->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-pen-to-square me-2"></i> Editar Libro
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('libros.update', $libro->id) }}" method="post">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Título</label>
                                    <input type="text" name="titulo" class="form-control" value="{{ $libro->titulo }}" required maxlength="255">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Año de Publicación</label>
                                    <input type="number" name="anio_publicacion_original" class="form-control" value="{{ $libro->anio_publicacion_original }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Clasificación</label>
                                    <select name="clasificacion_id" class="form-select" required>
                                        <option value="">Seleccione</option>
                                        @foreach($clasificacionesCatalogo as $clasificacion)
                                            <option value="{{ $clasificacion->id }}" {{ $libro->clasificacion_id == $clasificacion->id ? 'selected' : '' }}>
                                                {{ $clasificacion->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Género</label>
                                    <select name="genero_principal_id" class="form-select" required>
                                        <option value="">Seleccione</option>
                                        @foreach($generosCatalogo as $genero)
                                            <option value="{{ $genero->id }}" {{ $libro->genero_principal_id == $genero->id ? 'selected' : '' }}>
                                                {{ $genero->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Sinopsis</label>
                                    <textarea name="sinopsis" class="form-control" rows="5" required>{{ $libro->sinopsis }}</textarea>
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

        <div class="modal fade" id="modalDeleteLibro{{ $libro->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Estás seguro de eliminar el libro <br><strong>"{{ $libro->titulo }}"</strong>?</p>
                        <p class="text-muted small mb-0 mt-2">Esta acción no se puede deshacer.</p>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>

                        <form action="{{ route('libros.destroy', $libro->id) }}" method="post" class="d-inline">
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
