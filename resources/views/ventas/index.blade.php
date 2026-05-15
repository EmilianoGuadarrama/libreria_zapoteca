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

    <div class="card mb-3">

        <div class="card-body">

            <form method="GET"
                action="{{ route('ventas.reporte.general') }}">

                <div class="row">

                    {{-- FECHA --}}
                    <div class="col-md-3">

                        <label>Día</label>

                        <input type="date"
                            name="fecha"
                            class="form-control">

                    </div>

                    {{-- MES --}}
                    <div class="col-md-3">

                        <label>Mes</label>

                        <select name="mes"
                            class="form-control">

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

                        <label>Año</label>

                        <select name="anio"
                            class="form-control">

                            <option value="">Seleccione</option>

                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>

                        </select>

                    </div>

                    {{-- BOTÓN --}}
                    <div class="col-md-3 d-flex align-items-end">

                        <button type="submit"
                            class="btn btn-danger w-100"
                            onclick="return confirm('¿Generar reporte PDF?')">

                            <i class="fa-solid fa-file-pdf"></i>

                            Generar Reporte

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
@endsection