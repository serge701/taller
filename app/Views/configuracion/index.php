<div class="row justify-content-center">
<div class="col-lg-9 col-xl-8">
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="mb-4">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-gear me-2" style="color:#2563eb"></i>Datos del negocio
            </h5>
            <small class="text-muted">
                Esta información se muestra junto al logo y se imprime en el recibo de las ventas.
            </small>
        </div>

        <form method="POST" action="<?= url('configuracion') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-medium">
                    Nombre del Taller <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="bi bi-shop"></i></span>
                    <input type="text" name="nombre_taller" class="form-control" required
                           placeholder="Ej: Taller Mecánico Reyes"
                           value="<?= e($negocio['nombre_taller']) ?>">
                </div>
                <div class="form-text">Se muestra junto al logo (menú, login) y en el recibo impreso.</div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">RFC <span class="text-muted small">(opcional)</span></label>
                    <input type="text" name="rfc" class="form-control text-uppercase" maxlength="13"
                           placeholder="XAXX010101000"
                           value="<?= e($negocio['rfc'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Régimen fiscal <span class="text-muted small">(opcional)</span></label>
                    <select name="regimen_fiscal" class="form-select">
                        <option value="">Selecciona un régimen...</option>
                        <?php foreach (catalogo_regimenes_fiscales() as $regimen): ?>
                        <option value="<?= e($regimen) ?>" <?= ($negocio['regimen_fiscal'] ?? '') === $regimen ? 'selected' : '' ?>>
                            <?= e($regimen) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Dirección <span class="text-muted small">(opcional)</span></label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" name="direccion" class="form-control"
                           placeholder="Calle, número, colonia, ciudad"
                           value="<?= e($negocio['direccion'] ?? '') ?>">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Teléfono <span class="text-muted small">(opcional)</span></label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="bi bi-telephone"></i></span>
                        <input type="tel" name="telefono" class="form-control"
                               placeholder="(999) 123-4567"
                               value="<?= e($negocio['telefono'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email <span class="text-muted small">(opcional)</span></label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control"
                               placeholder="contacto@ejemplo.com"
                               value="<?= e($negocio['email'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Guardar cambios
            </button>
        </form>
    </div>
</div>
</div>
</div>
