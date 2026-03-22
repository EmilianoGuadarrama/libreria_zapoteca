@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0" style="color: #4b1c71; font-weight: 800;">Proveedores</h3>
            <!-- Botón para abrir el Modal de Creación -->
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-3" data-bs-toggle="modal" data-bs-target="#createModal" title="Nuevo Proveedor">
                <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
            </button>
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
                <th>Empresa</th>
                <th>Contacto</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($proveedores as $proveedor)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $proveedor->empresa }}</td>
                    <td>{{ $proveedor->nombre_contacto }}</td>
                    <td>{{ $proveedor->correo }}</td>
                    <td>{{ $proveedor->telefono ?? 'N/A' }}</td>
                    <td>
                        @if(strtolower($proveedor->estado) == 'activo')
                            <span class="badge" style="background-color: #7f4ca5;">Activo</span>
                        @else
                            <span class="badge bg-secondary">{{ $proveedor->estado }}</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <!-- Botón para abrir el Modal de Edición -->
                        <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $proveedor->id }}" title="Editar Proveedor">
                            <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                        </button>

                        <!-- Formulario para Eliminar -->
                        <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 text-decoration-none fs-5" onclick="return confirm('¿Estás seguro de eliminar este proveedor?')" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Proveedor">
                                <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                            </button>
                        </form>

                        <!-- MODAL DE EDICIÓN -->
                        <div class="modal fade text-start" id="editModal{{ $proveedor->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $proveedor->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 12px 35px rgba(75, 28, 113, .12);">
                                    
                                    <div class="modal-header" style="background: linear-gradient(180deg, #f8f2fb 0%, #fdf9ff 100%); border-bottom: 1px solid #eadcf2; border-radius: 16px 16px 0 0;">
                                        <h5 class="modal-title fw-bold" id="editModalLabel{{ $proveedor->id }}" style="color: #4b1c71;">
                                            <i class="fa-solid fa-pen-to-square me-2"></i> Editar Proveedor
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body px-4 py-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Nombre de la Empresa <span class="text-danger">*</span></label>
                                                    <input type="text" name="nombre" class="form-control" value="{{ $proveedor->empresa }}" required style="border-radius: 10px; border-color: #dbb6ee;">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Correo Electrónico <span class="text-danger">*</span></label>
                                                    <input type="email" name="correo" class="form-control" value="{{ $proveedor->correo }}" required style="border-radius: 10px; border-color: #dbb6ee;">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Teléfono</label>
                                                    <input type="text" name="telefono" class="form-control" value="{{ $proveedor->telefono }}" style="border-radius: 10px; border-color: #dbb6ee;">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Estado <span class="text-danger">*</span></label>
                                                    <select name="estado" class="form-select" required style="border-radius: 10px; border-color: #dbb6ee;">
                                                        <option value="Activo" {{ (strtolower($proveedor->estado) == 'activo') ? 'selected' : '' }}>Activo</option>
                                                        <option value="Inactivo" {{ (strtolower($proveedor->estado) == 'inactivo') ? 'selected' : '' }}>Inactivo</option>
                                                    </select>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Persona de Contacto <span class="text-danger">*</span></label>
                                                    <select name="persona_contacto_id" class="form-select" required style="border-radius: 10px; border-color: #dbb6ee;">
                                                        <option value="">-- Selecciona un contacto --</option>
                                                        @foreach($personas as $persona)
                                                            <option value="{{ $persona->id }}" {{ ($proveedor->persona_contacto_id == $persona->id) ? 'selected' : '' }}>
                                                                {{ $persona->nombre_completo }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer" style="border-top: 1px solid #eadcf2; background: #fdf9ff; border-radius: 0 0 16px 16px;">
                                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px; color: #7a6a88; border: 1px solid #eadcf2;">Cancelar</button>
                                            <button type="submit" class="btn text-white fw-bold px-4" style="border-radius: 10px; background: linear-gradient(180deg, #7f4ca5 0%, #4b1c71 100%); border: none;">Guardar Cambios</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <!-- FIN DEL MODAL -->

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- MODAL DE CREACIÓN -->
        <div class="modal fade text-start" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 12px 35px rgba(75, 28, 113, .12);">
                    
                    <div class="modal-header" style="background: linear-gradient(180deg, #f8f2fb 0%, #fdf9ff 100%); border-bottom: 1px solid #eadcf2; border-radius: 16px 16px 0 0;">
                        <h5 class="modal-title fw-bold" id="createModalLabel" style="color: #4b1c71;">
                            <i class="fa-solid fa-circle-plus me-2"></i> Nuevo Proveedor
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('proveedores.store') }}" method="POST">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Nombre de la Empresa <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required style="border-radius: 10px; border-color: #dbb6ee;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" required style="border-radius: 10px; border-color: #dbb6ee;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" style="border-radius: 10px; border-color: #dbb6ee;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Estado <span class="text-danger">*</span></label>
                                    <select name="estado" class="form-select" required style="border-radius: 10px; border-color: #dbb6ee;">
                                        <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Persona de Contacto <span class="text-danger">*</span></label>
                                    <select name="persona_contacto_id" class="form-select" required style="border-radius: 10px; border-color: #dbb6ee;">
                                        <option value="">-- Selecciona un contacto --</option>
                                        @foreach($personas as $persona)
                                            <option value="{{ $persona->id }}" {{ old('persona_contacto_id') == $persona->id ? 'selected' : '' }}>
                                                {{ $persona->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer" style="border-top: 1px solid #eadcf2; background: #fdf9ff; border-radius: 0 0 16px 16px;">
                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px; color: #7a6a88; border: 1px solid #eadcf2;">Cancelar</button>
                            <button type="submit" class="btn text-white fw-bold px-4" style="border-radius: 10px; background: linear-gradient(180deg, #7f4ca5 0%, #4b1c71 100%); border: none;">Guardar Proveedor</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- FIN DEL MODAL DE CREACIÓN -->

    </div>
@endsection