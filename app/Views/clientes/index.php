<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">Cliente</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th class="text-center">Ventas</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                <?php
                    $nombreCompleto = trim($c['nombre'] . ' ' . $c['apellido_paterno']);
                    $partes = array_values(array_filter(explode(' ', $nombreCompleto)));
                    $ini    = '';
                    foreach (array_slice($partes, 0, 2) as $p) {
                        $ini .= mb_strtoupper(mb_substr($p, 0, 1));
                    }
                    $palette = ['#2563eb','#0891b2','#059669','#7c3aed','#d97706','#dc2626','#0284c7'];
                    $color   = $palette[abs(crc32($nombreCompleto)) % count($palette)];
                ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <span style="width:38px;height:38px;border-radius:50%;background:<?= $color ?>;
                                         color:#fff;font-size:.75rem;font-weight:700;flex-shrink:0;
                                         display:inline-flex;align-items:center;justify-content:center;">
                                <?= e($ini) ?>
                            </span>
                            <div>
                                <div class="fw-semibold lh-sm">
                                    <?= e($nombreCompleto) ?>
                                    <?php if (!empty($c['rfc'])): ?>
                                    <i class="bi bi-receipt text-muted ms-1" data-bs-toggle="tooltip" data-bs-title="Tiene datos fiscales"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="tel:<?= e($c['telefono']) ?>" class="text-decoration-none text-body">
                            <i class="bi bi-telephone text-muted me-1"></i><?= e($c['telefono']) ?>
                        </a>
                    </td>
                    <td>
                        <?php if (!empty($c['email'])): ?>
                        <a href="mailto:<?= e($c['email']) ?>" class="text-decoration-none text-body">
                            <i class="bi bi-envelope text-muted me-1"></i><?= e($c['email']) ?>
                        </a>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <?= !empty($c['direccion']) ? e($c['direccion']) : '<span class="text-muted">—</span>' ?>
                    </td>
                    <td class="text-center">
                        <?php if ((int) $c['total_ventas'] > 0): ?>
                        <span class="badge rounded-pill bg-secondary"><?= (int) $c['total_ventas'] ?></span>
                        <?php else: ?>
                        <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?= url('clientes/' . $c['id'] . '/editar') ?>"
                           class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if (\App\Core\Auth::esAdmin()): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                data-confirm-url="<?= url('clientes/' . $c['id'] . '/eliminar') ?>"
                                data-confirm-title="¿Eliminar cliente?"
                                data-confirm-message="Se eliminará permanentemente a <strong><?= e($nombreCompleto) ?></strong>. Esta acción no se puede deshacer.">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($clientes)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
            No hay clientes registrados aún.
        </div>
        <?php endif; ?>
    </div>
</div>
