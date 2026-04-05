@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Reporte de Mermas</h1>

    <a href="{{ route('mermas.reporte.pdf') }}" class="btn btn-danger">
        Descargar PDF
    </a>

    <table class="table mt-3">
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
</div>
@endsection