<?php
$nombreCompleto = trim($cliente['nombre'] . ' ' . $cliente['apellido_paterno']);
$partes = array_values(array_filter(explode(' ', $nombreCompleto)));
$ini    = '';
foreach (array_slice($partes, 0, 2) as $p) {
    $ini .= mb_strtoupper(mb_substr($p, 0, 1));
}
$palette = ['#2563eb','#0891b2','#059669','#7c3aed','#d97706','#dc2626','#0284c7'];
$color   = $palette[abs(crc32($nombreCompleto)) % count($palette)];
?>

<div class="row g-3">

    <!-- Datos del cliente -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4 text-center">
                <span class="d-inline-flex align-items-center justify-content-center mb-3"
                      style="width:72px;height:72px;border-radius:50%;background:<?= $color ?>;color:#fff;font-size:1.5rem;font-weight:700;">
                    <?= e($ini) ?>
                </span>
                <h5 class="fw-bold mb-0"><?= e($nombreCompleto) ?></h5>
                <?php if (!empty($cliente['rfc'])): ?>
                <span class="badge text-bg-light border mt-2"><i class="bi bi-receipt me-1"></i>Tiene datos fiscales</span>
                <?php endif; ?>

                <div class="text-start mt-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-telephone text-muted"></i>
                        <a href="tel:<?= e($cliente['telefono']) ?>" class="text-decoration-none text-body"><?= e($cliente['telefono']) ?></a>
                    </div>
                    <?php if (!empty($cliente['email'])): ?>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-envelope text-muted"></i>
                        <a href="mailto:<?= e($cliente['email']) ?>" class="text-decoration-none text-body"><?= e($cliente['email']) ?></a>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($cliente['direccion'])): ?>
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="bi bi-geo-alt text-muted mt-1"></i>
                        <span><?= e($cliente['direccion']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($cliente['rfc'])): ?>
                <hr>
                <div class="text-start small">
                    <div class="text-muted mb-1">Datos fiscales</div>
                    <div><strong>RFC:</strong> <?= e($cliente['rfc']) ?></div>
                    <?php if (!empty($cliente['razon_social'])): ?>
                    <div><strong>Razón social:</strong> <?= e($cliente['razon_social']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($cliente['regimen_fiscal'])): ?>
                    <div><strong>Régimen:</strong> <?= e($cliente['regimen_fiscal']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($cliente['cp_fiscal'])): ?>
                    <div><strong>C.P.:</strong> <?= e($cliente['cp_fiscal']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Vehículos -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-car-front me-2" style="color:#2563eb"></i>Vehículos</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <?php if (empty($vehiculos)): ?>
                <div class="text-muted small">Sin vehículos registrados aún.</div>
                <?php else: ?>
                <?php foreach ($vehiculos as $v): ?>
                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                    <div>
                        <div class="fw-medium small"><?= e(vehiculo_resumen($v)) ?></div>
                        <div class="text-muted" style="font-size:.75rem;">Última vez: <?= fecha_legible($v['ultima_vez']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Servicios frecuentes -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-star me-2" style="color:#2563eb"></i>Servicios frecuentes</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <?php if (empty($serviciosFrecuentes)): ?>
                <div class="text-muted small">Aún no hay servicios registrados.</div>
                <?php else: ?>
                <?php foreach ($serviciosFrecuentes as $s): ?>
                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                    <span class="small"><?= e($s['nombre_servicio']) ?></span>
                    <span class="badge rounded-pill bg-secondary"><?= (int) $s['veces'] ?>×</span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- KPIs + historial -->
    <div class="col-lg-8">

        <div class="row g-3 mb-3">
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small mb-1"><i class="bi bi-cash-stack me-1"></i>Total gastado</div>
                        <div class="fw-bold fs-5"><?= formato_moneda($resumen['total_gastado']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small mb-1"><i class="bi bi-receipt me-1"></i>Visitas</div>
                        <div class="fw-bold fs-5"><?= (int) $resumen['total_ventas'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small mb-1"><i class="bi bi-calendar-check me-1"></i>Última visita</div>
                        <div class="fw-bold fs-6">
                            <?= !empty($resumen['ultima_visita']) ? fecha_legible($resumen['ultima_visita']) : '<span class="text-muted">—</span>' ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small mb-1"><i class="bi bi-hourglass-split me-1"></i>En curso</div>
                        <div class="fw-bold fs-5"><?= count($trabajosAbiertos) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($trabajosAbiertos)): ?>
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-hourglass-split me-2" style="color:#d97706"></i>Trabajos en curso</h6>
            </div>
            <div class="card-body p-0">
                <?php foreach ($trabajosAbiertos as $t): ?>
                <a href="<?= url('trabajos/' . $t['id']) ?>"
                   class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom text-decoration-none text-body">
                    <div>
                        <div class="fw-medium">#<?= (int) $t['id'] ?> — <?= e(vehiculo_resumen($t)) ?></div>
                        <div class="text-muted small"><?= (int) $t['total_servicios'] ?> servicio(s) · desde <?= fecha_legible($t['created_at']) ?></div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-2" style="color:#2563eb"></i>Historial de ventas</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($historial)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                    Este cliente aún no tiene ventas registradas.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-light">
                            <th class="ps-4">Fecha</th>
                            <th>Vehículo</th>
                            <th>Servicios</th>
                            <th>Pago</th>
                            <th class="text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $v): ?>
                        <tr style="cursor:pointer" onclick="location.href='<?= url('ventas/' . $v['id']) ?>'">
                            <td class="ps-4 text-muted small" style="white-space:nowrap;"><?= fecha_legible($v['created_at']) ?></td>
                            <td class="text-muted small"><?= e(vehiculo_resumen($v)) ?></td>
                            <td class="text-muted small" style="max-width:220px;"><?= e($v['servicios'] ?? '') ?></td>
                            <td><?= metodo_pago_badge($v['metodo_pago']) ?></td>
                            <td class="text-end fw-semibold pe-4"><?= formato_moneda($v['total']) ?></td>
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
