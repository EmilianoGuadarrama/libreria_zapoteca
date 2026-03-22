@extends('layouts.dashboard')
@section('dashboard-content')
    <div class="container mt-4">
        <h2 class="mb-4 text-white">Usuarios esperando aprobación</h2>

        <div class="card shadow-sm border-0" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
            <div class="card-body">
                <table class="table table-hover text-white">
                    <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Rol Solicitado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($pendientes as $u)
                        <tr>
                            <td>{{ $u->persona->nombre }} {{ $u->persona->apellido_paterno }}</td>
                            <td>{{ $u->correo }}</td>
                            <td><span class="badge bg-info">{{ $u->rol->nombre }}</span></td>
                            <td class="d-flex gap-2">
                                <!-- Botón Aprobar -->
                                <form action="{{ route('admin.activar', $u->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                                </form>

                                <!-- Botón Rechazar -->
                                <form action="{{ route('admin.rechazar', $u->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay solicitudes pendientes.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-navbar')
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Dashboard</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Salir
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </nav>
@endsection
