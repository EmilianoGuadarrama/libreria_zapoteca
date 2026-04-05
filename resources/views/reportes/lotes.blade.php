@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Reporte de Lotes</h1>

    <a href="{{ route('lotes.reporte.pdf') }}" class="btn btn-danger">
        Descargar PDF
    </a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID Lote</th>
                <th>Libro</th>
                <th>Cantidad</th>
                <th>Fecha Entrada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lotes as $lote)
            <tr>
                <td>{{ $lote->lote }}</td>
                <td>{{ $lote->libro }}</td>
                <td>{{ $lote->cantidad }}</td>
                <td>{{ $lote->fecha_entrada }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection