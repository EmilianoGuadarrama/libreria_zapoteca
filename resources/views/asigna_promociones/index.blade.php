@extends('layouts.dashboard')

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

@section('dashboard-content')
    <style>
        .book-thumb-btn { border: 0; background: transparent; padding: 0; }
        .book-thumb { width: 52px; height: 72px; object-fit: cover; border-radius: 10px; box-shadow: 0 8px 20px rgba(75, 28, 113, 0.15); transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .book-thumb-btn:hover .book-thumb { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(75, 28, 113, 0.22); }
        .book-thumb-placeholder { width: 52px; height: 72px; border-radius: 10px; border: 1px dashed #cdb7dc; background: linear-gradient(180deg, #fbf7ff 0%, #f1e6fb 100%); color: #7a6a88; font-size: 0.7rem; }
        .book-detail-cover { width: 100%; max-width: 240px; height: 340px; object-fit: cover; border-radius: 18px; box-shadow: 0 20px 40px rgba(75, 28, 113, 0.18); background: #f8f2fb; }
        .book-detail-empty { width: 100%; max-width: 240px; height: 340px; border-radius: 18px; border: 1px dashed #cfb3e2; background: linear-gradient(180deg, #fbf7ff 0%, #f3e9fb 100%); color: #7a6a88; }
        .detail-card { border-radius: 16px; padding: 1rem; height: 100%; }
        .detail-card.soft { background: #f8f2fb; }
        .detail-card.plain { border: 1px solid #eadcf3; background: #fff; }
        .price-panel { border-radius: 18px; background: linear-gradient(135deg, #fff7e8 0%, #fff2cf 100%); border: 1px solid #f2ddb2; }
        .dropzone { position: relative; border: 1px dashed #c49ddd; border-radius: 18px; background: linear-gradient(180deg, #fcf9ff 0%, #f3e9fb 100%); padding: 1rem; cursor: pointer; transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease; }
        .dropzone:hover, .dropzone.is-dragover { border-color: #7f4ca5; transform: translateY(-1px); box-shadow: 0 12px 24px rgba(127, 76, 165, 0.12); }
        .dropzone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .dropzone-preview { width: 86px; height: 118px; object-fit: cover; border-radius: 12px; box-shadow: 0 10px 24px rgba(75, 28, 113, 0.16); background: #fff; }
        .zoom-container { width: 200px; height: 300px; overflow: hidden; position: relative; border-radius: 12px; }
        .zoom-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s ease; }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold">Libros en Oferta</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
                    data-bs-toggle="modal" data-bs-target="#modalAssignPromocion"
                    title="Aplicar Oferta a Libro">
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
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first('error') }}
                <button type="button" class="btn-close" title="Cancelar" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped mi-datatable align-middle" style="width:100%">
                <thead style="background-color: #f8f2fb;">
                <tr>
                    <th>#</th>
                    <th>Imagen</th>
                    <th>Libro</th>
                    <th>ISBN</th>
                    <th>Promoción Aplicada</th>
                    <th>Descuento</th>
                    <th>Vigencia</th> <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($asignaciones as $asignacion)
                    @php
                        $precioOriginal = (float) ($asignacion->precio_venta ?? 0);
                        $descuento = (float) ($asignacion->porcentaje_descuento ?? 0);
                        $precioFinal = $precioOriginal - ($precioOriginal * ($descuento / 100));
                        $portadaUrl = !empty($asignacion->portada) ? asset('storage/' . $asignacion->portada) : '';

                        $fechaFinal = isset($asignacion->fecha_final) ? \Carbon\Carbon::parse($asignacion->fecha_final)->startOfDay() : null;
                        $diasRestantes = $fechaFinal ? now()->startOfDay()->diffInDays($fechaFinal, false) : 0;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-center">
                            <button type="button"
                                    class="book-thumb-btn js-open-book-detail"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetalleLibro"
                                    data-edicion-id="{{ $asignacion->edicion_id }}"
                                    data-update-url="{{ route('asigna_promociones.portada.update', $asignacion->edicion_id) }}"
                                    data-titulo="{{ $asignacion->libro_titulo }}"
                                    data-isbn="{{ $asignacion->isbn }}"
                                    data-autor="{{ $asignacion->autor ?? 'N/A' }}"
                                    data-editorial="{{ $asignacion->editorial ?? 'N/A' }}"
                                    data-anio="{{ $asignacion->anio_publicacion ?? 'N/A' }}"
                                    data-numero-edicion="{{ $asignacion->numero_edicion ?? 'N/A' }}"
                                    data-numero-paginas="{{ $asignacion->numero_paginas ?? 'N/A' }}"
                                    data-existencias="{{ $asignacion->existencias ?? 'N/A' }}"
                                    data-stock-minimo="{{ $asignacion->stock_minimo ?? 'N/A' }}"
                                    data-promocion="{{ $asignacion->promocion_nombre }}"
                                    data-descuento="{{ $descuento }}"
                                    data-precio-original="{{ $precioOriginal }}"
                                    data-precio-final="{{ $precioFinal }}"
                                    data-alt-imagen="{{ $asignacion->alt_imagen ?? 'Sin imagen' }}"
                                    data-imagen="{{ $portadaUrl }}"
                                    title="Ver detalles del libro">
                                @if($portadaUrl)
                                    <img src="{{ $portadaUrl }}" alt="{{ $asignacion->alt_imagen ?? $asignacion->libro_titulo }}" class="book-thumb" loading="lazy" decoding="async">
                                @else
                                    <div class="book-thumb-placeholder d-flex align-items-center justify-content-center mx-auto">Sin imagen</div>
                                @endif
                            </button>
                        </td>
                        <td class="fw-bold" style="color: #4b1c71;">{{ $asignacion->libro_titulo }}</td>
                        <td><small class="text-muted">{{ $asignacion->isbn }}</small></td>
                        <td>{{ $asignacion->promocion_nombre }}</td>
                        <td><span class="badge bg-success">{{ number_format($descuento, 0) }}%</span></td>

                        <td>
                            @if($fechaFinal)
                                @if($diasRestantes < 0)
                                    <span class="badge bg-danger rounded-pill"><i class="fa-solid fa-circle-xmark"></i> Expirada</span>
                                @elseif($diasRestantes == 0)
                                    <span class="badge bg-warning text-dark rounded-pill"><i class="fa-solid fa-clock"></i> Expira hoy</span>
                                @else
                                    <span class="badge bg-info text-dark rounded-pill" style="background-color: #dbb6ee !important;"><i class="fa-solid fa-hourglass-half"></i> Faltan {{ $diasRestantes }} días</span>
                                @endif
                                <div class="small text-muted mt-1 fw-semibold">Hasta {{ $fechaFinal->format('d/m/Y') }}</div>
                            @else
                                <span class="text-muted small">Sin fecha</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 text-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalQuitarAsignacion{{ $asignacion->id }}"
                                    title="Quitar Oferta del Libro">
                                <i class="fa-solid fa-link-slash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalAssignPromocion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-tag me-2"></i> Aplicar Oferta a Libro</h5>
                    <button type="button" class="btn-close btn-close-white" title="Cerrar" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('asigna_promociones.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Seleccionar Promoción</label>
                            <select id="select-promocion" name="promocion_id" class="form-select" required>
                                <option value="" disabled selected>-- Elige una promoción activa --</option>
                                @foreach($promociones as $promo)
                                    @php
                                        $fechaFinPromo = isset($promo->fecha_final) ? \Carbon\Carbon::parse($promo->fecha_final)->startOfDay() : now()->startOfDay();
                                        $diasPromo = now()->startOfDay()->diffInDays($fechaFinPromo, false);
                                    @endphp
                                    <option value="{{ $promo->id }}"
                                            data-descuento="{{ $promo->porcentaje_descuento }}"
                                            data-fin="{{ $fechaFinPromo->format('d/m/Y') }}"
                                            data-dias="{{ $diasPromo }}">
                                        {{ $promo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Seleccionar Libro (Edición)</label>
                            <select id="select-libro" name="edicion_id" class="form-select" required>
                                <option value="" disabled selected>-- Busca y elige un libro --</option>
                                @foreach($ediciones as $edicion)
                                    <option value="{{ $edicion->id }}"
                                            data-titulo="{{ $edicion->titulo }}"
                                            data-isbn="{{ $edicion->isbn }}"
                                            data-precio="{{ $edicion->precio_venta }}"
                                            data-autor="{{ $edicion->autor ?? 'N/A' }}"
                                            data-portada="{{ $edicion->portada }}"
                                            data-promocion="{{ $edicion->promo_nombre }}"
                                            data-descuento="{{ $edicion->promo_descuento }}">
                                        {{ $edicion->titulo }} | ISBN: {{ $edicion->isbn }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Aplicar Oferta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetalleLibro" tabindex="-1" aria-hidden="true">
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
                            <div class="text-center mb-4">
                                <img id="detailCoverImage" src="" alt="Portada del libro" class="book-detail-cover d-none" loading="lazy" decoding="async">
                                <div id="detailCoverEmpty" class="book-detail-empty d-flex align-items-center justify-content-center mx-auto">Sin imagen</div>
                            </div>

                            <form id="detailCoverForm" method="post" enctype="multipart/form-data">
                                @csrf
                                <div id="detailDropzone" class="dropzone">
                                    <input id="detailCoverInput" type="file" name="portada" accept="image/png,image/jpeg,image/jpg,image/webp">
                                    <div class="d-flex align-items-center gap-3">
                                        <img id="detailPreviewThumb" src="" alt="Vista previa" class="dropzone-preview d-none" loading="lazy" decoding="async">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold mb-1" style="color: #4b1c71;">Cambiar portada</div>
                                            <div class="small text-muted mb-2">Arrastra una imagen aqui o haz clic para seleccionarla.</div>
                                            <div class="small text-muted">Formatos: JPG, PNG, WEBP. Maximo 4 MB.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button id="detailSaveButton" type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;" disabled>Guardar portada</button>
                                    <button id="detailResetPreview" type="button" class="btn btn-light rounded-pill px-4 fw-bold">Restaurar vista</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-lg-8">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
                                <div>
                                    <h3 id="detailTitle" class="fw-bold mb-1" style="color:#4b1c71;"></h3>
                                    <p id="detailIsbn" class="text-muted mb-0"></p>
                                </div>
                                <div class="price-panel p-3 px-4 text-md-end">
                                    <div class="small text-muted mb-1">Precio con promoción</div>
                                    <div class="d-flex flex-column">
                                        <span id="detailOriginalPrice" class="text-muted"><del></del></span>
                                        <span id="detailFinalPrice" class="fw-bold text-success fs-4"></span>
                                        <span class="small text-success fw-semibold">Ahorro: <span id="detailSavings"></span> <span id="detailDiscountBadge" class="badge bg-success ms-1"></span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6"><div class="detail-card soft"><div class="small text-muted">Autor</div><div id="detailAuthor" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card soft"><div class="small text-muted">Editorial</div><div id="detailEditorial" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Año de publicación</div><div id="detailYear" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Número de edición</div><div id="detailEditionNumber" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Número de páginas</div><div id="detailPages" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Existencias</div><div id="detailStock" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Stock mínimo</div><div id="detailMinStock" class="fw-semibold"></div></div></div>
                                <div class="col-md-6"><div class="detail-card plain"><div class="small text-muted">Promoción activa</div><div class="fw-semibold"><span id="detailPromotion"></span><span id="detailPromotionBadge" class="badge bg-success ms-2"></span></div></div></div>
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

    @foreach($asignaciones as $asignacion)
        <div class="modal fade" id="modalQuitarAsignacion{{ $asignacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-link-slash me-2"></i> Quitar Oferta</h5>
                        <button type="button" class="btn-close btn-close-white" title="Cerrar" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row align-items-center">
                            <div class="zoom-container mx-auto">
                                @if(!empty($asignacion->portada))
                                    <img src="{{ asset('storage/' . $asignacion->portada) }}" alt="{{ $asignacion->alt_imagen ?? $asignacion->libro_titulo }}" class="zoom-img" loading="lazy" decoding="async">
                                @else
                                    <div class="book-detail-empty d-flex align-items-center justify-content-center mx-auto" style="max-width: 200px; height: 300px;">{{ $asignacion->alt_imagen ?? 'Sin imagen' }}</div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <h5 class="fw-bold mb-1" style="color:#4b1c71;">{{ $asignacion->libro_titulo }}</h5>
                                <p class="text-muted small mb-2">ISBN: {{ $asignacion->isbn }}</p>
                                <p class="mb-1 small"><strong>Autor:</strong> {{ $asignacion->autor ?? 'N/A' }}</p>
                                <p class="mb-2 small"><strong>Editorial:</strong> {{ $asignacion->editorial ?? 'N/A' }}</p>
                                @php
                                    $precio = $asignacion->precio_venta ?? 0;
                                    $descuento = $asignacion->porcentaje_descuento ?? 0;
                                    $precioFinal = $precio - ($precio * ($descuento / 100));
                                @endphp
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="text-muted small"><del>$ {{ number_format($precio, 2) }}</del></span>
                                    <span class="fw-bold text-success">$ {{ number_format($precioFinal, 2) }}</span>
                                    <span class="badge bg-success">-{{ $descuento }}%</span>
                                </div>
                                <div class="mt-3 p-2 rounded" style="background-color: #fff3f3;">
                                    <p class="mb-0 small text-danger fw-semibold">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Estas a punto de eliminar la promoción <strong>"{{ $asignacion->promocion_nombre }}"</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('asigna_promociones.destroy', $asignacion->id) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Si, quitar oferta</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if(session('confirm_replace'))
        @php $data = session('confirm_replace'); @endphp
        <div class="modal fade" id="modalConfirmReplace" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #7f4ca5; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-code-compare me-2"></i> Reemplazar Oferta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                @if(!empty($data['portada']))
                                    <img src="{{ asset('storage/' . $data['portada']) }}" alt="{{ $data['alt_imagen'] ?? 'Portada' }}" class="img-fluid rounded shadow-sm" style="max-height: 160px;" loading="lazy" decoding="async">
                                @else
                                    <div class="text-muted fst-italic">{{ $data['alt_imagen'] ?? 'Sin imagen' }}</div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <h5 class="fw-bold mb-1" style="color:#4b1c71;">{{ $data['libro_titulo'] }}</h5>
                                <p class="text-muted small mb-2">ISBN: {{ $data['isbn'] }}</p>
                                <p class="mb-1 small"><strong>Autor:</strong> {{ $data['autor'] ?? 'N/A' }}</p>
                                <p class="mb-2 small"><strong>Editorial:</strong> {{ $data['editorial'] ?? 'N/A' }}</p>
                                @php
                                    $precio = $data['precio'] ?? 0;
                                    $descuento = $data['descuento'] ?? 0;
                                    $precioFinal = $precio - ($precio * ($descuento / 100));
                                @endphp
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="text-muted small"><del>$ {{ number_format($precio, 2) }}</del></span>
                                    <span class="fw-bold text-success">$ {{ number_format($precioFinal, 2) }}</span>
                                    <span class="badge bg-success">-{{ $descuento }}%</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-light text-dark border">{{ $data['old_promocion_nombre'] }}</span>
                                    <i class="fa-solid fa-arrow-right text-muted"></i>
                                    <span class="badge text-white" style="background-color:#4b1c71;">{{ $data['new_promocion_nombre'] }}</span>
                                </div>
                                <div class="mt-2 p-2 rounded" style="background-color: #f3e8ff;">
                                    <p class="mb-0 small fw-semibold" style="color:#4b1c71;">
                                        <i class="fa-solid fa-code-compare me-1"></i> Se reemplazara la promoción actual por la nueva
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal" style="color: #7a6a88;">Mantener Anterior</button>
                        <form action="{{ route('asigna_promociones.store') }}" method="post" class="d-inline">
                            @csrf
                            <input type="hidden" name="promocion_id" value="{{ $data['promocion_id'] }}">
                            <input type="hidden" name="edicion_id" value="{{ $data['edicion_id'] }}">
                            <input type="hidden" name="force_replace" value="1">
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #7f4ca5; transition: all 0.3s;" onmouseover="this.style.backgroundColor='#4b1c71'" onmouseout="this.style.backgroundColor='#7f4ca5'">Si, Reemplazar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalConfirmReplace'));
                myModal.show();
            });
        </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Selector de Promociones
            new TomSelect("#select-promocion", {
                render: {
                    option: function(data, escape) {
                        let dias = parseInt(data.$option.dataset.dias);
                        let badgeDias = '';
                        if (dias < 0) { badgeDias = '<span class="badge bg-danger">Expirada</span>'; }
                        else if (dias === 0) { badgeDias = '<span class="badge bg-warning text-dark">Expira Hoy</span>'; }
                        else { badgeDias = `<span class="badge" style="background-color: #dbb6ee; color: #4b1c71;">Faltan ${dias} días</span>`; }

                        return `
                        <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                            <div>
                                <div class="fw-bold" style="color: #4b1c71;">${escape(data.text)}</div>
                                <div class="small text-muted"><i class="fa-regular fa-calendar"></i> Hasta: ${escape(data.$option.dataset.fin)}</div>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-success mb-1 fs-6">-${escape(data.$option.dataset.descuento)}%</div>
                                <div class="d-block">${badgeDias}</div>
                            </div>
                        </div>`;
                    },
                    item: function(data, escape) {
                        return `<div class="fw-bold">${escape(data.text)} <span class="badge bg-success ms-2">-${escape(data.$option.dataset.descuento)}%</span></div>`;
                    }
                }
            });

            // Selector de Libros
            new TomSelect("#select-libro", {
                render: {
                    option: function(data, escape) {
                        let portada = data.$option.dataset.portada ? `/storage/${data.$option.dataset.portada}` : 'https://via.placeholder.com/50x70?text=No+Img';
                        let promo = data.$option.dataset.promocion;
                        let descuento = data.$option.dataset.descuento;
                        let promoHTML = '';

                        if (promo) {
                            promoHTML = `
                            <div class="mt-1 p-1 rounded" style="background-color: #fff0ff; border: 1px dashed #dbb6ee;">
                                <div class="small fw-bold" style="color: #4b1c71;">
                                    <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Ya tiene promoción activa:
                                </div>
                                <div class="small text-muted">${escape(promo)} <span class="badge bg-success ms-1">-${descuento}%</span></div>
                            </div>`;
                        }

                        return `
                        <div class="d-flex align-items-start gap-3 p-2 border-bottom">
                            <img src="${portada}" loading="lazy" style="width:45px; height:65px; object-fit:cover; border-radius:6px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">${escape(data.$option.dataset.titulo)}</div>
                                <div class="d-flex justify-content-between">
                                    <div class="small text-muted">ISBN: ${escape(data.$option.dataset.isbn)}</div>
                                    <div class="small text-success fw-bold">$ ${parseFloat(data.$option.dataset.precio || 0).toFixed(2)}</div>
                                </div>
                                ${promoHTML}
                            </div>
                        </div>`;
                    },
                    item: function(data, escape) {
                        return `<div class="fw-semibold">${escape(data.text)}</div>`;
                    }
                }
            });

            document.querySelectorAll(".zoom-container").forEach(function(container) {
                const img = container.querySelector(".zoom-img");
                if (!img) return;
                container.addEventListener("mousemove", function(e) {
                    const rect = container.getBoundingClientRect();
                    const x = ((e.clientX - rect.left) / rect.width) * 100;
                    const y = ((e.clientY - rect.top) / rect.height) * 100;
                    img.style.transformOrigin = `${x}% ${y}%`;
                    img.style.transform = "scale(2.2)";
                });
                container.addEventListener("mouseleave", function() {
                    img.style.transform = "scale(1)";
                });
            });

            const detailModal = document.getElementById("modalDetalleLibro");
            const coverImage = document.getElementById("detailCoverImage");
            const coverEmpty = document.getElementById("detailCoverEmpty");
            const previewThumb = document.getElementById("detailPreviewThumb");
            const coverInput = document.getElementById("detailCoverInput");
            const coverForm = document.getElementById("detailCoverForm");
            const dropzone = document.getElementById("detailDropzone");
            const saveButton = document.getElementById("detailSaveButton");
            const resetPreviewButton = document.getElementById("detailResetPreview");
            const detailState = { image: "", emptyLabel: "Sin imagen" };

            function formatMoney(value) {
                const amount = Number(value || 0);
                return `$ ${amount.toFixed(2)}`;
            }

            function setCover(src, fallbackText) {
                detailState.image = src || "";
                detailState.emptyLabel = fallbackText || "Sin imagen";
                if (detailState.image) {
                    coverImage.src = detailState.image; coverImage.classList.remove("d-none"); coverEmpty.classList.add("d-none");
                    previewThumb.src = detailState.image; previewThumb.classList.remove("d-none");
                } else {
                    coverImage.removeAttribute("src"); coverImage.classList.add("d-none"); coverEmpty.textContent = detailState.emptyLabel; coverEmpty.classList.remove("d-none");
                    previewThumb.removeAttribute("src"); previewThumb.classList.add("d-none");
                }
            }

            function resetPreviewState() {
                coverInput.value = "";
                setCover(detailState.image, detailState.emptyLabel);
                saveButton.disabled = true;
            }

            function applyPreview(file) {
                if (!file) { resetPreviewState(); return; }
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverImage.src = e.target.result; coverImage.classList.remove("d-none"); coverEmpty.classList.add("d-none");
                    previewThumb.src = e.target.result; previewThumb.classList.remove("d-none"); saveButton.disabled = false;
                };
                reader.readAsDataURL(file);
            }

            detailModal.addEventListener("show.bs.modal", function(event) {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                const data = trigger.dataset;
                const original = Number(data.precioOriginal || 0);
                const finalPrice = Number(data.precioFinal || 0);
                const savings = original - finalPrice;
                const discount = Math.round(Number(data.descuento || 0));

                document.getElementById("detailTitle").textContent = data.titulo || "";
                document.getElementById("detailIsbn").textContent = `ISBN: ${data.isbn || "N/A"}`;
                document.getElementById("detailAuthor").textContent = data.autor || "N/A";
                document.getElementById("detailEditorial").textContent = data.editorial || "N/A";
                document.getElementById("detailYear").textContent = data.anio || "N/A";
                document.getElementById("detailEditionNumber").textContent = data.numeroEdicion || "N/A";
                document.getElementById("detailPages").textContent = data.numeroPaginas || "N/A";
                document.getElementById("detailStock").textContent = data.existencias || "N/A";
                document.getElementById("detailMinStock").textContent = data.stockMinimo || "N/A";
                document.getElementById("detailPromotion").textContent = data.promocion || "Sin promocion";
                document.getElementById("detailPromotionBadge").textContent = `${discount}%`;
                document.getElementById("detailDiscountBadge").textContent = `${discount}%`;
                document.getElementById("detailOriginalPrice").innerHTML = `<del>${formatMoney(original)}</del>`;
                document.getElementById("detailFinalPrice").textContent = formatMoney(finalPrice);
                document.getElementById("detailSavings").textContent = formatMoney(savings);

                coverForm.action = data.updateUrl || "";
                setCover(data.imagen || "", data.altImagen || "Sin imagen");
                resetPreviewState();
            });

            coverInput.addEventListener("change", function() { applyPreview(this.files[0]); });

            ["dragenter", "dragover"].forEach(function(eventName) {
                dropzone.addEventListener(eventName, function(e) {
                    e.preventDefault(); e.stopPropagation(); dropzone.classList.add("is-dragover");
                });
            });

            ["dragleave", "drop"].forEach(function(eventName) {
                dropzone.addEventListener(eventName, function(e) {
                    e.preventDefault(); e.stopPropagation(); dropzone.classList.remove("is-dragover");
                });
            });

            dropzone.addEventListener("drop", function(e) {
                const files = e.dataTransfer.files;
                if (!files || !files.length) return;
                const transfer = new DataTransfer();
                transfer.items.add(files[0]);
                coverInput.files = transfer.files;
                applyPreview(files[0]);
            });

            resetPreviewButton.addEventListener("click", function() { resetPreviewState(); });
        });
    </script>
@endsection
