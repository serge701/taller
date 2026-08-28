<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr class="table-light">
                    <th class="ps-4" style="width:56px;"></th>
                    <th>Usuario</th>
                    <th>Nivel</th>
                    <th class="text-center">Estado</th>
                    <th>Último acceso</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td class="ps-4">
                        <?php if (!empty($u['foto'])): ?>
                        <img src="<?= url('assets/img/avatars/' . e($u['foto'])) ?>" alt="<?= e($u['nombre']) ?>"
                             style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        <?php else: ?>
                        <?php
                            $partesU   = explode(' ', trim($u['nombre'] ?? ''));
                            $inicialesU = strtoupper(substr($partesU[0] ?? '', 0, 1) . substr($partesU[1] ?? '', 0, 1)) ?: '?';
                        ?>
                        <div style="width:40px;height:40px;border-radius:50%;background:#2563eb;color:#fff;
                                    font-size:.8rem;font-weight:700;display:flex;align-items:center;justify-content:center;">
                            <?= e($inicialesU) ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= e($u['nombre']) ?></div>
                        <div class="small text-muted">@<?= e($u['usuario']) ?></div>
                    </td>
                    <td>
                        <span class="badge <?= $u['nivel'] === 'Admin' ? 'text-bg-primary' : 'text-bg-secondary' ?>">
                            <?= e($u['nivel']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if ((int) $u['activo'] === 1): ?>
                        <span class="badge text-bg-success">Activo</span>
                        <?php else: ?>
                        <span class="badge text-bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <?= !empty($u['last_login']) ? fecha_legible($u['last_login']) : '<span class="text-muted">Nunca</span>' ?>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?= url('usuarios/' . $u['id'] . '/editar') ?>"
                           class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ((int) $u['id'] !== \App\Core\Auth::id()): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                data-confirm-url="<?= url('usuarios/' . $u['id'] . '/eliminar') ?>"
                                data-confirm-title="¿Eliminar usuario?"
                                data-confirm-message="Se eliminará permanentemente la cuenta de <strong><?= e($u['nombre']) ?></strong> (@<?= e($u['usuario']) ?>) y perderá acceso al sistema de inmediato.">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($usuarios)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-person-gear fs-1 d-block mb-2 opacity-25"></i>
            No hay usuarios registrados aún.
        </div>
        <?php endif; ?>
    </div>
</div>
