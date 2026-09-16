<?php
$esAdmin = \App\Core\Auth::esAdmin();
$__u      = \App\Core\Auth::user();
$__nombre = $__u['nombre'] ?? '';
$__nivel  = $__u['nivel']  ?? '';
$__uid    = (int)($__u['id'] ?? 0);

$__partes   = explode(' ', trim($__nombre));
$__initials = strtoupper(substr($__partes[0] ?? '', 0, 1) . substr($__partes[1] ?? '', 0, 1)) ?: '?';
$__colors   = ['2563eb','0891b2','059669','d97706','7c3aed','0284c7','16a34a','dc2626'];
$__color    = $__colors[$__uid % count($__colors)];
$__foto     = $__u['foto'] ?? null;
?>
<aside class="app-sidebar shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= url('dashboard') ?>" class="brand-link d-flex align-items-center gap-2">
            <img src="<?= asset('img/logo.png') ?>" alt="<?= e(negocio()['nombre_taller']) ?>"
                 style="height:34px;width:34px;object-fit:contain;flex-shrink:0;">
            <span class="brand-text"><?= e(negocio()['nombre_taller']) ?></span>
        </a>
    </div>

    <div class="sidebar-wrapper d-flex flex-column" style="height:calc(100% - 57px);">
        <nav class="mt-2 flex-grow-1" style="overflow-y:auto;overflow-x:hidden;">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation">

                <li class="nav-item">
                    <a href="<?= url('dashboard') ?>" class="nav-link <?= active('dashboard') ?>">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">OPERACIÓN</li>

                <li class="nav-item">
                    <a href="<?= url('ventas') ?>" class="nav-link <?= active('ventas') ?>">
                        <i class="nav-icon bi bi-receipt"></i>
                        <p>Ventas</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('clientes') ?>" class="nav-link <?= active('clientes') ?>">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Clientes</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('servicios') ?>" class="nav-link <?= active('servicios') ?>">
                        <i class="nav-icon bi bi-wrench-adjustable"></i>
                        <p>Servicios</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('trabajos') ?>" class="nav-link <?= active('trabajos') ?>">
                        <i class="nav-icon bi bi-hourglass-split"></i>
                        <p>Trabajos</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('notas-pago') ?>" class="nav-link <?= active('notas-pago') ?>">
                        <i class="nav-icon bi bi-receipt-cutoff"></i>
                        <p>Notas de Pago</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('reportes') ?>" class="nav-link <?= active('reportes') ?>">
                        <i class="nav-icon bi bi-clipboard-data"></i>
                        <p>Reportes</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('vin') ?>" class="nav-link <?= active('vin') ?>">
                        <i class="nav-icon bi bi-upc-scan"></i>
                        <p>VIN</p>
                    </a>
                </li>

                <?php if ($esAdmin): ?>
                <li class="nav-header">ADMINISTRACIÓN</li>

                <li class="nav-item">
                    <a href="<?= url('usuarios') ?>" class="nav-link <?= active('usuarios') ?>">
                        <i class="nav-icon bi bi-person-gear"></i>
                        <p>Usuarios</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('vehiculos') ?>" class="nav-link <?= active('vehiculos') ?>">
                        <i class="nav-icon bi bi-car-front"></i>
                        <p>Marcas y Modelos</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('configuracion') ?>" class="nav-link <?= active('configuracion') ?>">
                        <i class="nav-icon bi bi-gear"></i>
                        <p>Configuración</p>
                    </a>
                </li>
                <?php endif; ?>

            </ul>
        </nav>

        <a href="<?= url('perfil') ?>"
           class="d-flex align-items-center gap-2 text-decoration-none px-3 py-3"
           style="border-top:1px solid rgba(255,255,255,.08);
                  background:rgba(0,0,0,.15);
                  transition:background .15s;"
           onmouseover="this.style.background='rgba(255,255,255,.07)'"
           onmouseout="this.style.background='rgba(0,0,0,.15)'">

            <div class="position-relative flex-shrink-0">
                <?php if ($__foto): ?>
                <img src="<?= url('assets/img/avatars/' . e($__foto)) ?>"
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;
                            border:2px solid rgba(255,255,255,.2);">
                <?php else: ?>
                <div style="width:38px;height:38px;border-radius:50%;
                            background:#<?= $__color ?>;
                            display:flex;align-items:center;justify-content:center;
                            color:#fff;font-weight:700;font-size:.8rem;
                            border:2px solid rgba(255,255,255,.2);">
                    <?= e($__initials) ?>
                </div>
                <?php endif; ?>
                <span style="position:absolute;bottom:1px;right:1px;
                             width:9px;height:9px;border-radius:50%;
                             background:#22c55e;border:2px solid #343a40;
                             box-shadow:0 0 0 1px #22c55e;"></span>
            </div>

            <div class="overflow-hidden flex-grow-1">
                <div class="fw-semibold text-truncate"
                     style="font-size:.82rem;color:rgba(255,255,255,.9);line-height:1.25;">
                    <?= e($__nombre) ?>
                </div>
                <div style="font-size:.7rem;color:rgba(255,255,255,.45);line-height:1.2;">
                    <?= e($__nivel) ?>
                </div>
            </div>

            <i class="bi bi-chevron-right flex-shrink-0"
               style="font-size:.65rem;color:rgba(255,255,255,.3);"></i>
        </a>

    </div>
</aside>
