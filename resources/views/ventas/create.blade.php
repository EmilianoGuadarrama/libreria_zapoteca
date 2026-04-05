@extends('layouts.dashboard')

@section('dashboard-content')
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .pos-panel {
        background: var(--card);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 8px 25px rgba(75, 28, 113, 0.06);
        border: 1px solid var(--border);
    }

    .search-wrapper {
        position: relative;
        margin-bottom: 20px;
    }
    .search-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--purple-700);
    }
    .search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        margin-top: 5px;
    }
    .search-item {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
    }
    .search-item:last-child {
        border-bottom: none;
    }
    .search-item:hover {
        background: var(--purple-100);
    }
    .search-input {
        width: 100%;
        padding: 14px 14px 14px 45px;
        border-radius: 12px;
        border: 2px solid var(--border);
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fdfafc;
    }
    .search-input:focus {
        outline: none;
        border-color: var(--purple-500);
        background: var(--white);
        box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.1);
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
    }
    .cart-table th {
        text-align: left;
        padding: 12px;
        font-family: var(--font-display);
        color: var(--text-muted);
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border);
    }
    .cart-table td {
        padding: 16px 12px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
    }
    .cart-table tr:last-child td {
        border-bottom: none;
    }
    
    .qty-input {
        width: 70px;
        padding: 8px;
        border: 1px solid var(--border);
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
    }

    .btn-delete {
        background: rgba(255, 0, 0, 0.1);
        color: #d32f2f;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete:hover {
        background: #d32f2f;
        color: white;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 1.1rem;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px dashed var(--border);
        font-size: 1.8rem;
        color: var(--purple-900);
    }

    .payment-input-group {
        margin-top: 24px;
    }
    .payment-input-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text-dark);
    }
    .payment-input {
        width: 100%;
        padding: 15px;
        font-size: 1.5rem;
        text-align: right;
        border-radius: 12px;
        border: 2px solid var(--purple-300);
        color: var(--purple-900);
        font-weight: bold;
    }
    .payment-input:focus {
        outline: none;
        border-color: var(--purple-700);
    }

    .change-display {
        margin-top: 15px;
        padding: 15px;
        background: var(--purple-100);
        border-radius: 12px;
        text-align: right;
    }
    .change-display span {
        display: block;
        font-size: 0.9rem;
        color: var(--purple-700);
        font-weight: bold;
    }
    .change-display .amount {
        font-size: 1.8rem;
        color: var(--purple-900);
        font-family: var(--font-display);
    }

    .btn-checkout {
        width: 100%;
        padding: 18px;
        margin-top: 24px;
        background: linear-gradient(135deg, var(--purple-700) 0%, var(--purple-900) 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.4rem;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(75, 28, 113, 0.3);
    }

    @media (max-width: 1200px) {
        .pos-container {
            grid-template-columns: 1fr;
        }
    }

    .alert-container {
        margin-bottom: 24px;
        animation: slideInDown 0.4s ease-out;
    }

    .custom-alert {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        border: 1px solid transparent;
        background-color: #fef2f2;
        border-color: #fee2e2;
        color: #991b1b;
    }

    .custom-alert::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background-color: #ef4444;
    }

    .custom-alert .alert-icon {
        font-size: 1.8rem;
        margin-right: 16px;
        color: #ef4444;
    }

    .custom-alert .alert-content {
        flex-grow: 1;
    }

    .custom-alert .alert-title {
        font-weight: 700;
        margin-bottom: 2px;
        font-size: 1.1rem;
        font-family: var(--font-display, sans-serif);
    }

    .custom-alert .alert-message {
        font-size: 0.95rem;
        opacity: 0.9;
        margin: 0;
    }

    .custom-alert .close-btn {
        background: transparent;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.5;
        transition: opacity 0.2s, transform 0.2s;
        padding: 8px;
        color: #991b1b;
    }

    .custom-alert .close-btn:hover {
        opacity: 1;
        transform: scale(1.1);
    }

    @keyframes slideInDown {
        from { transform: translateY(-15px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

@if(session('error') || $errors->any())
    <div class="alert-container">
        <div class="custom-alert">
            <i class="fa-solid fa-circle-exclamation alert-icon"></i>
            <div class="alert-content">
                <div class="alert-title">¡Atención!</div>
                <div class="alert-message">{{ session('error') ?? $errors->first() }}</div>
            </div>
            <button type="button" class="close-btn" onclick="this.closest('.alert-container').remove()" title="Cerrar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
@endif

@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<div class="pos-container">
    
    <div class="pos-panel">
        <h2 class="bebas" style="font-size: 2rem; color: var(--purple-900); margin-bottom: 20px;">
            <i class="fa-solid fa-cart-shopping" style="margin-right: 10px;"></i> Nueva Venta
        </h2>

        <div class="search-wrapper" style="position: relative;">
            <i class="fa-solid fa-barcode"></i>
            <input type="text" id="buscador_libros" class="search-input" placeholder="Escanear código, ISBN o buscar por título..." autocomplete="off" autofocus>
            
            <div id="dropdown_resultados" class="search-dropdown" style="display: none;"></div>
        </div>

        <div style="min-height: 300px; max-height: 500px; overflow-y: auto;">
            <table class="cart-table" id="tabla_carrito">
                <thead>
                    <tr>
                        <th style="width: 15%;">ISBN</th>
                        <th style="width: 35%;">Título</th>
                        <th style="width: 15%;">Precio</th>
                        <th style="width: 15%;">Cant.</th>
                        <th style="width: 15%;">Subtotal</th>
                        <th style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="contenido_carrito">
                    <tr>
                        <td><small>9788445071408</small></td>
                        <td>
                            <strong>El Señor de los Anillos</strong><br>
                            <small style="color: var(--text-muted);">Edición Tapa Dura</small>
                        </td>
                        <td>$450.00</td>
                        <td>
                            <input type="number" class="qty-input" value="1" min="1">
                        </td>
                        <td style="font-weight: bold;">$450.00</td>
                        <td>
                            <button type="button" class="btn-delete" title="Quitar libro">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
            </table>
        </div>
    </div>

    <div class="pos-panel" style="display: flex; flex-direction: column;">
        <h3 class="bebas" style="font-size: 1.5rem; color: var(--purple-700); margin-bottom: 20px;">Resumen de Compra</h3>
        
        <div class="summary-row">
            <span style="color: var(--text-muted);">Artículos en carrito:</span>
            <span id="resumen_cantidad" style="font-weight: bold;">1</span>
        </div>
        
        <div class="summary-row">
            <span style="color: var(--text-muted);">Subtotal:</span>
            <span id="resumen_subtotal" style="font-weight: bold;">$450.00</span>
        </div>

        <div class="summary-row" style="color: #2e7d32;">
            <span>Descuentos promocionales:</span>
            <span id="resumen_descuento">-$0.00</span>
        </div>

        <div class="summary-total bebas">
            <span>TOTAL</span>
            <span id="gran_total">$450.00</span>
        </div>

        <div class="payment-input-group">
            <label for="monto_recibido">Efectivo Recibido:</label>
            <input type="number" id="monto_recibido" class="payment-input" placeholder="0.00" step="0.01">
        </div>

        <div class="change-display">
            <span>CAMBIO A ENTREGAR</span>
            <div class="amount" id="texto_cambio">$0.00</div>
        </div>

        <form id="form_venta" action="{{ route('ventas.store') }}" method="POST" style="margin-top: auto;">
            @csrf
            <input type="hidden" name="datos_venta" id="datos_venta">
            
            <button type="button" id="btn_procesar_venta" class="btn-checkout bebas">
                <i class="fa-solid fa-check-circle" style="margin-right: 8px;"></i> PROCESAR VENTA
            </button>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        let carrito = [];
        let totalGeneral = 0;
        let descuentoGlobal = 0;

        const tbodyCarrito = document.getElementById('contenido_carrito');
        const txtCantidad = document.getElementById('resumen_cantidad');
        const txtSubtotal = document.getElementById('resumen_subtotal');
        const txtTotal = document.getElementById('gran_total');
        const txtCambio = document.getElementById('texto_cambio');
        const inputMontoRecibido = document.getElementById('monto_recibido');
        const inputBuscador = document.getElementById('buscador_libros');
        const btnProcesar = document.getElementById('btn_procesar_venta');
        const formVenta = document.getElementById('form_venta');
        const inputDatosVenta = document.getElementById('datos_venta');

        renderizarCarrito();

        window.agregarAlCarrito = function(libro) {
            const existe = carrito.find(item => item.edicion_id === libro.edicion_id);

            if (existe) {
                existe.cantidad += 1;
                existe.subtotal = existe.cantidad * existe.precio_unitario;
            } else {
                carrito.push({
                    edicion_id: libro.edicion_id,
                    isbn: libro.isbn,
                    titulo: libro.titulo,
                    precio_unitario: parseFloat(libro.precio_venta),
                    cantidad: 1,
                    subtotal: parseFloat(libro.precio_venta)
                });
            }
            renderizarCarrito();
        };

        window.eliminarDelCarrito = function(edicion_id) {
            carrito = carrito.filter(item => item.edicion_id !== edicion_id);
            renderizarCarrito();
        };

        window.actualizarCantidad = function(edicion_id, nuevaCantidad) {
            const item = carrito.find(i => i.edicion_id === edicion_id);
            if (item) {
                item.cantidad = parseInt(nuevaCantidad) || 1;
                item.subtotal = item.cantidad * item.precio_unitario;
                renderizarCarrito();
            }
        };

        function renderizarCarrito() {
            tbodyCarrito.innerHTML = '';
            
            let cantidadArticulos = 0;
            totalGeneral = 0;

            if (carrito.length === 0) {
                tbodyCarrito.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 20px; color: var(--text-muted);">El carrito está vacío. Busca un libro para empezar.</td></tr>`;
            }

            carrito.forEach(item => {
                cantidadArticulos += item.cantidad;
                totalGeneral += item.subtotal;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><small>${item.isbn}</small></td>
                    <td><strong>${item.titulo}</strong></td>
                    <td>$${item.precio_unitario.toFixed(2)}</td>
                    <td>
                        <input type="number" class="qty-input" value="${item.cantidad}" min="1" 
                               onchange="actualizarCantidad(${item.edicion_id}, this.value)">
                    </td>
                    <td style="font-weight: bold;">$${item.subtotal.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn-delete" onclick="eliminarDelCarrito(${item.edicion_id})" title="Quitar libro">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                `;
                tbodyCarrito.appendChild(tr);
            });

            txtCantidad.textContent = cantidadArticulos;
            txtSubtotal.textContent = `$${totalGeneral.toFixed(2)}`;
            txtTotal.textContent = `$${(totalGeneral - descuentoGlobal).toFixed(2)}`;
            
            calcularCambio();
        }

        function calcularCambio() {
            const recibido = parseFloat(inputMontoRecibido.value) || 0;
            const aPagar = totalGeneral - descuentoGlobal;
            
            let cambio = recibido - aPagar;
            
            if (cambio < 0 || carrito.length === 0) {
                cambio = 0;
            }

            txtCambio.textContent = `$${cambio.toFixed(2)}`;
        }

        inputMontoRecibido.addEventListener('input', calcularCambio);

        btnProcesar.addEventListener('click', function () {
            if (carrito.length === 0) {
                alert('No hay artículos en el carrito.');
                return;
            }

            const recibido = parseFloat(inputMontoRecibido.value) || 0;
            const aPagar = totalGeneral - descuentoGlobal;

            if (recibido < aPagar) {
                alert('El monto recibido es menor al total de la compra.');
                inputMontoRecibido.focus();
                return;
            }

            const payload = {
                total: aPagar,
                monto_recibido: recibido,
                cambio: recibido - aPagar,
                carrito: carrito
            };

            inputDatosVenta.value = JSON.stringify(payload);
            
            btnProcesar.disabled = true;
            btnProcesar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> PROCESANDO...';
            
            formVenta.submit();
        });

        const dropdownResultados = document.getElementById('dropdown_resultados');
        let timeoutBuscador = null;

        inputBuscador.addEventListener('input', function() {
            clearTimeout(timeoutBuscador);
            const termino = this.value.trim();

            if (termino.length < 2) {
                dropdownResultados.style.display = 'none';
                return;
            }

            timeoutBuscador = setTimeout(() => {
                const urlBusqueda = "{{ route('ventas.buscar_libro') }}";
                
                fetch(`${urlBusqueda}?q=${termino}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Error de red o ruta no encontrada');
                        return response.json();
                    })
                    .then(data => {
                        dropdownResultados.innerHTML = '';
                        
                        if (data.length === 0) {
                            dropdownResultados.innerHTML = '<div class="search-item" style="color: var(--text-muted); cursor: default;">No se encontraron libros con stock. Revisa tu inventario.</div>';
                        } else {
                            data.forEach(libro => {
                                const div = document.createElement('div');
                                div.className = 'search-item';
                                div.innerHTML = `
                                    <div>
                                        <strong>${libro.titulo}</strong><br>
                                        <small style="color: var(--text-muted);">ISBN: ${libro.isbn} | Disp: ${libro.existencias}</small>
                                    </div>
                                    <div style="font-weight: bold; color: var(--purple-700);">
                                        $${parseFloat(libro.precio_venta).toFixed(2)}
                                    </div>
                                `;
                                
                                div.onclick = function() {
                                    const itemEnCarrito = carrito.find(i => i.edicion_id === libro.edicion_id);
                                    if(itemEnCarrito && itemEnCarrito.cantidad >= libro.existencias) {
                                        alert('No puedes agregar más copias. Stock máximo alcanzado.');
                                        return;
                                    }

                                    window.agregarAlCarrito(libro);
                                    
                                    inputBuscador.value = '';
                                    dropdownResultados.style.display = 'none';
                                    inputBuscador.focus();
                                };
                                dropdownResultados.appendChild(div);
                            });
                        }
                        dropdownResultados.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error en búsqueda:', error);
                        dropdownResultados.innerHTML = '<div class="search-item" style="color: red; cursor: default;">Hubo un error de conexión. Presiona F12 y revisa la pestaña "Console" o "Network".</div>';
                        dropdownResultados.style.display = 'block';
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!inputBuscador.contains(e.target) && !dropdownResultados.contains(e.target)) {
                dropdownResultados.style.display = 'none';
            }
        });

    });
</script>
@endsection