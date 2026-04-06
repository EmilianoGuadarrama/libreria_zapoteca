@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Reporte de Compras</h1>

    <a href="{{ route('compras.reporte.pdf') }}" class="btn btn-danger">
        Descargar PDF
    </a>

    <table class="table mt-3">
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
</div>
@endsection