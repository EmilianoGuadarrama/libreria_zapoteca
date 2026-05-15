@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold">Clasificaciones</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
                    data-bs-toggle="modal" data-bs-target="#modalCreateClasificacion"
                    data-bs-placement="left" title="Nueva clasificacion">
                <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
            </button>
        </div>

        @if(session('status'))
            <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i> <span class="fw-semibold">{{ session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped mi-datatable align-middle" style="width:100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($clasificaciones as $clasificacion)
                    @php
                        $librosDeEstaClasificacion = isset($librosVinculados) ? $librosVinculados->get($clasificacion->id, collect()) : collect();
                        $cantidadLibros = $librosDeEstaClasificacion->count();
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">
                            <button type="button"
                                    class="btn btn-link p-0 text-decoration-none text-start d-flex align-items-center"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalLibrosClasificacion{{ $clasificacion->id }}"
                                    title="Ver libros vinculados a esta clasificacion">
                                <span class="fw-bold fs-6" style="color: #4b1c71;">{{ $clasificacion->nombre }}</span>
                                <span class="badge rounded-pill ms-2" style="background-color: #fff0ff; color: #7f4ca5; border: 1px solid #dbb6ee;">
                                    <i class="fa-solid fa-book-open"></i> {{ $cantidadLibros }}
                                </span>
                            </button>
                        </td>

                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                                    data-bs-toggle="modal" data-bs-target="#modalEditClasificacion{{ $clasificacion->id }}"
                                    data-bs-placement="top" title="Editar clasificacion">
                                <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                            </button>

                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5"
                                    data-bs-toggle="modal" data-bs-target="#modalDeleteClasificacion{{ $clasificacion->id }}"
                                    data-bs-placement="top" title="Eliminar clasificacion">
                                <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalCreateClasificacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4">
                        <i class="fa-solid fa-circle-plus me-2"></i> Nueva clasificacion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('clasificaciones.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Nombre de clasificacion</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Ficcion, Terror..." required maxlength="100">
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

    @foreach($clasificaciones as $clasificacion)
        @php
            $librosDeEstaClasificacion = isset($librosVinculados) ? $librosVinculados->get($clasificacion->id, collect()) : collect();
            $cantidadLibros = $librosDeEstaClasificacion->count();
        @endphp

        <div class="modal fade" id="modalEditClasificacion{{ $clasificacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-pen-to-square me-2"></i> Editar clasificacion
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('clasificaciones.update', $clasificacion->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            @if($librosDeEstaClasificacion->isNotEmpty())
                                <div class="mb-4 p-3 rounded-4" style="background-color: #f8f2fb; border: 1px solid #eadcf3;">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <div>
                                            <div class="fw-bold mb-1" style="color: #4b1c71;">Libros afectados: {{ $cantidadLibros }}</div>
                                            <div class="small text-muted">Los cambios en esta clasificacion impactaran a los libros que ya la tienen asignada.</div>
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm text-white rounded-pill px-3 fw-bold"
                                                data-bs-dismiss="modal"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalLibrosClasificacion{{ $clasificacion->id }}"
                                                style="background-color: #4b1c71;">
                                            <i class="fa-solid fa-book-open me-1"></i> Ver libros afectados
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Nombre de clasificacion</label>
                                <input type="text" name="nombre" class="form-control" value="{{ $clasificacion->nombre }}" required maxlength="100">
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

        <div class="modal fade" id="modalDeleteClasificacion{{ $clasificacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar eliminacion
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">Estas seguro de eliminar la clasificacion <br><strong>"{{ $clasificacion->nombre }}"</strong>?</p>
                        <p class="text-muted small mb-0 mt-2">Esta accion no se puede deshacer.</p>

                        @if($librosDeEstaClasificacion->isNotEmpty())
                            <div class="mt-4 p-3 rounded-4 text-start" style="background-color: #fff3f3; border: 1px solid #f1c5c5;">
                                <div class="fw-bold mb-1 text-danger">Libros afectados: {{ $cantidadLibros }}</div>
                                <div class="small text-muted mb-3">Esta clasificacion esta vinculada actualmente a varios libros.</div>
                                <div class="text-center text-md-start">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"
                                            data-bs-dismiss="modal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalLibrosClasificacion{{ $clasificacion->id }}">
                                        <i class="fa-solid fa-book-open me-1"></i> Ver libros afectados
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>

                        <form action="{{ route('clasificaciones.destroy', $clasificacion->id) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Si, eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalLibrosClasificacion{{ $clasificacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-book-bookmark me-2"></i> Libros en "{{ $clasificacion->nombre }}"
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        @if($librosDeEstaClasificacion->isEmpty())
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fa-solid fa-books fa-2x" style="color: #cfb3e2;"></i>
                                </div>
                                <h5 class="fw-bold" style="color: #4b1c71;">Sin libros vinculados</h5>
                                <p class="text-muted mb-0">Esta clasificacion todavia no esta asignada a ningun libro.</p>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <span class="text-muted fw-bold">Libros actuales ({{ $cantidadLibros }})</span>
                            </div>

                            <div class="row g-3">
                                @foreach($librosDeEstaClasificacion as $libro)
                                    <div class="col-md-6">
                                        <button type="button"
                                                class="d-flex align-items-center w-100 bg-white p-3 rounded-4 shadow-sm h-100 border-0 text-start js-open-book-detail-classification"
                                                style="border: 1px solid #eadcf2; transition: transform 0.2s ease; cursor: pointer;"
                                                onmouseover="this.style.transform='translateY(-2px)';"
                                                onmouseout="this.style.transform='translateY(0)';"
                                                data-bs-dismiss="modal"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDetalleLibroClasificacion"
                                                data-parent-modal="#modalLibrosClasificacion{{ $clasificacion->id }}"
                                                data-titulo="{{ $libro->titulo }}"
                                                data-isbn="{{ $libro->isbn ?? 'N/A' }}"
                                                data-autor="{{ $libro->autores ?? 'Autor no registrado' }}"
                                                data-editorial="{{ $libro->editorial ?? 'N/A' }}"
                                                data-anio-original="{{ $libro->anio_publicacion_original ?? 'N/A' }}"
                                                data-anio-edicion="{{ $libro->anio_publicacion ?? 'N/A' }}"
                                                data-numero-edicion="{{ $libro->numero_edicion ?? 'N/A' }}"
                                                data-numero-paginas="{{ $libro->numero_paginas ?? 'N/A' }}"
                                                data-existencias="{{ $libro->existencias ?? 'N/A' }}"
                                                data-stock-minimo="{{ $libro->stock_minimo ?? 'N/A' }}"
                                                data-precio-venta="{{ $libro->precio_venta ?? 0 }}"
                                                data-clasificacion="{{ $clasificacion->nombre }}"
                                                data-alt-imagen="{{ $libro->alt_imagen ?? 'Sin imagen' }}"
                                                data-imagen="{{ !empty($libro->portada) ? asset('storage/' . $libro->portada) : '' }}">

                                            @if(!empty($libro->portada))
                                                <img src="{{ asset('storage/' . $libro->portada) }}"
                                                     alt="{{ $libro->titulo }}"
                                                     class="rounded shadow-sm"
                                                     style="width: 55px; height: 75px; object-fit: cover;">
                                            @else
                                                <div class="rounded d-flex align-items-center justify-content-center text-muted"
                                                     style="width: 55px; height: 75px; background: #f8f2fb; border: 1px dashed #cdb7dc; font-size: 0.7rem;">
                                                    Sin img
                                                </div>
                                            @endif

                                            <div class="ms-3 flex-grow-1 min-w-0">
                                                <h6 class="fw-bold mb-1 lh-sm" style="color: #4b1c71; font-size: 0.95rem;">{{ $libro->titulo }}</h6>
                                                <div class="small text-muted mb-2" style="font-size: 0.8rem;">{{ $libro->autores }}</div>
                                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                                    @if(!empty($libro->isbn))
                                                        <span class="small text-muted">ISBN: {{ $libro->isbn }}</span>
                                                    @endif
                                                    @if(!empty($libro->precio_venta))
                                                        <span class="fw-bold text-success">${{ number_format((float) $libro->precio_venta, 2) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="modalDetalleLibroClasificacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #4b1c71 0%, #7f4ca5 100%); color: white;">
                    <div>
                        <h5 class="modal-title bebas fs-3 mb-1"><i class="fa-solid fa-book-open me-2"></i>Detalle del libro</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" title="Cerrar" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 p-lg-5">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="text-center h-100 d-flex align-items-center justify-content-center">
                                <img id="detailClassificationCoverImage" src="" alt="Portada del libro" class="book-detail-cover d-none" loading="lazy" decoding="async">
                                <div id="detailClassificationCoverEmpty" class="book-detail-empty d-flex align-items-center justify-content-center mx-auto">Sin imagen</div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
                                <div>
                                    <h3 id="detailClassificationTitle" class="fw-bold mb-1" style="color:#4b1c71;"></h3>
                                    <p id="detailClassificationIsbn" class="text-muted mb-0"></p>
                                </div>
                                <div class="price-panel p-3 px-4 text-md-end">
                                    <div class="small text-muted mb-1">Precio de venta</div>
                                    <div id="detailClassificationPrice" class="fw-bold text-success fs-4"></div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6"><div class="detail-card soft"><div class="small text-muted">Autor</div><div id="detailClassificationAuthor" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card soft"><div class="small text-muted">Clasificación</div><div id="detailClassificationName" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Editorial</div><div id="detailClassificationEditorial" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Año original</div><div id="detailClassificationOriginalYear" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Año de esta edición</div><div id="detailClassificationEditionYear" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Número de edición</div><div id="detailClassificationEditionNumber" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Número de páginas</div><div id="detailClassificationPages" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Existencias</div><div id="detailClassificationStock" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Stock mínimo</div><div id="detailClassificationMinStock" class="fw-semibold"></div></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .book-detail-cover { width: 100%; max-width: 240px; height: 340px; object-fit: cover; border-radius: 18px; box-shadow: 0 20px 40px rgba(75, 28, 113, 0.18); background: #f8f2fb; }
        .book-detail-empty { width: 100%; max-width: 240px; height: 340px; border-radius: 18px; border: 1px dashed #cfb3e2; background: linear-gradient(180deg, #fbf7ff 0%, #f3e9fb 100%); color: #7a6a88; }
        .detail-card { border-radius: 16px; padding: 1rem; height: 100%; }
        .detail-card.soft { background: #f8f2fb; }
        .detail-card.plain { border: 1px solid #eadcf3; background: #fff; }
        .price-panel { border-radius: 18px; background: linear-gradient(135deg, #fff7e8 0%, #fff2cf 100%); border: 1px solid #f2ddb2; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detailModal = document.getElementById('modalDetalleLibroClasificacion');
            const coverImage = document.getElementById('detailClassificationCoverImage');
            const coverEmpty = document.getElementById('detailClassificationCoverEmpty');
            let parentModalId = null;

            function formatMoney(value) {
                return `$ ${Number(value || 0).toFixed(2)}`;
            }

            function setCover(src, fallbackText) {
                if (src) {
                    coverImage.src = src;
                    coverImage.classList.remove('d-none');
                    coverEmpty.classList.add('d-none');
                } else {
                    coverImage.removeAttribute('src');
                    coverImage.classList.add('d-none');
                    coverEmpty.textContent = fallbackText || 'Sin imagen';
                    coverEmpty.classList.remove('d-none');
                }
            }

            if (detailModal) {
                detailModal.addEventListener('show.bs.modal', function(event) {
                    const data = event.relatedTarget.dataset;
                    if (!data.titulo) return;

                    parentModalId = data.parentModal || null;

                    document.getElementById('detailClassificationTitle').textContent = data.titulo || '';
                    document.getElementById('detailClassificationIsbn').textContent = `ISBN: ${data.isbn || 'N/A'}`;
                    document.getElementById('detailClassificationAuthor').textContent = data.autor || 'N/A';
                    document.getElementById('detailClassificationName').textContent = data.clasificacion || 'N/A';
                    document.getElementById('detailClassificationEditorial').textContent = data.editorial || 'N/A';
                    document.getElementById('detailClassificationOriginalYear').textContent = data.anioOriginal || 'N/A';
                    document.getElementById('detailClassificationEditionYear').textContent = data.anioEdicion || 'N/A';
                    document.getElementById('detailClassificationEditionNumber').textContent = data.numeroEdicion || 'N/A';
                    document.getElementById('detailClassificationPages').textContent = data.numeroPaginas || 'N/A';
                    document.getElementById('detailClassificationStock').textContent = data.existencias || 'N/A';
                    document.getElementById('detailClassificationMinStock').textContent = data.stockMinimo || 'N/A';
                    document.getElementById('detailClassificationPrice').textContent = formatMoney(data.precioVenta || 0);

                    setCover(data.imagen || '', data.altImagen || 'Sin imagen');
                });

                detailModal.addEventListener('hidden.bs.modal', function() {
                    if (parentModalId) {
                        const parentModalEl = document.querySelector(parentModalId);
                        if (parentModalEl) {
                            bootstrap.Modal.getOrCreateInstance(parentModalEl).show();
                        }
                        parentModalId = null;
                    }
                });
            }
        });
    </script>

@endsection
