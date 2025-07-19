<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Detalle del Usuario</h1>

<div class="card p-4">
    <p><strong>ID:</strong> <?= htmlspecialchars($usuario['id']); ?></p>
    <p><strong>Usuario:</strong> <?= htmlspecialchars($usuario['username']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']); ?></p>
    <p><strong>Rol:</strong> <?= htmlspecialchars($usuario['rol_nombre']); ?></p>
    <p><strong>Estado:</strong> <?= $usuario['activo'] ? 'Activo' : 'Inactivo'; ?></p>
</div>

<a href="usuarios.php" class="btn btn-secondary mt-3">Volver</a>

<?php include 'views/layouts/footer.php'; ?>
