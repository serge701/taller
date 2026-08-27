<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/css/adminlte.min.css">
</head>
<body class="bg-body-secondary d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="text-center">
        <div class="fs-1 mb-3"><i class="bi bi-signpost-split" style="color:#2563eb"></i></div>
        <h2 class="fw-bold">404 · Página no encontrada</h2>
        <p class="text-muted">La página que buscas no existe.</p>
        <a href="<?= function_exists('url') ? url('dashboard') : '/' ?>" class="btn btn-primary mt-2">
            <i class="bi bi-house me-1"></i>Ir al inicio
        </a>
    </div>
</body>
</html>
