<div class="row justify-content-center">
<div class="col-xl-10">

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-4">
        <h6 class="fw-semibold mb-1"><i class="bi bi-upc-scan me-2" style="color:#2563eb"></i>Buscar por VIN</h6>
        <small class="text-muted d-block mb-3">
            Consulta el número de serie (VIN) de un vehículo y obtén su ficha técnica completa,
            usando la base de datos pública de NHTSA (vPIC). El VIN sigue un estándar internacional,
            así que también funciona con vehículos vendidos o fabricados en México,
            <a href="<?= url('vin/consultas') ?>" class="text-decoration-underline">historial de VIN consultados</a>.
        </small>

        <div class="input-group input-group-lg">
            <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="vinInput" class="form-control text-uppercase" style="font-family:monospace;letter-spacing:1px;"
                   maxlength="17" placeholder="Ej: 3VW2K7AJ5FM123456" autocomplete="off">
            <button type="button" class="btn btn-primary" id="btnBuscarVin">
                <span id="btnBuscarVinTexto"><i class="bi bi-search me-1"></i>Buscar</span>
                <span id="btnBuscarVinSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
        </div>
        <div class="form-text">17 caracteres, sin las letras I, O ni Q.</div>
    </div>
</div>

<div id="vinError" class="alert alert-danger d-none"></div>

<div id="vinResultado" class="d-none">

    <div id="vinAdvertencia" class="alert alert-warning d-none"></div>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-0" id="vinTitulo">—</h4>
                    <div class="text-muted" id="vinSubtitulo"></div>
                </div>
                <div class="text-end">
                    <div class="small text-muted">VIN</div>
                    <div class="fw-semibold" style="font-family:monospace;" id="vinTexto"></div>
                </div>
            </div>
            <div class="mt-3 d-flex flex-wrap gap-2" id="vinBadges"></div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 pt-3">
            <h6 class="fw-semibold mb-0"><i class="bi bi-list-ul me-2" style="color:#2563eb"></i>Ficha técnica completa</h6>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="row g-2" id="vinDetalle"></div>
        </div>
    </div>

</div>

</div>
</div>

<script>window.URL_VIN_BUSCAR = <?= json_encode(url('vin/buscar')) ?>;</script>

<?php $pageScript = <<<'HTML'
<script>
(function () {
    const input       = document.getElementById('vinInput');
    const btn         = document.getElementById('btnBuscarVin');
    const btnTexto    = document.getElementById('btnBuscarVinTexto');
    const btnSpinner  = document.getElementById('btnBuscarVinSpinner');
    const errorBox    = document.getElementById('vinError');
    const resultado   = document.getElementById('vinResultado');
    const advertencia = document.getElementById('vinAdvertencia');
    const titulo      = document.getElementById('vinTitulo');
    const subtitulo    = document.getElementById('vinSubtitulo');
    const vinTexto     = document.getElementById('vinTexto');
    const badgesBox    = document.getElementById('vinBadges');
    const detalleBox   = document.getElementById('vinDetalle');

    const CAMPOS_DESTACADOS = [
        'Make', 'Model', 'ModelYear', 'Trim', 'Series', 'Manufacturer',
        'VehicleType', 'BodyClass', 'DriveType', 'EngineCylinders',
        'DisplacementL', 'FuelTypePrimary', 'Doors', 'PlantCountry', 'PlantState', 'PlantCity',
    ];
    const CAMPOS_ERROR = ['ErrorCode', 'ErrorText', 'AdditionalErrorText'];

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : str;
        return div.innerHTML;
    }

    function formatearEtiqueta(key) {
        return key
            .replace(/([a-z0-9])([A-Z])/g, '$1 $2')
            .replace(/([A-Z]+)([A-Z][a-z])/g, '$1 $2')
            .trim();
    }

    function setLoading(cargando) {
        btn.disabled = cargando;
        input.disabled = cargando;
        btnTexto.classList.toggle('d-none', cargando);
        btnSpinner.classList.toggle('d-none', !cargando);
    }

    function mostrarError(msg) {
        resultado.classList.add('d-none');
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }

    function renderResultado(vin, data) {
        errorBox.classList.add('d-none');

        const partesTitulo = [data.ModelYear, data.Make, data.Model].filter(Boolean);
        titulo.textContent = partesTitulo.length ? partesTitulo.join(' ') : 'Vehículo encontrado';

        const partesSub = [data.Trim, data.Series].filter(Boolean);
        subtitulo.textContent = partesSub.join(' · ');

        vinTexto.textContent = vin;

        // Advertencia si el check-digit u otra validación de la API no calzó del todo.
        const codigo = data.ErrorCode;
        if (codigo && codigo !== '0') {
            const partes = [data.ErrorText, data.AdditionalErrorText].filter(Boolean);
            advertencia.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' +
                (partes.length ? escapeHtml(partes.join(' — ')) : 'La API reportó una advertencia al decodificar este VIN.');
            advertencia.classList.remove('d-none');
        } else {
            advertencia.classList.add('d-none');
        }

        // Badges con lo más relevante de un vistazo.
        const badges = [];
        if (data.BodyClass) badges.push(data.BodyClass);
        if (data.VehicleType) badges.push(data.VehicleType);
        if (data.DriveType) badges.push(data.DriveType);
        if (data.FuelTypePrimary) badges.push(data.FuelTypePrimary);
        if (data.EngineCylinders || data.DisplacementL) {
            let motor = '';
            if (data.EngineCylinders) motor += data.EngineCylinders + ' cil';
            if (data.DisplacementL) motor += (motor ? ' · ' : '') + data.DisplacementL + 'L';
            badges.push(motor);
        }
        if (data.Doors) badges.push(data.Doors + ' puertas');
        badgesBox.innerHTML = badges.map(b =>
            '<span class="badge text-bg-light border">' + escapeHtml(String(b)) + '</span>'
        ).join('');

        // Ficha completa: todos los campos no vacíos que regresó la API, excepto
        // los de error (ya mostrados arriba como advertencia).
        let filas = '';
        Object.keys(data).forEach(function (key) {
            if (CAMPOS_ERROR.indexOf(key) !== -1) return;
            const destacado = CAMPOS_DESTACADOS.indexOf(key) !== -1;
            filas += '<div class="col-md-6 col-lg-4">' +
                        '<div class="border rounded-3 p-2 h-100' + (destacado ? ' bg-light' : '') + '">' +
                            '<div class="small text-muted">' + escapeHtml(formatearEtiqueta(key)) + '</div>' +
                            '<div class="fw-medium">' + escapeHtml(String(data[key])) + '</div>' +
                        '</div>' +
                     '</div>';
        });
        detalleBox.innerHTML = filas;

        resultado.classList.remove('d-none');
    }

    function buscar() {
        const vin = input.value.trim().toUpperCase();
        if (!vin) {
            mostrarError('Ingresa un VIN.');
            return;
        }

        setLoading(true);
        errorBox.classList.add('d-none');

        fetch(window.URL_VIN_BUSCAR + '?vin=' + encodeURIComponent(vin))
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (!json.ok) {
                    mostrarError(json.error || 'No se pudo obtener información de ese VIN.');
                    return;
                }
                renderResultado(json.vin, json.data);
            })
            .catch(function () {
                mostrarError('No se pudo contactar al servicio. Revisa tu conexión e intenta de nuevo.');
            })
            .finally(function () {
                setLoading(false);
            });
    }

    btn.addEventListener('click', buscar);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscar();
        }
    });
    input.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });
})();
</script>
HTML;
?>
