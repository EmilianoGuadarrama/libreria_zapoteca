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

        <div class="table-responsive">
            <table class="table table-bordered table-striped mi-datatable" style="width:100%">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Sinopsis</th>
                    <th>Clasificación</th>
                    <th>Año</th>
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

    {{-- MODAL CREAR --}}
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
                    <input type="hidden" name="tipo_operacion" value="crear">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Título</label>
                                <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                                       value="{{ old('titulo') }}" placeholder="Ej: Rayuela" required maxlength="255">
                                @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Año de Publicación</label>
                                <input type="number" name="anio_publicacion_original" class="form-control @error('anio_publicacion_original') is-invalid @enderror"
                                       value="{{ old('anio_publicacion_original', 1000) }}" placeholder="1000" required min="1000" max="{{ date('Y') }}">
                                @error('anio_publicacion_original') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Clasificación</label>
                                <select name="clasificacion_id" class="form-select @error('clasificacion_id') is-invalid @enderror" required>
                                    <option value="">Seleccione una clasificación</option>
                                    @foreach($clasificacionesCatalogo as $clasificacion)
                                        <option value="{{ $clasificacion->id }}" {{ old('clasificacion_id') == $clasificacion->id ? 'selected' : '' }}>
                                            {{ $clasificacion->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('clasificacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Género</label>
                                <select name="genero_principal_id" class="form-select @error('genero_principal_id') is-invalid @enderror" required>
                                    <option value="">Seleccione un género</option>
                                    @foreach($generosCatalogo as $genero)
                                        <option value="{{ $genero->id }}" {{ old('genero_principal_id') == $genero->id ? 'selected' : '' }}>
                                            {{ $genero->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('genero_principal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Sinopsis</label>
                                <textarea name="sinopsis" class="form-control @error('sinopsis') is-invalid @enderror"
                                          rows="5" placeholder="Escribe un breve resumen de la obra..." required minlength="10">{{ old('sinopsis') }}</textarea>
                                @error('sinopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

    {{-- MODALES EDITAR Y ELIMINAR --}}
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
                        <input type="hidden" name="tipo_operacion" value="editar">
                        <input type="hidden" name="id_error" value="{{ $libro->id }}">

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Título</label>
                                    <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                                           value="{{ old('titulo', $libro->titulo) }}" placeholder="Ej: Don Quijote de la Mancha" required maxlength="255">
                                    @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Año de Publicación</label>
                                    <input type="number" name="anio_publicacion_original" class="form-control @error('anio_publicacion_original') is-invalid @enderror"
                                           value="{{ old('anio_publicacion_original', $libro->anio_publicacion_original) }}" placeholder="1605" required min="1000" max="{{ date('Y') }}">
                                    @error('anio_publicacion_original') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Clasificación</label>
                                    <select name="clasificacion_id" class="form-select @error('clasificacion_id') is-invalid @enderror" required>
                                        @foreach($clasificacionesCatalogo as $clasificacion)
                                            <option value="{{ $clasificacion->id }}" {{ old('clasificacion_id', $libro->clasificacion_id) == $clasificacion->id ? 'selected' : '' }}>
                                                {{ $clasificacion->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('clasificacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Género</label>
                                    <select name="genero_principal_id" class="form-select @error('genero_principal_id') is-invalid @enderror" required>
                                        @foreach($generosCatalogo as $genero)
                                            <option value="{{ $genero->id }}" {{ old('genero_principal_id', $libro->genero_principal_id) == $genero->id ? 'selected' : '' }}>
                                                {{ $genero->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('genero_principal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Sinopsis</label>
                                    <textarea name="sinopsis" class="form-control @error('sinopsis') is-invalid @enderror"
                                              rows="5" placeholder="Actualiza la sinopsis del libro..." required minlength="10">{{ old('sinopsis', $libro->sinopsis) }}</textarea>
                                    @error('sinopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if($errors->any())
            @if(old('tipo_operacion') == 'crear')
            var myModal = new bootstrap.Modal(document.getElementById('modalCreateLibro'));
            myModal.show();
            @elseif(old('tipo_operacion') == 'editar')
            var idModal = "{{ old('id_error') }}";
            var myModal = new bootstrap.Modal(document.getElementById('modalEditLibro' + idModal));
            myModal.show();
            @endif
            @endif
        });
    </script>
@endsection
