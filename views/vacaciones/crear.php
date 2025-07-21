<?php include 'views/layouts/header.php'; ?>



<h1 class="mt-4">Nueva Solicitud de Vacaciones</h1>

<a href="vacaciones.php" class="btn btn-secondary mb-3">← Volver al listado</a>

<?php if (!empty($_SESSION['errores'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($_SESSION['errores'] as $error): ?>
                <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errores']); ?>
<?php endif; ?>

<form action="vacaciones.php?accion=store" method="POST" id="form-vacaciones" novalidate>
    <div class="mb-3">
        <label for="colaborador_id" class="form-label">Empleado</label>

        <select name="colaborador_id" id="colaborador_id" class="form-select" required>
            <option value="">Seleccione un empleado</option>
            <?php foreach ($colaboradores as $colaborador): ?>
                <option 
                    value="<?= $colaborador['id']; ?>"
                    data-dias="<?= $colaborador['dias_disponibles']; ?>"
                    <?= (isset($_SESSION['datos_anteriores']['colaborador_id']) && $_SESSION['datos_anteriores']['colaborador_id'] == $colaborador['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($colaborador['nombre_completo']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mt-2">
    <strong>Días acumulados:</strong> <span id="dias-acumulados">-</span>
    </div>


    <div class="mb-3">
        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required
            value="<?= $_SESSION['datos_anteriores']['fecha_inicio'] ?? ''; ?>">
    </div>

    <div class="mb-3">
        <label for="fecha_fin" class="form-label">Fecha Fin</label>
        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required
            value="<?= $_SESSION['datos_anteriores']['fecha_fin'] ?? ''; ?>">
    </div>

    <div class="mb-3">
        <label for="dias_solicitados" class="form-label">Días Solicitados</label>
        <input type="number" name="dias_solicitados" id="dias_solicitados" class="form-control" min="1" required
            value="<?= $_SESSION['datos_anteriores']['dias_solicitados'] ?? ''; ?>">
    </div>

    <div class="mb-3">
        <label for="observaciones" class="form-label">Observaciones (opcional)</label>
        <textarea name="observaciones" id="observaciones" class="form-control" rows="3"><?= htmlspecialchars($_SESSION['datos_anteriores']['observaciones'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
</form>

<script>
    // Limpiar datos anteriores después de mostrar el formulario
    <?php unset($_SESSION['datos_anteriores']); ?>
</script>

<script>
    const selectEmpleado = document.getElementById('colaborador_id');
    const spanDias = document.getElementById('dias-acumulados');

    function actualizarDias() {
        const selectedOption = selectEmpleado.options[selectEmpleado.selectedIndex];
        const dias = selectedOption.getAttribute('data-dias');
        spanDias.textContent = dias ? dias : '-';
    }

    selectEmpleado.addEventListener('change', actualizarDias);

    // Ejecutar al cargar si ya hay un empleado seleccionado
    window.addEventListener('DOMContentLoaded', actualizarDias);
</script>



<?php include 'views/layouts/footer.php'; ?>
