@extends('layouts.dashboard')

@section('dashboard-content')
    <style>
        .zapoteca-error { color: #4b1c71; font-size: 0.85rem; font-weight: 600; margin-top: 5px; display: flex; align-items: center; }
        .form-control.is-invalid, .form-select.is-invalid { border-color: #7f4ca5 !important; box-shadow: 0 0 0 0.25rem rgba(127, 76, 165, 0.25) !important; }
        .bebas { font-family: 'Bebas Neue', sans-serif; }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 fw-bold bebas" style="font-size: 2rem; color: #4b1c71;">Editoriales</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2" data-bs-toggle="modal" data-bs-target="#modalCreateEditorial" title="Nueva Editorial">
                <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
            </button>
        </div>

        @if(session('status'))
            <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i> <span class="fw-semibold">{{ session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <span class="fw-semibold">{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <table class="table table-bordered table-striped mi-datatable mb-0" style="width:100%">
                <thead style="background-color: #fff0ff; color: #4b1c71;">
                <tr class="bebas">
                    <th style="width: 50px;">#</th>
                    <th>Nombre</th>
                    <th>País</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($editoriales as $ed)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $ed->nombre }}</td>
                        <td><span class="badge rounded-pill px-3" style="background-color: #f3e5f5; color: #4b1c71;">{{ $ed->pais->nombre ?? 'Sin país' }}</span></td>
                        <td>{{ $ed->correo ?? '—' }}</td>
                        <td>{{ $ed->telefono ?? '—' }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 fs-5 me-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $ed->id }}"><i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i></button>
                            <button type="button" class="btn btn-link p-0 fs-5" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $ed->id }}"><i class="fa-regular fa-trash-can" style="color: #000;"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <div class="modal fade" id="modalCreateEditorial" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-building me-2"></i> Nueva Editorial</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('editoriales.store') }}" method="post" novalidate>
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Nombre</label>
                            <input type="text" name="nombre" class="form-control @if($errors->has('nombre') && !old('id_edit')) is-invalid @endif" placeholder="Ej. Fondo de Cultura Económica" value="{{ !old('id_edit') ? old('nombre') : '' }}" required maxlength="200">
                            @if($errors->has('nombre') && !old('id_edit'))<div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first('nombre') }}</div>@endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">País</label>
                            <select name="pais_id" class="form-select @if($errors->has('pais_id') && !old('id_edit')) is-invalid @endif" required>
                                <option value="" selected disabled>Selecciona un país...</option>
                                @foreach($paises as $p)<option value="{{ $p->id }}" {{ (!old('id_edit') && old('pais_id') == $p->id) ? 'selected' : '' }}>{{ $p->nombre }}</option>@endforeach
                            </select>
                            @if($errors->has('pais_id') && !old('id_edit'))<div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first('pais_id') }}</div>@endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control @if($errors->has('correo') && !old('id_edit')) is-invalid @endif" placeholder="Ej. contacto@editorial.com" value="{{ !old('id_edit') ? old('correo') : '' }}" maxlength="200">
                            @if($errors->has('correo') && !old('id_edit'))<div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first('correo') }}</div>@endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="Ej. +52 55 1234 5678" value="{{ !old('id_edit') ? old('telefono') : '' }}" maxlength="50">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Guardar Editorial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($editoriales as $ed)
        {{-- MODAL EDIT --}}
        <div class="modal fade" id="modalEdit{{ $ed->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Editorial</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('editoriales.update', $ed->id) }}" method="post" novalidate>
                        @csrf @method('PUT')
                        <input type="hidden" name="id_edit" value="{{ $ed->id }}">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Nombre</label>
                                <input type="text" name="nombre" class="form-control @if($errors->has('nombre') && old('id_edit') == $ed->id) is-invalid @endif" value="{{ old('id_edit') == $ed->id ? old('nombre') : $ed->nombre }}" required maxlength="200">
                                @if($errors->has('nombre') && old('id_edit') == $ed->id)<div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first('nombre') }}</div>@endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">País</label>
                                <select name="pais_id" class="form-select" required>
                                    @foreach($paises as $p)<option value="{{ $p->id }}" {{ (old('id_edit') == $ed->id ? old('pais_id') : $ed->pais_id) == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>@endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control @if($errors->has('correo') && old('id_edit') == $ed->id) is-invalid @endif" value="{{ old('id_edit') == $ed->id ? old('correo') : $ed->correo }}" maxlength="200">
                                @if($errors->has('correo') && old('id_edit') == $ed->id)<div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first('correo') }}</div>@endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('id_edit') == $ed->id ? old('telefono') : $ed->telefono }}" maxlength="50">
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
        <div class="modal fade" id="modalDelete{{ $ed->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Eliminar la editorial <strong>"{{ $ed->nombre }}"</strong>?</p>
                        <p class="text-muted small">No se puede recuperar una vez eliminada.</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('editoriales.destroy', $ed->id) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var idEdit = '{{ old("id_edit") }}';
                var modalId = idEdit ? 'modalEdit' + idEdit : 'modalCreateEditorial';
                var el = document.getElementById(modalId);
                if (el) { var m = new bootstrap.Modal(el); m.show(); }
            });
        </script>
    @endif
@endsection
