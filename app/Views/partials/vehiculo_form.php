<?php
// Partial compartido por ventas/create.php y trabajos/create.php.
// Espera en scope: $vehiculoMarcas (catálogo marca→modelos), $vehiculoColores, $vehiculoAnios.
?>
<div id="vehiculoFormWrap" class="row g-3">
    <div id="vehiculoGuardadosWrap" class="col-12 d-none">
        <label class="form-label fw-medium small text-muted mb-1">
            <i class="bi bi-clock-history me-1"></i>Vehículos guardados de este cliente
        </label>
        <div id="vehiculoGuardadosChips" class="d-flex flex-wrap gap-2"></div>
    </div>

    <div class="col-md-3 col-6">
        <label class="form-label fw-medium">Marca <span class="text-danger">*</span></label>
        <select id="vehiculoMarca" class="form-select">
            <option value="">Selecciona...</option>
            <?php foreach ($vehiculoMarcas as $m): ?>
            <option value="<?= (int) $m['id'] ?>"><?= e($m['nombre']) ?></option>
            <?php endforeach; ?>
            <option value="otra">Otra marca...</option>
        </select>
        <input type="text" id="vehiculoMarcaOtra" class="form-control mt-2 d-none" placeholder="Escribe la marca">
    </div>

    <div class="col-md-3 col-6">
        <label class="form-label fw-medium">Modelo <span class="text-danger">*</span></label>
        <select id="vehiculoModelo" class="form-select" disabled>
            <option value="">Selecciona una marca primero</option>
        </select>
        <input type="text" id="vehiculoModeloOtro" class="form-control mt-2 d-none" placeholder="Escribe el modelo">
    </div>

    <div class="col-md-3 col-6">
        <label class="form-label fw-medium">Año <span class="text-danger">*</span></label>
        <select id="vehiculoAnio" class="form-select">
            <option value="">Selecciona...</option>
            <?php foreach ($vehiculoAnios as $a): ?>
            <option value="<?= (int) $a ?>"><?= (int) $a ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3 col-6">
        <label class="form-label fw-medium">Color <span class="text-danger">*</span></label>
        <div class="d-flex align-items-center gap-2">
            <span id="vehiculoColorSwatch" class="rounded-circle border flex-shrink-0"
                  style="width:24px;height:24px;background:#fff;border-color:#ced4da !important;"></span>
            <select id="vehiculoColor" class="form-select">
                <option value="">Selecciona...</option>
                <?php foreach ($vehiculoColores as $c): ?>
                <option value="<?= e($c['nombre']) ?>" data-hex="<?= e($c['hex']) ?>"><?= e($c['nombre']) ?></option>
                <?php endforeach; ?>
                <option value="otro">Otro color...</option>
            </select>
        </div>
        <input type="text" id="vehiculoColorOtro" class="form-control mt-2 d-none" placeholder="Escribe el color">
    </div>
</div>
