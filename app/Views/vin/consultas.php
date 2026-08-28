<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">Fecha</th>
                    <th>VIN</th>
                    <th class="text-center">Resultado</th>
                    <th>Vehículo</th>
                    <th>Carrocería / Tipo</th>
                    <th>Combustible / Motor</th>
                    <th class="pe-4">Consultó</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultas as $c): ?>
                <tr>
                    <td class="ps-4 text-muted small" style="white-space:nowrap;"><?= fecha_legible($c['created_at']) ?></td>
                    <td style="font-family:monospace;"><?= e($c['vin']) ?></td>
                    <td class="text-center">
                        <?php if ((int) $c['ok'] === 1): ?>
                        <i class="bi bi-check-circle-fill text-success" data-bs-toggle="tooltip" data-bs-title="Encontrado"></i>
                        <?php else: ?>
                        <i class="bi bi-x-circle-fill text-danger" data-bs-toggle="tooltip" data-bs-title="<?= e($c['error'] ?? 'Sin resultado') ?>"></i>
                        <?php endif; ?>
                    </td>
                    <td class="small">
                        <?php
                            $partes = array_filter([$c['model_year'], $c['make'], $c['model']]);
                        ?>
                        <?php if (!empty($partes)): ?>
                        <div class="fw-medium"><?= e(implode(' ', $partes)) ?></div>
                        <?php if (!empty($c['version']) || !empty($c['series'])): ?>
                        <div class="text-muted" style="font-size:.75rem;"><?= e(trim(($c['version'] ?? '') . ' ' . ($c['series'] ?? ''))) ?></div>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <?= e(trim(implode(' · ', array_filter([$c['body_class'] ?? '', $c['vehicle_type'] ?? '', $c['drive_type'] ?? '']))) ?: '—') ?>
                    </td>
                    <td class="text-muted small">
                        <?php
                            $motor = [];
                            if (!empty($c['fuel_type_primary'])) $motor[] = $c['fuel_type_primary'];
                            if (!empty($c['engine_cylinders'])) $motor[] = $c['engine_cylinders'] . ' cil';
                            if (!empty($c['displacement_l'])) $motor[] = $c['displacement_l'] . 'L';
                            if (!empty($c['doors'])) $motor[] = $c['doors'] . ' puertas';
                        ?>
                        <?= !empty($motor) ? e(implode(' · ', $motor)) : '<span class="text-muted">—</span>' ?>
                    </td>
                    <td class="pe-4 text-muted small"><?= e($c['usuario_nombre']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <?php if (empty($consultas)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-upc-scan fs-1 d-block mb-2 opacity-25"></i>
            Aún no se ha consultado ningún VIN.
        </div>
        <?php endif; ?>
    </div>
</div>
