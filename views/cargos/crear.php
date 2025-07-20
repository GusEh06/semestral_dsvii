<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Registrar Movimiento</h1>

<form id="form-cargo" class="card p-4">
    <div class="mb-3">
        <label for="colaborador_cedula" class="form-label">Cédula del colaborador</label>
        <input type="text" name="colaborador_cedula" id="colaborador_cedula" class="form-control" placeholder="Cédula del colaborador" required>
    </div>

    <div class="mb-3">
        <label for="cargo_nuevo_id" class="form-label">Cargo Nuevo</label>
        <select name="cargo_nuevo_id" id="cargo_nuevo_id" class="form-select" required>
            <option value="">Seleccione un cargo</option>
            <?php foreach ($cargos as $cargo): ?>
                <option value="<?= $cargo['id']; ?>"><?= htmlspecialchars($cargo['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="sueldo_nuevo" class="form-label">Sueldo Nuevo</label>
        <input type="number" step="0.01" name="sueldo_nuevo" id="sueldo_nuevo" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="departamento_nuevo_id" class="form-label">Departamento</label>
        <select name="departamento_nuevo_id" id="departamento_nuevo_id" class="form-select" required>
            <option value="">Seleccione un departamento</option>
            <?php foreach ($departamentos as $dep): ?>
                <option value="<?= $dep['id']; ?>"><?= htmlspecialchars($dep['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="tipo_movimiento" class="form-label">Tipo de Movimiento</label>
        <select name="tipo_movimiento" id="tipo_movimiento" class="form-select" required>
            <option value="">Seleccione...</option>
            <option value="Contratacion">Contratación</option>
            <option value="Ascenso">Ascenso</option>
            <option value="Promocion">Promoción</option>
            <option value="Traslado">Traslado</option>
            <option value="Ajuste_Salarial">Ajuste Salarial</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="fecha_efectiva" class="form-label">Fecha Efectiva</label>
        <input type="date" name="fecha_efectiva" id="fecha_efectiva" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="motivo" class="form-label">Motivo</label>
        <textarea name="motivo" id="motivo" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
    <a href="cargos.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('form-cargo').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('cargos.php?accion=store', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            Swal.fire('Éxito', result.message, 'success').then(() => {
                window.location.href = 'cargos.php';
            });
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    })
    .catch(() => {
        Swal.fire('Error', 'Error inesperado al enviar datos', 'error');
    });
});
</script>

<?php include 'views/layouts/footer.php'; ?>
