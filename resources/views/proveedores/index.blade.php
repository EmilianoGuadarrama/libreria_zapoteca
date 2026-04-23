@extends('layouts.dashboard')

@section('dashboard-content')
    <!-- Librerías para el selector de país (intl-tel-input) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <style>
        /* Forzar que el input de teléfono ocupe el 100% del ancho disponible */
        .iti { width: 100%; display: block; }
        .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/flags.png"); }
        @media (min-resolution: 2x) {
          .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/flags@2x.png"); }
        }

        /* Clase especial para enmarcar en rojo las filas incompletas o huérfanas */
        .fila-incompleta td {
            background-color: #fff0f0 !important; /* Un rojo muy suavecito de fondo */
            border-top: 2px solid #dc3545 !important;
            border-bottom: 2px solid #dc3545 !important;
        }
        .fila-incompleta td:first-child { border-left: 2px solid #dc3545 !important; }
        .fila-incompleta td:last-child { border-right: 2px solid #dc3545 !important; }
    </style>

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
                @php
                    // Evaluamos si a este registro le falta un dato o si la persona de contacto fue borrada (huérfano)
                    $datosIncompletos = empty($proveedor->empresa) || empty($proveedor->nombre_contacto) || empty($proveedor->correo) || empty($proveedor->telefono);
                @endphp
                
                <!-- Si faltan datos, le metemos la clase 'fila-incompleta' para que se pinte de rojo -->
                <tr class="{{ $datosIncompletos ? 'fila-incompleta' : '' }}" title="{{ $datosIncompletos ? 'Alerta: Faltan datos en este registro' : '' }}">
                    <td>
                        {{ $loop->iteration }}
                        @if($datosIncompletos)
                            <i class="fa-solid fa-triangle-exclamation text-danger ms-1" title="Registro incompleto o huérfano"></i>
                        @endif
                    </td>
                    <td class="fw-semibold">
                        {{ $proveedor->empresa ?? '- Falta Nombre -' }}
                    </td>
                    <td class="{{ empty($proveedor->nombre_contacto) ? 'text-danger fw-bold' : '' }}">
                        {{ $proveedor->nombre_contacto ?? 'Sin Asignar / Eliminado' }}
                    </td>
                    <td class="{{ empty($proveedor->correo) ? 'text-danger fw-bold' : '' }}">
                        {{ $proveedor->correo ?? 'Falta Correo' }}
                    </td>
                    <td class="{{ empty($proveedor->telefono) ? 'text-danger fw-bold' : '' }}">
                        {{ $proveedor->telefono ?? 'Falta Teléfono' }}
                    </td>
                    <td>
                        @if(strtolower($proveedor->estado) == 'activo')
                            <span class="badge" style="background-color: #7f4ca5;">Activo</span>
                        @else
                            <span class="badge bg-secondary">{{ $proveedor->estado ?? 'N/A' }}</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-2" data-bs-toggle="modal" data-bs-target="#showModal{{ $proveedor->id }}" title="Ver Expediente">
                            <i class="fa-solid fa-eye" style="color: {{ $datosIncompletos ? '#dc3545' : '#b57edc' }};"></i>
                        </button>

                        <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $proveedor->id }}" title="Editar Proveedor">
                            <i class="fa-solid fa-pen-to-square" style="color: {{ $datosIncompletos ? '#dc3545' : '#4b1c71' }};"></i>
                        </button>

                        <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 text-decoration-none fs-5" onclick="return confirm('¿Estás seguro de eliminar este proveedor?')" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Proveedor">
                                <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- ====================================================================== -->
        <!-- ZONA DE MODALES (Fuera de la tabla para evitar errores de DataTables)  -->
        <!-- ====================================================================== -->
        @foreach($proveedores as $proveedor)
            
            <!-- MODAL DE VER DETALLES -->
            <div class="modal fade text-start" id="showModal{{ $proveedor->id }}" tabindex="-1" aria-labelledby="showModalLabel{{ $proveedor->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 12px 35px rgba(75, 28, 113, .12);">
                        <div class="modal-header" style="background: linear-gradient(180deg, #f8f2fb 0%, #fdf9ff 100%); border-bottom: 1px solid #eadcf2; border-radius: 16px 16px 0 0;">
                            <h5 class="modal-title fw-bold" id="showModalLabel{{ $proveedor->id }}" style="color: #4b1c71;">
                                <i class="fa-solid fa-truck-field me-2"></i> Expediente: {{ $proveedor->empresa ?? 'Desconocida' }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 py-4">
                            <!-- Info Básica -->
                            <div class="row mb-4 p-3" style="background-color: #fdf9ff; border-radius: 12px; border: 1px solid #eadcf2;">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong style="color: #7f4ca5;">Contacto:</strong> {{ $proveedor->nombre_contacto ?? 'Sin asignar / Eliminado' }}</p>
                                    <p class="mb-1"><strong style="color: #7f4ca5;">Correo:</strong> {{ $proveedor->correo ?? 'Falta dato' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong style="color: #7f4ca5;">Teléfono:</strong> {{ $proveedor->telefono ?? 'Falta dato' }}</p>
                                    <p class="mb-1">
                                        <strong style="color: #7f4ca5;">Estado Actual:</strong> 
                                        <span class="badge {{ strtolower($proveedor->estado) == 'activo' ? 'bg-success' : 'bg-secondary' }}">{{ $proveedor->estado ?? 'N/A' }}</span>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Título y Buscador -->
                            <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 2px solid #eadcf2; padding-bottom: 8px;">
                                <h6 class="fw-bold mb-0" style="color: #4b1c71;">
                                    <i class="fa-solid fa-book-open me-2"></i> Historial de Libros Surtidos
                                </h6>
                                <!-- Solo mostramos el buscador si hay libros que buscar -->
                                @if(isset($librosSurtidos) && isset($librosSurtidos[$proveedor->id]) && count($librosSurtidos[$proveedor->id]) > 0)
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <span class="input-group-text bg-white" style="border-color: #dbb6ee; color: #7f4ca5;"><i class="fa-solid fa-magnifying-glass"></i></span>
                                        <input type="text" class="form-control buscador-libros" data-target="tabla-libros-{{ $proveedor->id }}" placeholder="Buscar por título o ISBN..." style="border-color: #dbb6ee;">
                                    </div>
                                @endif
                            </div>

                            <!-- Tabla de Libros -->
                            @if(isset($librosSurtidos) && isset($librosSurtidos[$proveedor->id]))
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-bordered" style="border-color: #eadcf2;">
                                        <thead style="background-color: #fff0ff; color: #4b1c71;">
                                            <tr>
                                                <th class="fw-semibold">Título del Libro</th>
                                                <th class="fw-semibold">ISBN (Edición)</th>
                                                <th class="text-center fw-semibold">Ejemplares Recibidos</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-libros-{{ $proveedor->id }}">
                                            @foreach($librosSurtidos[$proveedor->id] as $libro)
                                                <tr>
                                                    <td class="align-middle" style="color: #2d1f3a;">{{ $libro->titulo }}</td>
                                                    <td class="align-middle" style="color: #7a6a88;">{{ $libro->isbn }}</td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge rounded-pill" style="background-color: #b57edc; font-size: 0.85rem;">
                                                            {{ $libro->total_ejemplares }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert text-center my-3" style="background-color: transparent; border: 1px dashed #dbb6ee; color: #7a6a88;">
                                    <i class="fa-solid fa-box-open mb-2 fs-3" style="color: #dbb6ee;"></i><br>
                                    Aún no tenemos registros de libros recibidos por parte de este proveedor.
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #eadcf2; background: #fdf9ff; border-radius: 0 0 16px 16px;">
                            <button type="button" class="btn text-white fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px; background: linear-gradient(180deg, #7f4ca5 0%, #4b1c71 100%); border: none;">Cerrar Expediente</button>
                        </div>
                    </div>
                </div>
            </div>

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
                                <!-- Alerta si el usuario abre para editar un registro incompleto o huérfano -->
                                @if(empty($proveedor->empresa) || empty($proveedor->correo) || empty($proveedor->telefono) || empty($proveedor->nombre_contacto))
                                    <div class="alert alert-danger p-2 px-3 mb-4" style="border-radius: 10px;">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i> <strong>Atención:</strong> Faltan datos o el contacto original fue eliminado. Completa la información para guardar.
                                    </div>
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="color: #4b1c71;">Nombre de la Empresa <span class="text-danger">*</span></label>
                                        <input type="text" name="nombre" class="form-control" value="{{ $proveedor->empresa }}" required style="border-radius: 10px; border-color: #dbb6ee;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="color: #4b1c71;">Correo Electrónico <span class="text-danger">*</span></label>
                                        <input type="email" name="correo" class="form-control" value="{{ $proveedor->correo }}" placeholder="ejemplo@editorial.com" required style="border-radius: 10px; border-color: #dbb6ee;">
                                        <small style="color: #7a6a88; font-size: 0.8rem;">Ej: contacto@empresa.com</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="color: #4b1c71;">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control phone-input" value="{{ $proveedor->telefono }}" style="border-radius: 10px; border-color: #dbb6ee;">
                                        <small style="color: #7a6a88; font-size: 0.8rem;">Selecciona el país y escribe tu número.</small>
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
            
        @endforeach

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
                                    <!-- Aquí va el Placeholder y el Helper Text para el correo -->
                                    <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" placeholder="ejemplo@editorial.com" required style="border-radius: 10px; border-color: #dbb6ee;">
                                    <small style="color: #7a6a88; font-size: 0.8rem;">Ej: contacto@empresa.com</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Teléfono</label>
                                    <!-- Se agrega la clase 'phone-input' -->
                                    <input type="text" name="telefono" class="form-control phone-input" value="{{ old('telefono') }}" style="border-radius: 10px; border-color: #dbb6ee;">
                                    <small style="color: #7a6a88; font-size: 0.8rem;">Selecciona el país y escribe tu número.</small>
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

    </div>

    <!-- Script de intl-tel-input y Filtro del Historial -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ==========================================
            // 1. Lógica para las Banderitas del Teléfono
            // ==========================================
            var phoneInputs = document.querySelectorAll('.phone-input');
            phoneInputs.forEach(function(input) {
                var iti = window.intlTelInput(input, {
                    initialCountry: "mx",
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                });
                var form = input.closest('form');
                if(form) {
                    form.addEventListener('submit', function() {
                        input.value = iti.getNumber();
                    });
                }
            });

            // ==========================================
            // 2. Lógica para el Buscador de Libros en Modal
            // ==========================================
            var buscadores = document.querySelectorAll('.buscador-libros');
            buscadores.forEach(function(input) {
                input.addEventListener('keyup', function() {
                    // Tomamos el texto a buscar y lo pasamos a minúsculas
                    var filtro = this.value.toLowerCase();
                    // Buscamos el ID del <tbody> que corresponde a este modal
                    var targetId = this.getAttribute('data-target');
                    var tbody = document.getElementById(targetId);
                    
                    if (tbody) {
                        var filas = tbody.getElementsByTagName('tr');
                        // Iteramos sobre las filas de la tabla
                        for (var i = 0; i < filas.length; i++) {
                            var celdaTitulo = filas[i].getElementsByTagName('td')[0];
                            var celdaIsbn = filas[i].getElementsByTagName('td')[1];
                            
                            if (celdaTitulo || celdaIsbn) {
                                var textoFila = (celdaTitulo.textContent || celdaTitulo.innerText) + ' ' + (celdaIsbn.textContent || celdaIsbn.innerText);
                                // Si el texto coincide, mostramos la fila; si no, la ocultamos
                                if (textoFila.toLowerCase().indexOf(filtro) > -1) {
                                    filas[i].style.display = "";
                                } else {
                                    filas[i].style.display = "none";
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection