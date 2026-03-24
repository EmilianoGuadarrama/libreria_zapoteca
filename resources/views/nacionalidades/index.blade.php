@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold bebas" style="font-size: 2rem; color: #4b1c71;">Nacionalidades</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2" 
                    data-bs-toggle="modal" data-bs-target="#modalCreateNacionalidad" title="Nueva Nacionalidad">
                <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
            </button>
        </div>

        @if(session('status'))
            <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
                <i class="fa-solid fa-check-circle me-2"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-alert="close"></button>
            </div>
        @endif

        <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <table class="table table-bordered table-striped mi-datatable mb-0">
                <thead style="background-color: #fff0ff; color: #4b1c71;">
                    <tr class="bebas">
                        <th style="width: 50px;">#</th>
                        <th>Nacionalidad</th>
                        <th>País Origen</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nacionalidades as $nac)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $nac->nombre }}</td>
                        <td><span class="badge rounded-pill px-3" style="background-color: #f3e5f5; color: #4b1c71;">{{ $nac->pais->nombre }}</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 fs-5 me-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $nac->id }}">
                                <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                            </button>
                            <button type="button" class="btn btn-link p-0 fs-5" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $nac->id }}">
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
    <div class="modal fade" id="modalCreateNacionalidad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-flag me-2"></i> Nueva Nacionalidad</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('nacionalidades.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Nombre de la Nacionalidad</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Mexicana" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">País</label>
                            <select name="pais_id" class="form-select" required>
                                <option value="" selected disabled>Selecciona un país...</option>
                                @foreach($paises as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODALES EDIT Y DELETE --}}
    @foreach($nacionalidades as $nac)
        {{-- EDIT --}}
        <div class="modal fade" id="modalEdit{{ $nac->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editar</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('nacionalidades.update', $nac->id) }}" method="post">
                        @csrf @method('PUT')
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="{{ $nac->nombre }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">País</label>
                                <select name="pais_id" class="form-select" required>
                                    @foreach($paises as $p)
                                        <option value="{{ $p->id }}" {{ $nac->pais_id == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL DELETE --}}
        <div class="modal fade" id="modalDelete{{ $nac->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Estás seguro de eliminar la nacionalidad <br><strong>"{{ $nac->nombre }}"</strong>?</p>
                        <p class="text-muted small mb-0 mt-2 text-uppercase fw-bold" style="letter-spacing: 1px;">No se puede recuperar una vez eliminada</p>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        {{-- BOTÓN CANCELAR --}}
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        {{-- FORMULARIO DE ELIMINACIÓN --}}
                        <form action="{{ route('nacionalidades.destroy', $nac->id) }}" method="post" class="d-inline">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                                Sí, eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    @endforeach
@endsection