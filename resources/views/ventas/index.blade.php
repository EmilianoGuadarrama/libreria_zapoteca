@extends('layouts.dashboard')

@section('dashboard-content')

@if(session('error'))
<div class="alert alert-warning">
    {{ session('error') }}
</div>
@endif

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold">Ventas</h3>
        <a href="{{ route('ventas.create') }}" class="btn btn-link p-0 text-decoration-none fs-2" title="Nueva Venta">
            <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
        <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i>
        <span class="fw-semibold">{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))

    <div class="alert alert-warning">

        {{ session('error') }}

    </div>

    @endif

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
            <form id="formReporte" method="GET" action="{{ route('ventas.reporte.general') }}">
                <div class="row g-3">
                    {{-- FECHA --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="color: #4b1c71;">Día</label>
                        <div class="input-group">
                            <input type="text" name="fecha" class="form-control selector-fecha-reporte bg-light border-end-0" placeholder="aaaa-mm-dd">
                            <span class="input-group-text bg-light"><i class="fa-regular fa-calendar" style="color: #7a6a88;"></i></span>
                        </div>
                    </div>

                    {{-- MES --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="color: #4b1c71;">Mes</label>
                        <select name="mes" class="form-select bg-light">
                            <option value="">Seleccione</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>

                    {{-- AÑO --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold" style="color: #4b1c71;">Año</label>
                        <select name="anio" class="form-select bg-light">
                            <option value="">Seleccione</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>

                    {{-- BOTÓN --}}
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#modalConfirmarReporte">
                            <i class="fa-solid fa-file-pdf me-1"></i> Generar Reporte
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped mi-datatable" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Folio</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $venta->folio }}</td>
                    <td>
                        @if($venta->usuario && $venta->usuario->persona)
                        {{ $venta->usuario->persona->nombre ?? '' }}
                        {{ $venta->usuario->persona->apellido_paterno ?? '' }}
                        {{ $venta->usuario->persona->apellido_materno ?? '' }}
                        @elseif($venta->usuario)
                        Usuario ID: {{ $venta->usuario->id }}
                        @else
                        Usuario ID: {{ $venta->usuario_id }}
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                    <td class="fw-semibold">${{ number_format($venta->total, 2) }}</td>
                    <td class="text-end">
                        <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-link p-0 text-decoration-none fs-5 me-3" title="Ver Venta">
                            <i class="fa-solid fa-eye" style="color: #4b1c71;"></i>
                        </a>

                        <a href="{{ route('ventas.edit', $venta->id) }}" class="btn btn-link p-0 text-decoration-none fs-5 me-3" title="Editar Venta">
                            <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                        </a>

                        <a href="{{ route('ventas.ticket', $venta->id) }}" class="btn btn-link p-0 text-decoration-none fs-5 me-3" title="Ver Ticket">
                            <i class="fa-solid fa-receipt" style="color: #4b1c71;"></i>
                        </a>

                        <a href="{{ route('ventas.pdf', $venta->id) }}"
                            class="btn btn-danger btn-sm"
                            title="Generar PDF">

                            <i class="fa-solid fa-file-pdf"></i>
                        </a>

                        <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 text-decoration-none fs-5 border-0 bg-transparent" title="Eliminar Venta">
                                <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No hay ventas registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $ventas->links() }}
    </div>
</div>

{{-- MODAL CONFIRMAR REPORTE --}}
<div class="modal fade" id="modalConfirmarReporte" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4">
                    <i class="fa-solid fa-file-pdf me-2"></i> Generar Reporte PDF
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="fs-5 mb-1">¿Estás seguro de generar el reporte PDF con los filtros seleccionados?</p>
                <p class="text-muted small mb-0 mt-2">El documento se descargará automáticamente.</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" onclick="document.getElementById('formReporte').submit();" data-bs-dismiss="modal">Sí, generar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr(".selector-fecha-reporte", {
                locale: "es",
                dateFormat: "Y-m-d",
                maxDate: "today",
                disableMobile: true
            });
        }
    });
</script>
@endsection