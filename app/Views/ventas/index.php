<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">#</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Método de pago</th>
                    <th class="text-center">Factura</th>
                    <th>Atendió</th>
                    <th>Fecha</th>
                    <th class="text-end">Total</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $v): ?>
                <tr>
                    <td class="ps-4">
                        #<?= (int) $v['id'] ?>
                        <?php if (!empty($v['trabajo_id'])): ?>
                        <i class="bi bi-hourglass-split text-muted ms-1" data-bs-toggle="tooltip" data-bs-title="Viene del Trabajo #<?= (int) $v['trabajo_id'] ?>"></i>
                        <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= e($v['cliente_nombre'] . ' ' . $v['cliente_apellido']) ?></td>
                    <td class="text-muted small"><?= e(vehiculo_resumen($v)) ?></td>
                    <td><?= metodo_pago_badge($v['metodo_pago']) ?></td>
                    <td class="text-center">
                        <?php if ((int) $v['factura'] === 1): ?>
                        <i class="bi bi-check-circle-fill text-success" data-bs-toggle="tooltip" data-bs-title="Facturado"></i>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted"><?= e($v['usuario_nombre']) ?></td>
                    <td class="text-muted small"><?= fecha_legible($v['created_at']) ?></td>
                    <td class="text-end fw-semibold"><?= formato_moneda($v['total']) ?></td>
                    <td class="text-end pe-4">
                        <a href="<?= url('ventas/' . $v['id']) ?>"
                           class="btn btn-sm btn-outline-secondary" title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if (\App\Core\Auth::esAdmin()): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                data-confirm-url="<?= url('ventas/' . $v['id'] . '/eliminar') ?>"
                                data-confirm-title="¿Eliminar venta?"
                                data-confirm-message="Se eliminará permanentemente la venta <strong>#<?= (int) $v['id'] ?></strong> de <strong><?= e($v['cliente_nombre'] . ' ' . $v['cliente_apellido']) ?></strong> por <strong><?= formato_moneda($v['total']) ?></strong>. Esta acción no se puede deshacer.">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($ventas)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
            No hay ventas registradas aún.
        </div>
        <?php endif; ?>
    </div>
</div>
