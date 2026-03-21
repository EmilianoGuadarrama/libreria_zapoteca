@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Libros</h3>
            <a href="{{ route('libros.create') }}" class="text-decoration-none fs-3" data-bs-toggle="tooltip" data-bs-placement="left" title="Nuevo Libro">
                <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
            </a>
        </div>

        @if(session('status'))
            <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i> <span class="fw-semibold">{{ session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <table class="table table-bordered table-striped mi-datatable" style="width:100%">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Titulo</th>
                <th>Sinopsis</th>
                <th>Clasificacion</th>
                <th>Año de Publicación</th>
                <th>Genero</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($libros as $libro)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $libro->titulo }}</td>
                    <td>{{ Str::limit($libro->sinopsis, 50) }}</td>
                    <td>{{ $libro->nombre_clasificacion }}</td>
                    <td>{{ $libro->anio_publicacion_original }}</td>
                    <td>{{ $libro->nombre_genero }}</td>

                    <td class="text-end">
                        <a href="{{ route('libros.edit', $libro->id) }}" class="text-decoration-none fs-5 me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Libro">
                            <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                        </a>

                        <form action="{{ route('libros.destroy', $libro->id) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 text-decoration-none fs-5" onclick="return confirm('¿Eliminar?')" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Libro">
                                <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection
