@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold">Historial de Compras</h3>
            <button type="button" class="btn btn-link p-0 text-decoration-none fs-2"
                    data-bs-toggle="modal" data-bs-target="#modalCreateCompra"
                    data-bs-placement="left" title="Nueva Compra">
                <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
            </button>
        </div>

        @if(session('success') || session('status'))
            <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i> 
                <span class="fw-semibold">{{ session('success') ?? session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any() || session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> 
                {{ session('error') ?? $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped mi-datatable" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th class="bebas">#</th>
                        <th class="bebas">Folio / Factura</th>
                        <th class="bebas">Proveedor</th>
                        <th class="bebas">Fecha</th>
                        <th class="bebas">Total</th>
                        <th class="bebas">Estado</th>
                        <th class="text-end bebas">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compras as $compra)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $compra->folio_factura }}</td>
                            <td>{{ $compra->proveedor->nombre ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y') }}</td>
                            <td class="fw-bold" style="color: #4b1c71;">
                                ${{ number_format($compra->total_compra, 2) }}
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $compra->estado == 'Recibida' ? '#e6f4ea' : '#fef7e0' }}; color: {{ $compra->estado == 'Recibida' ? '#1e8e3e' : '#b06000' }}; border-radius: 8px;">
                                    {{ $compra->estado }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 me-3"
                                        data-bs-toggle="modal" data-bs-target="#modalShowCompra{{ $compra->id }}"
                                        data-bs-placement="top" title="Ver Detalles">
                                    <i class="fa-solid fa-eye" style="color: #4b1c71;"></i>
                                </button>

                                <button type="button" class="btn btn-link p-0 text-decoration-none fs-5"
                                        data-bs-toggle="modal" data-bs-target="#modalDeleteCompra{{ $compra->id }}"
                                        data-bs-placement="top" title="Eliminar Compra">
                                    <i class="fa-regular fa-trash-can" style="color: rgb(0, 0, 0);"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $compras->links() }}</div>
    </div>

    <div class="modal fade" id="modalCreateCompra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-cart-plus me-2"></i> Registrar Entrada de Mercancía</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('compras.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="items_compra" id="items_compra_json">
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Proveedor:</label>
                                <select name="proveedor_id" class="form-select rounded-3 bg-light" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($proveedores as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Folio Factura:</label>
                                <input type="text" name="folio_factura" class="form-control rounded-3 bg-light" placeholder="Ej: FAC-990" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" style="color: #4b1c71;">Fecha Factura:</label>
                                <input type="date" name="fecha_compra" class="form-control rounded-3 bg-light" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="p-3 mb-3 shadow-sm" style="background-color: #fff0ff; border-radius: 12px; border: 1px dashed #dbb6ee;">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Añadir Libros a la Compra:</label>
                            <div class="position-relative">
                                <i class="fa-solid fa-search position-absolute" style="left: 15px; top: 12px; color: #7f4ca5;"></i>
                                <input type="text" id="busqueda_libro_modal" class="form-control ps-5 rounded-pill border-0 shadow-sm" placeholder="Buscar por título o ISBN...">
                                <div id="res_busqueda_modal" class="shadow-lg position-absolute w-100 bg-white" style="display:none; z-index:1050; border:1px solid #dbb6ee; border-radius: 0 0 15px 15px; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-3" style="max-height: 250px;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="bebas text-purple">Libro</th>
                                        <th class="bebas text-purple text-center">Costo Unit.</th>
                                        <th class="bebas text-purple text-center">Cant.</th>
                                        <th class="bebas text-purple text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="lista_compra_modal"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                        <div class="fs-3" style="color: #4b1c71;"><span class="bebas">TOTAL:</span> <strong id="total_compra_modal">$0.00</strong></div>
                        <div>
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Guardar Compra</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($compras as $compra)
        
        <div class="modal fade" id="modalShowCompra{{ $compra->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Detalle de Factura #{{ $compra->folio_factura }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="fw-bold" style="color: #4b1c71;">Datos del Proveedor</h6>
                                <p class="mb-0">{{ $compra->proveedor->nombre ?? 'N/A' }}<br>
                                Contacto: {{ $compra->proveedor->personaContacto->nombre ?? 'N/A' }}</p>
                            </div>
                            <div class="col-sm-6 text-end">
                                <h6 class="fw-bold" style="color: #4b1c71;">Información de Compra</h6>
                                <p class="mb-0">Fecha: {{ \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y') }}<br>
                                Registró: {{ $compra->usuario->name ?? $compra->usuario->correo }}</p>
                            </div>
                        </div>
                        <h6 class="fw-bold mb-3" style="color: #4b1c71;">Artículos Adquiridos</h6>
                        <div class="table-responsive border rounded-3">
                            <table class="table table-striped mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Libro (ISBN)</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Costo Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($compra->detalles as $detalle)
                                        <tr>
                                            <td>
                                                <strong class="d-block">{{ $detalle->edicion->libro->titulo }}</strong>
                                                <small class="text-muted">{{ $detalle->edicion->isbn }}</small>
                                            </td>
                                            <td class="text-center align-middle">{{ $detalle->cantidad }}</td>
                                            <td class="text-end align-middle">${{ number_format($detalle->subtotal / $detalle->cantidad, 2) }}</td>
                                            <td class="text-end fw-bold align-middle" style="color: #4b1c71;">${{ number_format($detalle->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-end">
                        <h4 class="mb-0" style="color: #4b1c71;"><span class="bebas me-2">GRAN TOTAL:</span> ${{ number_format($compra->total_compra, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDeleteCompra{{ $compra->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Estás seguro de eliminar la compra con folio <br><strong>"{{ $compra->folio_factura }}"</strong>?</p>
                        <p class="text-muted small mb-0 mt-2">Esta acción eliminará el registro y sus detalles. No se puede deshacer.</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('compras.destroy', $compra->id) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

<style>
    .text-purple { color: #4b1c71 !important; }
    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<script>
    /* LÓGICA DEL CARRITO PARA EL MODAL CREATE */
    let carritoCompra = [];
    const edicionesParaCompra = @json($ediciones ?? []);

    document.getElementById('busqueda_libro_modal').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const res = document.getElementById('res_busqueda_modal');
        if(query.length < 2) { res.style.display = 'none'; return; }

        const filtrados = edicionesParaCompra.filter(ed => 
            ed.libro.titulo.toLowerCase().includes(query) || ed.isbn.includes(query)
        ).slice(0, 5);

        res.innerHTML = filtrados.map(ed => `
            <div onclick="addToCompra(${ed.id}, '${ed.libro.titulo.replace(/'/g, "\\'")}', ${ed.costo_base ?? 0})" class="p-2 border-bottom result-item" style="cursor:pointer;">
                <div class="fw-bold" style="color: #4b1c71;">${ed.libro.titulo}</div>
                <small class="text-muted">ISBN: ${ed.isbn}</small>
            </div>
        `).join('');
        res.style.display = 'block';
    });

    document.addEventListener('click', function(e) {
        if (!document.getElementById('busqueda_libro_modal').contains(e.target)) {
            document.getElementById('res_busqueda_modal').style.display = 'none';
        }
    });

    function addToCompra(id, titulo, costo) {
        const existe = carritoCompra.find(i => i.edicion_id === id);
        if(!existe) { carritoCompra.push({ edicion_id: id, titulo: titulo, precio_costo: costo, cantidad: 1 }); } 
        else { existe.cantidad += 1; }
        document.getElementById('res_busqueda_modal').style.display = 'none';
        document.getElementById('busqueda_libro_modal').value = '';
        renderModalCarrito();
    }

    function updateRowCompra(index, campo, valor) {
        carritoCompra[index][campo] = parseFloat(valor) || 0;
        renderModalCarrito();
    }

    function removeRowCompra(index) {
        carritoCompra.splice(index, 1);
        renderModalCarrito();
    }

    function renderModalCarrito() {
        const tbody = document.getElementById('lista_compra_modal');
        let total = 0;
        
        tbody.innerHTML = carritoCompra.map((item, index) => {
            const subtotal = item.cantidad * item.precio_costo;
            total += subtotal;
            return `
                <tr>
                    <td class="small fw-bold align-middle">${item.titulo}</td>
                    <td class="text-center align-middle">
                        <div class="input-group input-group-sm mx-auto shadow-sm" style="width: 100px;">
                            <span class="input-group-text bg-white border-end-0">$</span>
                            <input type="number" step="0.01" class="form-control border-start-0 ps-0 text-end" value="${item.precio_costo}" onchange="updateRowCompra(${index}, 'precio_costo', this.value)">
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <input type="number" class="form-control form-control-sm mx-auto text-center shadow-sm" style="width: 70px;" value="${item.cantidad}" onchange="updateRowCompra(${index}, 'cantidad', this.value)">
                    </td>
                    <td class="text-end fw-bold align-middle" style="color: #4b1c71;">$${subtotal.toFixed(2)}</td>
                    <td class="text-center align-middle">
                        <button type="button" onclick="removeRowCompra(${index})" class="btn btn-link text-danger p-0"><i class="fa-solid fa-circle-xmark fs-5"></i></button>
                    </td>
                </tr>
            `;
        }).join('');

        document.getElementById('total_compra_modal').innerText = `$${total.toFixed(2)}`;
        document.getElementById('items_compra_json').value = JSON.stringify(carritoCompra);
    }
</script>
@endsection