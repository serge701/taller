<?php $edit = $servicio !== null; ?>
<div class="row justify-content-center">
<div class="col-lg-5 col-xl-4">
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="mb-4">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-<?= $edit ? 'pencil-square' : 'plus-circle' ?> me-2" style="color:#2563eb"></i>
                <?= $edit ? 'Editar Servicio' : 'Nuevo Servicio' ?>
            </h5>
            <?php if ($edit): ?>
            <small class="text-muted">ID #<?= (int) $servicio['id'] ?></small>
            <?php endif; ?>
        </div>

        <form method="POST"
              action="<?= $edit ? url('servicios/' . $servicio['id']) : url('servicios') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-medium">
                    Nombre del servicio <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="bi bi-wrench-adjustable"></i></span>
                    <input type="text" name="nombre" class="form-control" required
                           placeholder="Ej: Cambio de aceite"
                           value="<?= $edit ? e($servicio['nombre'] ?? '') : old('nombre') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">
                    Precio <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text text-muted">$</span>
                    <input type="number" step="0.01" min="0.01" name="precio" class="form-control" required
                           placeholder="0.00"
                           value="<?= $edit ? e((string) $servicio['precio']) : old('precio') ?>">
                </div>
            </div>

            <?php if ($edit): ?>
            <div class="mb-4 form-check form-switch">
                <input type="checkbox" class="form-check-input" role="switch" id="activo" name="activo" value="1"
                       <?= (int) ($servicio['activo'] ?? 1) === 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Servicio activo (disponible para nuevas ventas)</label>
            </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i><?= $edit ? 'Guardar cambios' : 'Agregar Servicio' ?>
                </button>
                <a href="<?= url('servicios') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
