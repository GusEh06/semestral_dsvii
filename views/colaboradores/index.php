<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Colaboradores</h1>

<a href="colaboradores.php?accion=crear" class="btn btn-primary mb-3">
    <i class="fas fa-user-plus"></i> Nuevo Colaborador
</a>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Correo</th>
            <th>Departamento</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($colaboradores as $col): ?>
        <tr>
            <td><?= htmlspecialchars($col['id']); ?></td>
            <td><?= htmlspecialchars($col['primer_nombre'] . ' ' . $col['primer_apellido']); ?></td>
            <td><?= htmlspecialchars($col['cedula']); ?></td>
            <td><?= htmlspecialchars($col['correo_personal']); ?></td>
            <td><?= htmlspecialchars($col['departamento_id']); ?></td>
            <td><?= $col['empleado_activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>'; ?></td>
            <td>
                <?php if ($col['empleado_activo']): ?>
                    <button onclick="confirmarDesactivar(<?= $col['id']; ?>)" class="btn btn-warning btn-sm">
                        <i class="fas fa-user-slash"></i> Desactivar
                    </button>
                <?php else: ?>
                    <button onclick="confirmarActivar(<?= $col['id']; ?>)" class="btn btn-success btn-sm">
                        <i class="fas fa-user-check"></i> Activar
                    </button>
                <?php endif; ?>
                <a href="colaboradores.php?accion=editar&id=<?= $col['id']; ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="colaboradores.php?accion=ver&id=<?= $col['id']; ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> Ver
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function confirmarDesactivar(id) {
    Swal.fire({
        title: '¿Desactivar colaborador?',
        text: "No podrá iniciar sesión ni aparecerá en listados activos.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'colaboradores.php?accion=desactivar&id=' + id;
        }
    });
}

function confirmarActivar(id) {
    Swal.fire({
        title: '¿Activar colaborador?',
        text: "El colaborador podrá iniciar sesión y aparecerá en los listados activos.",
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'colaboradores.php?accion=activar&id=' + id;
        }
    });
}

// Mensajes flash SweetAlert2
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
