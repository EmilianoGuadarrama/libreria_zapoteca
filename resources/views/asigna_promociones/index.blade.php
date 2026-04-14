@extends('layouts.dashboard')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
@section('dashboard-content')
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
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i> <span class="fw-semibold">{{ session('status') }}</span>
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
            <table class="table table-bordered table-striped mi-datatable" style="width:100%">
                <thead style="background-color: #f8f2fb;">
                <tr>
                    <th>#</th>
                    <th>Libro</th>
                    <th>ISBN</th>
                    <th>Promoción Aplicada</th>
                    <th>Descuento</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($asignaciones as $asignacion)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold" style="color: #4b1c71;">{{ $asignacion->libro_titulo }}</td>
                        <td><small class="text-muted">{{ $asignacion->isbn }}</small></td>
                        <td>{{ $asignacion->promocion_nombre }}</td>
                        <td><span class="badge bg-success">{{ $asignacion->porcentaje_descuento }}%</span></td>
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
                            <select name="promocion_id" class="form-select" required>
                                <option value="" disabled selected>-- Elige una promoción activa --</option>
                                @foreach($promociones as $promo)
                                    <option value="{{ $promo->id }}">{{ $promo->nombre }} ({{ $promo->porcentaje_descuento }}%)</option>
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
                                            data-portada="{{ $edicion->portada }}">

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

                            {{-- 📘 PORTADA --}}
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                @if(!empty($asignacion->portada))
                                    <img src="{{ asset('storage/' . $asignacion->portada) }}"
                                         alt="{{ $asignacion->alt_imagen ?? 'Portada' }}"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 160px;">
                                @else
                                    <div class="text-muted fst-italic">
                                        {{ $asignacion->alt_imagen ?? 'Sin imagen' }}
                                    </div>
                                @endif
                            </div>

                            {{-- 📖 INFO --}}
                            <div class="col-md-8">

                                <h5 class="fw-bold mb-1" style="color:#4b1c71;">
                                    {{ $asignacion->libro_titulo }}
                                </h5>

                                <p class="text-muted small mb-2">
                                    ISBN: {{ $asignacion->isbn }}
                                </p>

                                <p class="mb-1 small">
                                    <strong>Autor:</strong> {{ $asignacion->autor ?? 'N/A' }}
                                </p>

                                <p class="mb-2 small">
                                    <strong>Editorial:</strong> {{ $asignacion->editorial ?? 'N/A' }}
                                </p>

                                {{-- 💰 PRECIO --}}
                                @php
                                    $precio = $asignacion->precio_venta ?? 0;
                                    $descuento = $asignacion->porcentaje_descuento ?? 0;
                                    $precioFinal = $precio - ($precio * ($descuento / 100));
                                @endphp

                                <div class="d-flex align-items-center gap-2 mb-2">
                <span class="text-muted small">
                    <del>$ {{ number_format($precio, 2) }}</del>
                </span>

                                    <span class="fw-bold text-success">
                    $ {{ number_format($precioFinal, 2) }}
                </span>

                                    <span class="badge bg-success">
                    -{{ $descuento }}%
                </span>
                                </div>

                                {{-- 🎯 MENSAJE --}}
                                <div class="mt-3 p-2 rounded" style="background-color: #fff3f3;">
                                    <p class="mb-0 small text-danger fw-semibold">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                        Estás a punto de eliminar la promoción
                                        <strong>"{{ $asignacion->promocion_nombre }}"</strong>
                                    </p>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('asigna_promociones.destroy', $asignacion->id) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, quitar oferta</button>
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
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-code-compare me-2"></i> Reemplazar Oferta
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="row align-items-center">

                            {{-- 📘 PORTADA --}}
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                @if(!empty($data['portada']))
                                    <img src="{{ asset('storage/' . $data['portada']) }}"
                                         alt="{{ $data['alt_imagen'] ?? 'Portada' }}"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 160px;">
                                @else
                                    <div class="text-muted fst-italic">
                                        {{ $data['alt_imagen'] ?? 'Sin imagen' }}
                                    </div>
                                @endif
                            </div>

                            {{-- 📖 INFO --}}
                            <div class="col-md-8">

                                <h5 class="fw-bold mb-1" style="color:#4b1c71;">
                                    {{ $data['libro_titulo'] }}
                                </h5>

                                <p class="text-muted small mb-2">
                                    ISBN: {{ $data['isbn'] }}
                                </p>

                                <p class="mb-1 small">
                                    <strong>Autor:</strong> {{ $data['autor'] ?? 'N/A' }}
                                </p>

                                <p class="mb-2 small">
                                    <strong>Editorial:</strong> {{ $data['editorial'] ?? 'N/A' }}
                                </p>

                                {{-- 💰 PRECIO --}}
                                @php
                                    $precio = $data['precio'] ?? 0;
                                    $descuento = $data['descuento'] ?? 0;
                                    $precioFinal = $precio - ($precio * ($descuento / 100));
                                @endphp

                                <div class="d-flex align-items-center gap-2 mb-3">
                <span class="text-muted small">
                    <del>$ {{ number_format($precio, 2) }}</del>
                </span>

                                    <span class="fw-bold text-success">
                    $ {{ number_format($precioFinal, 2) }}
                </span>

                                    <span class="badge bg-success">
                    -{{ $descuento }}%
                </span>
                                </div>

                                {{-- 🔄 COMPARACIÓN DE PROMOCIONES --}}
                                <div class="d-flex align-items-center gap-2 mb-3">

                <span class="badge bg-light text-dark border">
                    {{ $data['old_promocion_nombre'] }}
                </span>

                                    <i class="fa-solid fa-arrow-right text-muted"></i>

                                    <span class="badge text-white" style="background-color:#4b1c71;">
                    {{ $data['new_promocion_nombre'] }}
                </span>

                                </div>

                                {{-- 🎯 MENSAJE --}}
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new TomSelect("#select-libro", {

                render: {
                    option: function(data, escape) {

                        let portada = data.$option.dataset.portada
                            ? `/storage/${data.$option.dataset.portada}`
                            : 'https://via.placeholder.com/50x70?text=No+Img';

                        return `
                    <div class="d-flex align-items-center gap-2 p-2">

                        <img src="${portada}"
                             style="width:40px; height:55px; object-fit:cover; border-radius:6px;">

                        <div>
                            <div class="fw-bold">${escape(data.$option.dataset.titulo)}</div>
                            <div class="small text-muted">
                                ISBN: ${escape(data.$option.dataset.isbn)}
                            </div>
                            <div class="small text-success fw-semibold">
                                $ ${parseFloat(data.$option.dataset.precio || 0).toFixed(2)}
                            </div>
                            <div class="small text-muted">
                                ${escape(data.$option.dataset.autor || 'N/A')}
                            </div>
                        </div>
                    </div>
                `;
                    },

                    item: function(data, escape) {
                        return `<div class="fw-semibold">${escape(data.text)}</div>`;
                    }
                }
            });
        });
    </script>
@endsection
