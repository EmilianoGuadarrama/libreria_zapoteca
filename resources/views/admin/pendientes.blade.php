@extends('layouts.dashboard')

@section('dashboard-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold bebas" style="font-size: 2rem; color: #4b1c71;">
            Usuarios Esperando Aprobación
        </h3>
        {{-- Icono decorativo para mantener simetría con el botón "Plus" de Autores --}}
        <div class="fs-2">
            <i class="fa-solid fa-user-clock" style="color: #4b1c71;"></i>
        </div>
    </div>

    {{-- Alertas con el estilo de Autores --}}
    @if(session('success') || session('status'))
        <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') ?? session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #7f4ca5; border: 1px solid #dbb6ee; border-radius: 12px;">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <table class="table table-bordered table-striped mi-datatable mb-0">
            <thead style="background-color: #fff0ff; color: #4b1c71;">
                <tr class="bebas">
                    <th>Nombre Completo</th>
                    <th>Correo Electrónico</th>
                    <th>Rol Solicitado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendientes as $u)
                <tr>
                    <td class="fw-semibold">
                        {{ $u->persona->nombre }} {{ $u->persona->apellido_paterno }}
                    </td>
                    <td>{{ $u->correo }}</td>
                    <td>
                        <span class="badge" style="background-color: #f3e8ff; color: #7f4ca5; border-radius: 8px; padding: 5px 10px;">
                            {{ $u->rol->nombre }}
                        </span>
                    </td>
                    <td class="text-end">
                        {{-- Botón para abrir Modal Aprobar --}}
                        <button class="btn btn-link p-0 fs-5 me-3" data-bs-toggle="modal" data-bs-target="#modalAprobar{{ $u->id }}" title="Aprobar">
                            <i class="fa-solid fa-circle-check" style="color: #4b1c71;"></i>
                        </button>
                        {{-- Botón para abrir Modal Rechazar --}}
                        <button class="btn btn-link p-0 fs-5" data-bs-toggle="modal" data-bs-target="#modalRechazar{{ $u->id }}" title="Rechazar">
                            <i class="fa-regular fa-trash-can" style="color: #000;"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No hay solicitudes pendientes.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($pendientes as $u)
    {{-- MODAL APROBAR (Siguiendo el estilo del Modal Edit de Autores) --}}
    <div class="modal fade" id="modalAprobar{{ $u->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-user-check me-2"></i> Autorizar Acceso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="fs-5">¿Deseas autorizar el acceso al sistema para <br><strong>"{{ $u->persona->nombre }} {{ $u->persona->apellido_paterno }}"</strong>?</p>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('admin.activar', $u->id) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Sí, Autorizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL RECHAZAR (Siguiendo el estilo del Modal Delete de Autores) --}}
    <div class="modal fade" id="modalRechazar{{ $u->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="fs-5">¿Deseas rechazar y eliminar la solicitud de <br><strong>"{{ $u->persona->nombre }} {{ $u->persona->apellido_paterno }}"</strong>?</p>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('admin.rechazar', $u->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection