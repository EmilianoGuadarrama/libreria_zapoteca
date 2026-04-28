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
            background-color: #fff0f0 !important;
            border-top: 2px solid #dc3545 !important;
            border-bottom: 2px solid #dc3545 !important;
        }
        .fila-incompleta td:first-child { border-left: 2px solid #dc3545 !important; }
        .fila-incompleta td:last-child { border-right: 2px solid #dc3545 !important; }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0" style="color: #4b1c71; font-weight: 800;">Proveedores</h3>
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

        <!-- Alerta general más clara (ahora dice que revisen el formulario) -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Hay errores en el formulario. Por favor, revísalos.
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
                    $datosIncompletos = empty($proveedor->empresa) || empty($proveedor->nombre_contacto) || empty($proveedor->correo) || empty($proveedor->telefono);
                @endphp
                
                <tr class="{{ $datosIncompletos ? 'fila-incompleta' : '' }}" title="{{ $datosIncompletos ? 'Alerta: Faltan datos en este registro' : '' }}">
                    <td>
                        {{ $loop->iteration }}
                        @if($datosIncompletos)
                            <i class="fa-solid fa-triangle-exclamation text-danger ms-1" title="Registro incompleto o huérfano"></i>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $proveedor->empresa ?? '- Falta Nombre -' }}</td>
                    <td class="{{ empty($proveedor->nombre_contacto) ? 'text-danger fw-bold' : '' }}">{{ $proveedor->nombre_contacto ?? 'Sin Asignar / Eliminado' }}</td>
                    <td class="{{ empty($proveedor->correo) ? 'text-danger fw-bold' : '' }}">{{ $proveedor->correo ?? 'Falta Correo' }}</td>
                    <td class="{{ empty($proveedor->telefono) ? 'text-danger fw-bold' : '' }}">{{ $proveedor->telefono ?? 'Falta Teléfono' }}</td>
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
                                                        <span class="badge rounded-pill" style="background-color: #b57edc; font-size: 0.85rem;">{{ $libro->total_ejemplares }}</span>
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
                            
                            <!-- Variables de control para reabrir el modal con errores -->
                            <input type="hidden" name="tipo_operacion" value="editar">
                            <input type="hidden" name="id_error" value="{{ $proveedor->id }}">

                            <div class="modal-body px-4 py-4">
                                @if(empty($proveedor->empresa) || empty($proveedor->correo) || empty($proveedor->telefono) || empty($proveedor->nombre_contacto))
                                    <div class="alert alert-danger p-2 px-3 mb-4" style="border-radius: 10px;">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i> <strong>Atención:</strong> Faltan datos o el contacto original fue eliminado. Completa la información para guardar.
                                    </div>
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="color: #4b1c71;">Nombre de la Empresa <span class="text-danger">*</span></label>
                                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $proveedor->empresa) }}" required style="border-radius: 10px; border-color: #dbb6ee;">
                                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="color: #4b1c71;">Correo Electrónico <span class="text-danger">*</span></label>
                                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $proveedor->correo) }}" placeholder="ejemplo@editorial.com" required style="border-radius: 10px; border-color: #dbb6ee;">
                                        <small style="color: #7a6a88; font-size: 0.8rem;">Ej: contacto@empresa.com</small>
                                        @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="color: #4b1c71;">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control phone-input @error('telefono') is-invalid @enderror" value="{{ old('telefono', $proveedor->telefono) }}" style="border-radius: 10px; border-color: #dbb6ee;">
                                        <small style="color: #7a6a88; font-size: 0.8rem;">Selecciona el país y escribe tu número.</small>
                                        <!-- Como intl-tel-input rompe los estilos de is-invalid a veces, lo ponemos como un div simple -->
                                        @error('telefono') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" style="color: #4b1c71;">Estado <span class="text-danger">*</span></label>
                                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required style="border-radius: 10px; border-color: #dbb6ee;">
                                            <option value="Activo" {{ old('estado', $proveedor->estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                                            <option value="Inactivo" {{ old('estado', $proveedor->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                        @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold" style="color: #4b1c71;">Persona de Contacto <span class="text-danger">*</span></label>
                                        <select name="persona_contacto_id" class="form-select @error('persona_contacto_id') is-invalid @enderror" required style="border-radius: 10px; border-color: #dbb6ee;">
                                            <option value="">-- Selecciona un contacto --</option>
                                            @foreach($personas as $persona)
                                                <option value="{{ $persona->id }}" {{ old('persona_contacto_id', $proveedor->persona_contacto_id) == $persona->id ? 'selected' : '' }}>
                                                    {{ $persona->nombre_completo }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('persona_contacto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <!-- Variable de control para reabrir el modal de creación -->
                        <input type="hidden" name="tipo_operacion" value="crear">

                        <div class="modal-body px-4 py-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Nombre de la Empresa <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required style="border-radius: 10px; border-color: #dbb6ee;">
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}" placeholder="ejemplo@editorial.com" required style="border-radius: 10px; border-color: #dbb6ee;">
                                    <small style="color: #7a6a88; font-size: 0.8rem;">Ej: contacto@empresa.com</small>
                                    @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control phone-input @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" style="border-radius: 10px; border-color: #dbb6ee;">
                                    <small style="color: #7a6a88; font-size: 0.8rem;">Selecciona el país y escribe tu número.</small>
                                    @error('telefono') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Estado <span class="text-danger">*</span></label>
                                    <select name="estado" class="form-select @error('estado') is-invalid @enderror" required style="border-radius: 10px; border-color: #dbb6ee;">
                                        <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" style="color: #4b1c71;">Persona de Contacto <span class="text-danger">*</span></label>
                                    <select name="persona_contacto_id" class="form-select @error('persona_contacto_id') is-invalid @enderror" required style="border-radius: 10px; border-color: #dbb6ee;">
                                        <option value="">-- Selecciona un contacto --</option>
                                        @foreach($personas as $persona)
                                            <option value="{{ $persona->id }}" {{ old('persona_contacto_id') == $persona->id ? 'selected' : '' }}>
                                                {{ $persona->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('persona_contacto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

    <!-- Script de intl-tel-input, Filtro del Historial y REAPERTURA DE MODALES -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Lógica para las Banderitas del Teléfono
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

            // 2. Lógica para el Buscador de Libros en Modal
            var buscadores = document.querySelectorAll('.buscador-libros');
            buscadores.forEach(function(input) {
                input.addEventListener('keyup', function() {
                    var filtro = this.value.toLowerCase();
                    var targetId = this.getAttribute('data-target');
                    var tbody = document.getElementById(targetId);
                    if (tbody) {
                        var filas = tbody.getElementsByTagName('tr');
                        for (var i = 0; i < filas.length; i++) {
                            var celdaTitulo = filas[i].getElementsByTagName('td')[0];
                            var celdaIsbn = filas[i].getElementsByTagName('td')[1];
                            if (celdaTitulo || celdaIsbn) {
                                var textoFila = (celdaTitulo.textContent || celdaTitulo.innerText) + ' ' + (celdaIsbn.textContent || celdaIsbn.innerText);
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

            // 3. LÓGICA PARA REABRIR EL MODAL SI HAY ERRORES DE VALIDACIÓN
            @if($errors->any())
                @if(old('tipo_operacion') == 'crear')
                    // Si el error ocurrió al crear un proveedor nuevo
                    var myModal = new bootstrap.Modal(document.getElementById('createModal'));
                    myModal.show();
                @elseif(old('tipo_operacion') == 'editar')
                    // Si el error ocurrió al editar un proveedor específico
                    var idModal = "{{ old('id_error') }}";
                    var myModal = new bootstrap.Modal(document.getElementById('editModal' + idModal));
                    myModal.show();
                @endif
            @endif
        });
    </script>
@endsection