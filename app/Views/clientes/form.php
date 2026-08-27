<?php $edit = $cliente !== null; ?>
<div class="row justify-content-center">
<div class="col-lg-9 col-xl-8">
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="mb-4">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-<?= $edit ? 'pencil-square' : 'person-plus' ?> me-2" style="color:#2563eb"></i>
                <?= $edit ? 'Editar Cliente' : 'Nuevo Cliente' ?>
            </h5>
            <?php if ($edit): ?>
            <small class="text-muted">ID #<?= (int) $cliente['id'] ?></small>
            <?php endif; ?>
        </div>

        <form method="POST"
              action="<?= $edit ? url('clientes/' . $cliente['id']) : url('clientes') ?>">
            <?= csrf_field() ?>

            <!-- Nombre -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Nombre <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="nombre" class="form-control" required
                               placeholder="Ej: Juan Carlos"
                               value="<?= $edit ? e($cliente['nombre'] ?? '') : old('nombre') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Apellido Paterno <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="apellido_paterno" class="form-control" required
                           placeholder="Ej: García"
                           value="<?= $edit ? e($cliente['apellido_paterno'] ?? '') : old('apellido_paterno') ?>">
                </div>
            </div>

            <!-- Contacto -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">
                        Teléfono <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="bi bi-telephone"></i></span>
                        <input type="tel" name="telefono" class="form-control" required
                               placeholder="(999) 123-4567"
                               value="<?= $edit ? e($cliente['telefono'] ?? '') : old('telefono') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email <span class="text-muted small">(opcional)</span></label>
                    <div class="input-group">
                        <span class="input-group-text text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control"
                               placeholder="correo@ejemplo.com"
                               value="<?= $edit ? e($cliente['email'] ?? '') : old('email') ?>">
                    </div>
                </div>
            </div>

            <!-- Dirección -->
            <div class="mb-3">
                <label class="form-label fw-medium">Dirección <span class="text-muted small">(opcional)</span></label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" name="direccion" class="form-control"
                           placeholder="Calle, número, colonia, ciudad"
                           value="<?= $edit ? e($cliente['direccion'] ?? '') : old('direccion') ?>">
                </div>
            </div>

            <!-- Datos fiscales (colapsable) -->
            <div class="mb-4">
                <button type="button" class="btn btn-sm btn-outline-secondary w-100 d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#datosFiscales">
                    <span><i class="bi bi-receipt me-1"></i>Datos fiscales <span class="text-muted">(opcional, para facturar)</span></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="collapse <?= $edit && !empty($cliente['rfc']) ? 'show' : '' ?> mt-3" id="datosFiscales">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">RFC</label>
                            <input type="text" name="rfc" class="form-control text-uppercase" maxlength="13"
                                   placeholder="XAXX010101000"
                                   value="<?= $edit ? e($cliente['rfc'] ?? '') : old('rfc') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Razón social</label>
                            <input type="text" name="razon_social" class="form-control"
                                   placeholder="Ej: Transportes García SA de CV"
                                   value="<?= $edit ? e($cliente['razon_social'] ?? '') : old('razon_social') ?>">
                        </div>
                        <div class="col-md-8">
                            <?php $regimenActual = $edit ? ($cliente['regimen_fiscal'] ?? '') : old('regimen_fiscal'); ?>
                            <label class="form-label fw-medium">Régimen fiscal</label>
                            <select name="regimen_fiscal" class="form-select">
                                <option value="">Selecciona un régimen...</option>
                                <?php foreach (catalogo_regimenes_fiscales() as $regimen): ?>
                                <option value="<?= e($regimen) ?>" <?= $regimenActual === $regimen ? 'selected' : '' ?>>
                                    <?= e($regimen) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">C.P. fiscal</label>
                            <input type="text" name="cp_fiscal" class="form-control" maxlength="10"
                                   placeholder="00000"
                                   value="<?= $edit ? e($cliente['cp_fiscal'] ?? '') : old('cp_fiscal') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i><?= $edit ? 'Guardar cambios' : 'Agregar Cliente' ?>
                </button>
                <a href="<?= url('clientes') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
