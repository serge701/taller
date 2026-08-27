<?php
$fotoUrl = !empty($usuario['foto']) ? url('assets/img/avatars/' . $usuario['foto']) : null;
$partes  = explode(' ', trim($usuario['nombre'] ?? ''));
$iniciales = strtoupper(substr($partes[0] ?? '', 0, 1) . substr($partes[1] ?? '', 0, 1)) ?: '?';
?>
<div class="row g-3 justify-content-center">

    <!-- Foto de perfil -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <h6 class="fw-semibold mb-3 text-start"><i class="bi bi-image me-2" style="color:#2563eb"></i>Foto de perfil</h6>

                <img id="avatarPreview" src="<?= $fotoUrl ? e($fotoUrl) : '' ?>" alt="Foto de perfil"
                     style="width:140px;height:140px;border-radius:50%;object-fit:cover;border:3px solid #e2e8f0;<?= $fotoUrl ? '' : 'display:none;' ?>">
                <div id="avatarInicial" class="mx-auto"
                     style="width:140px;height:140px;border-radius:50%;background:#2563eb;color:#fff;font-size:2.5rem;font-weight:700;align-items:center;justify-content:center;display:<?= $fotoUrl ? 'none' : 'flex' ?>;">
                    <?= e($iniciales) ?>
                </div>
                <div id="avatarPreviewAviso" class="small text-primary mt-2 d-none">
                    <i class="bi bi-eye me-1"></i>Vista previa — aún no se ha guardado
                </div>

                <form method="POST" action="<?= url('perfil/foto') ?>" enctype="multipart/form-data" class="mt-4">
                    <?= csrf_field() ?>
                    <input type="file" name="foto" id="inputFoto" accept="image/jpeg,image/png,image/webp" class="form-control mb-2" required>
                    <div class="form-text mb-3">JPG, PNG o WEBP · máximo 3MB</div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-upload me-1"></i>Subir foto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Datos personales + contraseña -->
    <div class="col-lg-7">

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-person me-2" style="color:#2563eb"></i>Datos personales</h6>

                <form method="POST" action="<?= url('perfil') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nombre de usuario</label>
                            <input type="text" class="form-control" value="<?= e($usuario['usuario']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nivel de acceso</label>
                            <input type="text" class="form-control" value="<?= e($usuario['nivel']) ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required
                                   value="<?= e($usuario['nombre']) ?>">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-lock me-2" style="color:#2563eb"></i>Cambiar contraseña</h6>

                <form method="POST" action="<?= url('perfil/password') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Contraseña actual <span class="text-danger">*</span></label>
                            <input type="password" name="password_actual" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nueva contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_nueva" class="form-control" required
                                   placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Confirmar nueva contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmar" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-key me-1"></i>Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>

<?php $pageScript = <<<'HTML'
<script>
(function () {
    const input  = document.getElementById('inputFoto');
    const img    = document.getElementById('avatarPreview');
    const inicial = document.getElementById('avatarInicial');
    const aviso  = document.getElementById('avatarPreviewAviso');

    input.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;

        const url = URL.createObjectURL(file);
        img.src = url;
        img.style.display = '';
        if (inicial) inicial.style.display = 'none';
        aviso.classList.remove('d-none');
    });
})();
</script>
HTML;
?>
