<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">Servicio</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $s): ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <span class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                  style="width:38px;height:38px;background:#e0e7ff;color:#4f46e5;">
                                <i class="bi bi-wrench-adjustable"></i>
                            </span>
                            <div class="fw-semibold lh-sm">
                                <a href="<?= url('servicios/' . $s['id']) ?>" class="text-decoration-none text-body">
                                    <?= e($s['nombre']) ?>
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small" style="max-width:280px;">
                        <?php if (!empty($s['descripcion'])): ?>
                        <span class="text-truncate d-inline-block" style="max-width:280px;" data-bs-toggle="tooltip" data-bs-title="<?= e($s['descripcion']) ?>">
                            <?= e($s['descripcion']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= formato_moneda($s['precio']) ?></td>
                    <td class="text-center">
                        <?php if ((int) $s['activo'] === 1): ?>
                        <span class="badge text-bg-success">Activo</span>
                        <?php else: ?>
                        <span class="badge text-bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?= url('servicios/' . $s['id']) ?>"
                           class="btn btn-sm btn-outline-secondary" title="Ver historial">
                            <i class="bi bi-clock-history"></i>
                        </a>
                        <a href="<?= url('servicios/' . $s['id'] . '/editar') ?>"
                           class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if (\App\Core\Auth::esAdmin()): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                data-confirm-url="<?= url('servicios/' . $s['id'] . '/eliminar') ?>"
                                data-confirm-title="¿Eliminar servicio?"
                                data-confirm-message="Se eliminará permanentemente <strong><?= e($s['nombre']) ?></strong> del catálogo. Las ventas ya registradas con este servicio no se verán afectadas.">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($servicios)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-wrench-adjustable fs-1 d-block mb-2 opacity-25"></i>
            No hay servicios registrados aún.
        </div>
        <?php endif; ?>
    </div>
</div>
