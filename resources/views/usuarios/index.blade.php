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
        
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #7f4ca5 !important;
            box-shadow: 0 0 0 0.25rem rgba(127, 76, 165, 0.25) !important;
        }

        .bebas { font-family: 'Bebas Neue', sans-serif; }
        
        .form-label { color: #4b1c71; font-weight: bold; }
        
        .btn-purple {
            background-color: #4b1c71;
            color: white;
            border-radius: 50px;
            font-weight: bold;
            padding: 8px 25px;
        }
        .btn-purple:hover { color: white; background-color: #5e2a8a; }

        /* Colores dinámicos para estados */
        .bg-activo { background-color: #28a745; }
        .bg-inactivo { background-color: #6c757d; }
        .bg-rechasado { background-color: #dc3545; }
        .bg-despedido { background-color: #343a40; }
        .bg-pendiente { background-color: #ffc107; color: #000 !important; }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 fw-bold bebas" style="font-size: 2.5rem; color: #4b1c71;">Gestión de Personal</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2" data-bs-toggle="modal" data-bs-target="#modalCreateUsuario">
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
                <i class="fa-solid fa-circle-xmark me-2"></i> <span class="fw-semibold">{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; background: white;">
            <table class="table table-bordered table-striped mi-datatable mb-0">
                <thead style="background-color: #fff0ff; color: #4b1c71;">
                    <tr class="bebas">
                        <th>#</th>
                        <th>Nombre Completo</th>
                        <th>Correo Electrónico</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">
                                {{ $usuario->persona->nombre ?? 'N/A' }} 
                                {{ $usuario->persona->apellido_paterno ?? '' }} 
                                {{ $usuario->persona->apellido_materno ?? '' }}
                            </td>
                            <td>{{ $usuario->correo }}</td>
                            <td>
                                <span class="badge rounded-pill px-3" style="background-color: #4b1c71; color: white;">
                                    {{ $usuario->rol->nombre ?? 'Sin Rol' }}
                                </span>
                            </td>
                            <td>
                                @php 
                                    $est = $usuario->estado ?? 'Activo';
                                    $clase = 'bg-' . strtolower($est);
                                @endphp
                                <span class="badge {{ $clase }} px-3">
                                    {{ $est }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-link p-0 fs-5 me-3" data-bs-toggle="modal" data-bs-target="#modalEditUsuario{{ $usuario->id }}">
                                    <i class="fa-solid fa-pen-to-square" style="color: #4b1c71;"></i>
                                </button>
                                <button type="button" class="btn btn-link p-0 fs-5" data-bs-toggle="modal" data-bs-target="#modalDeleteUsuario{{ $usuario->id }}">
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
    <div class="modal fade" id="modalCreateUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-user-plus me-2"></i> Nuevo Personal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('usuarios.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}">
                                @error('nombre') <div class="zapoteca-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ap. Paterno</label>
                                <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror" value="{{ old('apellido_paterno') }}">
                                @error('apellido_paterno') <div class="zapoteca-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ap. Materno</label>
                                <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Género</label>
                                <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                                    <option value="" disabled selected>Selecciona</option>
                                    <option value="Mujer" {{ old('genero') == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                                    <option value="Hombre" {{ old('genero') == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                                    <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rol del Sistema</label>
                                <select name="rol_id" class="form-select @error('rol_id') is-invalid @enderror">
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}">
                                @error('correo') <div class="zapoteca-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="contrasena" class="form-control @error('contrasena') is-invalid @enderror">
                                @error('contrasena') <div class="zapoteca-error">Mín. 6 caracteres</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="Rechasado" {{ old('estado') == 'Rechasado' ? 'selected' : '' }}>Rechasado</option>
                                    <option value="Despedido" {{ old('estado') == 'Despedido' ? 'selected' : '' }}>Despedido</option>
                                    <option value="Pendiente" {{ old('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-purple px-4">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($usuarios as $usuario)
        {{-- MODAL EDIT --}}
        <div class="modal fade" id="modalEditUsuario{{ $usuario->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Personal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" novalidate>
                        @csrf @method('PUT')
                        <input type="hidden" name="id_edit" value="{{ $usuario->id }}">
                        <div class="modal-body p-4 text-start">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nombre(s)</label>
                                    <input type="text" name="nombre" class="form-control" value="{{ old('id_edit') == $usuario->id ? old('nombre') : ($usuario->persona->nombre ?? '') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ap. Paterno</label>
                                    <input type="text" name="apellido_paterno" class="form-control" value="{{ old('id_edit') == $usuario->id ? old('apellido_paterno') : ($usuario->persona->apellido_paterno ?? '') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ap. Materno</label>
                                    <input type="text" name="apellido_materno" class="form-control" value="{{ old('id_edit') == $usuario->id ? old('apellido_materno') : ($usuario->persona->apellido_materno ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Género</label>
                                    <select name="genero" class="form-select">
                                        @php $g = old('id_edit') == $usuario->id ? old('genero') : ($usuario->persona->genero ?? ''); @endphp
                                        <option value="Mujer" {{ $g == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                                        <option value="Hombre" {{ $g == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                                        <option value="Otro" {{ $g == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Rol del Sistema</label>
                                    <select name="rol_id" class="form-select">
                                        @foreach($roles as $rol)
                                            @php $r_id = old('id_edit') == $usuario->id ? old('rol_id') : ($usuario->rol_id ?? ''); @endphp
                                            <option value="{{ $rol->id }}" {{ $r_id == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Correo</label>
                                    <input type="email" name="correo" class="form-control" value="{{ old('id_edit') == $usuario->id ? old('correo') : $usuario->correo }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select">
                                        @php $e_val = old('id_edit') == $usuario->id ? old('estado') : ($usuario->estado ?? 'Activo'); @endphp
                                        <option value="Activo" {{ $e_val == 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Inactivo" {{ $e_val == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                        <option value="Rechasado" {{ $e_val == 'Rechasado' ? 'selected' : '' }}>Rechasado</option>
                                        <option value="Despedido" {{ $e_val == 'Despedido' ? 'selected' : '' }}>Despedido</option>
                                        <option value="Pendiente" {{ $e_val == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    </select>
                                </div>
                               
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-purple px-4">Actualizar Datos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL DELETE --}}
        <div class="modal fade" id="modalDeleteUsuario{{ $usuario->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Estás seguro de eliminar a <br>
                        <strong>{{ $usuario->persona->nombre ?? $usuario->correo }}</strong>?</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="post" class="d-inline">
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
                var modalId = '{{ old("id_edit") }}' ? 'modalEditUsuario' + '{{ old("id_edit") }}' : 'modalCreateUsuario';
                var myModal = new bootstrap.Modal(document.getElementById(modalId));
                myModal.show();
            });
        </script>
    @endif
@endsection