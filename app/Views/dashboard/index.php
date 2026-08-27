<?php
$esAdmin = \App\Core\Auth::esAdmin();
$ticketPromedioMes = ((int) $mes['ventas'] > 0) ? ((float) $mes['total'] / (int) $mes['ventas']) : 0;

$diasLabels = [];
$diasTotales = [];
foreach ($ventasPorDia as $fecha => $d) {
    $diasLabels[]  = date('d/M', strtotime($fecha));
    $diasTotales[] = round((float) $d['total'], 2);
}

$mesesNombres = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
$mesesTotales = [];
foreach ($ventasPorMes as $num => $d) {
    $mesesTotales[] = round((float) $d['total'], 2);
}

$pagoLabels = []; $pagoTotales = [];
foreach ($distribucionPago as $p) {
    $pagoLabels[]  = $p['metodo_pago'];
    $pagoTotales[] = round((float) $p['total'], 2);
}

$topServLabels = []; $topServTotales = [];
foreach ($topServicios as $s) {
    $topServLabels[]  = $s['nombre_servicio'];
    $topServTotales[] = round((float) $s['total'], 2);
}
?>

<!-- KPIs principales -->
<div class="row g-3 mb-3">
    <div class="<?= $esAdmin ? 'col-sm-6 col-lg-3' : 'col-md-5 col-lg-4' ?>">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:52px;height:52px;background:#dcfce7;color:#16a34a;font-size:1.4rem;">
                    <i class="bi bi-sun"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold lh-1"><?= formato_moneda($hoy['total']) ?></div>
                    <div class="text-muted small">Hoy · <?= (int) $hoy['ventas'] ?> venta<?= (int) $hoy['ventas'] === 1 ? '' : 's' ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($esAdmin): ?>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:52px;height:52px;background:#dbeafe;color:#2563eb;font-size:1.4rem;">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold lh-1"><?= formato_moneda($semana['total']) ?></div>
                    <div class="text-muted small">Esta semana · <?= (int) $semana['ventas'] ?> venta<?= (int) $semana['ventas'] === 1 ? '' : 's' ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:52px;height:52px;background:#fef3c7;color:#d97706;font-size:1.4rem;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-4 fw-bold lh-1"><?= formato_moneda($mes['total']) ?></span>
                        <?php if ($cambioMes !== null): ?>
                        <span class="badge rounded-pill <?= $cambioMes >= 0 ? 'text-bg-success' : 'text-bg-danger' ?>" style="font-size:.68rem;">
                            <i class="bi bi-arrow-<?= $cambioMes >= 0 ? 'up' : 'down' ?>-short"></i><?= number_format(abs($cambioMes), 1) ?>%
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small">Este mes · <?= (int) $mes['ventas'] ?> venta<?= (int) $mes['ventas'] === 1 ? '' : 's' ?> vs. mes anterior</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:52px;height:52px;background:#e0e7ff;color:#4f46e5;font-size:1.4rem;">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold lh-1"><?= formato_moneda($anio['total']) ?></div>
                    <div class="text-muted small">Año <?= $anioActual ?> · <?= (int) $anio['ventas'] ?> ventas</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- KPIs secundarios -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <i class="bi bi-hourglass-split fs-4" style="color:#ea580c"></i>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold"><?= (int) $trabajosAbiertos['total'] ?></span>
                        <?php if ((int) $trabajosAbiertos['atrasados'] > 0): ?>
                        <span class="badge rounded-pill text-bg-danger" style="font-size:.65rem;">
                            <?= (int) $trabajosAbiertos['atrasados'] ?> +7 días
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small">
                        Trabajos abiertos<?= (float) $trabajosAbiertos['anticipos'] > 0 ? ' · ' . formato_moneda($trabajosAbiertos['anticipos']) . ' en anticipos' : '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <i class="bi bi-people fs-4" style="color:#2563eb"></i>
                <div>
                    <div class="fw-bold"><?= (int) $totalClientes ?></div>
                    <div class="text-muted small">Clientes registrados</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <i class="bi bi-wrench-adjustable fs-4" style="color:#2563eb"></i>
                <div>
                    <div class="fw-bold"><?= (int) $totalServicios ?></div>
                    <div class="text-muted small">Servicios activos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <i class="bi bi-receipt-cutoff fs-4" style="color:#2563eb"></i>
                <div>
                    <div class="fw-bold"><?= formato_moneda($ticketPromedioMes) ?></div>
                    <div class="text-muted small">Ticket promedio (mes)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas -->
<div class="row g-3 mb-3">
    <?php if ($esAdmin): ?>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0">Ventas — últimos 14 días</h6>
            </div>
            <div class="card-body">
                <canvas id="chartDias" height="90"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="<?= $esAdmin ? 'col-lg-4' : 'col-lg-6' ?>">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0">Métodos de pago</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <?php if (empty($pagoLabels)): ?>
                <div class="text-muted small text-center py-4">Sin datos aún</div>
                <?php else: ?>
                <canvas id="chartPago" height="200"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0">Ventas por mes — <?= $anioActual ?></h6>
            </div>
            <div class="card-body">
                <canvas id="chartMeses" height="90"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0">Servicios más vendidos</h6>
            </div>
            <div class="card-body">
                <?php if (empty($topServLabels)): ?>
                <div class="text-muted small text-center py-4">Sin datos aún</div>
                <?php else: ?>
                <canvas id="chartTopServicios" height="140"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-semibold mb-0">Trabajos en curso</h6>
        <a href="<?= url('trabajos') ?>" class="small text-decoration-none">Ver todos <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($trabajosEnCurso)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-hourglass-split fs-1 d-block mb-2 opacity-25"></i>
            No hay trabajos abiertos en este momento.
        </div>
        <?php else: ?>
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">#</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th class="text-center">Servicios</th>
                    <th class="text-end">Anticipo</th>
                    <th class="text-end pe-4">Días abierto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trabajosEnCurso as $t): ?>
                <tr style="cursor:pointer" onclick="location.href='<?= url('trabajos/' . $t['id']) ?>'">
                    <td class="ps-4">#<?= (int) $t['id'] ?></td>
                    <td class="fw-semibold"><?= e($t['cliente_nombre'] . ' ' . $t['cliente_apellido']) ?></td>
                    <td class="text-muted small"><?= e($t['vehiculo_marca'] . ' · ' . $t['vehiculo_color']) ?></td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-secondary"><?= (int) $t['total_servicios'] ?></span>
                    </td>
                    <td class="text-end"><?= (float) $t['anticipo'] > 0 ? formato_moneda($t['anticipo']) : '<span class="text-muted">—</span>' ?></td>
                    <td class="text-end pe-4">
                        <?php $dias = (int) $t['dias_abierto']; ?>
                        <span class="<?= $dias >= 7 ? 'text-danger fw-semibold' : 'text-muted' ?>">
                            <?= $dias ?> día<?= $dias === 1 ? '' : 's' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-semibold mb-0">Últimas ventas</h6>
        <a href="<?= url('ventas') ?>" class="small text-decoration-none">Ver todas <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($ultimasVentas)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
            Aún no hay ventas registradas.
        </div>
        <?php else: ?>
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr class="table-light">
                    <th class="ps-4">#</th>
                    <th>Cliente</th>
                    <th>Método de pago</th>
                    <th>Atendió</th>
                    <th>Fecha</th>
                    <th class="text-end pe-4">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimasVentas as $v): ?>
                <tr style="cursor:pointer" onclick="location.href='<?= url('ventas/' . $v['id']) ?>'">
                    <td class="ps-4">#<?= (int) $v['id'] ?></td>
                    <td><?= e($v['cliente_nombre'] . ' ' . $v['cliente_apellido']) ?></td>
                    <td><?= metodo_pago_badge($v['metodo_pago']) ?></td>
                    <td class="text-muted"><?= e($v['usuario_nombre']) ?></td>
                    <td class="text-muted small"><?= fecha_legible($v['created_at']) ?></td>
                    <td class="text-end pe-4 fw-semibold"><?= formato_moneda($v['total']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php
$dataDias  = json_encode(['labels' => $diasLabels, 'data' => $diasTotales], JSON_UNESCAPED_UNICODE);
$dataMeses = json_encode(['labels' => $mesesNombres, 'data' => $mesesTotales], JSON_UNESCAPED_UNICODE);
$dataPago  = json_encode(['labels' => $pagoLabels, 'data' => $pagoTotales], JSON_UNESCAPED_UNICODE);
$dataTop   = json_encode(['labels' => $topServLabels, 'data' => $topServTotales], JSON_UNESCAPED_UNICODE);

$pageScript = <<<HTML
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const azul = '#2563eb';
    const paletteFull = ['#2563eb','#0891b2','#059669','#d97706','#7c3aed','#dc2626'];

    const diasData  = {$dataDias};
    const mesesData = {$dataMeses};
    const pagoData  = {$dataPago};
    const topData   = {$dataTop};

    const diasCanvas = document.getElementById('chartDias');
    if (diasCanvas) {
        new Chart(diasCanvas, {
            type: 'line',
            data: {
                labels: diasData.labels,
                datasets: [{
                    label: 'Ventas (\$)',
                    data: diasData.data,
                    borderColor: azul,
                    backgroundColor: 'rgba(37,99,235,.12)',
                    fill: true,
                    tension: .35,
                    pointRadius: 3,
                    pointBackgroundColor: azul,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => '\$' + v } } }
            }
        });
    }

    new Chart(document.getElementById('chartMeses'), {
        type: 'bar',
        data: {
            labels: mesesData.labels,
            datasets: [{
                label: 'Ventas (\$)',
                data: mesesData.data,
                backgroundColor: azul,
                borderRadius: 6,
                maxBarThickness: 40,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => '\$' + v } } }
        }
    });

    const pagoCanvas = document.getElementById('chartPago');
    if (pagoCanvas) {
        new Chart(pagoCanvas, {
            type: 'doughnut',
            data: {
                labels: pagoData.labels,
                datasets: [{ data: pagoData.data, backgroundColor: paletteFull }]
            },
            options: {
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 14 } } }
            }
        });
    }

    const topCanvas = document.getElementById('chartTopServicios');
    if (topCanvas) {
        new Chart(topCanvas, {
            type: 'bar',
            data: {
                labels: topData.labels,
                datasets: [{
                    label: 'Total vendido (\$)',
                    data: topData.data,
                    backgroundColor: paletteFull,
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { callback: v => '\$' + v } } }
            }
        });
    }
})();
</script>
HTML;
?>
