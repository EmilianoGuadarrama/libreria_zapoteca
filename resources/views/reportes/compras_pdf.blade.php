@extends('reportes.layout_pdf')

@section('titulo', 'Reporte de Compras')

@section('contenido')
<table>
    <thead>
        <tr>
            <th>ID Compra</th>
            <th>Libro</th>
            <th>Cantidad</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($compras as $c)
        <tr>
            <td>{{ $c->compra }}</td>
            <td>{{ $c->libro }}</td>
            <td>{{ $c->total_cantidad }}</td>
            <td>{{ $c->fecha }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection