<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Historial de Cargos</h1>

<a href="cargos.php?accion=crear" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Nuevo Movimiento
</a>

<form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" name="direccion" class="form-control" placeholder="Buscar por Dirección" value="<?= htmlspecialchars($_GET['direccion'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="departamento" class="form-select">
            <option value="">Todos los Departamentos</option>
            <?php foreach ($departamentos as $dep): ?>
                <option value="<?= $dep['id']; ?>" <?= (isset($_GET['departamento']) && $_GET['departamento'] == $dep['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($dep['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="tipo_movimiento" class="form-select">
            <option value="">Todos los Movimientos</option>
            <option value="Contratacion" <?= ($_GET['tipo_movimiento'] ?? '') == 'Contratacion' ? 'selected' : ''; ?>>Contratación</option>
            <option value="Ascenso" <?= ($_GET['tipo_movimiento'] ?? '') == 'Ascenso' ? 'selected' : ''; ?>>Ascenso</option>
            <option value="Promocion" <?= ($_GET['tipo_movimiento'] ?? '') == 'Promocion' ? 'selected' : ''; ?>>Promoción</option>
            <option value="Traslado" <?= ($_GET['tipo_movimiento'] ?? '') == 'Traslado' ? 'selected' : ''; ?>>Traslado</option>
            <option value="Ajuste_Salarial" <?= ($_GET['tipo_movimiento'] ?? '') == 'Ajuste_Salarial' ? 'selected' : ''; ?>>Ajuste Salarial</option>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-filter"></i> Filtrar
        </button>
    </div>
</form>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Colaborador</th>
            <th>Cargo Nuevo</th>
            <th>Sueldo Nuevo</th>
            <th>Departamento</th>
            <th>Tipo Movimiento</th>
            <th>Fecha Efectiva</th>
            <th>Firma</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($cargos)): ?>
            <?php foreach ($cargos as $cargo): ?>
                <tr>
                    <td><?= htmlspecialchars($cargo['id']); ?></td>
                    <td><?= htmlspecialchars($cargo['colaborador_nombre']); ?><small class="text-muted">(<?= htmlspecialchars($cargo['cedula']); ?>)</small></td>
                    <td><?= htmlspecialchars($cargo['cargo_nombre']); ?></td>
                    <td>$<?= htmlspecialchars(number_format($cargo['sueldo_nuevo'], 2)); ?></td>
                    <td><?= htmlspecialchars($cargo['departamento_nombre']); ?></td>
                    <td><?= htmlspecialchars($cargo['tipo_movimiento']); ?></td>
                    <td><?= htmlspecialchars($cargo['fecha_efectiva']); ?></td>
                    <td>
                        <?php if ($this->cargo->verificarFirma($cargo)): ?>
                            <span class="badge bg-success">Válida</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Corrupta</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="cargos.php?accion=ver&id=<?= $cargo['id']; ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center">No se encontraron resultados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Mostrar mensaje SweetAlert si hay uno en la sesión
<?php if (isset($_SESSION['mensaje'])): ?>
    Swal.fire(
        '<?= $_SESSION['mensaje']['tipo'] === 'success' ? 'Éxito' : 'Error' ?>',
        '<?= $_SESSION['mensaje']['texto'] ?>',
        '<?= $_SESSION['mensaje']['tipo'] ?>'
    );
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>
</script>

<?php include 'views/layouts/footer.php'; ?>
