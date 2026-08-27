<?php $edit = $usuario !== null; ?>
<div class="row justify-content-center">
<div class="col-lg-9 col-xl-8">
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="mb-4">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-<?= $edit ? 'pencil-square' : 'person-plus' ?> me-2" style="color:#2563eb"></i>
                <?= $edit ? 'Editar Usuario' : 'Nuevo Usuario' ?>
            </h5>
            <?php if ($edit): ?>
            <small class="text-muted">ID #<?= (int) $usuario['id'] ?></small>
            <?php endif; ?>
        </div>

        <form method="POST"
              action="<?= $edit ? url('usuarios/' . $usuario['id']) : url('usuarios') ?>">
            <?= csrf_field() ?>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Nombre de usuario <span class="text-danger">*</span>
                    </label>
                    <?php if ($edit): ?>
                    <input type="text" class="form-control" value="<?= e($usuario['usuario']) ?>" disabled>
                    <small class="text-muted">No se puede modificar</small>
                    <?php else: ?>
                    <input type="text" name="usuario" class="form-control" required
                           placeholder="Ej: jperez" value="<?= old('usuario') ?>">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Nombre completo <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nombre" class="form-control" required
                           placeholder="Ej: Juan Pérez"
                           value="<?= $edit ? e($usuario['nombre'] ?? '') : old('nombre') ?>">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nivel de acceso</label>
                    <select name="nivel" class="form-select">
                        <?php $nivelActual = $edit ? ($usuario['nivel'] ?? 'Usuario') : 'Usuario'; ?>
                        <option value="Usuario" <?= $nivelActual === 'Usuario' ? 'selected' : '' ?>>Usuario</option>
                        <option value="Admin" <?= $nivelActual === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Contraseña <?= $edit ? '<span class="text-muted small">(dejar en blanco para no cambiar)</span>' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Mínimo 6 caracteres" <?= $edit ? '' : 'required' ?>>
                </div>
            </div>

            <?php if ($edit): ?>
            <div class="mb-4 form-check form-switch">
                <input type="checkbox" class="form-check-input" role="switch" id="activo" name="activo" value="1"
                       <?= (int) ($usuario['activo'] ?? 1) === 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Usuario activo (puede iniciar sesión)</label>
            </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i><?= $edit ? 'Guardar cambios' : 'Crear Usuario' ?>
                </button>
                <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
