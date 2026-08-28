<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-4">
        <h6 class="fw-semibold mb-3"><i class="bi bi-funnel me-2" style="color:#2563eb"></i>Filtros</h6>

        <form method="GET" action="<?= url('reportes') ?>">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <label class="form-label fw-medium small">Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control"
                           value="<?= e($filtros['fecha_inicio']) ?>">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-medium small">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control"
                           value="<?= e($filtros['fecha_fin']) ?>">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-medium small">Cliente</label>
                    <select name="cliente" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($clientes as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= $filtros['cliente'] === (string) $c['id'] ? 'selected' : '' ?>>
                            <?= e($c['nombre'] . ' ' . $c['apellido_paterno']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-medium small">Servicio</label>
                    <input type="text" name="servicio" class="form-control" placeholder="Ej: Afinación..."
                           value="<?= e($filtros['servicio']) ?>">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-medium small">Facturada</label>
                    <select name="factura" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" <?= $filtros['factura'] === '1' ? 'selected' : '' ?>>Sí</option>
                        <option value="0" <?= $filtros['factura'] === '0' ? 'selected' : '' ?>>No</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-medium small">Método de pago</label>
                    <select name="metodo_pago" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($metodosPago as $m): ?>
                        <option value="<?= e($m) ?>" <?= $filtros['metodo_pago'] === $m ? 'selected' : '' ?>><?= e($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                    <a href="<?= url('reportes') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">#</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Servicios</th>
                    <th>Método de pago</th>
                    <th class="text-center">Factura</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">IVA</th>
                    <th class="text-end">Total</th>
                    <th>Atendió</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $v): ?>
                <tr>
                    <td class="ps-4">#<?= (int) $v['id'] ?></td>
                    <td class="text-muted small" style="white-space:nowrap;"><?= fecha_legible($v['created_at']) ?></td>
                    <td class="fw-semibold"><?= e($v['cliente_nombre'] . ' ' . $v['cliente_apellido']) ?></td>
                    <td class="text-muted small"><?= e(vehiculo_resumen($v)) ?></td>
                    <td class="text-muted small" style="max-width:220px;"><?= e($v['servicios'] ?? '') ?></td>
                    <td><?= metodo_pago_badge($v['metodo_pago']) ?></td>
                    <td class="text-center">
                        <?php if ((int) $v['factura'] === 1): ?>
                        <i class="bi bi-check-circle-fill text-success" data-bs-toggle="tooltip" data-bs-title="Facturada"></i>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end"><?= formato_moneda($v['subtotal']) ?></td>
                    <td class="text-end"><?= (int) $v['factura'] === 1 ? formato_moneda($v['iva']) : '<span class="text-muted">—</span>' ?></td>
                    <td class="text-end fw-semibold"><?= formato_moneda($v['total']) ?></td>
                    <td class="text-muted small"><?= e($v['usuario_nombre']) ?></td>
                    <td class="text-end pe-4">
                        <a href="<?= url('ventas/' . $v['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <?php if (empty($ventas)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-search fs-1 d-block mb-2 opacity-25"></i>
            No se encontraron ventas con esos filtros.
        </div>
        <?php endif; ?>
    </div>
</div>
