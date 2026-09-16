<div class="row justify-content-center">
<div class="col-lg-5 col-xl-4">
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="mb-4">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-plus-circle me-2" style="color:#2563eb"></i>Nueva Nota de Pago
            </h5>
            <small class="text-muted">Nace con estado "Pendiente de pago"</small>
        </div>

        <form method="POST" action="<?= url('notas-pago') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-medium">
                    Monto <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text text-muted">$</span>
                    <input type="number" step="0.01" min="0.01" name="monto" class="form-control" required
                           placeholder="0.00" value="<?= old('monto') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">
                    Descripción <span class="text-danger">*</span>
                </label>
                <textarea name="descripcion" class="form-control" rows="3" required
                          placeholder="Parte(s) compradas, refaccionaria, etc."><?= old('descripcion') ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium">
                    Trabajo relacionado <span class="text-muted small">(opcional)</span>
                </label>
                <select name="trabajo_id" class="form-select">
                    <option value="">— Sin trabajo asociado —</option>
                    <?php foreach ($trabajos as $t): ?>
                    <option value="<?= (int) $t['id'] ?>" <?= old('trabajo_id') === (string) $t['id'] ? 'selected' : '' ?>>
                        #<?= (int) $t['id'] ?> — <?= e($t['cliente_nombre'] . ' ' . $t['cliente_apellido']) ?> · <?= e(vehiculo_resumen($t)) ?> (<?= e($t['estado']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Sirve para rastrear en qué trabajo se usaron las partes.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Registrar Nota
                </button>
                <a href="<?= url('notas-pago') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
