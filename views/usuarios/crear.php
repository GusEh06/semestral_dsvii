<?php include 'views/layouts/header.php';?>

<h1 class="mt-4">Crear Nuevo Usuario</h1>

<form action="usuarios.php?accion=store" method="POST" class="card p-4">
    <div class="mb-3">
        <label for="username" class="form-label">Usuario</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" name="email" id="email" class="form-control">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="rol_id" class="form-label">Rol</label>
        <select name="rol_id" id="rol_id" class="form-select" required>
            <option value="">Seleccione un rol</option>
            <?php foreach ($roles as $rol): ?>
                <?php   if ($rol['id'] != 1 || $_SESSION['rol_nombre'] === 'Super_Admin'): ?>
                    <option value="<?= $rol['id']; ?>">
                        <?= htmlspecialchars($rol['nombre']); ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="activo" class="form-label">Estado</label>
        <select name="activo" id="activo" class="form-select" required>
            <option value="1" selected>Activo</option>
            <option value="0">Inactivo</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
</form>

<script>
// Mensaje de éxito/error después de crear
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
