<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Editar Colaborador</h1>

<form action="colaboradores.php?accion=update&id=<?= $colaborador['id']; ?>" method="POST" enctype="multipart/form-data" class="card p-4">

    <h5 class="mb-3">Datos Personales</h5>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="primer_nombre" class="form-label">Primer Nombre</label>
            <input type="text" name="primer_nombre" id="primer_nombre" class="form-control" value="<?= htmlspecialchars($colaborador['primer_nombre']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
            <input type="text" name="segundo_nombre" id="segundo_nombre" class="form-control" value="<?= htmlspecialchars($colaborador['segundo_nombre']); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label for="primer_apellido" class="form-label">Primer Apellido</label>
            <input type="text" name="primer_apellido" id="primer_apellido" class="form-control" value="<?= htmlspecialchars($colaborador['primer_apellido']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
            <input type="text" name="segundo_apellido" id="segundo_apellido" class="form-control" value="<?= htmlspecialchars($colaborador['segundo_apellido']); ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label for="sexo" class="form-label">Sexo</label>
            <select name="sexo" id="sexo" class="form-select" required>
                <option value="M" <?= $colaborador['sexo'] === 'M' ? 'selected' : ''; ?>>Masculino</option>
                <option value="F" <?= $colaborador['sexo'] === 'F' ? 'selected' : ''; ?>>Femenino</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label for="cedula" class="form-label">Cédula</label>
            <input type="text" name="cedula" id="cedula" class="form-control" value="<?= htmlspecialchars($colaborador['cedula']); ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($colaborador['fecha_nacimiento']); ?>" required>
        </div>
    </div>

    <h5 class="mb-3">Información de Contacto</h5>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control" value="<?= htmlspecialchars($colaborador['telefono']); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label for="celular" class="form-label">Celular</label>
            <input type="text" name="celular" id="celular" class="form-control" value="<?= htmlspecialchars($colaborador['celular']); ?>">
        </div>
        <div class="col-md-12 mb-3">
            <label for="correo_personal" class="form-label">Correo Personal</label>
            <input type="email" name="correo_personal" id="correo_personal" class="form-control" value="<?= htmlspecialchars($colaborador['correo_personal']); ?>">
        </div>
        <div class="col-md-12 mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea name="direccion" id="direccion" class="form-control" rows="2"><?= htmlspecialchars($colaborador['direccion']); ?></textarea>
        </div>
    </div>

    <h5 class="mb-3">Foto de Perfil</h5>
    <div class="mb-3">
        <input type="file" name="foto_perfil" id="foto_perfil" class="form-control" accept=".jpg,.jpeg,.png">
        <?php if ($colaborador['foto_perfil']): ?>
            <p class="mt-2">
                Foto actual: <img src="<?= htmlspecialchars($colaborador['foto_perfil']); ?>" class="img-thumbnail" style="width: 80px; height: 80px;">
            </p>
        <?php endif; ?>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-lock"></i> Los datos laborales no pueden ser modificados desde esta vista.
    </div>

    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Actualizar
        </button>
        <a href="colaboradores.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
    </div>
</form>

<?php include 'views/layouts/footer.php'; ?>
