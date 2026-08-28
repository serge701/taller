<?php
$u        = auth();
$esAdmin  = \App\Core\Auth::esAdmin();
?>
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">

        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list fs-5"></i>
                </a>
            </li>

            <li class="nav-item d-none d-md-flex">
                <div class="vr mx-2 my-2 opacity-25"></div>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-title="Nueva venta">
                <a class="nav-link px-2" href="<?= url('ventas/nueva') ?>">
                    <i class="bi bi-cart-plus fs-5 text-success"></i>
                </a>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-title="Nuevo trabajo">
                <a class="nav-link px-2" href="<?= url('trabajos/nuevo') ?>">
                    <i class="bi bi-tools fs-5 text-primary"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-flex">
                <div class="vr mx-2 my-2 opacity-25"></div>
            </li>
            <li class="nav-item d-none d-md-flex" data-bs-toggle="tooltip" data-bs-title="Ventas">
                <a class="nav-link px-2" href="<?= url('ventas') ?>">
                    <i class="bi bi-receipt fs-5"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-flex" data-bs-toggle="tooltip" data-bs-title="Trabajos">
                <a class="nav-link px-2" href="<?= url('trabajos') ?>">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-flex" data-bs-toggle="tooltip" data-bs-title="Clientes">
                <a class="nav-link px-2" href="<?= url('clientes') ?>">
                    <i class="bi bi-people fs-5"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-flex" data-bs-toggle="tooltip" data-bs-title="Servicios">
                <a class="nav-link px-2" href="<?= url('servicios') ?>">
                    <i class="bi bi-wrench-adjustable fs-5"></i>
                </a>
            </li>
            <li class="nav-item d-none d-lg-flex" data-bs-toggle="tooltip" data-bs-title="Reportes">
                <a class="nav-link px-2" href="<?= url('reportes') ?>">
                    <i class="bi bi-clipboard-data fs-5"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item d-none d-sm-flex align-items-center px-2">
                <span class="text-muted small">
                    <?= date('d/M/Y') ?> &nbsp;·&nbsp;
                    <span id="reloj" class="fw-semibold text-body" style="font-variant-numeric:tabular-nums">
                        00:00:00
                    </span>
                </span>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                    <?php if (!empty($u['foto'])): ?>
                    <img src="<?= url('assets/img/avatars/' . e($u['foto'])) ?>"
                         style="width:26px;height:26px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                    <i class="bi bi-person-circle fs-5"></i>
                    <?php endif; ?>
                    <span class="d-none d-sm-inline"><?= e($u['nombre'] ?? '') ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><span class="dropdown-item-text small text-muted"><?= e($u['nivel'] ?? '') ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="<?= url('perfil') ?>">
                            <i class="bi bi-person-gear me-2"></i>Mi perfil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="<?= url('logout') ?>">
                            <?= csrf_field() ?>
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>

    </div>
</nav>
