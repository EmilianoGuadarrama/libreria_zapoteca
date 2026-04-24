@extends('layouts.dashboard')

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

@section('dashboard-content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold">Gestión de Promociones</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
                    data-bs-toggle="modal" data-bs-target="#modalCreatePromocion"
                    title="Nueva Promoción">
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

        <div class="card border-0 shadow-sm mb-5" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header border-0 py-3" style="background-color: #f8f2fb;">
                <h5 class="mb-0 fw-bold" style="color: #4b1c71;"> Promociones Activas</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-bordered table-striped mi-datatable align-middle" style="width:100%">
                        <thead style="background-color: #f8f2fb;">
                        <tr>
                            <th>#</th>
                            <th>Nombre de la Promoción</th>
                            <th>Descuento</th>
                            <th>Vigencia</th>
                            <th>Autorizado Por</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($promocionesActivas as $promocion)
                            @php
                                $fechaInicio = \Carbon\Carbon::parse($promocion->fecha_inicio)->startOfDay();
                                $fechaFinal = \Carbon\Carbon::parse($promocion->fecha_final)->startOfDay();
                                $hoy = now()->startOfDay();
                                $diasRestantes = $hoy->diffInDays($fechaFinal, false);
                                $librosDeEstaPromo = isset($librosVinculados) ? $librosVinculados->get($promocion->id, collect()) : collect();
                                $cantidadLibros = $librosDeEstaPromo->count();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold">
                                    <button type="button" class="btn btn-link p-0 text-decoration-none text-start d-flex align-items-center"
                                            data-bs-toggle="modal" data-bs-target="#modalLibrosPromocion{{ $promocion->id }}"
                                            title="Ver libros vinculados a esta promoción">
                                        <span class="fw-bold fs-6" style="color: #4b1c71; text-decoration: underline; text-underline-offset: 4px; text-decoration-color: #cdb7dc;">{{ $promocion->nombre }}</span>
                                        <span class="badge rounded-pill ms-2" style="background-color: #fff0ff; color: #7f4ca5; border: 1px solid #dbb6ee;">
                                            <i class="fa-solid fa-book-open"></i> {{ $cantidadLibros }}
                                        </span>
                                    </button>
                                </td>
                                <td><span class="badge bg-success fs-6">{{ $promocion->porcentaje_descuento }}%</span></td>
                                <td>
                                    <div class="mb-1 text-muted small">{{ $fechaInicio->format('d/m/Y') }} — {{ $fechaFinal->format('d/m/Y') }}</div>
                                    @if($hoy->lt($fechaInicio))
                                        <span class="badge bg-secondary rounded-pill"><i class="fa-solid fa-calendar-clock"></i> Próximamente</span>
                                    @elseif($diasRestantes == 0)
                                        <span class="badge bg-warning text-dark rounded-pill"><i class="fa-solid fa-clock"></i> Expira hoy</span>
                                    @else
                                        <span class="badge bg-info text-dark rounded-pill" style="background-color: #dbb6ee !important; border: 1px solid #7f4ca5;"><i class="fa-solid fa-hourglass-half"></i> Faltan {{ $diasRestantes }} días</span>
                                    @endif
                                </td>
                                <td>{{ $promocion->nombre_autorizado }} {{ $promocion->ape_paterno }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3" data-bs-toggle="modal" data-bs-target="#modalEditPromocion{{ $promocion->id }}"><i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i></button>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none fs-5" data-bs-toggle="modal" data-bs-target="#modalDeletePromocion{{ $promocion->id }}"><i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($promocionesExpiradas->count() > 0)
            <div class="shadow-sm mb-4" style="border-radius: 16px; overflow: hidden; border: 1px solid #eadcf3; background: #fff;">
                <button id="btnToggleExpiradas" class="w-100 text-start border-0 fw-bold p-3 d-flex justify-content-between align-items-center" style="background-color: #fcf9ff; color: #4b1c71; cursor: pointer; transition: background-color 0.2s;">
                <span>
                    <i class="fa-solid fa-box-archive me-2 text-muted"></i>
                    Promociones Expiradas ({{ $promocionesExpiradas->count() }})
                </span>
                    <i id="iconoToggleExpiradas" class="fa-solid fa-chevron-down text-muted" style="transition: transform 0.3s ease;"></i>
                </button>
                <div id="contenedorTablaExpiradas" class="contenedor-colapsable">
                    <div class="p-3 border-top">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mi-datatable align-middle" style="width:100%">
                                <thead style="background-color: #f4f4f4;">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre de la Oferta</th>
                                    <th>Descuento</th>
                                    <th>Vigencia Pasada</th>
                                    <th>Autorizado Por</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($promocionesExpiradas as $promocion)
                                    @php
                                        $librosDeEstaPromo = isset($librosVinculados) ? $librosVinculados->get($promocion->id, collect()) : collect();
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold text-muted">
                                            <button type="button" class="btn btn-link p-0 text-decoration-none text-start d-flex align-items-center text-muted" data-bs-toggle="modal" data-bs-target="#modalLibrosPromocion{{ $promocion->id }}">
                                                <span class="fw-bold fs-6" style="text-decoration: underline; text-underline-offset: 4px;">{{ $promocion->nombre }}</span>
                                                <span class="badge rounded-pill ms-2 bg-light text-secondary border"><i class="fa-solid fa-book-open"></i> {{ $librosDeEstaPromo->count() }}</span>
                                            </button>
                                        </td>
                                        <td><span class="badge bg-secondary fs-6">{{ $promocion->porcentaje_descuento }}%</span></td>
                                        <td>
                                            <div class="mb-1 text-muted small">
                                                {{ \Carbon\Carbon::parse($promocion->fecha_inicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($promocion->fecha_final)->format('d/m/Y') }}
                                            </div>
                                            <span class="badge bg-danger rounded-pill"><i class="fa-solid fa-circle-xmark"></i> Expirada</span>
                                        </td>
                                        <td class="text-muted">{{ $promocion->nombre_autorizado }} {{ $promocion->ape_paterno }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3" data-bs-toggle="modal" data-bs-target="#modalRenovarPromocion{{ $promocion->id }}" title="Reprogramar promoción"><i class="fa-solid fa-calendar-plus" style="color: #198754;"></i></button>
                                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3 text-muted" data-bs-toggle="modal" data-bs-target="#modalEditPromocion{{ $promocion->id }}"><i class="fa-solid fa-pen-to-square"></i></button>
                                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 text-muted" data-bs-toggle="modal" data-bs-target="#modalDeletePromocion{{ $promocion->id }}"><i class="fa-regular fa-trash-can"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                                    <img src="{{ asset('storage/' . $data['portada']) }}"
                                         alt="{{ $data['alt_imagen'] ?? 'Portada' }}"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 160px;"
                                         loading="lazy"
                                         decoding="async">
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
                                        <i class="fa-solid fa-code-compare me-1"></i>
                                        Se reemplazará la promoción actual por la nueva
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
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #7f4ca5; transition: all 0.3s;" onmouseover="this.style.backgroundColor='#4b1c71'" onmouseout="this.style.backgroundColor='#7f4ca5'">
                                Sí, Reemplazar
                            </button>
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
                                            <div class="small text-muted mb-2">Arrastra una imagen aquí.</div>
                                            <div class="small text-muted">Formatos: JPG, PNG, WEBP. Máx 4 MB.</div>
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

    <div class="modal fade" id="modalCreatePromocion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-circle-plus me-2"></i> Nueva Promoción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('promociones.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Nombre de Promoción</label>
                                <input type="text" name="nombre" class="form-control" required maxlength="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Descuento (%)</label>
                                <input type="number" name="porcentaje_descuento" class="form-control" min="0" max="100" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Fecha de Inicio</label>
                                <input type="text" name="fecha_inicio" class="form-control selector-fecha" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Fecha Final</label>
                                <input type="text" name="fecha_final" class="form-control selector-fecha" required>
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

    @php
        $todasLasPromocionesUnidas = $promocionesActivas->merge($promocionesExpiradas);
    @endphp

    @foreach($todasLasPromocionesUnidas as $promocion)

        @php $librosDeEstaPromo = isset($librosVinculados) ? $librosVinculados->get($promocion->id, collect()) : collect(); @endphp

        <div class="modal fade" id="modalLibrosPromocion{{ $promocion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #7f4ca5; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-book-bookmark me-2"></i> Libros en "{{ $promocion->nombre }}"</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" style="background-color: #fcf9ff;">

                        @if($librosDeEstaPromo->isEmpty())
                            <div class="text-center py-5">
                                <i class="fa-solid fa-book-open-reader fs-1 text-muted mb-3" style="color: #cdb7dc !important;"></i>
                                <h5 class="fw-bold" style="color: #4b1c71;">Sin libros vinculados</h5>
                                <p class="text-muted">Esta promoción no está aplicada a ningún libro.</p>
                                <button type="button" class="btn mt-2 text-white rounded-pill px-4 fw-bold btn-asignar-promo"
                                        data-bs-dismiss="modal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalAssignPromocion"
                                        data-promo-id="{{ $promocion->id }}"
                                        style="background-color: #4b1c71;">
                                    Asignar libros ahora
                                </button>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-bold">Libros actuales ({{ $librosDeEstaPromo->count() }})</span>
                                <button type="button" class="btn btn-sm text-white rounded-pill px-3 fw-bold btn-asignar-promo"
                                        data-bs-dismiss="modal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalAssignPromocion"
                                        data-promo-id="{{ $promocion->id }}"
                                        style="background-color: #4b1c71;">
                                    <i class="fa-solid fa-plus"></i> Agregar más libros
                                </button>
                            </div>
                            <div class="row g-3">
                                @foreach($librosDeEstaPromo as $libro)
                                    @php
                                        $precioOriginal = $libro->precio_venta;
                                        $precioFinal = $precioOriginal - ($precioOriginal * ($promocion->porcentaje_descuento / 100));
                                    @endphp
                                    <div class="col-md-6">
                                        <button type="button" class="d-flex align-items-center w-100 bg-white p-3 rounded-4 shadow-sm h-100 border-0 text-start js-open-book-detail"
                                                style="border: 1px solid #eadcf2; transition: transform 0.2s ease; cursor: pointer;"
                                                onmouseover="this.style.transform='translateY(-2px)';"
                                                onmouseout="this.style.transform='translateY(0)';"
                                                data-bs-dismiss="modal"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDetalleLibro"
                                                data-parent-modal="#modalLibrosPromocion{{ $promocion->id }}"
                                                data-edicion-id="{{ $libro->edicion_id }}"
                                                data-update-url="{{ route('asigna_promociones.portada.update', $libro->edicion_id) }}"
                                                data-titulo="{{ $libro->libro_titulo }}"
                                                data-isbn="{{ $libro->isbn }}"
                                                data-autor="{{ $libro->autor ?? 'N/A' }}"
                                                data-editorial="{{ $libro->editorial ?? 'N/A' }}"
                                                data-anio="{{ $libro->anio_publicacion ?? 'N/A' }}"
                                                data-numero-edicion="{{ $libro->numero_edicion ?? 'N/A' }}"
                                                data-numero-paginas="{{ $libro->numero_paginas ?? 'N/A' }}"
                                                data-existencias="{{ $libro->existencias ?? 'N/A' }}"
                                                data-stock-minimo="{{ $libro->stock_minimo ?? 'N/A' }}"
                                                data-promocion="{{ $promocion->nombre }}"
                                                data-descuento="{{ $promocion->porcentaje_descuento }}"
                                                data-precio-original="{{ $precioOriginal }}"
                                                data-precio-final="{{ $precioFinal }}"
                                                data-alt-imagen="{{ $libro->alt_imagen ?? 'Sin imagen' }}"
                                                data-imagen="{{ $libro->portada ? asset('storage/' . $libro->portada) : '' }}">

                                            @if($libro->portada)
                                                <img src="{{ asset('storage/' . $libro->portada) }}" alt="{{ $libro->libro_titulo }}" class="rounded shadow-sm" style="width: 55px; height: 75px; object-fit: cover;">
                                            @else
                                                <div class="rounded d-flex align-items-center justify-content-center text-muted" style="width: 55px; height: 75px; background: #f8f2fb; border: 1px dashed #cdb7dc; font-size: 0.7rem;">Sin img</div>
                                            @endif

                                            <div class="ms-3 flex-grow-1">
                                                <h6 class="fw-bold mb-1 lh-sm" style="color: #4b1c71; font-size: 0.95rem;">{{ $libro->libro_titulo }}</h6>
                                                <div class="small text-muted mb-2" style="font-size: 0.8rem;">ISBN: {{ $libro->isbn }}</div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted small"><del>${{ number_format($precioOriginal, 2) }}</del></span>
                                                    <span class="fw-bold text-success">${{ number_format($precioFinal, 2) }}</span>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalRenovarPromocion{{ $promocion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 text-white" style="background-color: #198754; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-calendar-plus me-2"></i> Reprogramar Promoción</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('promociones.renovar', $promocion->id) }}" method="post">
                        @csrf
                        <div class="modal-body p-4 text-center">
                            <h5 class="fw-bold mb-3">Reprogramar "{{ $promocion->nombre }}"</h5>
                            <p class="text-muted mb-4">Las fechas sugeridas corresponden al proximo año, pero puedes ajustarlas en el calendario.</p>

                            <div class="row g-3 text-start">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success">Fecha de Inicio</label>
                                    <input type="text" name="fecha_inicio" class="form-control selector-fecha" value="{{ \Carbon\Carbon::parse($promocion->fecha_inicio)->addYear()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success">Fecha Final</label>
                                    <input type="text" name="fecha_final" class="form-control selector-fecha" value="{{ \Carbon\Carbon::parse($promocion->fecha_final)->addYear()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold text-white shadow-sm">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditPromocion{{ $promocion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Promoción</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('promociones.update', $promocion->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Nombre de Promoción</label>
                                    <input type="text" name="nombre" class="form-control" value="{{ $promocion->nombre }}" required maxlength="100">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Descuento (%)</label>
                                    <input type="number" name="porcentaje_descuento" class="form-control" value="{{ $promocion->porcentaje_descuento }}" min="0" max="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Fecha de Inicio</label>
                                    <input type="text" name="fecha_inicio" class="form-control selector-fecha" value="{{ \Carbon\Carbon::parse($promocion->fecha_inicio)->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="color: #4b1c71;">Fecha Final</label>
                                    <input type="text" name="fecha_final" class="form-control selector-fecha" value="{{ \Carbon\Carbon::parse($promocion->fecha_final)->format('Y-m-d') }}" required>
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

        <div class="modal fade" id="modalDeletePromocion{{ $promocion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Estás seguro de eliminar <br><strong>"{{ $promocion->nombre }}"</strong>?</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('promociones.destroy', $promocion->id) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endforeach

    <style>
        .contenedor-colapsable { max-height: 0; opacity: 0; overflow: hidden; transition: max-height 0.3s cubic-bezier(0, 1, 0, 1), opacity 0.3s ease-out; }
        .contenedor-colapsable.abierto { max-height: 4000px; opacity: 1; transition: max-height 0.6s ease-in-out, opacity 0.4s ease-in; }
        .rotar-icono { transform: rotate(180deg); }
        #btnToggleExpiradas:hover { background-color: #f0e6f7 !important; }

        .book-detail-cover { width: 100%; max-width: 240px; height: 340px; object-fit: cover; border-radius: 18px; box-shadow: 0 20px 40px rgba(75, 28, 113, 0.18); background: #f8f2fb; }
        .book-detail-empty { width: 100%; max-width: 240px; height: 340px; border-radius: 18px; border: 1px dashed #cfb3e2; background: linear-gradient(180deg, #fbf7ff 0%, #f3e9fb 100%); color: #7a6a88; }
        .detail-card { border-radius: 16px; padding: 1rem; height: 100%; }
        .detail-card.soft { background: #f8f2fb; }
        .detail-card.plain { border: 1px solid #eadcf3; background: #fff; }
        .price-panel { border-radius: 18px; background: linear-gradient(135deg, #fff7e8 0%, #fff2cf 100%); border: 1px solid #f2ddb2; }
        .dropzone { position: relative; border: 1px dashed #c49ddd; border-radius: 18px; background: linear-gradient(180deg, #fcf9ff 0%, #f3e9fb 100%); padding: 1rem; cursor: pointer; transition: all 0.2s ease; }
        .dropzone:hover, .dropzone.is-dragover { border-color: #7f4ca5; transform: translateY(-1px); box-shadow: 0 12px 24px rgba(127, 76, 165, 0.12); }
        .dropzone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .dropzone-preview { width: 86px; height: 118px; object-fit: cover; border-radius: 12px; box-shadow: 0 10px 24px rgba(75, 28, 113, 0.16); background: #fff; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            if (typeof flatpickr !== 'undefined') {
                let fechaMaxima = new Date();
                fechaMaxima.setFullYear(fechaMaxima.getFullYear() + 2);
                flatpickr(".selector-fecha", { locale: "es", dateFormat: "Y-m-d", minDate: "today", maxDate: fechaMaxima, disableMobile: true });
            }

            const btnToggle = document.getElementById('btnToggleExpiradas');
            const contenedor = document.getElementById('contenedorTablaExpiradas');
            const icono = document.getElementById('iconoToggleExpiradas');

            if (btnToggle && contenedor) {
                btnToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    contenedor.classList.toggle('abierto');
                    icono.classList.toggle('rotar-icono');
                });
            }

            let selectPromo = null;
            if (document.getElementById("select-promocion")) {
                selectPromo = new TomSelect("#select-promocion", {
                    render: {
                        option: function(data, escape) {
                            let dias = parseInt(data.$option.dataset.dias);
                            let badgeDias = (dias < 0) ? '<span class="badge bg-danger">Expirada</span>' : (dias === 0 ? '<span class="badge bg-warning text-dark">Expira Hoy</span>' : `<span class="badge" style="background-color: #dbb6ee; color: #4b1c71;">Faltan ${dias} días</span>`);
                            return `
                            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                <div>
                                    <div class="fw-bold" style="color: #4b1c71;">${escape(data.text)}</div>
                                    <div class="small text-muted">Hasta: ${escape(data.$option.dataset.fin)}</div>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-success mb-1 fs-6">-${escape(data.$option.dataset.descuento)}%</div>
                                    <div class="d-block">${badgeDias}</div>
                                </div>
                            </div>`;
                        },
                        item: function(data, escape) { return `<div class="fw-bold">${escape(data.text)} <span class="badge bg-success ms-2">-${escape(data.$option.dataset.descuento)}%</span></div>`; }
                    }
                });
            }

            if (document.getElementById("select-libro")) {
                new TomSelect("#select-libro", {
                    render: {
                        option: function(data, escape) {
                            let portada = data.$option.dataset.portada ? `/storage/${data.$option.dataset.portada}` : 'https://via.placeholder.com/50x70?text=No+Img';
                            let promoHTML = data.$option.dataset.promocion ? `<div class="small text-warning fw-bold"> ${escape(data.$option.dataset.promocion)} (${data.$option.dataset.descuento}%)</div>` : '';
                            return `
                            <div class="d-flex align-items-center gap-2 p-2">
                                <img src="${portada}" loading="lazy" style="width:40px; height:55px; object-fit:cover; border-radius:6px;">
                                <div>
                                    <div class="fw-bold">${escape(data.$option.dataset.titulo)}</div>
                                    <div class="small text-muted">ISBN: ${escape(data.$option.dataset.isbn)}</div>
                                    ${promoHTML}
                                </div>
                            </div>`;
                        }
                    }
                });
            }

            document.querySelectorAll('.btn-asignar-promo').forEach(btn => {
                btn.addEventListener('click', function() {
                    if(selectPromo) {
                        selectPromo.setValue(this.dataset.promoId);
                    }
                });
            });

            const detailModal = document.getElementById("modalDetalleLibro");
            const coverImage = document.getElementById("detailCoverImage"), coverEmpty = document.getElementById("detailCoverEmpty"), previewThumb = document.getElementById("detailPreviewThumb"), coverInput = document.getElementById("detailCoverInput"), coverForm = document.getElementById("detailCoverForm"), dropzone = document.getElementById("detailDropzone"), saveButton = document.getElementById("detailSaveButton"), resetPreviewButton = document.getElementById("detailResetPreview");
            let detailState = { image: "", emptyLabel: "Sin imagen" };
            let parentModalId = null;

            function formatMoney(value) { return `$ ${Number(value || 0).toFixed(2)}`; }

            function setCover(src, fallbackText) {
                detailState.image = src || ""; detailState.emptyLabel = fallbackText || "Sin imagen";
                if (detailState.image) {
                    coverImage.src = detailState.image; coverImage.classList.remove("d-none"); coverEmpty.classList.add("d-none");
                    previewThumb.src = detailState.image; previewThumb.classList.remove("d-none");
                } else {
                    coverImage.removeAttribute("src"); coverImage.classList.add("d-none"); coverEmpty.textContent = detailState.emptyLabel; coverEmpty.classList.remove("d-none");
                    previewThumb.removeAttribute("src"); previewThumb.classList.add("d-none");
                }
            }

            function resetPreviewState() { coverInput.value = ""; setCover(detailState.image, detailState.emptyLabel); saveButton.disabled = true; }

            function applyPreview(file) {
                if (!file) { resetPreviewState(); return; }
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverImage.src = e.target.result; coverImage.classList.remove("d-none"); coverEmpty.classList.add("d-none");
                    previewThumb.src = e.target.result; previewThumb.classList.remove("d-none"); saveButton.disabled = false;
                };
                reader.readAsDataURL(file);
            }

            if (detailModal) {
                detailModal.addEventListener("show.bs.modal", function(event) {
                    const data = event.relatedTarget.dataset;
                    if (!data.titulo) return;

                    parentModalId = data.parentModal || null;

                    const original = Number(data.precioOriginal || 0), finalPrice = Number(data.precioFinal || 0), savings = original - finalPrice, discount = Math.round(Number(data.descuento || 0));
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

                detailModal.addEventListener("hidden.bs.modal", function() {
                    if (parentModalId) {
                        const parentModalEl = document.querySelector(parentModalId);
                        if (parentModalEl) {
                            const modalInstance = bootstrap.Modal.getOrCreateInstance(parentModalEl);
                            modalInstance.show();
                        }
                        parentModalId = null;
                    }
                });
            }

            if (coverInput) coverInput.addEventListener("change", function() { applyPreview(this.files[0]); });
            if (resetPreviewButton) resetPreviewButton.addEventListener("click", function() { resetPreviewState(); });

            if (dropzone) {
                ["dragenter", "dragover"].forEach(eventName => dropzone.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); dropzone.classList.add("is-dragover"); }));
                ["dragleave", "drop"].forEach(eventName => dropzone.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); dropzone.classList.remove("is-dragover"); }));
                dropzone.addEventListener("drop", e => {
                    const files = e.dataTransfer.files; if (!files || !files.length) return;
                    const transfer = new DataTransfer(); transfer.items.add(files[0]); coverInput.files = transfer.files; applyPreview(files[0]);
                });
            }
        });
    </script>
@endsection
