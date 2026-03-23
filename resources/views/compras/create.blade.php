<div class="modal-overlay" id="modalCompra">
    <div class="modal-content" style="max-width: 950px; width: 95%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--purple-100); padding-bottom: 10px;">
            <h3 class="bebas" style="color: var(--purple-900); font-size: 1.8rem; margin: 0;">
                <i class="fa-solid fa-cart-plus"></i> Registrar Entrada de Mercancía
            </h3>
            <button onclick="cerrarModalCompra()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('compras.store') }}" method="POST" id="formNuevaCompra">
            @csrf
            <input type="hidden" name="items_compra" id="items_compra_json">

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div class="form-group">
                    <label>Proveedor:</label>
                    <select name="proveedor_id" class="form-control" required>
                        <option value="">Seleccione...</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Folio Factura:</label>
                    <input type="text" name="folio_factura" class="form-control" placeholder="Ej: FAC-990" required>
                </div>
                <div class="form-group">
                    <label>Fecha Factura:</label>
                    <input type="date" name="fecha_compra" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div style="background: var(--purple-50); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                <label style="font-weight: bold; color: var(--purple-900);">Añadir Libros a la Compra:</label>
                <div style="position: relative; margin-top: 5px;">
                    <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 12px; color: var(--purple-300);"></i>
                    <input type="text" id="busqueda_libro_modal" class="form-control" style="padding-left: 35px;" placeholder="Buscar por título o ISBN...">
                    <div id="res_busqueda_modal" style="display:none; position:absolute; width:100%; background:white; z-index:1000; border:1px solid #ddd; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 0 0 10px 10px; max-height: 200px; overflow-y: auto;"></div>
                </div>
            </div>

            <div style="max-height: 250px; overflow-y: auto; border: 1px solid var(--border); border-radius: 10px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                        <tr style="border-bottom: 2px solid var(--purple-100);">
                            <th style="padding: 10px; text-align: left;">Libro</th>
                            <th style="padding: 10px; text-align: center;">Costo Unit.</th>
                            <th style="padding: 10px; text-align: center;">Cant.</th>
                            <th style="padding: 10px; text-align: right;">Subtotal</th>
                            <th style="padding: 10px;"></th>
                        </tr>
                    </thead>
                    <tbody id="lista_compra_modal">
                        </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 2px dashed var(--purple-100);">
                <div style="font-size: 1.5rem; color: var(--purple-900);">
                    <span class="bebas">TOTAL:</span> <strong id="total_compra_modal">$0.00</strong>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn-secondary" onclick="cerrarModalCompra()">Cancelar</button>
                    <button type="submit" class="btn-primary" style="padding: 10px 25px;">
                        <i class="fa-solid fa-save"></i> GUARDAR COMPRA
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let carritoCompra = [];
    const edicionesParaCompra = @json($ediciones);

    function abrirModalCompra() { 
        document.getElementById('modalCompra').classList.add('active'); 
    }
    function cerrarModalCompra() { 
        document.getElementById('modalCompra').classList.remove('active'); 
    }

    // Buscador
    document.getElementById('busqueda_libro_modal').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const res = document.getElementById('res_busqueda_modal');
        if(query.length < 2) { res.style.display = 'none'; return; }

        const filtrados = edicionesParaCompra.filter(ed => 
            ed.libro.titulo.toLowerCase().includes(query) || ed.isbn.includes(query)
        ).slice(0, 5);

        res.innerHTML = filtrados.map(ed => `
            <div onclick="addToCompra(${ed.id}, '${ed.libro.titulo}', ${ed.costo_base ?? 0})" style="padding:10px; cursor:pointer; border-bottom:1px solid #eee;">
                <strong>${ed.libro.titulo}</strong> <br> <small>ISBN: ${ed.isbn}</small>
            </div>
        `).join('');
        res.style.display = 'block';
    });

    function addToCompra(id, titulo, costo) {
        const existe = carritoCompra.find(i => i.edicion_id === id);
        if(!existe) {
            carritoCompra.push({ edicion_id: id, titulo, precio_costo: costo, cantidad: 1 });
        }
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
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding:10px;">${item.titulo}</td>
                    <td style="padding:10px; text-align:center;">
                        <input type="number" step="0.01" class="form-control" style="width:100px; text-align:right;" value="${item.precio_costo}" onchange="updateRowCompra(${index}, 'precio_costo', this.value)">
                    </td>
                    <td style="padding:10px; text-align:center;">
                        <input type="number" class="form-control" style="width:70px; text-align:center;" value="${item.cantidad}" onchange="updateRowCompra(${index}, 'cantidad', this.value)">
                    </td>
                    <td style="padding:10px; text-align:right; font-weight:bold;">$${subtotal.toFixed(2)}</td>
                    <td style="padding:10px; text-align:center;">
                        <button type="button" onclick="removeRowCompra(${index})" style="color:#d32f2f; border:none; background:none; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            `;
        }).join('');

        document.getElementById('total_compra_modal').innerText = `$${total.toFixed(2)}`;
        document.getElementById('items_compra_json').value = JSON.stringify(carritoCompra);
    }
</script>