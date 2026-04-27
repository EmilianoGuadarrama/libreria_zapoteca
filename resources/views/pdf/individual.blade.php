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
        <tr>
            @foreach($datos as $valor)
            <td>{{ $valor }}</td>
            @endforeach
        </tr>
    </tbody>
</table>

@endsection