<div class="row g-3">

    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="fw-semibold mb-1">Trabajo #<?= (int) $trabajo['id'] ?></h5>
                        <small class="text-muted"><?= fecha_legible($trabajo['created_at']) ?> · Abrió: <?= e($trabajo['usuario_nombre']) ?></small>
                    </div>
                    <?= trabajo_estado_badge($trabajo['estado']) ?>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Cliente</div>
                        <div class="fw-semibold"><?= e($trabajo['cliente_nombre'] . ' ' . $trabajo['cliente_apellido']) ?></div>
                        <?php if (!empty($trabajo['cliente_telefono'])): ?>
                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= e($trabajo['cliente_telefono']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Vehículo</div>
                        <div class="fw-semibold"><?= e(vehiculo_resumen($trabajo)) ?></div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Anticipo</div>
                        <div class="fw-semibold"><?= (float) $trabajo['anticipo'] > 0 ? formato_moneda($trabajo['anticipo']) : '<span class="text-muted">Sin anticipo</span>' ?></div>
                    </div>
                    <?php if ($trabajo['estado'] === 'Cerrado' && !empty($trabajo['venta_id'])): ?>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Venta generada</div>
                        <a href="<?= url('ventas/' . $trabajo['venta_id']) ?>" class="fw-semibold text-decoration-none">
                            Venta #<?= (int) $trabajo['venta_id'] ?> <i class="bi bi-arrow-up-right-square ms-1"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mb-2">
                    <div class="text-muted small mb-2">Servicios planeados</div>
                    <?php if (empty($servicios)): ?>
                    <span class="text-muted small">Sin servicios registrados.</span>
                    <?php else: ?>
                    <?php foreach ($servicios as $s): ?>
                    <span class="badge text-bg-light border me-1 mb-1"><i class="bi bi-wrench-adjustable me-1"></i><?= e($s['nombre_servicio']) ?></span>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if ($trabajo['estado'] === 'Abierto'): ?>
                <div class="d-flex gap-2 mt-4">
                    <a href="<?= url('trabajos/' . $trabajo['id'] . '/finalizar') ?>" class="btn btn-primary">
                        <i class="bi bi-flag me-1"></i>Finalizar y generar venta
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Línea de tiempo -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-clock-history me-2" style="color:#2563eb"></i>Avance del trabajo</h6>

                <?php if ($trabajo['estado'] === 'Abierto'): ?>
                <form method="POST" action="<?= url('trabajos/' . $trabajo['id'] . '/comentarios') ?>" class="mb-4">
                    <?= csrf_field() ?>
                    <textarea name="comentario" class="form-control mb-2" rows="2" required
                              placeholder="Ej: Se desmontó el motor, esperando refacción..."></textarea>
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Agregar comentario
                    </button>
                </form>
                <?php endif; ?>

                <?php if (empty($comentarios)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-chat-square-text fs-1 d-block mb-2 opacity-25"></i>
                    Aún no hay comentarios.
                </div>
                <?php else: ?>
                <div class="d-flex flex-column gap-3" style="max-height:520px;overflow-y:auto;">
                    <?php foreach ($comentarios as $c): ?>
                    <div class="border-start border-3 ps-3" style="border-color:#2563eb !important;">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="fw-semibold small"><?= e($c['usuario_nombre']) ?></span>
                            <span class="text-muted" style="font-size:.72rem;"><?= fecha_legible($c['created_at']) ?></span>
                        </div>
                        <div class="small"><?= nl2br(e($c['comentario'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
