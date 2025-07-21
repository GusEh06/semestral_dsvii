<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Vacaciones del Personal</h1>

<a href="vacaciones.php?accion=crear" class="btn btn-primary mb-3">Nueva Solicitud de Vacaciones</a>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Empleado</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Días Solicitados</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($vacaciones as $vacacion): ?>
        <tr>
            <td><?= htmlspecialchars($vacacion['id']); ?></td>
            <td><?= htmlspecialchars($vacacion['colaborador_nombre']); ?></td>
            <td><?= htmlspecialchars($vacacion['fecha_inicio']); ?></td>
            <td><?= htmlspecialchars($vacacion['fecha_fin']); ?></td>
            <td><?= htmlspecialchars($vacacion['dias_solicitados']); ?></td>
            <td><?= htmlspecialchars($vacacion['estado']); ?></td>
            <td>
                <a href="vacaciones.php?accion=ver&id=<?= $vacacion['id']; ?>" class="btn btn-info btn-sm">Ver</a>
                 <a href="vacaciones.php?accion=verPDF&id=<?= $vacacion['id']; ?>" target="_blank" class="btn btn-secondary btn-sm">Ver PDF</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
<?php if (!empty($_SESSION['mensaje'])): ?>
Swal.fire({
    icon: '<?= $_SESSION['mensaje']['tipo']; ?>',
    title: '<?= $_SESSION['mensaje']['tipo'] === 'success' ? 'Éxito' : 'Error'; ?>',
    text: '<?= $_SESSION['mensaje']['texto']; ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>
</script>

<?php include 'views/layouts/footer.php'; ?>
