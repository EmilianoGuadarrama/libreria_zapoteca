@extends('layouts.dashboard')
@section('dashboard-content')
    <style>
        /* Estilos base para mantener la coherencia con Libros y Compras */
        .bebas {
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: 0.5px;
        }

        .mi-datatable thead {
            background-color: #fff0ff;
            color: #4b1c71;
        }

        .mi-datatable th {
            padding: 15px !important;
            vertical-align: middle;
        }

        .mi-datatable td {
            padding: 12px 15px !important;
            vertical-align: middle;
            color: #2d1f3a;
        }

        .badge-rol {
            background-color: #f3e8ff;
            color: #7f4ca5;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.85rem;
        }

        /* Ajuste para las alertas personalizadas */
        .custom-alert {
            background-color: #fff0ff;
            color: #4b1c71;
            border: 1px solid #dbb6ee;
            border-radius: 12px;
        }

        /* Efecto hover para las filas */
        .mi-datatable tbody tr:hover {
            background-color: #fdfafc;
        }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 bebas" style="font-size: 2rem; color: #4b1c71;">
                <i class="fa-solid fa-user-clock me-2"></i> Usuarios Esperando Aprobación
            </h3>
            <i class="fa-solid fa-shield-halved fs-3" style="color: #4b1c71;" data-bs-toggle="tooltip" title="Panel de Validación"></i>
        </div>


        @if(session('success') || session('status'))
            <div class="alert alert-dismissible fade show shadow-sm custom-alert" role="alert">
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i>
                <span class="fw-semibold">{{ session('success') ?? session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <span class="fw-semibold">{{ $errors->first() }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tabla de Usuarios --}}
        <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #eadcf2;">
            <table class="table table-bordered table-striped mi-datatable mb-0" style="width:100%; background: white;">
                <thead>
                <tr class="bebas">
                    <th style="width: 50px;">#</th>
                    <th>Nombre Completo</th>
                    <th>Correo Electrónico</th>
                    <th>Rol Solicitado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pendientes as $u)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold">
                            {{ $u->persona->nombre }} {{ $u->persona->apellido_paterno }}
                        </td>
                        <td>{{ $u->correo }}</td>
                        <td>
                            <span class="badge-rol">
                                <i class="fa-solid fa-tag me-1"></i> {{ $u->rol->nombre }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-3">
                                {{-- Botón Aprobar (Estilo Icono Morado como Libros) --}}
                                <form action="{{ route('admin.activar', $u->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-link p-0 text-decoration-none fs-5"
                                            onclick="return confirm('¿Deseas autorizar el acceso a este usuario?')"
                                            data-bs-toggle="tooltip" title="Aprobar Registro">
                                        <i class="fa-solid fa-circle-check" style="color: #4b1c71;"></i>
                                    </button>
                                </form>

                                {{-- Botón Rechazar (Estilo Basura Negro como Libros) --}}
                                <form action="{{ route('admin.rechazar', $u->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 text-decoration-none fs-5"
                                            onclick="return confirm('¿Seguro que deseas rechazar esta solicitud?')"
                                            data-bs-toggle="tooltip" title="Rechazar y Eliminar">
                                        <i class="fa-regular fa-trash-can" style="color: #000;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open fa-3x mb-3 d-block" style="color: #dbb6ee;"></i>
                            No hay usuarios pendientes de aprobación por el momento.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Inicializar tooltips de Bootstrap si los usas
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection
