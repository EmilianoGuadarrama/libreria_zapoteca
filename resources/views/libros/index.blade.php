@extends('layouts.dashboard')

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

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
            <table class="table table-bordered table-striped mi-datatable align-middle" style="width:100%">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Portada</th>
                    <th>Título</th>
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
                        <td class="text-center">
                            @if($libro->portada)
                                <img src="{{ asset('storage/' . $libro->portada) }}" alt="{{ $libro->titulo }}" class="rounded shadow-sm" style="width: 45px; height: 65px; object-fit: cover;">
                            @else
                                <div class="rounded d-flex align-items-center justify-content-center text-muted mx-auto" style="width: 45px; height: 65px; background: #f8f2fb; border: 1px dashed #cdb7dc; font-size: 0.7rem;">Sin img</div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $libro->titulo }}</td>
                        <td>{{ $libro->clasificacion->nombre ?? 'N/A' }}</td>
                        <td>{{ $libro->anio_publicacion_original }}</td>
                        <td>{{ $libro->genero->nombre ?? 'N/A' }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                                    data-bs-toggle="modal" data-bs-target="#modalShowLibro{{ $libro->id }}"
                                    title="Ver Detalles">
                                <i class="fa-solid fa-eye" style="color: #4b1c71;"></i>
                            </button>

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

                <form action="{{ route('libros.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipo_operacion" value="crear">

                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Portada</label>
                                <div class="dropzone-container position-relative rounded-4 p-3 text-center" style="border: 2px dashed #cdb7dc; background: #fcf9ff; cursor: pointer; transition: all 0.3s ease;">
                                    <input type="file" name="portada" class="file-input position-absolute w-100 h-100" style="top:0; left:0; opacity:0; cursor:pointer;" accept="image/png,image/jpeg,image/jpg,image/webp">
                                    <div class="preview-area d-none flex-column align-items-center">
                                        <img src="" class="preview-img rounded shadow-sm mb-2" style="max-width: 100%; height: 200px; object-fit: cover;">
                                        <span class="small text-muted">Haz clic para cambiar</span>
                                    </div>
                                    <div class="upload-area py-5">
                                        <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2" style="color: #7f4ca5;"></i>
                                        <div class="fw-bold" style="color: #4b1c71;">Sube una portada</div>
                                        <div class="small text-muted">JPG, PNG, WEBP. Máx 4 MB.</div>
                                    </div>
                                </div>
                                @error('portada') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-lg-8">
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
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Género Principal</label>
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
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Autores</label>
                                        <select name="autores[]" class="tom-select-multiple @error('autores') is-invalid @enderror" multiple placeholder="Seleccione autores...">
                                            @foreach($autoresCatalogo as $autor)
                                                @php $nombreCompleto = $autor->persona ? $autor->persona->nombre . ' ' . $autor->persona->apellido_paterno : 'Desconocido'; @endphp
                                                <option value="{{ $autor->id }}" {{ (is_array(old('autores')) && in_array($autor->id, old('autores'))) ? 'selected' : '' }}>
                                                    {{ $nombreCompleto }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('autores') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Subgéneros</label>
                                        <select name="subgeneros[]" class="tom-select-multiple @error('subgeneros') is-invalid @enderror" multiple placeholder="Seleccione subgéneros...">
                                            @foreach($subgenerosCatalogo as $subgenero)
                                                <option value="{{ $subgenero->id }}" {{ (is_array(old('subgeneros')) && in_array($subgenero->id, old('subgeneros'))) ? 'selected' : '' }}>
                                                    {{ $subgenero->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subgeneros') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Sinopsis</label>
                                        <textarea name="sinopsis" class="form-control @error('sinopsis') is-invalid @enderror"
                                                  rows="3" placeholder="Escribe un breve resumen de la obra..." required minlength="10">{{ old('sinopsis') }}</textarea>
                                        @error('sinopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Guardar Libro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODALES EDITAR, VER Y ELIMINAR --}}
    @foreach($libros as $libro)
        
        {{-- MODAL SHOW --}}
        <div class="modal fade" id="modalShowLibro{{ $libro->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="modal-header border-0" style="background: linear-gradient(135deg, #4b1c71 0%, #7f4ca5 100%); color: white;">
                        <h5 class="modal-title bebas fs-3"><i class="fa-solid fa-book-open me-2"></i> Detalle del Libro</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 p-lg-5">
                        <div class="row g-4">
                            <div class="col-lg-4 text-center">
                                @if($libro->portada)
                                    <img src="{{ asset('storage/' . $libro->portada) }}" alt="{{ $libro->titulo }}" class="img-fluid rounded-4 shadow-lg" style="max-height: 400px; object-fit: cover;">
                                @else
                                    <div class="rounded-4 d-flex align-items-center justify-content-center mx-auto" style="width: 100%; max-width: 250px; height: 350px; background: linear-gradient(180deg, #fbf7ff 0%, #f3e9fb 100%); border: 2px dashed #cfb3e2; color: #7a6a88;">
                                        <div class="text-center">
                                            <i class="fa-solid fa-image fs-1 mb-2 text-muted"></i>
                                            <p class="mb-0 fw-semibold">Sin Portada</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-8">
                                <h2 class="fw-bold mb-2" style="color: #4b1c71;">{{ $libro->titulo }}</h2>
                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    <span class="badge" style="background-color: #fff0ff; color: #7f4ca5; border: 1px solid #dbb6ee; font-size: 0.9rem;">{{ $libro->clasificacion->nombre ?? 'Sin Clasificación' }}</span>
                                    <span class="badge" style="background-color: #f0e6f7; color: #4b1c71; border: 1px solid #cdb7dc; font-size: 0.9rem;">{{ $libro->genero->nombre ?? 'Sin Género' }}</span>
                                    <span class="badge bg-light text-dark border font-size: 0.9rem;"><i class="fa-regular fa-calendar me-1"></i> {{ $libro->anio_publicacion_original }}</span>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 h-100" style="background-color: #fcf9ff; border: 1px solid #eadcf2;">
                                            <div class="small text-muted fw-bold mb-2 text-uppercase" style="letter-spacing: 0.5px;"><i class="fa-solid fa-users me-1"></i> Autores</div>
                                            @if($libro->autores->count() > 0)
                                                <ul class="list-unstyled mb-0 ps-1">
                                                    @foreach($libro->autores as $autor)
                                                        <li class="mb-1"><i class="fa-solid fa-feather-pointed me-2" style="color: #7f4ca5; font-size: 0.8rem;"></i> {{ $autor->persona->nombre ?? '' }} {{ $autor->persona->apellido_paterno ?? '' }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted fst-italic">No hay autores registrados.</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 h-100" style="background-color: #fcf9ff; border: 1px solid #eadcf2;">
                                            <div class="small text-muted fw-bold mb-2 text-uppercase" style="letter-spacing: 0.5px;"><i class="fa-solid fa-tags me-1"></i> Subgéneros</div>
                                            @if($libro->subgeneros->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($libro->subgeneros as $subgenero)
                                                        <span class="badge bg-white text-dark border">{{ $subgenero->nombre }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic">No hay subgéneros registrados.</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background-color: #fff; border: 1px solid #eadcf3;">
                                    <div class="small text-muted fw-bold mb-2 text-uppercase" style="letter-spacing: 0.5px;">Sinopsis</div>
                                    <p class="mb-0 text-dark" style="line-height: 1.6;">{{ $libro->sinopsis }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #4b1c71;" data-bs-toggle="modal" data-bs-target="#modalEditLibro{{ $libro->id }}">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL EDITAR --}}
        <div class="modal fade" id="modalEditLibro{{ $libro->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-pen-to-square me-2"></i> Editar Libro
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('libros.update', $libro->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tipo_operacion" value="editar">
                        <input type="hidden" name="id_error" value="{{ $libro->id }}">

                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Portada (Opcional)</label>
                                    <div class="dropzone-container position-relative rounded-4 p-3 text-center" style="border: 2px dashed #cdb7dc; background: #fcf9ff; cursor: pointer; transition: all 0.3s ease;">
                                        <input type="file" name="portada" class="file-input position-absolute w-100 h-100" style="top:0; left:0; opacity:0; cursor:pointer;" accept="image/png,image/jpeg,image/jpg,image/webp">
                                        
                                        @if($libro->portada)
                                            <div class="preview-area flex-column align-items-center">
                                                <img src="{{ asset('storage/' . $libro->portada) }}" class="preview-img rounded shadow-sm mb-2" style="max-width: 100%; height: 200px; object-fit: cover;">
                                                <span class="small text-muted">Haz clic para reemplazar</span>
                                            </div>
                                            <div class="upload-area d-none py-5">
                                                <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2" style="color: #7f4ca5;"></i>
                                                <div class="fw-bold" style="color: #4b1c71;">Sube una portada</div>
                                                <div class="small text-muted">JPG, PNG, WEBP. Máx 4 MB.</div>
                                            </div>
                                        @else
                                            <div class="preview-area d-none flex-column align-items-center">
                                                <img src="" class="preview-img rounded shadow-sm mb-2" style="max-width: 100%; height: 200px; object-fit: cover;">
                                                <span class="small text-muted">Haz clic para cambiar</span>
                                            </div>
                                            <div class="upload-area py-5">
                                                <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2" style="color: #7f4ca5;"></i>
                                                <div class="fw-bold" style="color: #4b1c71;">Sube una portada</div>
                                                <div class="small text-muted">JPG, PNG, WEBP. Máx 4 MB.</div>
                                            </div>
                                        @endif
                                    </div>
                                    @error('portada') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-lg-8">
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
                                            <label class="form-label fw-bold" style="color: #4b1c71;">Género Principal</label>
                                            <select name="genero_principal_id" class="form-select @error('genero_principal_id') is-invalid @enderror" required>
                                                @foreach($generosCatalogo as $genero)
                                                    <option value="{{ $genero->id }}" {{ old('genero_principal_id', $libro->genero_principal_id) == $genero->id ? 'selected' : '' }}>
                                                        {{ $genero->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('genero_principal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        
                                        @php
                                            $libroAutoresIds = $libro->autores->pluck('id')->toArray();
                                            $libroSubgenerosIds = $libro->subgeneros->pluck('id')->toArray();
                                        @endphp

                                        <div class="col-md-12">
                                            <label class="form-label fw-bold" style="color: #4b1c71;">Autores</label>
                                            <select name="autores[]" class="tom-select-multiple @error('autores') is-invalid @enderror" multiple placeholder="Seleccione autores...">
                                                @foreach($autoresCatalogo as $autor)
                                                    @php $nombreCompleto = $autor->persona ? $autor->persona->nombre . ' ' . $autor->persona->apellido_paterno : 'Desconocido'; @endphp
                                                    <option value="{{ $autor->id }}" {{ (is_array(old('autores', $libroAutoresIds)) && in_array($autor->id, old('autores', $libroAutoresIds))) ? 'selected' : '' }}>
                                                        {{ $nombreCompleto }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('autores') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-bold" style="color: #4b1c71;">Subgéneros</label>
                                            <select name="subgeneros[]" class="tom-select-multiple @error('subgeneros') is-invalid @enderror" multiple placeholder="Seleccione subgéneros...">
                                                @foreach($subgenerosCatalogo as $subgenero)
                                                    <option value="{{ $subgenero->id }}" {{ (is_array(old('subgeneros', $libroSubgenerosIds)) && in_array($subgenero->id, old('subgeneros', $libroSubgenerosIds))) ? 'selected' : '' }}>
                                                        {{ $subgenero->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('subgeneros') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-bold" style="color: #4b1c71;">Sinopsis</label>
                                            <textarea name="sinopsis" class="form-control @error('sinopsis') is-invalid @enderror"
                                                      rows="3" placeholder="Actualiza la sinopsis del libro..." required minlength="10">{{ old('sinopsis', $libro->sinopsis) }}</textarea>
                                            @error('sinopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Actualizar Libro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL ELIMINAR --}}
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
            
            document.querySelectorAll('.tom-select-multiple').forEach(function(el) {
                new TomSelect(el, {
                    plugins: ['remove_button'],
                    placeholder: el.getAttribute('placeholder') || 'Selecciona opciones...',
                    maxOptions: 50
                });
            });

            document.querySelectorAll('.file-input').forEach(input => {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    const container = this.closest('.dropzone-container');
                    const previewArea = container.querySelector('.preview-area');
                    const previewImg = container.querySelector('.preview-img');
                    const uploadArea = container.querySelector('.upload-area');

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewArea.classList.remove('d-none');
                            previewArea.classList.add('d-flex');
                            uploadArea.classList.add('d-none');
                            container.style.borderColor = '#7f4ca5';
                            container.style.backgroundColor = '#fff';
                        };
                        reader.readAsDataURL(file);
                    }
                });
                
                const dropzone = input.closest('.dropzone-container');
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, preventDefaults, false);
                });
                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, preventDefaults, false);
                });
                
                dropzone.addEventListener('dragover', function() {
                    this.style.borderColor = '#7f4ca5';
                    this.style.backgroundColor = '#f3e9fb';
                });
                dropzone.addEventListener('dragleave', function() {
                    this.style.borderColor = '#cdb7dc';
                    this.style.backgroundColor = '#fcf9ff';
                });
                dropzone.addEventListener('drop', function(e) {
                    this.style.borderColor = '#7f4ca5';
                    this.style.backgroundColor = '#fff';
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    input.files = files;
                    const event = new Event('change');
                    input.dispatchEvent(event);
                });
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

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
