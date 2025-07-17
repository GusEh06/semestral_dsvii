<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Usuarios Administrativos</h1>

<a href="usuarios.php?accion=crear" class="btn btn-primary mb-3">Nuevo Usuario</a>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?= htmlspecialchars($usuario['id']); ?></td>
            <td><?= htmlspecialchars($usuario['username']); ?></td>
            <td><?= htmlspecialchars($usuario['email']); ?></td>
            <td><?= htmlspecialchars($usuario['rol_nombre']); ?></td>
            <td><?= $usuario['activo'] ? 'Activo' : 'Inactivo'; ?></td>
            <td>
                <?php if ($usuario['activo']): ?>
                    <button onclick="confirmarDesactivar(<?= $usuario['id']; ?>)" class="btn btn-warning btn-sm">Desactivar</button>
                <?php else: ?>
                    <button onclick="confirmarActivar(<?= $usuario['id']; ?>)" class="btn btn-success btn-sm">Activar</button>
                <?php endif; ?>
                <a href="usuarios.php?accion=editar&id=<?= $usuario['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
// Confirmación para desactivar
function confirmarDesactivar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este usuario será desactivado y no podrá iniciar sesión.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'usuarios.php?accion=desactivar&id=' + id;
        }
    });
}

// Confirmación para activar
function confirmarActivar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este usuario será activado y podrá iniciar sesión.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'usuarios.php?accion=activar&id=' + id;
        }
    });
}

// Mensajes SweetAlert2
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
