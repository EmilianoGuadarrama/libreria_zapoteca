@extends('layouts.dashboard')

@section('dashboard-content')

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 text-dark fw-bold">Ediciones</h3>
            <small class="text-muted">Gestión de ediciones, portadas, ISBN, precios y existencias.</small>
        </div>

        <button type="button"
                class="btn btn-link p-0 text-decoration-none fs-2"
                data-bs-toggle="modal"
                data-bs-target="#modalCreateEdicion"
                title="Nueva edición">
            <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
        </button>
    </div>

    @if(session('status'))
        <div class="alert alert-dismissible fade show shadow-sm"
             role="alert"
             style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
            <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i>
            <span class="fw-semibold">{{ session('status') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <span class="fw-semibold">{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped mi-datatable align-middle" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th class="text-center">Portada</th>
                    <th>Libro</th>
                    <th>Editorial</th>
                    <th>Idioma</th>
                    <th>Formato</th>
                    <th>ISBN</th>
                    <th>Edición</th>
                    <th>Precio</th>
                    <th>Existencias</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($ediciones as $edicion)
                    @php
                        $portadaEdicion = $edicion->portada ? asset('storage/' . $edicion->portada) : null;
                        $portadaLibro = optional($edicion->libro)->portada ? asset('storage/' . $edicion->libro->portada) : null;
                        $portadaFinal = $portadaEdicion ?? $portadaLibro;
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td class="text-center">
                            @if($portadaFinal)
                                <img src="{{ $portadaFinal }}"
                                     alt="{{ $edicion->alt_imagen ?? optional($edicion->libro)->titulo }}"
                                     class="rounded shadow-sm"
                                     style="width: 45px; height: 65px; object-fit: cover;">
                            @else
                                <div class="rounded d-flex align-items-center justify-content-center text-muted mx-auto"
                                     style="width: 45px; height: 65px; background: #f8f2fb; border: 1px dashed #cdb7dc; font-size: 0.7rem;">
                                    Sin img
                                </div>
                            @endif
                        </td>

                        <td class="fw-semibold">
                            {{ optional($edicion->libro)->titulo ?? 'N/A' }}
                        </td>

                        <td>{{ optional($edicion->editorial)->nombre ?? 'N/A' }}</td>
                        <td>{{ optional($edicion->idioma)->nombre ?? 'N/A' }}</td>
                        <td>{{ optional($edicion->formato)->nombre ?? 'N/A' }}</td>
                        <td>{{ $edicion->isbn }}</td>
                        <td>{{ $edicion->numero_edicion }}ª</td>
                        <td>${{ number_format($edicion->precio_venta, 2) }}</td>
                        <td>{{ $edicion->existencias }}</td>

                        <td class="text-end">
                            <button type="button"
                                    class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalShowEdicion{{ $edicion->id }}"
                                    title="Ver detalles">
                                <i class="fa-solid fa-eye" style="color: #4b1c71;"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditEdicion{{ $edicion->id }}"
                                    title="Editar edición">
                                <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-link p-0 text-decoration-none fs-5"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDeleteEdicion{{ $edicion->id }}"
                                    title="Eliminar edición">
                                <i class="fa-regular fa-trash-can" style="color: #000;"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            No hay ediciones registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="modalCreateEdicion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0"
                 style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4">
                    <i class="fa-solid fa-circle-plus me-2"></i> Nueva Edición
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('ediciones.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body p-4">
                    <div class="row g-4">

                        <div class="col-lg-4">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Portada de la edición</label>

                            <div class="dropzone-container position-relative rounded-4 p-3 text-center"
                                 style="border: 2px dashed #cdb7dc; background: #fcf9ff; cursor: pointer;">
                                <input type="file"
                                       name="portada"
                                       class="js-portada-input position-absolute w-100 h-100"
                                       style="top:0; left:0; opacity:0; cursor:pointer;"
                                       accept="image/png,image/jpeg,image/jpg,image/webp">

                                <div class="js-preview-area d-none flex-column align-items-center">
                                    <img src=""
                                         class="js-preview-img rounded shadow-sm mb-2"
                                         style="max-width: 100%; height: 250px; object-fit: cover;">
                                    <span class="small text-muted">Haz clic para cambiar</span>
                                </div>

                                <div class="js-upload-area py-5">
                                    <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2" style="color: #7f4ca5;"></i>
                                    <div class="fw-bold" style="color: #4b1c71;">Sube una portada</div>
                                    <div class="small text-muted">JPG, PNG o WEBP. Máx 4 MB.</div>
                                </div>
                            </div>

                            @error('portada')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <div class="mt-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Texto alternativo</label>
                                <input type="text"
                                       name="alt_imagen"
                                       class="form-control @error('alt_imagen') is-invalid @enderror"
                                       value="{{ old('alt_imagen') }}"
                                       placeholder="Ej: Portada del libro">
                                @error('alt_imagen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="row g-3">

                                <div class="col-md-12">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Libro</label>
                                    <select name="libro_id"
                                            class="form-select tom-select-single @error('libro_id') is-invalid @enderror"
                                            required>
                                        <option value="">Seleccione un libro</option>
                                        @foreach($librosCatalogo as $libro)
                                            <option value="{{ $libro->id }}" {{ old('libro_id') == $libro->id ? 'selected' : '' }}>
                                                {{ $libro->titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('libro_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Editorial</label>
                                    <select name="editorial_id"
                                            class="form-select tom-select-single @error('editorial_id') is-invalid @enderror"
                                            required>
                                        <option value="">Seleccione una editorial</option>
                                        @foreach($editorialesCatalogo as $editorial)
                                            <option value="{{ $editorial->id }}" {{ old('editorial_id') == $editorial->id ? 'selected' : '' }}>
                                                {{ $editorial->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editorial_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Idioma</label>
                                    <select name="idioma_id"
                                            class="form-select @error('idioma_id') is-invalid @enderror"
                                            required>
                                        <option value="">Seleccione</option>
                                        @foreach($idiomasCatalogo as $idioma)
                                            <option value="{{ $idioma->id }}" {{ old('idioma_id') == $idioma->id ? 'selected' : '' }}>
                                                {{ $idioma->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('idioma_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Formato</label>
                                    <select name="formato_id"
                                            class="form-select @error('formato_id') is-invalid @enderror"
                                            required>
                                        <option value="">Seleccione</option>
                                        @foreach($formatosCatalogo as $formato)
                                            <option value="{{ $formato->id }}" {{ old('formato_id') == $formato->id ? 'selected' : '' }}>
                                                {{ $formato->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('formato_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">ISBN</label>
                                    <input type="text"
                                           name="isbn"
                                           class="form-control @error('isbn') is-invalid @enderror"
                                           value="{{ old('isbn') }}"
                                           placeholder="Ej: 9780000000001"
                                           maxlength="17"
                                           required>
                                    @error('isbn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Año publicación</label>
                                    <input type="number"
                                           name="anio_publicacion"
                                           class="form-control @error('anio_publicacion') is-invalid @enderror"
                                           value="{{ old('anio_publicacion', date('Y')) }}"
                                           min="1000"
                                           max="{{ date('Y') }}"
                                           required>
                                    @error('anio_publicacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">No. edición</label>
                                    <input type="number"
                                           name="numero_edicion"
                                           class="form-control @error('numero_edicion') is-invalid @enderror"
                                           value="{{ old('numero_edicion', 1) }}"
                                           min="1"
                                           required>
                                    @error('numero_edicion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Páginas</label>
                                    <input type="number"
                                           name="numero_paginas"
                                           class="form-control @error('numero_paginas') is-invalid @enderror"
                                           value="{{ old('numero_paginas') }}"
                                           min="1"
                                           required>
                                    @error('numero_paginas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Precio venta</label>
                                    <input type="number"
                                           name="precio_venta"
                                           class="form-control @error('precio_venta') is-invalid @enderror"
                                           value="{{ old('precio_venta', 0) }}"
                                           step="0.01"
                                           min="0"
                                           required>
                                    @error('precio_venta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Existencias</label>
                                    <input type="number"
                                           name="existencias"
                                           class="form-control @error('existencias') is-invalid @enderror"
                                           value="{{ old('existencias', 0) }}"
                                           min="0"
                                           required>
                                    @error('existencias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Stock mínimo</label>
                                    <input type="number"
                                           name="stock_minimo"
                                           class="form-control @error('stock_minimo') is-invalid @enderror"
                                           value="{{ old('stock_minimo', 0) }}"
                                           min="0"
                                           required>
                                    @error('stock_minimo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">
                        Guardar Edición
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODALES SHOW, EDIT Y DELETE --}}
@foreach($ediciones as $edicion)
    @php
        $portadaEdicion = $edicion->portada ? asset('storage/' . $edicion->portada) : null;
        $portadaLibro = optional($edicion->libro)->portada ? asset('storage/' . $edicion->libro->portada) : null;
        $portadaFinal = $portadaEdicion ?? $portadaLibro;
    @endphp

    {{-- MODAL SHOW --}}
    <div class="modal fade" id="modalShowEdicion{{ $edicion->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-header border-0"
                     style="background: linear-gradient(135deg, #4b1c71 0%, #7f4ca5 100%); color: white;">
                    <h5 class="modal-title bebas fs-3">
                        <i class="fa-solid fa-book-open me-2"></i> Detalle de la Edición
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4 p-lg-5">
                    <div class="row g-4">
                        <div class="col-lg-4 text-center">
                            @if($portadaFinal)
                                <img src="{{ $portadaFinal }}"
                                     alt="{{ $edicion->alt_imagen ?? optional($edicion->libro)->titulo }}"
                                     class="img-fluid rounded-4 shadow-lg"
                                     style="max-height: 430px; object-fit: cover;">
                            @else
                                <div class="rounded-4 d-flex align-items-center justify-content-center mx-auto"
                                     style="width: 100%; max-width: 280px; height: 400px; background: linear-gradient(180deg, #fbf7ff 0%, #f3e9fb 100%); border: 2px dashed #cfb3e2; color: #7a6a88;">
                                    <div class="text-center">
                                        <i class="fa-solid fa-image fs-1 mb-2 text-muted"></i>
                                        <p class="mb-0 fw-semibold">Sin portada</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-8">
                            <h2 class="fw-bold mb-2" style="color: #4b1c71;">
                                {{ optional($edicion->libro)->titulo ?? 'Sin libro asignado' }}
                            </h2>

                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <span class="badge" style="background-color: #fff0ff; color: #7f4ca5; border: 1px solid #dbb6ee; font-size: .9rem;">
                                    {{ optional($edicion->editorial)->nombre ?? 'Sin editorial' }}
                                </span>

                                <span class="badge" style="background-color: #f0e6f7; color: #4b1c71; border: 1px solid #cdb7dc; font-size: .9rem;">
                                    {{ optional($edicion->idioma)->nombre ?? 'Sin idioma' }}
                                </span>

                                <span class="badge bg-light text-dark border" style="font-size: .9rem;">
                                    {{ optional($edicion->formato)->nombre ?? 'Sin formato' }}
                                </span>
                            </div>

                            <h3 class="fw-bold mb-4" style="color: #4b1c71;">
                                ${{ number_format($edicion->precio_venta, 2) }}
                            </h3>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="p-3 rounded-4 h-100" style="background-color: #f8f2fb;">
                                        <div class="text-muted">Editorial</div>
                                        <div class="fw-bold">{{ optional($edicion->editorial)->nombre ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 rounded-4 h-100" style="background-color: #f8f2fb;">
                                        <div class="text-muted">Idioma</div>
                                        <div class="fw-bold">{{ optional($edicion->idioma)->nombre ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 rounded-4 h-100" style="background-color: #f8f2fb;">
                                        <div class="text-muted">Formato</div>
                                        <div class="fw-bold">{{ optional($edicion->formato)->nombre ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="p-3 rounded-4 h-100 border">
                                        <div class="text-muted">ISBN</div>
                                        <div class="fw-bold">{{ $edicion->isbn }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="p-3 rounded-4 h-100 border">
                                        <div class="text-muted">Edición</div>
                                        <div class="fw-bold">{{ $edicion->numero_edicion }}ª</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="p-3 rounded-4 h-100 border">
                                        <div class="text-muted">Páginas</div>
                                        <div class="fw-bold">{{ $edicion->numero_paginas }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="p-3 rounded-4 h-100 border">
                                        <div class="text-muted">Existencias</div>
                                        <div class="fw-bold">{{ $edicion->existencias }} uds.</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 rounded-4 h-100 border">
                                        <div class="text-muted">Stock mínimo</div>
                                        <div class="fw-bold">{{ $edicion->stock_minimo }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 rounded-4 h-100 border">
                                        <div class="text-muted">Año publicación</div>
                                        <div class="fw-bold">{{ $edicion->anio_publicacion }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 rounded-4 h-100 border">
                                        <div class="text-muted">Portada usada</div>
                                        <div class="fw-bold">
                                            @if($edicion->portada)
                                                Edición
                                            @elseif(optional($edicion->libro)->portada)
                                                Libro
                                            @else
                                                Sin portada
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(optional($edicion->libro)->sinopsis)
                                <div class="p-4 rounded-4" style="border: 1px solid #eadcf2;">
                                    <div class="small text-muted fw-bold mb-2 text-uppercase">Sinopsis</div>
                                    <p class="mb-0">{{ $edicion->libro->sinopsis }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEditEdicion{{ $edicion->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0"
                     style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Edición
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('ediciones.update', $edicion->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body p-4">
                        <div class="row g-4">

                            <div class="col-lg-4">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Portada de la edición</label>

                                <div class="dropzone-container position-relative rounded-4 p-3 text-center"
                                     style="border: 2px dashed #cdb7dc; background: #fcf9ff; cursor: pointer;">
                                    <input type="file"
                                           name="portada"
                                           class="js-portada-input position-absolute w-100 h-100"
                                           style="top:0; left:0; opacity:0; cursor:pointer;"
                                           accept="image/png,image/jpeg,image/jpg,image/webp">

                                    <div class="js-preview-area {{ $portadaFinal ? 'd-flex' : 'd-none' }} flex-column align-items-center">
                                        <img src="{{ $portadaFinal ?? '' }}"
                                             class="js-preview-img rounded shadow-sm mb-2"
                                             style="max-width: 100%; height: 250px; object-fit: cover;">
                                        <span class="small text-muted">Haz clic para cambiar</span>
                                    </div>

                                    <div class="js-upload-area {{ $portadaFinal ? 'd-none' : '' }} py-5">
                                        <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2" style="color: #7f4ca5;"></i>
                                        <div class="fw-bold" style="color: #4b1c71;">Sube una portada</div>
                                        <div class="small text-muted">JPG, PNG o WEBP. Máx 4 MB.</div>
                                    </div>
                                </div>

                                @error('portada')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                                <div class="mt-3">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Texto alternativo</label>
                                    <input type="text"
                                           name="alt_imagen"
                                           class="form-control"
                                           value="{{ old('alt_imagen', $edicion->alt_imagen) }}"
                                           placeholder="Ej: Portada del libro">
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="row g-3">

                                    <div class="col-md-12">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Libro</label>
                                        <select name="libro_id" class="form-select tom-select-single" required>
                                            <option value="">Seleccione un libro</option>
                                            @foreach($librosCatalogo as $libro)
                                                <option value="{{ $libro->id }}" {{ old('libro_id', $edicion->libro_id) == $libro->id ? 'selected' : '' }}>
                                                    {{ $libro->titulo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Editorial</label>
                                        <select name="editorial_id" class="form-select tom-select-single" required>
                                            <option value="">Seleccione una editorial</option>
                                            @foreach($editorialesCatalogo as $editorial)
                                                <option value="{{ $editorial->id }}" {{ old('editorial_id', $edicion->editorial_id) == $editorial->id ? 'selected' : '' }}>
                                                    {{ $editorial->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Idioma</label>
                                        <select name="idioma_id" class="form-select" required>
                                            <option value="">Seleccione</option>
                                            @foreach($idiomasCatalogo as $idioma)
                                                <option value="{{ $idioma->id }}" {{ old('idioma_id', $edicion->idioma_id) == $idioma->id ? 'selected' : '' }}>
                                                    {{ $idioma->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Formato</label>
                                        <select name="formato_id" class="form-select" required>
                                            <option value="">Seleccione</option>
                                            @foreach($formatosCatalogo as $formato)
                                                <option value="{{ $formato->id }}" {{ old('formato_id', $edicion->formato_id) == $formato->id ? 'selected' : '' }}>
                                                    {{ $formato->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">ISBN</label>
                                        <input type="text"
                                               name="isbn"
                                               class="form-control"
                                               value="{{ old('isbn', $edicion->isbn) }}"
                                               maxlength="17"
                                               required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Año publicación</label>
                                        <input type="number"
                                               name="anio_publicacion"
                                               class="form-control"
                                               value="{{ old('anio_publicacion', $edicion->anio_publicacion) }}"
                                               min="1000"
                                               max="{{ date('Y') }}"
                                               required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">No. edición</label>
                                        <input type="number"
                                               name="numero_edicion"
                                               class="form-control"
                                               value="{{ old('numero_edicion', $edicion->numero_edicion) }}"
                                               min="1"
                                               required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Páginas</label>
                                        <input type="number"
                                               name="numero_paginas"
                                               class="form-control"
                                               value="{{ old('numero_paginas', $edicion->numero_paginas) }}"
                                               min="1"
                                               required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Precio venta</label>
                                        <input type="number"
                                               name="precio_venta"
                                               class="form-control"
                                               value="{{ old('precio_venta', $edicion->precio_venta) }}"
                                               step="0.01"
                                               min="0"
                                               required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Existencias</label>
                                        <input type="number"
                                               name="existencias"
                                               class="form-control"
                                               value="{{ old('existencias', $edicion->existencias) }}"
                                               min="0"
                                               required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color: #4b1c71;">Stock mínimo</label>
                                        <input type="number"
                                               name="stock_minimo"
                                               class="form-control"
                                               value="{{ old('stock_minimo', $edicion->stock_minimo) }}"
                                               min="0"
                                               required>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">
                            Actualizar Edición
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="modalDeleteEdicion{{ $edicion->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0"
                     style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Eliminar Edición
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="mb-1">¿Deseas eliminar esta edición?</p>
                    <p class="fw-bold mb-0" style="color: #4b1c71;">
                        {{ optional($edicion->libro)->titulo ?? 'Sin libro' }} - {{ $edicion->numero_edicion }}ª edición
                    </p>
                    <small class="text-muted">Esta acción aplicará eliminación lógica si tu modelo usa SoftDeletes.</small>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <form action="{{ route('ediciones.destroy', $edicion->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.tom-select-single').forEach(function (element) {
            if (!element.tomselect) {
                new TomSelect(element, {
                    create: false,
                    allowEmptyOption: true,
                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    }
                });
            }
        });

        document.querySelectorAll('.js-portada-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const file = this.files[0];
                const container = this.closest('.dropzone-container');

                if (!file || !container) {
                    return;
                }

                const previewArea = container.querySelector('.js-preview-area');
                const previewImg = container.querySelector('.js-preview-img');
                const uploadArea = container.querySelector('.js-upload-area');

                const reader = new FileReader();

                reader.onload = function (event) {
                    previewImg.src = event.target.result;

                    previewArea.classList.remove('d-none');
                    previewArea.classList.add('d-flex');

                    uploadArea.classList.add('d-none');
                };

                reader.readAsDataURL(file);
            });
        });
    });
</script>

@endsection