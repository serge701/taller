<?php $negocio = negocio(); ?>
<style>
    /* Vista de impresión: solo se ve el recibo, todo lo demás (sidebar, navbar,
       footer, botones) se oculta. #reciboImprimible vive oculto en pantalla y
       solo se revela dentro de @media print. */
    #reciboImprimible { display: none; }

    @media print {
        .app-sidebar, .app-header, .app-footer, .app-content-header,
        .no-print, .modal { display: none !important; }
        .app-main, .app-content, .container-fluid { margin: 0 !important; padding: 0 !important; }
        body { background: #fff !important; }

        #reciboImprimible {
            display: block !important;
            max-width: 720px;
            margin: 0 auto;
            color: #111;
            font-size: 13px;
        }
        #reciboImprimible .recibo-total { font-size: 18px; }
    }
</style>

<div class="row justify-content-center no-print">
<div class="col-lg-7">
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h5 class="fw-semibold mb-1">Venta #<?= (int) $venta['id'] ?></h5>
                <small class="text-muted"><?= fecha_legible($venta['created_at']) ?> · Atendió: <?= e($venta['usuario_nombre']) ?></small>
                <?php if (!empty($venta['trabajo_id'])): ?>
                <div class="small mt-1">
                    <a href="<?= url('trabajos/' . $venta['trabajo_id']) ?>" class="text-decoration-none">
                        <i class="bi bi-hourglass-split me-1"></i>Generada desde el Trabajo #<?= (int) $venta['trabajo_id'] ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <?= metodo_pago_badge($venta['metodo_pago']) ?>
                <?php if ((int) $venta['factura'] === 1): ?>
                <span class="badge text-bg-dark ms-1"><i class="bi bi-receipt me-1"></i>Facturado</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="text-muted small mb-1">Cliente</div>
                <div class="fw-semibold"><?= e($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido']) ?></div>
                <?php if (!empty($venta['cliente_telefono'])): ?>
                <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= e($venta['cliente_telefono']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <div class="text-muted small mb-1">Vehículo</div>
                <div class="fw-semibold">
                    <?= e(vehiculo_resumen($venta)) ?>
                </div>
            </div>
        </div>

        <?php if ((int) $venta['factura'] === 1 && !empty($venta['cliente_rfc'])): ?>
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="border rounded-3 p-3 bg-light">
                    <div class="text-muted small mb-1"><i class="bi bi-receipt me-1"></i>Datos fiscales</div>
                    <div class="fw-semibold"><?= e($venta['cliente_razon_social'] ?? '') ?></div>
                    <div class="small text-muted">RFC: <?= e($venta['cliente_rfc']) ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($venta['comentarios'])): ?>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="text-muted small mb-1">Comentarios</div>
                <div><?= nl2br(e($venta['comentarios'])) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <table class="table align-middle mb-0">
            <thead>
                <tr class="table-light">
                    <th>Servicio</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                <tr>
                    <td><?= e($it['nombre_servicio']) ?></td>
                    <td class="text-center"><?= (int) $it['cantidad'] ?></td>
                    <td class="text-end"><?= formato_moneda($it['precio']) ?></td>
                    <td class="text-end fw-semibold"><?= formato_moneda($it['subtotal']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <?php if ((int) $venta['factura'] === 1): ?>
                <tr>
                    <td colspan="3" class="text-end text-muted border-0">Subtotal</td>
                    <td class="text-end border-0"><?= formato_moneda($venta['subtotal']) ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end text-muted border-0">IVA (16%)</td>
                    <td class="text-end border-0"><?= formato_moneda($venta['iva']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="3" class="text-end fw-semibold fs-5 border-0">Total</td>
                    <td class="text-end fw-bold fs-5 border-0" style="color:#2563eb;"><?= formato_moneda($venta['total']) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex gap-2 mt-4">
            <a href="<?= url('ventas') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver al listado
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Imprimir
            </button>
        </div>

    </div>
</div>
</div>
</div>

<!-- ═══════════ Recibo imprimible (solo visible en @media print) ═══════════ -->
<div id="reciboImprimible">

    <div class="d-flex align-items-center gap-3" style="border-bottom:2px solid #111;padding-bottom:12px;margin-bottom:16px;">
        <img src="<?= asset('img/logo.png') ?>" alt="<?= e($negocio['nombre_taller']) ?>" style="height:64px;width:64px;object-fit:contain;">
        <div class="flex-grow-1">
            <div style="font-size:19px;font-weight:700;"><?= e($negocio['nombre_taller']) ?></div>
            <?php if (!empty($negocio['rfc'])): ?>
            <div style="font-size:11px;color:#555;">RFC: <?= e($negocio['rfc']) ?></div>
            <?php endif; ?>
            <?php if (!empty($negocio['direccion'])): ?>
            <div style="font-size:11px;color:#555;"><?= e($negocio['direccion']) ?></div>
            <?php endif; ?>
            <?php if (!empty($negocio['telefono'])): ?>
            <div style="font-size:11px;color:#555;">Tel: <?= e($negocio['telefono']) ?></div>
            <?php endif; ?>
        </div>
        <div class="text-end">
            <div style="font-size:16px;font-weight:700;">Venta #<?= (int) $venta['id'] ?></div>
            <div style="font-size:12px;color:#555;"><?= fecha_legible($venta['created_at']) ?></div>
            <?php if ((int) $venta['factura'] === 1): ?>
            <div style="font-size:11px;font-weight:700;">FACTURADO</div>
            <?php endif; ?>
        </div>
    </div>

    <table style="width:100%;border-collapse:collapse;margin-bottom:16px;">
        <tr>
            <td style="width:50%;vertical-align:top;padding-right:12px;">
                <div style="font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Cliente</div>
                <div style="font-weight:700;"><?= e($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido']) ?></div>
                <?php if (!empty($venta['cliente_telefono'])): ?>
                <div>Tel: <?= e($venta['cliente_telefono']) ?></div>
                <?php endif; ?>
                <?php if (!empty($venta['cliente_email'])): ?>
                <div><?= e($venta['cliente_email']) ?></div>
                <?php endif; ?>
                <?php if (!empty($venta['cliente_direccion'])): ?>
                <div><?= e($venta['cliente_direccion']) ?></div>
                <?php endif; ?>
            </td>
            <td style="width:50%;vertical-align:top;">
                <div style="font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Vehículo</div>
                <div style="font-weight:700;"><?= e(vehiculo_resumen($venta)) ?></div>
                <div style="margin-top:6px;">
                    <span style="color:#555;">Método de pago:</span> <?= e($venta['metodo_pago']) ?>
                </div>
                <div><span style="color:#555;">Atendió:</span> <?= e($venta['usuario_nombre']) ?></div>
            </td>
        </tr>
    </table>

    <?php if ((int) $venta['factura'] === 1 && !empty($venta['cliente_rfc'])): ?>
    <table style="width:100%;border:1px solid #ccc;border-collapse:collapse;margin-bottom:16px;">
        <tr>
            <td style="padding:8px 10px;">
                <div style="font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Datos fiscales</div>
                <div style="font-weight:700;"><?= e($venta['cliente_razon_social'] ?? '') ?></div>
                <div>RFC: <?= e($venta['cliente_rfc']) ?>
                    <?php if (!empty($venta['cliente_cp_fiscal'])): ?>
                    &nbsp;·&nbsp; C.P.: <?= e($venta['cliente_cp_fiscal']) ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($venta['cliente_regimen_fiscal'])): ?>
                <div><?= e($venta['cliente_regimen_fiscal']) ?></div>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <?php endif; ?>

    <table style="width:100%;border-collapse:collapse;margin-bottom:14px;">
        <thead>
            <tr style="border-bottom:1.5px solid #111;">
                <th style="text-align:left;padding:4px 0;">Servicio</th>
                <th style="text-align:center;padding:4px 0;">Cant.</th>
                <th style="text-align:right;padding:4px 0;">Precio</th>
                <th style="text-align:right;padding:4px 0;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $it): ?>
            <tr style="border-bottom:1px solid #ddd;">
                <td style="padding:5px 0;"><?= e($it['nombre_servicio']) ?></td>
                <td style="text-align:center;padding:5px 0;"><?= (int) $it['cantidad'] ?></td>
                <td style="text-align:right;padding:5px 0;"><?= formato_moneda($it['precio']) ?></td>
                <td style="text-align:right;padding:5px 0;font-weight:700;"><?= formato_moneda($it['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table style="width:100%;border-collapse:collapse;margin-bottom:16px;">
        <?php if ((int) $venta['factura'] === 1): ?>
        <tr>
            <td style="text-align:right;padding:2px 0;color:#555;">Subtotal</td>
            <td style="text-align:right;padding:2px 0;width:110px;"><?= formato_moneda($venta['subtotal']) ?></td>
        </tr>
        <tr>
            <td style="text-align:right;padding:2px 0;color:#555;">IVA (16%)</td>
            <td style="text-align:right;padding:2px 0;width:110px;"><?= formato_moneda($venta['iva']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td style="text-align:right;padding:6px 0 0;font-weight:700;border-top:1.5px solid #111;" class="recibo-total">Total</td>
            <td style="text-align:right;padding:6px 0 0;font-weight:700;border-top:1.5px solid #111;width:110px;" class="recibo-total"><?= formato_moneda($venta['total']) ?></td>
        </tr>
    </table>

    <?php if (!empty($venta['comentarios'])): ?>
    <div style="margin-bottom:16px;">
        <div style="font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Comentarios</div>
        <div><?= nl2br(e($venta['comentarios'])) ?></div>
    </div>
    <?php endif; ?>

    <div style="text-align:center;border-top:1px solid #ccc;padding-top:12px;margin-top:24px;color:#555;font-size:12px;">
        Gracias por su preferencia
    </div>

</div>
