@extends('reportes.layout_pdf')

@section('titulo', 'Reporte de Lotes')

@section('contenido')
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Libro</th>
            <th>Cantidad</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lotes as $l)
        <tr>
            <td>{{ $l->lote }}</td>
            <td>{{ $l->libro }}</td>
            <td>{{ $l->cantidad }}</td>
            <td>{{ $l->fecha_entrada }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection