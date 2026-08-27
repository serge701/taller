<?php
// Precio de catálogo indexado por servicio_id, para prellenar el carrito con lo planeado.
$preciosCatalogo = [];
foreach ($servicios as $s) {
    $preciosCatalogo[(int) $s['id']] = (float) $s['precio'];
}

$carritoInicial = [];
foreach ($serviciosPlaneados as $sp) {
    $sid = $sp['servicio_id'] !== null ? (int) $sp['servicio_id'] : null;
    $carritoInicial[] = [
        'servicioId' => $sid,
        'nombre'     => $sp['nombre_servicio'],
        'precio'     => ($sid !== null && isset($preciosCatalogo[$sid])) ? $preciosCatalogo[$sid] : 0,
        'cantidad'   => 1,
    ];
}
?>
<style>
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
    .resultados-buscar .list-group-item:hover,
    .resultados-buscar .list-group-item:focus {
        background: #dbeafe;
    }
</style>

<div class="alert alert-info d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-flag fs-5"></i>
    <div>Estás cerrando el <strong>Trabajo #<?= (int) $trabajo['id'] ?></strong>. Revisa/ajusta los servicios y precios, agrega lo que haga falta y registra la venta final.</div>
</div>

<div class="row g-3">

    <div class="col-lg-7">

        <!-- Cliente y vehículo (fijos, definidos al abrir el trabajo) -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-person me-2" style="color:#2563eb"></i>Cliente y vehículo</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Cliente</div>
                        <div class="fw-semibold"><?= e($trabajo['cliente_nombre'] . ' ' . $trabajo['cliente_apellido']) ?></div>
                        <?php if (!empty($trabajo['cliente_telefono'])): ?>
                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= e($trabajo['cliente_telefono']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Vehículo</div>
                        <div class="fw-semibold"><?= e($trabajo['vehiculo_marca']) ?> · <?= e($trabajo['vehiculo_color']) ?></div>
                        <?php if (!empty($trabajo['vehiculo_placas'])): ?>
                        <div class="small text-muted"><i class="bi bi-credit-card-2-front me-1"></i>Placas: <?= e($trabajo['vehiculo_placas']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-wrench-adjustable me-2" style="color:#2563eb"></i>Servicios</h6>
                <div class="form-text mb-3">
                    <i class="bi bi-info-circle me-1"></i>Ya se cargaron los servicios planeados. Agrega los que hagan falta o quita los que no se hicieron.
                </div>

                <?php if (!empty($servicios)): ?>
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
                <h6 class="fw-semibold mb-3"><i class="bi bi-cart me-2" style="color:#2563eb"></i>Resumen de la venta</h6>

                <div id="carritoVacio" class="text-center py-4 text-muted d-none">
                    <i class="bi bi-cart-x fs-1 d-block mb-2 opacity-25"></i>
                    Agrega al menos un servicio.
                </div>

                <div id="carritoTabla">
                    <div id="carritoBody" class="mb-2"></div>
                    <div class="form-text mb-3">
                        <i class="bi bi-info-circle me-1"></i>El precio de cada servicio es editable.
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
                        <?php if ((float) $trabajo['anticipo'] > 0): ?>
                        <div class="d-flex justify-content-between small text-success mt-1">
                            <span>Anticipo ya pagado</span>
                            <span>&minus; <?= formato_moneda($trabajo['anticipo']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-1 border-top mt-1">
                            <span class="fw-semibold">Saldo a cobrar</span>
                            <span class="fw-bold" id="carritoSaldo" style="color:#16a34a;">$0.00</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <form method="POST" action="<?= url('trabajos/' . $trabajo['id'] . '/finalizar') ?>" id="formVenta">
                    <?= csrf_field() ?>
                    <input type="hidden" name="items" id="inputItems">

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
                        <textarea name="comentarios" class="form-control" rows="2" placeholder="Notas sobre el trabajo entregado..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2" id="btnGuardarVenta" disabled>
                        <i class="bi bi-check-circle me-1"></i>Finalizar y generar venta
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
window.URL_SERVICIOS_BUSCAR = <?= json_encode(url('servicios/buscar')) ?>;
window.CARRITO_INICIAL      = <?= json_encode($carritoInicial, JSON_UNESCAPED_UNICODE) ?>;
window.ANTICIPO             = <?= json_encode((float) $trabajo['anticipo']) ?>;
</script>

<?php $pageScript = <<<'HTML'
<script>
(function () {
    let nextUid = 1;
    let cart = (window.CARRITO_INICIAL || []).map(function (i) {
        return { uid: nextUid++, servicioId: i.servicioId, nombre: i.nombre, precio: i.precio, cantidad: i.cantidad };
    });

    // ---- Buscador de servicios ----
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

    document.addEventListener('click', function (e) {
        if (servicioBuscadorBox && !servicioBuscadorBox.contains(e.target)) servicioResultsBox.style.display = 'none';
    });

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

    document.querySelectorAll('.btn-agregar-servicio').forEach(btn => {
        btn.addEventListener('click', function () {
            agregarServicio(parseInt(this.dataset.id, 10), this.dataset.nombre, parseFloat(this.dataset.precio));
        });
    });

    function agregarServicio(servicioId, nombre, precio) {
        const existente = cart.find(i => i.servicioId === servicioId);
        if (existente) {
            existente.cantidad += 1;
        } else {
            cart.push({ uid: nextUid++, servicioId: servicioId, nombre: nombre, precio: precio, cantidad: 1 });
        }
        renderCarrito();
    }

    function cambiarCantidad(uid, delta) {
        const item = cart.find(i => i.uid === uid);
        if (!item) return;
        item.cantidad += delta;
        if (item.cantidad <= 0) {
            cart = cart.filter(i => i.uid !== uid);
        }
        renderCarrito();
    }

    function quitarItem(uid) {
        cart = cart.filter(i => i.uid !== uid);
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
                    '<button type="button" class="btn btn-sm btn-link text-danger p-0 btn-quitar" data-uid="' + item.uid + '">' +
                        '<i class="bi bi-trash"></i>' +
                    '</button>' +
                '</div>' +
                '<div class="d-flex align-items-center gap-2 flex-wrap">' +
                    '<div class="input-group input-group-sm" style="width:140px;">' +
                        '<span class="input-group-text px-2">$</span>' +
                        '<input type="number" class="form-control precio-input" data-uid="' + item.uid + '" ' +
                            'value="' + item.precio.toFixed(2) + '" step="0.01" min="0">' +
                    '</div>' +
                    '<div class="input-group input-group-sm" style="width:92px;">' +
                        '<button type="button" class="btn btn-outline-secondary btn-menos" data-uid="' + item.uid + '">-</button>' +
                        '<span class="form-control text-center px-1">' + item.cantidad + '</span>' +
                        '<button type="button" class="btn btn-outline-secondary btn-mas" data-uid="' + item.uid + '">+</button>' +
                    '</div>' +
                    '<div class="ms-auto fw-semibold small" data-subtotal-uid="' + item.uid + '">$' + sub.toFixed(2) + '</div>' +
                '</div>';
            body.appendChild(row);
        });

        body.querySelectorAll('.btn-menos').forEach(b => b.addEventListener('click', () => cambiarCantidad(parseInt(b.dataset.uid, 10), -1)));
        body.querySelectorAll('.btn-mas').forEach(b => b.addEventListener('click', () => cambiarCantidad(parseInt(b.dataset.uid, 10), 1)));
        body.querySelectorAll('.btn-quitar').forEach(b => b.addEventListener('click', () => quitarItem(parseInt(b.dataset.uid, 10))));

        actualizarTotales();
    }

    document.getElementById('carritoBody').addEventListener('input', function (e) {
        if (!e.target.classList.contains('precio-input')) return;
        const uid = parseInt(e.target.dataset.uid, 10);
        const item = cart.find(i => i.uid === uid);
        if (!item) return;

        let val = parseFloat(e.target.value);
        if (isNaN(val) || val < 0) val = 0;
        item.precio = val;

        const subtotalEl = document.querySelector('[data-subtotal-uid="' + uid + '"]');
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

        const saldoEl = document.getElementById('carritoSaldo');
        if (saldoEl) {
            const saldo = Math.max(0, total - (window.ANTICIPO || 0));
            saldoEl.textContent = '$' + saldo.toFixed(2);
        }

        actualizarBotonGuardar();
    }

    function actualizarBotonGuardar() {
        const preciosOk = cart.length > 0 && cart.every(i => i.precio > 0);
        document.getElementById('btnGuardarVenta').disabled = !preciosOk;
    }

    document.getElementById('formVenta').addEventListener('submit', function (e) {
        if (cart.length === 0 || cart.some(i => !(i.precio > 0))) {
            e.preventDefault();
            alert('Todos los servicios deben tener un precio mayor a $0.');
            return;
        }
        document.getElementById('inputItems').value = JSON.stringify(
            cart.map(i => ({ servicio_id: i.servicioId, nombre: i.nombre, cantidad: i.cantidad, precio: i.precio }))
        );
    });

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : str;
        return div.innerHTML;
    }

    renderCarrito();
})();
</script>
HTML;
?>
