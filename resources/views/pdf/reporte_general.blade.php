@extends('pdf.layout')

@section('title', $titulo)

@section('content')

<h1>{{ $titulo }}</h1>

<p>
    Fecha de generación:
    {{ now()->format('d/m/Y H:i') }}
</p>

@if(isset($estadisticas))
<div class="estadisticas">
    @foreach($estadisticas as $label => $valor)
    <div class="card">
        <strong>{{ $label }}</strong><br>
        {{ $valor }}
    </div>
    @endforeach
</div>
@endif

<table>
    <thead>
        <tr>
            @foreach($columnas as $columna)
            <th>{{ $columna }}</th>
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