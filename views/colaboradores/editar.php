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

    <h5 class="mb-3">Contacto</h5>

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

    <h5 class="mb-3">Datos Laborales</h5>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="sueldo" class="form-label">Sueldo ($)</label>
            <input type="number" name="sueldo" id="sueldo" step="0.01" class="form-control" value="<?= htmlspecialchars($colaborador['sueldo']); ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label for="departamento_id" class="form-label">Departamento</label>
            <select name="departamento_id" id="departamento_id" class="form-select" required>
                <option value="1" <?= $colaborador['departamento_id'] == 1 ? 'selected' : ''; ?>>Recursos Humanos</option>
                <option value="2" <?= $colaborador['departamento_id'] == 2 ? 'selected' : ''; ?>>Administración</option>
                <option value="3" <?= $colaborador['departamento_id'] == 3 ? 'selected' : ''; ?>>Finanzas</option>
                <option value="4" <?= $colaborador['departamento_id'] == 4 ? 'selected' : ''; ?>>Tecnología</option>
                <option value="5" <?= $colaborador['departamento_id'] == 5 ? 'selected' : ''; ?>>Operaciones</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label for="fecha_contratacion" class="form-label">Fecha de Contratación</label>
            <input type="date" name="fecha_contratacion" id="fecha_contratacion" class="form-control" value="<?= htmlspecialchars($colaborador['fecha_contratacion']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="tipo_empleado" class="form-label">Tipo Empleado</label>
            <select name="tipo_empleado" id="tipo_empleado" class="form-select" required>
                <option value="Permanente" <?= $colaborador['tipo_empleado'] === 'Permanente' ? 'selected' : ''; ?>>Permanente</option>
                <option value="Eventual" <?= $colaborador['tipo_empleado'] === 'Eventual' ? 'selected' : ''; ?>>Eventual</option>
                <option value="Interno" <?= $colaborador['tipo_empleado'] === 'Interno' ? 'selected' : ''; ?>>Interno</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="ocupacion" class="form-label">Ocupación</label>
            <input type="text" name="ocupacion" id="ocupacion" class="form-control" value="<?= htmlspecialchars($colaborador['ocupacion']); ?>">
        </div>
    </div>

    <h5 class="mb-3">Archivos</h5>

    <div class="mb-3">
        <label for="foto_perfil" class="form-label">Cambiar Foto de Perfil (JPG/PNG, máx 2MB)</label>
        <input type="file" name="foto_perfil" id="foto_perfil" class="form-control" accept=".jpg,.jpeg,.png">
        <?php if ($colaborador['foto_perfil']): ?>
            <p class="mt-2">
                Foto actual: <img src="<?= htmlspecialchars($colaborador['foto_perfil']); ?>" class="img-thumbnail" style="width: 80px; height: 80px;">
            </p>
        <?php endif; ?>
    </div>

    <h5 class="mb-3">Historial Académico Actual</h5>
    <?php if (!empty($documentos)): ?>
        <ul class="list-group mb-3">
            <?php foreach ($documentos as $doc): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($doc['nombre_documento']); ?>
                    <div>
                        <a href="<?= htmlspecialchars($doc['archivo_pdf']); ?>" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="<?= htmlspecialchars($doc['archivo_pdf']); ?>" download class="btn btn-sm btn-secondary">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No hay documentos académicos subidos.</p>
    <?php endif; ?>

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="toggleHistorial">
        <label class="form-check-label" for="toggleHistorial">
            ¿Desea añadir nuevo historial académico?
        </label>
    </div>

    <div id="historialAcademicoCampos" style="display:none;">
        <!-- Mismos campos adicionales que en crear.php -->
        <div class="mb-3">
            <label for="tipo_documento" class="form-label">Tipo de Documento</label>
            <select name="tipo_documento" id="tipo_documento" class="form-select">
                <option value="">Seleccione...</option>
                <option value="Diploma">Diploma</option>
                <option value="Certificado">Certificado</option>
                <option value="Titulo">Título</option>
                <option value="Transcript">Transcript</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="nombre_documento" class="form-label">Nombre del Documento</label>
            <input type="text" name="nombre_documento" id="nombre_documento" class="form-control">
        </div>
        <div class="mb-3">
            <label for="institucion" class="form-label">Institución</label>
            <input type="text" name="institucion" id="institucion" class="form-control">
        </div>
        <div class="mb-3">
            <label for="fecha_emision" class="form-label">Fecha de Emisión</label>
            <input type="date" name="fecha_emision" id="fecha_emision" class="form-control">
        </div>
        <div class="mb-3">
            <label for="archivo_pdf" class="form-label">Archivo PDF (máx 5MB)</label>
            <input type="file" name="archivo_pdf" id="archivo_pdf" class="form-control" accept=".pdf">
        </div>
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

<script>
// Mostrar/ocultar campos de historial académico
document.getElementById('toggleHistorial').addEventListener('change', function() {
    const campos = document.getElementById('historialAcademicoCampos');
    campos.style.display = this.checked ? 'block' : 'none';
});
</script>

<?php include 'views/layouts/footer.php'; ?>
