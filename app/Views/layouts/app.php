<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(negocio()['nombre_taller']) ?></title>
    <link rel="icon" href="<?= asset('img/logo.png') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
        .app-sidebar { background: #0f172a !important; }

        .sidebar-brand .brand-link {
            border-bottom: 1px solid rgba(255,255,255,.07) !important;
            padding: 1rem 1.2rem !important;
        }
        .sidebar-brand .brand-text {
            font-weight: 700 !important;
            font-size: 1.05rem;
            color: #fff !important;
        }

        .sidebar-menu .nav-header {
            font-weight: 800 !important;
            font-size: .68rem;
            letter-spacing: .1em;
            color: rgba(255,255,255,.35) !important;
            padding: 1rem 1rem .3rem 1.2rem;
        }

        .sidebar-menu .nav-link {
            border-radius: 8px !important;
            margin: 1px 8px !important;
            padding: .45rem .9rem !important;
            transition: background .15s, color .15s;
            color: rgba(255,255,255,.65) !important;
        }
        .sidebar-menu .nav-link:hover {
            background: rgba(37,99,235,.18) !important;
            color: #fff !important;
        }
        .sidebar-menu .nav-link.active {
            background: #2563eb !important;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(37,99,235,.35);
        }
        .sidebar-menu .nav-link.active .nav-icon { color: #fff !important; }
        .sidebar-menu .nav-icon {
            color: #60a5fa !important;
            font-size: 1rem;
            margin-right: .55rem !important;
        }
        .sidebar-menu .nav-link:hover .nav-icon { color: #93c5fd !important; }
        .sidebar-menu .nav-link p { font-size: .875rem; font-weight: 500; }
        .sidebar-menu .nav-link.active p { font-weight: 600; }

        .btn-primary {
            --bs-btn-bg: #2563eb; --bs-btn-border-color: #2563eb;
            --bs-btn-hover-bg: #1d4ed8; --bs-btn-hover-border-color: #1d4ed8;
            --bs-btn-active-bg: #1d4ed8; --bs-btn-active-border-color: #1d4ed8;
        }
        a { color: #2563eb; }

        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            padding: .85rem 1.5rem .5rem;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: .5rem 1.5rem .85rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

    <?php require dirname(__DIR__) . '/partials/navbar.php'; ?>
    <?php require dirname(__DIR__) . '/partials/sidebar.php'; ?>

    <main class="app-main">

        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0"><?= e($pageTitle ?? 'Inicio') ?></h3>
                        <?php if (!empty($pageSubtitle)): ?>
                        <small class="text-muted"><?= e($pageSubtitle) ?></small>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($pageActions)): ?>
                    <div class="col-auto">
                        <?= $pageActions ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">

                <?php if ($ok = get_flash('success')): ?>
                    <div class="alert alert-success alert-dismissible d-print-none">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <?= e($ok) ?>
                    </div>
                <?php endif; ?>
                <?php if ($err = get_flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible d-print-none">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <?= e($err) ?>
                    </div>
                <?php endif; ?>

                <?= $content ?>

            </div>
        </div>
    </main>

    <footer class="app-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span class="text-muted small"><?= e(negocio()['nombre_taller']) ?> &copy; <?= date('Y') ?></span>
        <span class="text-muted small d-none d-sm-inline">
            Desarrollado por <a href="https://ninubo.com" target="_blank" rel="noopener">Ninubo</a>
        </span>
    </footer>
</div>

<!-- Modal de confirmación genérico (reemplaza confirm() nativo) -->
<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                     style="width:64px;height:64px;border-radius:50%;background:#fee2e2;">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size:1.6rem;"></i>
                </div>
                <h5 class="fw-semibold mb-2" id="modalConfirmarTitulo">¿Confirmar acción?</h5>
                <p class="text-muted mb-0" id="modalConfirmarMensaje"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" id="formConfirmar" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger px-4" id="modalConfirmarBoton">
                        <i class="bi bi-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/js/adminlte.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    const DT_LANG = { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-MX.json' };

    document.querySelectorAll('table.datatable').forEach(t => {
        // order: [] respeta el orden que ya trae la tabla desde el servidor
        // (p. ej. ventas más recientes primero) en vez de que DataTables
        // imponga su propio orden ascendente por la primera columna.
        new DataTable(t, { language: DT_LANG, order: [] });
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // Modal de confirmación genérico: cualquier botón con data-confirm-url
    // abre el modal en vez de un confirm() nativo del navegador.
    (function () {
        const modalEl = document.getElementById('modalConfirmar');
        if (!modalEl) return;

        const modal    = new bootstrap.Modal(modalEl);
        const form     = document.getElementById('formConfirmar');
        const titulo   = document.getElementById('modalConfirmarTitulo');
        const mensaje  = document.getElementById('modalConfirmarMensaje');
        const boton    = document.getElementById('modalConfirmarBoton');

        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('[data-confirm-url]');
            if (!trigger) return;

            form.action = trigger.dataset.confirmUrl;
            titulo.textContent = trigger.dataset.confirmTitle || '¿Confirmar acción?';
            mensaje.innerHTML = trigger.dataset.confirmMessage || '';
            boton.innerHTML = '<i class="bi bi-' + (trigger.dataset.confirmIcon || 'trash') + ' me-1"></i>'
                + (trigger.dataset.confirmButton || 'Eliminar');
            boton.className = 'btn px-4 btn-' + (trigger.dataset.confirmVariant || 'danger');

            modal.show();
        });
    })();

    (function tick() {
        const el = document.getElementById('reloj');
        if (el) {
            const n = new Date(), pad = v => String(v).padStart(2, '0');
            el.textContent = pad(n.getHours()) + ':' + pad(n.getMinutes()) + ':' + pad(n.getSeconds());
        }
        setTimeout(tick, 1000);
    })();
</script>
<?= $pageScript ?? '' ?>
</body>
</html>
