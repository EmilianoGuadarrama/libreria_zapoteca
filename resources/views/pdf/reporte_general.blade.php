@extends('pdf.layout')

@section('contenido')

<table class="table">
    <thead>
        <tr>
            @foreach($columnas as $col)
            <th>{{ $col }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($datos as $fila)
        <tr>
            @foreach($fila as $valor)
            <td>{{ $valor }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

@endsection