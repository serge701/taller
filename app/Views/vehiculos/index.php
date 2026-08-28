<div class="row g-3">

    <!-- Formularios de alta -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-plus-circle me-2" style="color:#2563eb"></i>Agregar marca</h6>
                <form method="POST" action="<?= url('vehiculos/marcas') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nombre de la marca</label>
                        <input type="text" name="nombre" class="form-control" required
                               placeholder="Ej: Tesla, SsangYong...">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Agregar marca
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-plus-circle me-2" style="color:#2563eb"></i>Agregar modelo</h6>
                <form method="POST" action="<?= url('vehiculos/modelos') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Marca</label>
                        <select name="marca_id" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <?php foreach ($marcas as $m): ?>
                            <option value="<?= (int) $m['id'] ?>"><?= e($m['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nombre del modelo</label>
                        <input type="text" name="nombre" class="form-control" required
                               placeholder="Ej: Model 3, Kicks e-Power...">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Agregar modelo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Catálogo actual (solo consulta) -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-collection me-2" style="color:#2563eb"></i>Catálogo actual</h6>

                <div class="input-group mb-3">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filtroCatalogo" class="form-control" placeholder="Filtrar por marca o modelo...">
                </div>

                <div id="listaCatalogo" style="max-height:640px;overflow-y:auto;">
                    <?php foreach ($marcas as $m): ?>
                    <div class="border rounded-3 p-3 mb-2 marca-bloque"
                         data-buscar="<?= e(mb_strtolower($m['nombre'] . ' ' . implode(' ', array_column($m['modelos'], 'nombre')))) ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold"><?= e($m['nombre']) ?></span>
                            <span class="badge rounded-pill bg-secondary"><?= count($m['modelos']) ?></span>
                        </div>
                        <?php if (empty($m['modelos'])): ?>
                        <span class="text-muted small">Sin modelos registrados aún.</span>
                        <?php else: ?>
                        <?php foreach ($m['modelos'] as $mo): ?>
                        <span class="badge text-bg-light border me-1 mb-1"><?= e($mo['nombre']) ?></span>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>

                    <div id="sinResultados" class="text-center py-4 text-muted d-none">
                        <i class="bi bi-search fs-1 d-block mb-2 opacity-25"></i>
                        Sin coincidencias.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $pageScript = <<<'HTML'
<script>
(function () {
    const filtro = document.getElementById('filtroCatalogo');
    const bloques = document.querySelectorAll('.marca-bloque');
    const sinResultados = document.getElementById('sinResultados');

    filtro.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        let visibles = 0;
        bloques.forEach(function (b) {
            const coincide = q === '' || b.dataset.buscar.includes(q);
            b.classList.toggle('d-none', !coincide);
            if (coincide) visibles++;
        });
        sinResultados.classList.toggle('d-none', visibles > 0);
    });
})();
</script>
HTML;
?>
