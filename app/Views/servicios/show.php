<div class="row g-3">

    <!-- Datos del servicio + KPIs -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4 text-center">
                <span class="d-inline-flex align-items-center justify-content-center mb-3"
                      style="width:72px;height:72px;border-radius:16px;background:#e0e7ff;color:#4f46e5;font-size:1.75rem;">
                    <i class="bi bi-wrench-adjustable"></i>
                </span>
                <h5 class="fw-bold mb-1"><?= e($servicio['nombre']) ?></h5>
                <div class="fs-5 fw-semibold text-primary mb-2"><?= formato_moneda($servicio['precio']) ?></div>
                <?php if ((int) $servicio['activo'] === 1): ?>
                <span class="badge text-bg-success">Activo</span>
                <?php else: ?>
                <span class="badge text-bg-secondary">Inactivo</span>
                <?php endif; ?>

                <?php if (!empty($servicio['descripcion'])): ?>
                <hr>
                <div class="text-start small">
                    <div class="text-muted mb-1">Descripción</div>
                    <div style="white-space:pre-wrap;"><?= e($servicio['descripcion']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6 col-lg-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small mb-1"><i class="bi bi-receipt me-1"></i>Veces vendido</div>
                        <div class="fw-bold fs-5"><?= (int) $resumen['veces_vendido'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small mb-1"><i class="bi bi-cash-stack me-1"></i>Total generado</div>
                        <div class="fw-bold fs-5"><?= formato_moneda($resumen['total_generado']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small mb-1"><i class="bi bi-calendar-check me-1"></i>Última venta</div>
                        <div class="fw-bold fs-6">
                            <?= !empty($resumen['ultima_venta']) ? fecha_legible($resumen['ultima_venta']) : '<span class="text-muted">—</span>' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de ventas -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-2" style="color:#2563eb"></i>Historial de ventas</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($historial)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                    Este servicio aún no se ha vendido.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-light">
                            <th class="ps-4">Fecha</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $h): ?>
                        <tr style="cursor:pointer" onclick="location.href='<?= url('ventas/' . $h['id']) ?>'">
                            <td class="ps-4 text-muted small" style="white-space:nowrap;"><?= fecha_legible($h['created_at']) ?></td>
                            <td class="fw-medium small"><?= e($h['cliente_nombre'] . ' ' . $h['cliente_apellido']) ?></td>
                            <td class="text-muted small"><?= e(vehiculo_resumen($h)) ?></td>
                            <td class="text-center"><?= (int) $h['cantidad'] ?></td>
                            <td class="text-end"><?= formato_moneda($h['precio']) ?></td>
                            <td class="text-end fw-semibold pe-4"><?= formato_moneda($h['subtotal']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
