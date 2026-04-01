@extends('layouts.dashboard')

@section('dashboard-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold bebas" style="font-size: 2rem; color: #4b1c71;">Autores</h3>
        <button type="button" class="btn btn-link p-0 text-decoration-none fs-2" 
                data-bs-toggle="modal" data-bs-target="#modalCreateAutor" title="Nuevo Autor">
            <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
        </button>
    </div>

    @if(session('status'))
        <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <table class="table table-bordered table-striped mi-datatable mb-0">
            <thead style="background-color: #fff0ff; color: #4b1c71;">
                <tr class="bebas">
                    <th>Nombre Completo</th>
                    <th>Género</th>
                    <th>Nacionalidad</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($autores as $autor)
                <tr>
                    <td class="fw-semibold">
                        {{ $autor->persona->nombre }} {{ $autor->persona->apellido_paterno }} {{ $autor->persona->apellido_materno }}
                    </td>
                    <td>{{ $autor->persona->genero }}</td>
                   <td>{{ $autor->nacionalidad->nombre ?? 'Sin nacionalidad' }}</td>
                    <td class="text-end">
                        <button class="btn btn-link p-0 fs-5 me-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $autor->id }}">
                            <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                        </button>
                        <button class="btn btn-link p-0 fs-5" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $autor->id }}">
                            <i class="fa-regular fa-trash-can" style="color: #000;"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL CREATE --}}
<div class="modal fade" id="modalCreateAutor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-feather me-2"></i> Registrar Nuevo Autor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('autores.store') }}" method="post">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        {{-- Datos de Persona --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nombre(s)</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Apellido Materno</label>
                            <input type="text" name="apellido_materno" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Género</label>
                            <select name="genero" class="form-select" required>
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        {{-- Datos de Autor --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nacionalidad</label>
                            <select name="nacionalidad_id" class="form-select" required>
                                @foreach($nacionalidades as $n)
                                    <option value="{{ $n->id }}">{{ $n->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Biografía</label>
                            <textarea name="biografia" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Guardar Autor</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODALES EDIT Y DELETE (Generar con un foreach igual al de nacionalidades) --}}
@foreach($autores as $autor)
    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit{{ $autor->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Autor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('autores.update', $autor->id) }}" method="post">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="{{ $autor->persona->nombre }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" class="form-control" value="{{ $autor->persona->apellido_paterno }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Apellido Materno</label>
                                <input type="text" name="apellido_materno" class="form-control" value="{{ $autor->persona->apellido_materno }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nacionalidad</label>
                                <select name="nacionalidad_id" class="form-select">
                                    @foreach($nacionalidades as $n)
                                        <option value="{{ $n->id }}" {{ $autor->nacionalidad_id == $n->id ? 'selected' : '' }}>{{ $n->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Género</label>
                                <select name="genero" class="form-select">
                                    <option value="Hombre" {{ $autor->persona->genero == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                                    <option value="Mujer" {{ $autor->persona->genero == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                                    <option value="Otro" {{ $autor->persona->genero == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Biografía</label>
                                <textarea name="biografia" class="form-control" rows="3">{{ $autor->biografia }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Actualizar Datos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="modalDelete{{ $autor->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="fs-5">¿Deseas eliminar al autor <br><strong>"{{ $autor->persona->nombre }} {{ $autor->persona->apellido_paterno }}"</strong>?</p>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('autores.destroy', $autor->id) }}" method="post" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection