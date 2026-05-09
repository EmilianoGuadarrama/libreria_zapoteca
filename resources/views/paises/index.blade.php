@extends('layouts.dashboard')

@section('dashboard-content')
    <style>
        .zapoteca-error {
            color: #4b1c71;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 5px;
            display: flex;
            align-items: center;
        }
        
        .form-control.is-invalid {
            border-color: #7f4ca5 !important;
            box-shadow: 0 0 0 0.25rem rgba(127, 76, 165, 0.25) !important;
        }

        .bebas { font-family: 'Bebas Neue', sans-serif; }
    </style>

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold bebas" style="font-size: 2rem; color: #4b1c71;">Países</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
                    data-bs-toggle="modal" data-bs-target="#modalCreatePais"
                    title="Nuevo País">
                <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
            </button>
        </div>

        {{-- Alertas de Éxito --}}
        @if(session('status'))
            <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i> <span class="fw-semibold">{{ session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <table class="table table-bordered table-striped mi-datatable mb-0" style="width:100%">
                <thead style="background-color: #fff0ff; color: #4b1c71;">
                <tr class="bebas">
                    <th style="width: 50px;">#</th>
                    <th>Nombre del País</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($paises as $pais)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $pais->nombre }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                                    data-bs-toggle="modal" data-bs-target="#modalEditPais{{ $pais->id }}"
                                    title="Editar País">
                                <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                            </button>

                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5"
                                    data-bs-toggle="modal" data-bs-target="#modalDeletePais{{ $pais->id }}"
                                    title="Eliminar País">
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
    <div class="modal fade" id="modalCreatePais" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4">
                        <i class="fa-solid fa-earth-americas me-2"></i> Nuevo País
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('paises.store') }}" method="post" novalidate>
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Nombre del País</label>
                            {{-- Solo muestra OLD si no hay un id_edit en sesión (significa que el error fue aquí) --}}
                            <input type="text" name="nombre" 
                                   class="form-control @if($errors->has('nombre') && !old('id_edit')) is-invalid @endif" 
                                   placeholder="Ej. México, España..." 
                                   value="{{ !old('id_edit') ? old('nombre') : '' }}" required maxlength="200">
                            
                            @if($errors->has('nombre') && !old('id_edit'))
                                <div class="zapoteca-error">
                                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first('nombre') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Guardar País</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODALES EDIT Y DELETE --}}
    @foreach($paises as $pais)

        {{-- MODAL EDIT --}}
        <div class="modal fade" id="modalEditPais{{ $pais->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-pen-to-square me-2"></i> Editar País
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('paises.update', $pais->id) }}" method="post" novalidate>
                        @csrf
                        @method('PUT')
                        {{-- Campo oculto para identificar este modal en caso de error --}}
                        <input type="hidden" name="id_edit" value="{{ $pais->id }}">

                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Nombre del País</label>
                                {{-- Muestra OLD solo si este es el ID que falló al editar --}}
                                <input type="text" name="nombre" 
                                       class="form-control @if($errors->has('nombre') && old('id_edit') == $pais->id) is-invalid @endif" 
                                       value="{{ old('id_edit') == $pais->id ? old('nombre') : $pais->nombre }}" 
                                       required maxlength="200">
                                
                                @if($errors->has('nombre') && old('id_edit') == $pais->id)
                                    <div class="zapoteca-error">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first('nombre') }}
                                    </div>
                                @endif
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
        <div class="modal fade" id="modalDeletePais{{ $pais->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Estás seguro de eliminar el país?><strong>"{{ $pais->nombre }}"</strong>?</p>
                        <p class="text-muted small mb-0 mt-2">No se puede recuperar una vez eliminado.</p>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('paises.destroy', $pais->id) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Script inteligente para reabrir el modal correcto --}}
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(old('id_edit'))
                    var modalId = 'modalEditPais' + '{{ old("id_edit") }}';
                @else
                    var modalId = 'modalCreatePais';
                @endif
                
                var myModalElement = document.getElementById(modalId);
                if (myModalElement) {
                    var myModal = new bootstrap.Modal(myModalElement);
                    myModal.show();
                }
            });
        </script>
    @endif

@endsection