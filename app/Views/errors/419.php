<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión expirada</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/css/adminlte.min.css">
</head>
<body class="bg-body-secondary d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="text-center">
        <div class="fs-1 mb-3"><i class="bi bi-clock-history" style="color:#d97706"></i></div>
        <h2 class="fw-bold">419 · Sesión expirada</h2>
        <p class="text-muted">Tu formulario tardó demasiado o la sesión expiró. Intenta de nuevo.</p>
        <a href="<?= function_exists('url') ? url('') : '/' ?>" class="btn btn-primary mt-2">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Volver a intentar
        </a>
    </div>
</body>
</html>
