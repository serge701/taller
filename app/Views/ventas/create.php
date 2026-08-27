<style>
    /* Resultados de búsqueda (cliente/servicio): fondo e hijos con color propio
       para que se noten como panel flotante y no se pierdan con la tarjeta blanca. */
    .resultados-buscar {
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(15, 23, 42, .12);
    }
    .resultados-buscar .list-group-item {
        background: #eff6ff;
        border-color: #dbeafe;
        color: #1e3a8a;
    }
    .resultados-buscar .list-group-item .text-muted {
        color: #3b82f6 !important;
    }
    .resultados-buscar .list-group-item:hover,
    .resultados-buscar .list-group-item:focus {
        background: #dbeafe;
    }
</style>

<div class="row g-3">

    <!-- Columna izquierda: cliente + vehículo + servicios disponibles -->
    <div class="col-lg-7">

        <!-- Cliente -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-person me-2" style="color:#2563eb"></i>1. Selecciona el cliente</h6>

                <div id="clienteBuscador">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="clienteSearch" class="form-control"
                               placeholder="Busca por nombre, apellido o teléfono...">
                    </div>
                    <div id="clienteResultados" class="list-group resultados-buscar mt-2" style="display:none;position:relative;z-index:10;"></div>
                </div>

                <div id="clienteSeleccionado" class="d-none">
                    <div class="d-flex align-items-center justify-content-between border rounded-3 p-3 bg-light">
                        <div class="d-flex align-items-center gap-3">
                            <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                  style="width:42px;height:42px;background:#2563eb;color:#fff;font-weight:700;">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <div>
                                <div class="fw-semibold" id="clienteNombreTxt"></div>
                                <div class="small text-muted" id="clienteTelTxt"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCambiarCliente">
                            <i class="bi bi-arrow-repeat me-1"></i>Cambiar
                        </button>
                    </div>
                </div>

                <div class="mt-2">
                    <a href="<?= url('clientes/nuevo') ?>" class="small text-decoration-none" target="_blank">
                        <i class="bi bi-plus-circle me-1"></i>¿Cliente nuevo? Regístralo aquí
                    </a>
                </div>
            </div>
        </div>

        <!-- Vehículo -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-car-front me-2" style="color:#2563eb"></i>2. Datos del vehículo</h6>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-medium">Marca <span class="text-danger">*</span></label>
                        <input type="text" id="vehiculoMarca" class="form-control" required
                               placeholder="Ej: Nissan, Chevrolet...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Color <span class="text-danger">*</span></label>
                        <input type="text" id="vehiculoColor" class="form-control" required
                               placeholder="Ej: Blanco">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Placas <span class="text-muted small">(opcional)</span></label>
                        <input type="text" id="vehiculoPlacas" class="form-control text-uppercase"
                               placeholder="ABC-1234">
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-wrench-adjustable me-2" style="color:#2563eb"></i>3. Agrega servicios</h6>

                <?php if (empty($servicios)): ?>
                <div class="text-center py-4 text-muted">
                    No hay servicios activos. <a href="<?= url('servicios/nuevo') ?>">Crea uno primero</a>.
                </div>
                <?php else: ?>

                <div id="servicioBuscador" class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="servicioSearch" class="form-control"
                               placeholder="Busca un servicio por nombre...">
                    </div>
                    <div id="servicioResultados" class="list-group resultados-buscar mt-2" style="display:none;position:relative;z-index:10;"></div>
                </div>

                <div class="text-muted small mb-2">O elige uno de acceso rápido:</div>
                <div class="row g-2" id="listaServicios">
                    <?php foreach ($servicios as $s): ?>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-secondary w-100 d-flex justify-content-between align-items-center py-2 btn-agregar-servicio"
                                data-id="<?= (int) $s['id'] ?>"
                                data-nombre="<?= e($s['nombre']) ?>"
                                data-precio="<?= e((string) $s['precio']) ?>">
                            <span><i class="bi bi-plus-circle me-2"></i><?= e($s['nombre']) ?></span>
                            <span class="fw-semibold"><?= formato_moneda($s['precio']) ?></span>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Columna derecha: carrito -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0" style="position:sticky;top:1rem;">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-cart me-2" style="color:#2563eb"></i>4. Resumen de la venta</h6>

                <div id="carritoVacio" class="text-center py-4 text-muted">
                    <i class="bi bi-cart-x fs-1 d-block mb-2 opacity-25"></i>
                    Aún no agregas servicios.
                </div>

                <div id="carritoTabla" class="d-none">
                    <div id="carritoBody" class="mb-2"></div>
                    <div class="form-text mb-3">
                        <i class="bi bi-info-circle me-1"></i>El precio de cada servicio es editable: ajústalo si varía según el vehículo.
                    </div>
                    <div class="border-top pt-3 mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Subtotal</span>
                            <span id="carritoSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1 d-none" id="filaIva">
                            <span>IVA (16%)</span>
                            <span id="carritoIva">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <span class="fw-semibold fs-5">Total</span>
                            <span class="fw-bold fs-4" id="carritoTotal" style="color:#2563eb;">$0.00</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="<?= url('ventas') ?>" id="formVenta">
                    <?= csrf_field() ?>
                    <input type="hidden" name="cliente_id" id="inputClienteId">
                    <input type="hidden" name="items" id="inputItems">
                    <input type="hidden" name="vehiculo_marca" id="inputVehiculoMarca">
                    <input type="hidden" name="vehiculo_color" id="inputVehiculoColor">
                    <input type="hidden" name="vehiculo_placas" id="inputVehiculoPlacas">

                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" role="switch" id="chkFactura" name="factura" value="1">
                        <label class="form-check-label" for="chkFactura">Esta venta requiere factura (+16% IVA)</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Método de pago <span class="text-danger">*</span></label>
                        <select name="metodo_pago" class="form-select" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta de Débito">Tarjeta de Débito</option>
                            <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Comentarios</label>
                        <textarea name="comentarios" class="form-control" rows="2" placeholder="Notas sobre el servicio realizado..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2" id="btnGuardarVenta" disabled>
                        <i class="bi bi-check-circle me-1"></i>Registrar venta
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
window.URL_CLIENTES_BUSCAR  = <?= json_encode(url('clientes/buscar')) ?>;
window.URL_SERVICIOS_BUSCAR = <?= json_encode(url('servicios/buscar')) ?>;
</script>

<?php $pageScript = <<<'HTML'
<script>
(function () {
    let cliente = null;
    let cart = [];

    const searchInput   = document.getElementById('clienteSearch');
    const resultsBox     = document.getElementById('clienteResultados');
    const buscadorBox    = document.getElementById('clienteBuscador');
    const seleccionadoBox = document.getElementById('clienteSeleccionado');
    const nombreTxt      = document.getElementById('clienteNombreTxt');
    const telTxt         = document.getElementById('clienteTelTxt');
    const inputClienteId = document.getElementById('inputClienteId');
    const btnCambiar      = document.getElementById('btnCambiarCliente');

    let debounceTimer = null;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) { resultsBox.style.display = 'none'; resultsBox.innerHTML = ''; return; }
        debounceTimer = setTimeout(() => buscarClientes(q), 250);
    });

    function buscarClientes(q) {
        fetch(window.URL_CLIENTES_BUSCAR + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                resultsBox.innerHTML = '';
                if (!data.length) {
                    resultsBox.innerHTML = '<div class="list-group-item text-muted small">Sin resultados</div>';
                } else {
                    data.forEach(c => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = '<div class="fw-semibold">' + escapeHtml(c.nombre + ' ' + c.apellido_paterno) + '</div>' +
                                         '<div class="small text-muted">' + escapeHtml(c.telefono || '') + '</div>';
                        item.addEventListener('click', () => seleccionarCliente(c));
                        resultsBox.appendChild(item);
                    });
                }
                resultsBox.style.display = 'block';
            });
    }

    function seleccionarCliente(c) {
        cliente = c;
        inputClienteId.value = c.id;
        nombreTxt.textContent = c.nombre + ' ' + c.apellido_paterno;
        telTxt.textContent = c.telefono || '';
        buscadorBox.classList.add('d-none');
        seleccionadoBox.classList.remove('d-none');
        resultsBox.style.display = 'none';
        searchInput.value = '';
        actualizarBotonGuardar();
    }

    btnCambiar.addEventListener('click', function () {
        cliente = null;
        inputClienteId.value = '';
        buscadorBox.classList.remove('d-none');
        seleccionadoBox.classList.add('d-none');
        actualizarBotonGuardar();
    });

    document.addEventListener('click', function (e) {
        if (!buscadorBox.contains(e.target)) resultsBox.style.display = 'none';
        if (servicioBuscadorBox && !servicioBuscadorBox.contains(e.target)) servicioResultsBox.style.display = 'none';
    });

    // ---- Buscador de servicios (AJAX, igual que el de cliente) ----
    const servicioSearchInput = document.getElementById('servicioSearch');
    const servicioResultsBox  = document.getElementById('servicioResultados');
    const servicioBuscadorBox = document.getElementById('servicioBuscador');

    let servicioDebounce = null;
    if (servicioSearchInput) {
        servicioSearchInput.addEventListener('input', function () {
            clearTimeout(servicioDebounce);
            const q = this.value.trim();
            if (q.length < 1) { servicioResultsBox.style.display = 'none'; servicioResultsBox.innerHTML = ''; return; }
            servicioDebounce = setTimeout(() => buscarServicios(q), 250);
        });
    }

    function buscarServicios(q) {
        fetch(window.URL_SERVICIOS_BUSCAR + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                servicioResultsBox.innerHTML = '';
                if (!data.length) {
                    servicioResultsBox.innerHTML = '<div class="list-group-item text-muted small">Sin resultados</div>';
                } else {
                    data.forEach(s => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        item.innerHTML = '<span>' + escapeHtml(s.nombre) + '</span>' +
                                         '<span class="fw-semibold text-muted">$' + parseFloat(s.precio).toFixed(2) + '</span>';
                        item.addEventListener('click', function () {
                            agregarServicio(parseInt(s.id, 10), s.nombre, parseFloat(s.precio));
                            servicioResultsBox.style.display = 'none';
                            servicioSearchInput.value = '';
                        });
                        servicioResultsBox.appendChild(item);
                    });
                }
                servicioResultsBox.style.display = 'block';
            });
    }

    // ---- Accesos rápidos de servicios ----
    document.querySelectorAll('.btn-agregar-servicio').forEach(btn => {
        btn.addEventListener('click', function () {
            agregarServicio(parseInt(this.dataset.id, 10), this.dataset.nombre, parseFloat(this.dataset.precio));
        });
    });

    // ---- Carrito ----
    function agregarServicio(id, nombre, precio) {
        const existente = cart.find(i => i.id === id);
        if (existente) {
            existente.cantidad += 1;
        } else {
            cart.push({ id: id, nombre: nombre, precio: precio, cantidad: 1 });
        }
        renderCarrito();
    }

    function cambiarCantidad(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;
        item.cantidad += delta;
        if (item.cantidad <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        renderCarrito();
    }

    function quitarItem(id) {
        cart = cart.filter(i => i.id !== id);
        renderCarrito();
    }

    const chkFactura = document.getElementById('chkFactura');
    chkFactura.addEventListener('change', actualizarTotales);

    function renderCarrito() {
        const body = document.getElementById('carritoBody');
        const vacio = document.getElementById('carritoVacio');
        const tabla = document.getElementById('carritoTabla');

        if (cart.length === 0) {
            vacio.classList.remove('d-none');
            tabla.classList.add('d-none');
            actualizarBotonGuardar();
            return;
        }

        vacio.classList.add('d-none');
        tabla.classList.remove('d-none');

        body.innerHTML = '';
        cart.forEach(item => {
            const sub = item.precio * item.cantidad;

            const row = document.createElement('div');
            row.className = 'border rounded-3 p-2 mb-2';
            row.innerHTML =
                '<div class="d-flex justify-content-between align-items-start mb-2">' +
                    '<div class="fw-medium small">' + escapeHtml(item.nombre) + '</div>' +
                    '<button type="button" class="btn btn-sm btn-link text-danger p-0 btn-quitar" data-id="' + item.id + '">' +
                        '<i class="bi bi-trash"></i>' +
                    '</button>' +
                '</div>' +
                '<div class="d-flex align-items-center gap-2 flex-wrap">' +
                    '<div class="input-group input-group-sm" style="width:140px;">' +
                        '<span class="input-group-text px-2">$</span>' +
                        '<input type="number" class="form-control precio-input" data-id="' + item.id + '" ' +
                            'value="' + item.precio.toFixed(2) + '" step="0.01" min="0">' +
                    '</div>' +
                    '<div class="input-group input-group-sm" style="width:92px;">' +
                        '<button type="button" class="btn btn-outline-secondary btn-menos" data-id="' + item.id + '">-</button>' +
                        '<span class="form-control text-center px-1">' + item.cantidad + '</span>' +
                        '<button type="button" class="btn btn-outline-secondary btn-mas" data-id="' + item.id + '">+</button>' +
                    '</div>' +
                    '<div class="ms-auto fw-semibold small" data-subtotal-id="' + item.id + '">$' + sub.toFixed(2) + '</div>' +
                '</div>';
            body.appendChild(row);
        });

        body.querySelectorAll('.btn-menos').forEach(b => b.addEventListener('click', () => cambiarCantidad(parseInt(b.dataset.id, 10), -1)));
        body.querySelectorAll('.btn-mas').forEach(b => b.addEventListener('click', () => cambiarCantidad(parseInt(b.dataset.id, 10), 1)));
        body.querySelectorAll('.btn-quitar').forEach(b => b.addEventListener('click', () => quitarItem(parseInt(b.dataset.id, 10))));

        actualizarTotales();
    }

    // Precio editable: al escribir, solo se actualizan los totales (sin re-renderizar
    // el carrito completo) para no perder el foco del campo mientras se edita.
    document.getElementById('carritoBody').addEventListener('input', function (e) {
        if (!e.target.classList.contains('precio-input')) return;
        const id = parseInt(e.target.dataset.id, 10);
        const item = cart.find(i => i.id === id);
        if (!item) return;

        let val = parseFloat(e.target.value);
        if (isNaN(val) || val < 0) val = 0;
        item.precio = val;

        const subtotalEl = document.querySelector('[data-subtotal-id="' + id + '"]');
        if (subtotalEl) subtotalEl.textContent = '$' + (item.precio * item.cantidad).toFixed(2);

        actualizarTotales();
    });

    function actualizarTotales() {
        const subtotal = cart.reduce((acc, i) => acc + (i.precio * i.cantidad), 0);
        const factura = chkFactura.checked;
        const iva = factura ? subtotal * 0.16 : 0;
        const total = subtotal + iva;

        document.getElementById('carritoSubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('carritoIva').textContent = '$' + iva.toFixed(2);
        document.getElementById('carritoTotal').textContent = '$' + total.toFixed(2);
        document.getElementById('filaIva').classList.toggle('d-none', !factura);

        actualizarBotonGuardar();
    }

    function actualizarBotonGuardar() {
        const vehiculoOk = document.getElementById('vehiculoMarca').value.trim() !== ''
            && document.getElementById('vehiculoColor').value.trim() !== '';
        const preciosOk = cart.length > 0 && cart.every(i => i.precio > 0);
        document.getElementById('btnGuardarVenta').disabled = !(cliente && cart.length > 0 && vehiculoOk && preciosOk);
    }

    document.getElementById('vehiculoMarca').addEventListener('input', actualizarBotonGuardar);
    document.getElementById('vehiculoColor').addEventListener('input', actualizarBotonGuardar);

    document.getElementById('formVenta').addEventListener('submit', function (e) {
        const marca = document.getElementById('vehiculoMarca').value.trim();
        const color = document.getElementById('vehiculoColor').value.trim();
        if (marca === '' || color === '') {
            e.preventDefault();
            alert('Marca y color del vehículo son obligatorios.');
            return;
        }
        if (cart.some(i => !(i.precio > 0))) {
            e.preventDefault();
            alert('Todos los servicios deben tener un precio mayor a $0.');
            return;
        }
        document.getElementById('inputVehiculoMarca').value = marca;
        document.getElementById('inputVehiculoColor').value = color;
        document.getElementById('inputVehiculoPlacas').value = document.getElementById('vehiculoPlacas').value.trim();
        document.getElementById('inputItems').value = JSON.stringify(
            cart.map(i => ({ servicio_id: i.id, cantidad: i.cantidad, precio: i.precio }))
        );
    });

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : str;
        return div.innerHTML;
    }
})();
</script>
HTML;
?>
