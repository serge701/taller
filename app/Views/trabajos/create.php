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
                <h6 class="fw-semibold mb-3"><i class="bi bi-wrench-adjustable me-2" style="color:#2563eb"></i>3. Servicios planeados</h6>
                <div class="form-text mb-3">
                    <i class="bi bi-info-circle me-1"></i>Solo indica qué se va a hacer; el precio y el resumen de la venta se definen hasta que el trabajo se entregue.
                </div>

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
                        <button type="button" class="btn btn-outline-secondary w-100 text-start py-2 btn-agregar-servicio"
                                data-id="<?= (int) $s['id'] ?>"
                                data-nombre="<?= e($s['nombre']) ?>">
                            <i class="bi bi-plus-circle me-2"></i><?= e($s['nombre']) ?>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Columna derecha: servicios elegidos + anticipo -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0" style="position:sticky;top:1rem;">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-clipboard-check me-2" style="color:#2563eb"></i>4. Anticipo y detalles</h6>

                <div id="listaVacia" class="text-center py-4 text-muted">
                    <i class="bi bi-list-check fs-1 d-block mb-2 opacity-25"></i>
                    Aún no agregas servicios planeados.
                </div>

                <div id="listaElegidos" class="mb-3"></div>

                <form method="POST" action="<?= url('trabajos') ?>" id="formTrabajo">
                    <?= csrf_field() ?>
                    <input type="hidden" name="cliente_id" id="inputClienteId">
                    <input type="hidden" name="items" id="inputItems">
                    <input type="hidden" name="vehiculo_marca" id="inputVehiculoMarca">
                    <input type="hidden" name="vehiculo_color" id="inputVehiculoColor">
                    <input type="hidden" name="vehiculo_placas" id="inputVehiculoPlacas">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Anticipo <span class="text-muted small">(opcional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="anticipo" id="inputAnticipo" class="form-control" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2" id="btnCrearTrabajo" disabled>
                        <i class="bi bi-check-circle me-1"></i>Crear trabajo
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
    let servicios = [];

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
        actualizarBoton();
    }

    btnCambiar.addEventListener('click', function () {
        cliente = null;
        inputClienteId.value = '';
        buscadorBox.classList.remove('d-none');
        seleccionadoBox.classList.add('d-none');
        actualizarBoton();
    });

    document.addEventListener('click', function (e) {
        if (!buscadorBox.contains(e.target)) resultsBox.style.display = 'none';
        if (servicioBuscadorBox && !servicioBuscadorBox.contains(e.target)) servicioResultsBox.style.display = 'none';
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
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = s.nombre;
                        item.addEventListener('click', function () {
                            agregarServicio(parseInt(s.id, 10), s.nombre);
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
            agregarServicio(parseInt(this.dataset.id, 10), this.dataset.nombre);
        });
    });

    function agregarServicio(id, nombre) {
        if (servicios.some(s => s.id === id)) return;
        servicios.push({ id: id, nombre: nombre });
        renderLista();
    }

    function quitarServicio(id) {
        servicios = servicios.filter(s => s.id !== id);
        renderLista();
    }

    function renderLista() {
        const vacio = document.getElementById('listaVacia');
        const cont  = document.getElementById('listaElegidos');

        if (servicios.length === 0) {
            vacio.classList.remove('d-none');
            cont.innerHTML = '';
            actualizarBoton();
            return;
        }

        vacio.classList.add('d-none');
        cont.innerHTML = '';
        servicios.forEach(s => {
            const row = document.createElement('div');
            row.className = 'd-flex justify-content-between align-items-center border rounded-3 p-2 mb-2';
            row.innerHTML =
                '<span class="small fw-medium"><i class="bi bi-wrench-adjustable me-2 text-muted"></i>' + escapeHtml(s.nombre) + '</span>' +
                '<button type="button" class="btn btn-sm btn-link text-danger p-0 btn-quitar" data-id="' + s.id + '"><i class="bi bi-trash"></i></button>';
            cont.appendChild(row);
        });

        cont.querySelectorAll('.btn-quitar').forEach(b => b.addEventListener('click', () => quitarServicio(parseInt(b.dataset.id, 10))));

        actualizarBoton();
    }

    function actualizarBoton() {
        const vehiculoOk = document.getElementById('vehiculoMarca').value.trim() !== ''
            && document.getElementById('vehiculoColor').value.trim() !== '';
        document.getElementById('btnCrearTrabajo').disabled = !(cliente && servicios.length > 0 && vehiculoOk);
    }

    document.getElementById('vehiculoMarca').addEventListener('input', actualizarBoton);
    document.getElementById('vehiculoColor').addEventListener('input', actualizarBoton);

    document.getElementById('formTrabajo').addEventListener('submit', function (e) {
        const marca = document.getElementById('vehiculoMarca').value.trim();
        const color = document.getElementById('vehiculoColor').value.trim();
        if (marca === '' || color === '') {
            e.preventDefault();
            alert('Marca y color del vehículo son obligatorios.');
            return;
        }
        document.getElementById('inputVehiculoMarca').value = marca;
        document.getElementById('inputVehiculoColor').value = color;
        document.getElementById('inputVehiculoPlacas').value = document.getElementById('vehiculoPlacas').value.trim();
        document.getElementById('inputItems').value = JSON.stringify(
            servicios.map(s => ({ servicio_id: s.id, nombre: s.nombre }))
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
