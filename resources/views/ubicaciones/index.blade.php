@extends('layouts.dashboard')

<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

@section('dashboard-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold">Ubicaciones</h3>
        <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
                data-bs-toggle="modal" data-bs-target="#modalCreateUbicacion" title="Nueva Ubicación">
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

    <div class="card border-0 shadow-sm mb-5" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header border-0 py-3" style="background-color: #f8f2fb;">
            <h5 class="mb-0 fw-bold" style="color: #4b1c71;"><i class="fa-solid fa-location-dot me-2"></i>Listado de Ubicaciones</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-bordered mi-datatable align-middle" id="tablaUbicaciones" style="width:100%">
                    <thead>
                    <tr>
                        <th class="ubi-th">#</th>
                        <th class="ubi-th">Pasillo</th>
                        <th class="ubi-th">Estante</th>
                        <th class="ubi-th">Nivel</th>
                        <th class="ubi-th">Código</th>
                        <th class="ubi-th">Género</th>
                        <th class="ubi-th text-center">Libros</th>
                        <th class="ubi-th text-end">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ubicaciones as $ubicacion)
                        @php
                            $edicionesEnUbi = $ubicacion->lotes->filter(fn($l) => $l->edicion && $l->edicion->libro)->unique('edicion_id');
                            $cantLibros = $edicionesEnUbi->count();
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ubicacion->pasillo }}</td>
                            <td>{{ $ubicacion->estante }}</td>
                            <td>{{ $ubicacion->nivel }}</td>
                            <td><span class="badge rounded-pill fw-bold px-3 py-2" style="background-color:#f8f2fb;color:#4b1c71;border:1px solid #dbb6ee;font-size:0.85rem;">{{ $ubicacion->codigo }}</span></td>
                            <td>{{ $ubicacion->genero->nombre ?? 'Sin género' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-link p-0 text-decoration-none"
                                        data-bs-toggle="modal" data-bs-target="#modalLibrosUbicacion{{ $ubicacion->id }}"
                                        title="Ver libros en esta ubicación" style="cursor:pointer;">
                                    <span class="badge rounded-pill px-3 py-2 ubi-libro-badge" style="background-color:{{ $cantLibros > 0 ? '#fff0ff' : '#f4f4f4' }};color:{{ $cantLibros > 0 ? '#7f4ca5' : '#999' }};border:1px solid {{ $cantLibros > 0 ? '#dbb6ee' : '#ddd' }};font-size:0.85rem;transition:all .2s;">
                                        <i class="fa-solid fa-book-open me-1"></i>{{ $cantLibros }}
                                    </span>
                                </button>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-2"
                                        data-bs-toggle="modal" data-bs-target="#modalEditUbicacion{{ $ubicacion->id }}" title="Editar">
                                    <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                                </button>
                                <button type="button" class="btn btn-link p-0 text-decoration-none fs-5"
                                        data-bs-toggle="modal" data-bs-target="#modalDeleteUbicacion{{ $ubicacion->id }}" title="Eliminar">
                                    <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="modalCreateUbicacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-circle-plus me-2"></i> Nueva Ubicación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ubicaciones.store') }}" method="post">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="color:#4b1c71;">Pasillo</label>
                            <input type="text" name="pasillo" class="form-control" placeholder="Ej. A" required maxlength="10">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="color:#4b1c71;">Estante</label>
                            <input type="number" name="estante" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="color:#4b1c71;">Nivel</label>
                            <input type="number" name="nivel" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-12">
                            <div class="p-2 rounded-3 d-flex align-items-center gap-2" style="background-color:#f8f2fb;border:1px solid #eadcf3;">
                                <i class="fa-solid fa-circle-info" style="color:#7f4ca5;"></i>
                                <span class="small text-muted">El <strong>código</strong> se genera automáticamente a partir de Pasillo, Estante y Nivel.</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="color:#4b1c71;">Género</label>
                            <select name="genero_id" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach($generosCatalogo as $genero)
                                    <option value="{{ $genero->id }}">{{ $genero->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold" style="color:#4b1c71;">
                                <i class="fa-solid fa-book me-1"></i>Asignar Libros (Ediciones)
                            </label>
                            <select id="select-ediciones-create" name="edicion_ids[]" multiple>
                                @foreach($ediciones as $ed)
                                    <option value="{{ $ed->id }}"
                                        data-titulo="{{ $ed->libro->titulo ?? '' }}"
                                        data-isbn="{{ $ed->isbn }}"
                                        data-editorial="{{ $ed->editorial->nombre ?? 'N/A' }}"
                                        data-portada="{{ ($ed->portada ? asset('storage/' . $ed->portada) : ($ed->libro && $ed->libro->portada ? asset('storage/' . $ed->libro->portada) : '')) }}">
                                        {{ $ed->libro->titulo ?? 'Sin título' }} | ISBN: {{ $ed->isbn }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted small">Selecciona las ediciones que estarán en esta ubicación.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color:#4b1c71;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($ubicaciones as $ubicacion)
    @php
        $edicionIdsAsignadas = $ubicacion->lotes->pluck('edicion_id')->unique()->toArray();
        $edicionesEnUbi = $ubicacion->lotes->filter(fn($l) => $l->edicion && $l->edicion->libro)->unique('edicion_id');
        $cantLibros = $edicionesEnUbi->count();
    @endphp

    {{-- MODAL EDITAR --}}
    <div class="modal fade" id="modalEditUbicacion{{ $ubicacion->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Ubicación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('ubicaciones.update', $ubicacion->id) }}" method="post">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color:#4b1c71;">Pasillo</label>
                                <input type="text" name="pasillo" class="form-control" value="{{ $ubicacion->pasillo }}" required maxlength="10">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color:#4b1c71;">Estante</label>
                                <input type="number" name="estante" class="form-control" value="{{ $ubicacion->estante }}" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color:#4b1c71;">Nivel</label>
                                <input type="number" name="nivel" class="form-control" value="{{ $ubicacion->nivel }}" min="1" required>
                            </div>
                            <div class="col-md-12">
                                <div class="p-2 rounded-3 d-flex align-items-center gap-2" style="background-color:#f8f2fb;border:1px solid #eadcf3;">
                                    <i class="fa-solid fa-circle-info" style="color:#7f4ca5;"></i>
                                    <span class="small text-muted">Código actual: <strong style="color:#4b1c71;">{{ $ubicacion->codigo }}</strong> — se actualiza automáticamente.</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold" style="color:#4b1c71;">Género</label>
                                <select name="genero_id" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    @foreach($generosCatalogo as $genero)
                                        <option value="{{ $genero->id }}" {{ $ubicacion->genero_id == $genero->id ? 'selected' : '' }}>{{ $genero->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold" style="color:#4b1c71;">
                                    <i class="fa-solid fa-book me-1"></i>Libros Asignados (Ediciones)
                                </label>
                                <select class="select-ediciones-edit" name="edicion_ids[]" multiple>
                                    @foreach($ediciones as $ed)
                                        <option value="{{ $ed->id }}"
                                            {{ in_array($ed->id, $edicionIdsAsignadas) ? 'selected' : '' }}
                                            data-titulo="{{ $ed->libro->titulo ?? '' }}"
                                            data-isbn="{{ $ed->isbn }}"
                                            data-editorial="{{ $ed->editorial->nombre ?? 'N/A' }}"
                                            data-portada="{{ ($ed->portada ? asset('storage/' . $ed->portada) : ($ed->libro && $ed->libro->portada ? asset('storage/' . $ed->libro->portada) : '')) }}">
                                            {{ $ed->libro->titulo ?? 'Sin título' }} | ISBN: {{ $ed->isbn }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted small">Modifica las ediciones asignadas a esta ubicación.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color:#4b1c71;">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ELIMINAR --}}
    <div class="modal fade" id="modalDeleteUbicacion{{ $ubicacion->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="fs-5 mb-1">¿Estás seguro de eliminar la ubicación <br><strong>"{{ $ubicacion->codigo }}"</strong>?</p>
                    <p class="text-muted small mb-0 mt-2">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('ubicaciones.destroy', $ubicacion->id) }}" method="post" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL LIBROS EN UBICACIÓN --}}
    <div class="modal fade" id="modalLibrosUbicacion{{ $ubicacion->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #4b1c71 0%, #7f4ca5 100%); color: white; border-radius: 20px 20px 0 0;">
                    <div>
                        <h5 class="modal-title bebas fs-4 mb-0">
                            <i class="fa-solid fa-book-bookmark me-2"></i>Libros en ubicación <span class="text-white-50">{{ $ubicacion->codigo }}</span>
                        </h5>
                        <div class="small mt-1" style="color:rgba(255,255,255,0.7);">
                            <i class="fa-solid fa-location-dot me-1"></i>Pasillo {{ $ubicacion->pasillo }} · Estante {{ $ubicacion->estante }} · Nivel {{ $ubicacion->nivel }}
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #fcf9ff;">
                    @if($edicionesEnUbi->isEmpty())
                        <div class="text-center py-5">
                            <i class="fa-solid fa-book-open-reader" style="font-size:3rem;color:#cdb7dc;"></i>
                            <h5 class="fw-bold mt-3" style="color:#4b1c71;">No hay libros asignados</h5>
                            <p class="text-muted mb-0">No hay libros asignados a esta ubicación.<br><span class="small">Puedes asignarlos editando esta ubicación.</span></p>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold" style="color:#4b1c71;">Libros actuales en esta ubicación</span>
                            <span class="badge rounded-pill" style="background-color:#fff0ff;color:#7f4ca5;border:1px solid #dbb6ee;">
                                <i class="fa-solid fa-book-open me-1"></i>{{ $cantLibros }} {{ $cantLibros === 1 ? 'libro' : 'libros' }}
                            </span>
                        </div>
                        <div class="row g-3">
                            @foreach($edicionesEnUbi as $lote)
                                @php
                                    $ed = $lote->edicion;
                                    $lib = $ed->libro;
                                    $autStr = $lib->autores->pluck('nombre')->join(', ') ?: 'N/A';
                                    $subStr = $lib->subgeneros->pluck('nombre')->join(', ');
                                    $genStr = $lib->genero->nombre ?? '';
                                    $catStr = $subStr ? ($genStr ? $genStr.' / '.$subStr : $subStr) : $genStr;
                                    $port = $ed->portada ? asset('storage/'.$ed->portada) : ($lib->portada ? asset('storage/'.$lib->portada) : null);
                                    $totalCant = $ubicacion->lotes->where('edicion_id', $ed->id)->sum('cantidad');
                                @endphp
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start w-100 bg-white p-3 rounded-4 shadow-sm h-100 ubi-libro-card" style="border:1px solid #eadcf2;">
                                        @if($port)
                                            <img src="{{ $port }}" alt="{{ $lib->titulo }}" class="rounded shadow-sm flex-shrink-0" style="width:60px;height:82px;object-fit:cover;" loading="lazy">
                                        @else
                                            <div class="rounded d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width:60px;height:82px;background:#f8f2fb;border:1px dashed #cdb7dc;font-size:0.7rem;">Sin img</div>
                                        @endif
                                        <div class="ms-3 flex-grow-1" style="min-width:0;">
                                            <h6 class="fw-bold mb-1 lh-sm text-truncate" style="color:#4b1c71;font-size:0.95rem;" title="{{ $lib->titulo }}">{{ $lib->titulo }}</h6>
                                            <div class="small text-muted mb-1" style="font-size:0.8rem;"><i class="fa-solid fa-barcode me-1"></i>ISBN: {{ $ed->isbn }}</div>
                                            <div class="small mb-1" style="font-size:0.8rem;"><i class="fa-solid fa-feather-pointed me-1" style="color:#7f4ca5;"></i><span class="text-muted">{{ $autStr }}</span></div>
                                            @if($ed->editorial)<div class="small mb-1" style="font-size:0.8rem;"><i class="fa-solid fa-building me-1" style="color:#7f4ca5;"></i><span class="text-muted">{{ $ed->editorial->nombre }}</span></div>@endif
                                            @if($catStr)<div class="small mb-1" style="font-size:0.8rem;"><i class="fa-solid fa-bookmark me-1" style="color:#7f4ca5;"></i><span class="text-muted">{{ $catStr }}</span></div>@endif
                                            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                                @if($ed->precio_venta)<span class="badge bg-success rounded-pill px-2 py-1" style="font-size:0.78rem;">${{ number_format($ed->precio_venta, 2) }}</span>@endif
                                                <span class="badge rounded-pill px-2 py-1" style="background-color:#f0f9ff;color:#0c6ebd;border:1px solid #b6d4fe;font-size:0.78rem;"><i class="fa-solid fa-boxes-stacked me-1"></i>{{ $totalCant }} uds.</span>
                                                @if($ed->existencias !== null)
                                                    <span class="badge rounded-pill px-2 py-1" style="background-color:{{ $ed->existencias > 0 ? '#f0fff4' : '#fff5f5' }};color:{{ $ed->existencias > 0 ? '#198754' : '#dc3545' }};border:1px solid {{ $ed->existencias > 0 ? '#b7ebc9' : '#f1c5c5' }};font-size:0.78rem;">{{ $ed->existencias > 0 ? 'Disponible' : 'Agotado' }}</span>
                                                @endif
                                                <span class="badge rounded-pill px-2 py-1" style="background-color:#f8f2fb;color:#4b1c71;border:1px solid #dbb6ee;font-size:0.75rem;"><i class="fa-solid fa-location-dot me-1"></i>{{ $ubicacion->codigo }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
    .ubi-th { background-color: #4b1c71; color: #fff; border-color: #5e2d86 !important; font-weight: 700; font-size: 0.88rem; padding: 14px 16px; white-space: nowrap; }
    #tablaUbicaciones { border-collapse: separate; border-spacing: 0; }
    #tablaUbicaciones tbody tr:nth-child(even) { background-color: #faf7fc; }
    #tablaUbicaciones tbody tr:hover { background-color: #f3eaf8 !important; }
    #tablaUbicaciones tbody td { padding: 12px 16px; vertical-align: middle; border-color: #f0e6f7; }
    .ubi-libro-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .ubi-libro-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(75,28,113,0.12) !important; }
    .ubi-libro-badge:hover { background-color: #f3e8ff !important; transform: scale(1.08); }
    .ts-control { border-radius: 10px !important; border-color: #dbb6ee !important; }
    .ts-control:focus-within { border-color: #7f4ca5 !important; box-shadow: 0 0 0 0.2rem rgba(127,76,165,0.15) !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function tomSelectConfig() {
        return {
            plugins: ['remove_button'],
            placeholder: 'Buscar y seleccionar ediciones...',
            render: {
                option: function(data, escape) {
                    var opt = data.$option;
                    var portada = opt.dataset.portada;
                    var imgHtml = portada
                        ? '<img src="' + portada + '" class="rounded" style="width:40px;height:55px;object-fit:cover;">'
                        : '<div class="rounded d-flex align-items-center justify-content-center" style="width:40px;height:55px;background:#f8f2fb;border:1px dashed #cdb7dc;font-size:0.6rem;color:#999;">Sin img</div>';
                    return '<div class="d-flex align-items-center p-2 gap-2">'
                        + imgHtml
                        + '<div>'
                        + '<div class="fw-bold" style="color:#4b1c71;">' + escape(opt.dataset.titulo) + '</div>'
                        + '<div class="small text-muted">ISBN: ' + escape(opt.dataset.isbn) + ' · ' + escape(opt.dataset.editorial) + '</div>'
                        + '</div></div>';
                },
                item: function(data, escape) {
                    var opt = data.$option;
                    return '<div>' + escape(opt.dataset.titulo) + ' <span class="text-muted small">(' + escape(opt.dataset.isbn) + ')</span></div>';
                }
            }
        };
    }

    // Crear TomSelect al abrir modal de creación
    var createModal = document.getElementById('modalCreateUbicacion');
    if (createModal) {
        createModal.addEventListener('shown.bs.modal', function() {
            var sel = this.querySelector('#select-ediciones-create');
            if (sel && !sel.tomselect) {
                new TomSelect(sel, tomSelectConfig());
            }
        });
    }

    // Crear TomSelect al abrir cada modal de edición
    document.querySelectorAll('[id^="modalEditUbicacion"]').forEach(function(modal) {
        modal.addEventListener('shown.bs.modal', function() {
            var sel = this.querySelector('.select-ediciones-edit');
            if (sel && !sel.tomselect) {
                new TomSelect(sel, tomSelectConfig());
            }
        });
    });
});
</script>
@endsection
