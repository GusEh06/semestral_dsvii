<?php include 'views/layouts/header.php'; ?>

<?php
// Obtener la página actual de la solicitud GET, por defecto será 1
$paginaActual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
?>

<h1 class="mt-4">Reporte de Colaboradores</h1>

<!-- Botón para Exportar a Excel -->
<a href="reportes.php?accion=exportar&<?= http_build_query($_GET); ?>" class="btn btn-success mb-3">
    <i class="fas fa-file-excel"></i> Exportar a Excel
</a>

<!-- Filtro de búsqueda -->
<form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" name="nombre" class="form-control" placeholder="Buscar por Nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <input type="text" name="apellido" class="form-control" placeholder="Buscar por Apellido" value="<?= htmlspecialchars($_GET['apellido'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="sexo" class="form-select">
            <option value="">Sexo</option>
            <option value="M" <?= ($_GET['sexo'] ?? '') == 'M' ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?= ($_GET['sexo'] ?? '') == 'F' ? 'selected' : ''; ?>>Femenino</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="number" name="salario" class="form-control" placeholder="Salario mayor a" value="<?= htmlspecialchars($_GET['salario'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
</form>

<!-- Tabla de colaboradores -->
<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Cédula</th>
            <th>Sexo</th>
            <th>Salario</th>
            <th>Fecha de Nacimiento</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($colaboradores)): ?>
            <?php foreach ($colaboradores as $colaborador): ?>
                <tr>
                    <td><?= htmlspecialchars($colaborador['id']); ?></td>
                    <td><?= htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['segundo_nombre']); ?></td>
                    <td><?= htmlspecialchars($colaborador['primer_apellido'] . ' ' . $colaborador['segundo_apellido']); ?></td>
                    <td><?= htmlspecialchars($colaborador['cedula']); ?></td>
                    <td><?= htmlspecialchars($colaborador['sexo']); ?></td>
                    <td>$<?= htmlspecialchars(number_format($colaborador['sueldo'], 2)); ?></td>
                    <td><?= htmlspecialchars($colaborador['fecha_nacimiento']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">No se encontraron resultados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Paginación -->
<div class="d-flex justify-content-between">
    <div>
        <span>Página <?= $paginaActual ?> de <?= $totalPaginas ?></span>
    </div>
    <div>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => max(1, $paginaActual - 1)])) ?>" class="btn btn-primary">Anterior</a>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => min($totalPaginas, $paginaActual + 1)])) ?>" class="btn btn-primary">Siguiente</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Mostrar mensaje de error si no se aplican filtros -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '<?= $_SESSION['mensaje']['texto']; ?>',
        });
    </script>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php include 'views/layouts/footer.php'; ?>
