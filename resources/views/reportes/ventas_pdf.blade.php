@extends('reportes.layout_pdf')

@section('titulo', 'Reporte de Ventas')

@section('contenido')
<table>
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
@endsection