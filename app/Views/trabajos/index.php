<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">#</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th class="text-center">Servicios</th>
                    <th class="text-end">Anticipo</th>
                    <th class="text-center">Estado</th>
                    <th>Abierto por</th>
                    <th>Fecha</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trabajos as $t): ?>
                <tr style="cursor:pointer" onclick="location.href='<?= url('trabajos/' . $t['id']) ?>'">
                    <td class="ps-4">#<?= (int) $t['id'] ?></td>
                    <td class="fw-semibold"><?= e($t['cliente_nombre'] . ' ' . $t['cliente_apellido']) ?></td>
                    <td class="text-muted small"><?= e($t['vehiculo_marca'] . ' · ' . $t['vehiculo_color']) ?></td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-secondary"><?= (int) $t['total_servicios'] ?></span>
                    </td>
                    <td class="text-end"><?= (float) $t['anticipo'] > 0 ? formato_moneda($t['anticipo']) : '<span class="text-muted">—</span>' ?></td>
                    <td class="text-center"><?= trabajo_estado_badge($t['estado']) ?></td>
                    <td class="text-muted small"><?= e($t['usuario_nombre']) ?></td>
                    <td class="text-muted small" style="white-space:nowrap;"><?= fecha_legible($t['created_at']) ?></td>
                    <td class="text-end pe-4" onclick="event.stopPropagation()">
                        <a href="<?= url('trabajos/' . $t['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if ($t['estado'] === 'Abierto' && \App\Core\Auth::esAdmin()): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                data-confirm-url="<?= url('trabajos/' . $t['id'] . '/eliminar') ?>"
                                data-confirm-title="¿Eliminar trabajo?"
                                data-confirm-message="Se eliminará permanentemente el trabajo <strong>#<?= (int) $t['id'] ?></strong> de <strong><?= e($t['cliente_nombre'] . ' ' . $t['cliente_apellido']) ?></strong>, junto con sus comentarios. Esta acción no se puede deshacer.">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($trabajos)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-hourglass-split fs-1 d-block mb-2 opacity-25"></i>
            No hay trabajos registrados aún.
        </div>
        <?php endif; ?>
    </div>
</div>
