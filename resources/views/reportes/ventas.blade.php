@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Reporte de Ventas</h1>

    <a href="{{ route('ventas.reporte.pdf') }}" class="btn btn-danger">
        Descargar PDF
    </a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>Libro</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $item)
            <tr>
                <td>{{ $item->venta }}</td>
                <td>{{ $item->libro }}</td>
                <td>{{ $item->total_vendidos }}</td>
                <td>${{ $item->total_venta }}</td>
                <td>{{ $item->fecha }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection