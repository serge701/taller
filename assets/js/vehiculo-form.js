/**
 * Lógica compartida del bloque "Datos del vehículo" (Marca → Modelo dependiente,
 * Año, Color con swatch) usado en /ventas/nueva y /trabajos/nuevo.
 * Requiere que el partial app/Views/partials/vehiculo_form.php ya esté en el DOM.
 */
window.VehiculoForm = (function () {
    function $(id) { return document.getElementById(id); }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : str;
        return div.innerHTML;
    }

    function poblarModelos(catalogo, marcaId) {
        const modeloSelect = $('vehiculoModelo');
        const modeloOtro   = $('vehiculoModeloOtro');
        if (!modeloSelect) return;

        modeloOtro.classList.add('d-none');
        modeloOtro.required = false;
        modeloOtro.value = '';

        if (marcaId === 'otra') {
            modeloSelect.classList.add('d-none');
            modeloSelect.disabled = true;
            modeloSelect.required = false;
            modeloOtro.classList.remove('d-none');
            modeloOtro.required = true;
            modeloOtro.placeholder = 'Escribe el modelo';
            return;
        }

        modeloSelect.classList.remove('d-none');

        if (!marcaId) {
            modeloSelect.disabled = true;
            modeloSelect.required = false;
            modeloSelect.innerHTML = '<option value="">Selecciona una marca primero</option>';
            return;
        }

        const marca = catalogo.find(function (m) { return String(m.id) === String(marcaId); });
        modeloSelect.disabled = false;
        modeloSelect.required = true;
        let opts = '<option value="">Selecciona...</option>';
        (marca ? marca.modelos : []).forEach(function (mo) {
            opts += '<option value="' + mo.id + '">' + escapeHtml(mo.nombre) + '</option>';
        });
        opts += '<option value="otro">Otro modelo...</option>';
        modeloSelect.innerHTML = opts;
    }

    function init(catalogo) {
        catalogo = catalogo || [];

        const marcaSelect = $('vehiculoMarca');
        if (!marcaSelect) return; // esta página no tiene el formulario de vehículo

        const marcaOtra    = $('vehiculoMarcaOtra');
        const modeloSelect = $('vehiculoModelo');
        const modeloOtro   = $('vehiculoModeloOtro');
        const colorSelect  = $('vehiculoColor');
        const colorOtro    = $('vehiculoColorOtro');
        const colorSwatch  = $('vehiculoColorSwatch');

        marcaSelect.addEventListener('change', function () {
            const esOtra = this.value === 'otra';
            marcaOtra.classList.toggle('d-none', !esOtra);
            marcaOtra.required = esOtra;
            if (!esOtra) marcaOtra.value = '';
            poblarModelos(catalogo, this.value);
        });

        if (modeloSelect) {
            modeloSelect.addEventListener('change', function () {
                const esOtro = this.value === 'otro';
                modeloOtro.classList.toggle('d-none', !esOtro);
                modeloOtro.required = esOtro;
                if (!esOtro) modeloOtro.value = '';
            });
        }

        if (colorSelect) {
            colorSelect.addEventListener('change', function () {
                const esOtro = this.value === 'otro';
                colorOtro.classList.toggle('d-none', !esOtro);
                colorOtro.required = esOtro;
                if (!esOtro) colorOtro.value = '';
                const opt = this.options[this.selectedIndex];
                const hex = (!esOtro && opt) ? opt.dataset.hex : null;
                if (colorSwatch) {
                    colorSwatch.style.background = hex || '#fff';
                    colorSwatch.style.borderColor = hex || '#ced4da';
                }
            });
        }

        // Estado inicial (por si el navegador restaura valores de un formulario recargado).
        poblarModelos(catalogo, marcaSelect.value);
    }

    function getData() {
        const marcaSelect  = $('vehiculoMarca');
        const marcaOtra    = $('vehiculoMarcaOtra');
        const modeloSelect = $('vehiculoModelo');
        const modeloOtro   = $('vehiculoModeloOtro');
        const colorSelect  = $('vehiculoColor');
        const colorOtro    = $('vehiculoColorOtro');
        const anioSelect   = $('vehiculoAnio');

        if (!marcaSelect) {
            return { marca: '', modelo: '', anio: '', color: '' };
        }

        let marca = '';
        if (marcaSelect.value === 'otra') {
            marca = marcaOtra.value.trim();
        } else if (marcaSelect.value) {
            marca = marcaSelect.options[marcaSelect.selectedIndex].textContent.trim();
        }

        let modelo = '';
        if (marcaSelect.value === 'otra' || (modeloSelect && modeloSelect.value === 'otro')) {
            modelo = modeloOtro.value.trim();
        } else if (modeloSelect && modeloSelect.value) {
            modelo = modeloSelect.options[modeloSelect.selectedIndex].textContent.trim();
        }

        let color = '';
        if (colorSelect) {
            color = colorSelect.value === 'otro' ? colorOtro.value.trim() : colorSelect.value;
        }

        return {
            marca: marca,
            modelo: modelo,
            anio: anioSelect ? anioSelect.value : '',
            color: color,
        };
    }

    function isValid() {
        const d = getData();
        return d.marca !== '' && d.modelo !== '' && d.anio !== '' && d.color !== '';
    }

    function buscarOptionPorTexto(select, texto) {
        if (!select || !texto) return null;
        texto = String(texto).trim().toLowerCase();
        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            if (opt.value && opt.value !== 'otra' && opt.value !== 'otro' &&
                opt.textContent.trim().toLowerCase() === texto) {
                return opt;
            }
        }
        return null;
    }

    /**
     * Prellena el formulario con un vehículo guardado ({marca, modelo, anio, color},
     * todos texto plano). Intenta calzar cada valor contra el catálogo (Marca/Modelo);
     * si no encuentra coincidencia usa el escape hatch "Otra/Otro..." con el texto tal cual.
     */
    function setData(datos) {
        datos = datos || {};
        const marcaSelect  = $('vehiculoMarca');
        const marcaOtra    = $('vehiculoMarcaOtra');
        const modeloSelect = $('vehiculoModelo');
        const modeloOtro   = $('vehiculoModeloOtro');
        const colorSelect  = $('vehiculoColor');
        const colorOtro    = $('vehiculoColorOtro');
        const anioSelect   = $('vehiculoAnio');
        if (!marcaSelect) return;

        // Marca
        const marcaOpt = buscarOptionPorTexto(marcaSelect, datos.marca);
        marcaSelect.value = marcaOpt ? marcaOpt.value : (datos.marca ? 'otra' : '');
        marcaSelect.dispatchEvent(new Event('change'));
        if (marcaSelect.value === 'otra' && marcaOtra) {
            marcaOtra.value = datos.marca || '';
        }

        // Modelo (poblarModelos ya corrió como parte del 'change' de arriba)
        if (modeloSelect && !modeloSelect.disabled) {
            const modeloOpt = buscarOptionPorTexto(modeloSelect, datos.modelo);
            modeloSelect.value = modeloOpt ? modeloOpt.value : (datos.modelo ? 'otro' : '');
            modeloSelect.dispatchEvent(new Event('change'));
        }
        if (modeloOtro && (marcaSelect.value === 'otra' || (modeloSelect && modeloSelect.value === 'otro'))) {
            modeloOtro.value = datos.modelo || '';
        }

        // Año (el value del option es el año mismo)
        if (anioSelect) {
            anioSelect.value = datos.anio || '';
        }

        // Color
        if (colorSelect) {
            const existe = Array.prototype.some.call(colorSelect.options, function (o) {
                return o.value === datos.color;
            });
            colorSelect.value = existe ? datos.color : (datos.color ? 'otro' : '');
            colorSelect.dispatchEvent(new Event('change'));
            if (colorSelect.value === 'otro' && colorOtro) {
                colorOtro.value = datos.color || '';
            }
        }
    }

    /**
     * Muestra los vehículos guardados de un cliente como chips clicables arriba del
     * formulario y prellena automáticamente con el más reciente (el primero de la lista).
     * `lista` es un arreglo de {marca, modelo, anio, color, resumen}.
     */
    function mostrarGuardados(lista) {
        const wrap  = $('vehiculoGuardadosWrap');
        const chips = $('vehiculoGuardadosChips');
        if (!wrap || !chips) return;

        lista = lista || [];
        if (lista.length === 0) {
            limpiarGuardados();
            return;
        }

        chips.innerHTML = lista.map(function (v, idx) {
            return '<button type="button" class="btn btn-sm btn-outline-primary vehiculo-guardado-chip' + (idx === 0 ? ' active' : '') + '" data-idx="' + idx + '">' +
                       '<i class="bi bi-car-front me-1"></i>' + escapeHtml(v.resumen || '') +
                   '</button>';
        }).join('');

        chips.querySelectorAll('.vehiculo-guardado-chip').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const v = lista[parseInt(this.dataset.idx, 10)];
                setData(v);
                chips.querySelectorAll('.vehiculo-guardado-chip').forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');
            });
        });

        wrap.classList.remove('d-none');

        // Prellenado automático con el vehículo más reciente.
        setData(lista[0]);
    }

    function limpiarGuardados() {
        const wrap  = $('vehiculoGuardadosWrap');
        const chips = $('vehiculoGuardadosChips');
        if (wrap) wrap.classList.add('d-none');
        if (chips) chips.innerHTML = '';
    }

    return {
        init: init,
        getData: getData,
        isValid: isValid,
        setData: setData,
        mostrarGuardados: mostrarGuardados,
        limpiarGuardados: limpiarGuardados,
    };
})();
