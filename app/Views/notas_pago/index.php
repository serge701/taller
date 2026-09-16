<?php if ($totalPendiente > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 d-print-none">
    <i class="bi bi-exclamation-circle-fill"></i>
    <div>Total pendiente de pago: <strong><?= formato_moneda($totalPendiente) ?></strong></div>
</div>
<?php endif; ?>

<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">Monto</th>
                    <th>Descripción</th>
                    <th>Trabajo</th>
                    <th>Registrado por</th>
                    <th class="text-center">Estado</th>
                    <th>Comentarios</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notas as $n): ?>
                <tr>
                    <td class="ps-4">
                        <div class="fw-semibold"><?= formato_moneda($n['monto']) ?></div>
                        <div class="text-muted small" style="white-space:nowrap;"><?= fecha_legible($n['created_at']) ?></div>
                    </td>
                    <td class="text-muted small" style="max-width:280px;">
                        <span class="text-truncate d-inline-block" style="max-width:280px;" data-bs-toggle="tooltip" data-bs-title="<?= e($n['descripcion']) ?>">
                            <?= e($n['descripcion']) ?>
                        </span>
                    </td>
                    <td class="text-muted small">
                        <?php if (!empty($n['trabajo_id'])): ?>
                        <a href="<?= url('trabajos/' . $n['trabajo_id']) ?>">
                            #<?= (int) $n['trabajo_id'] ?> — <?= e(trim($n['trabajo_cliente_nombre'] . ' ' . $n['trabajo_cliente_apellido'])) ?>
                        </a>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= e($n['usuario_nombre']) ?></td>
                    <td class="text-center">
                        <?php if ($n['estado'] === 'Pagado'): ?>
                        <span class="badge text-bg-success">Pagado</span>
                        <?php else: ?>
                        <span class="badge text-bg-warning">Pendiente de pago</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small" style="max-width:220px;">
                        <?php if (!empty($n['comentarios'])): ?>
                        <span class="text-truncate d-inline-block" style="max-width:220px;" data-bs-toggle="tooltip" data-bs-title="<?= e($n['comentarios']) ?>">
                            <?= e($n['comentarios']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-4">
                        <?php if ($n['estado'] === 'Pendiente'): ?>
                        <button type="button" class="btn btn-sm btn-outline-success" title="Marcar como pagado"
                                data-pagar-url="<?= url('notas-pago/' . $n['id'] . '/pagar') ?>"
                                data-pagar-monto="<?= e(formato_moneda($n['monto'])) ?>"
                                data-bs-toggle="modal" data-bs-target="#modalPagar">
                            <i class="bi bi-check-circle me-1"></i>Marcar pagado
                        </button>
                        <?php endif; ?>
                        <?php if (\App\Core\Auth::esAdmin()): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                data-confirm-url="<?= url('notas-pago/' . $n['id'] . '/eliminar') ?>"
                                data-confirm-title="¿Eliminar nota de pago?"
                                data-confirm-message="Se eliminará permanentemente esta nota de <strong><?= e(formato_moneda($n['monto'])) ?></strong>. Esta acción no se puede deshacer.">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($notas)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt-cutoff fs-1 d-block mb-2 opacity-25"></i>
            No hay notas de pago registradas aún.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para marcar una nota como pagada -->
<div class="modal fade" id="modalPagar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formPagar">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:64px;height:64px;border-radius:50%;background:#dcfce7;">
                        <i class="bi bi-check-circle text-success" style="font-size:1.6rem;"></i>
                    </div>
                    <h5 class="fw-semibold mb-2 text-center">Marcar nota como pagada</h5>
                    <p class="text-muted text-center mb-3">Monto: <strong id="modalPagarMonto"></strong></p>

                    <label class="form-label fw-medium">Comentarios <span class="text-muted small">(opcional)</span></label>
                    <textarea name="comentarios" class="form-control" rows="3"
                              placeholder="Ej: pagado en efectivo el día de hoy, referencia de transferencia, etc."></textarea>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-1"></i>Confirmar pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $pageScript = <<<'HTML'
<script>
    (function () {
        const modalEl = document.getElementById('modalPagar');
        if (!modalEl) return;

        const form  = document.getElementById('formPagar');
        const monto = document.getElementById('modalPagarMonto');

        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('[data-pagar-url]');
            if (!trigger) return;

            form.action = trigger.dataset.pagarUrl;
            monto.textContent = trigger.dataset.pagarMonto || '';
        });
    })();
</script>
HTML;
?>
