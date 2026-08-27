<style>
body.login-page {
    background: linear-gradient(145deg, #0f172a 0%, #1e293b 60%, #0f172a 100%) !important;
    min-height: 100vh;
}
</style>

<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;">
    <div style="width:100%;max-width:420px;">

        <div class="text-center mb-4">
            <img src="<?= asset('img/logo.png') ?>" alt="<?= e(negocio()['nombre_taller']) ?>"
                 style="height:110px;width:110px;object-fit:contain;filter:drop-shadow(0 4px 12px rgba(0,0,0,.5));">
            <h4 class="fw-bold mt-2 mb-0" style="color:#fff;letter-spacing:-.02em;"><?= e(negocio()['nombre_taller']) ?></h4>
            <p class="mb-0" style="color:rgba(255,255,255,.5);">Sistema de administración</p>
        </div>

        <div class="card border-0"
             style="border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,.5);overflow:hidden;">

            <div style="height:4px;background:linear-gradient(90deg,#1d4ed8,#2563eb,#60a5fa);"></div>

            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">
                    <h5 class="fw-bold mb-1" style="letter-spacing:-.02em;">Bienvenido</h5>
                    <p class="text-muted small mb-0">Ingresa tus credenciales para continuar</p>
                </div>

                <?php if ($err = get_flash('error')): ?>
                <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3"
                     style="background:#fef2f2;">
                    <i class="bi bi-exclamation-circle me-1"></i><?= e($err) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('login') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="usuario" class="form-control border-start-0 ps-0"
                                   style="background:#f8fafc;"
                                   placeholder="Nombre de usuario"
                                   value="<?= old('usuario') ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-medium text-muted">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0"
                                   style="background:#f8fafc;"
                                   placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit"
                            class="btn btn-primary w-100 fw-semibold py-2"
                            style="background:#2563eb;border-color:#2563eb;border-radius:10px;letter-spacing:.02em;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                    </button>
                </form>

            </div>
        </div>

        <p class="text-center mt-4 mb-0" style="color:rgba(255,255,255,.3);font-size:.75rem;">
            <?= e(negocio()['nombre_taller']) ?> &copy; <?= date('Y') ?>
        </p>

    </div>
</div>
