@extends('reportes.layout_pdf')

@section('titulo', 'Reporte de Mermas')

@section('contenido')
<table>
    <thead>
        <tr>
            <th>Libro</th>
            <th>Total Registros</th>
            <th>Cantidad Perdida</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $item)
        <tr>
            <td>{{ $item->libro }}</td>
            <td>{{ $item->total_mermas }}</td>
            <td>{{ $item->cantidad_total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection