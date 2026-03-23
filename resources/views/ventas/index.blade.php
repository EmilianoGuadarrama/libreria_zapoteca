@extends('layouts.dashboard')

@section('dashboard-content')
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background:#f4efff;
            font-family:Arial, Helvetica, sans-serif;
        }
        .page-title{
            color:#4b1d95;
            font-weight:700;
        }
        .card-custom{
            border:none;
            border-radius:20px;
            box-shadow:0 10px 25px rgba(75,29,149,0.10);
        }
        .btn-primary-custom{
            background:#5b21b6;
            border:none;
        }
        .btn-primary-custom:hover{
            background:#4b1d95;
        }
        .table thead{
            background:#5b21b6;
            color:#fff;
        }
        .table thead th{
            border:none;
        }
        .table tbody tr:hover{
            background:#f3ebff;
        }
        .badge-custom{
            background:#ede9fe;
            color:#5b21b6;
            font-weight:600;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title mb-0">Listado de ventas</h1>
            <a href="{{ route('ventas.create') }}" class="btn btn-primary-custom text-white px-4 rounded-pill">Nueva venta</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-4 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="card card-custom">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventas as $venta)
                                <tr>
                                    <td><span class="badge rounded-pill badge-custom">{{ $venta->folio }}</span></td>
                                    <td>{{ $venta->usuario->name ?? $venta->usuario->nombre ?? 'Sin usuario' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                                    <td>${{ number_format($venta->total, 2) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">Ver</a>
                                            <a href="{{ route('ventas.edit', $venta->id) }}" class="btn btn-sm btn-outline-warning rounded-pill">Editar</a>
                                            <a href="{{ route('ventas.ticket', $venta->id) }}" class="btn btn-sm btn-outline-success rounded-pill">Ticket</a>
                                            <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No hay ventas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $ventas->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
@endsection