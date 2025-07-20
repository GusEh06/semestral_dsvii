<?php include 'views/layouts/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($_SESSION['mensaje'])): ?>
<script>
Swal.fire({
    icon: '<?= $_SESSION['mensaje']['tipo']; ?>',
    title: '<?= $_SESSION['mensaje']['tipo'] === 'success' ? 'Éxito' : 'Error'; ?>',
    text: '<?= $_SESSION['mensaje']['texto']; ?>',
    timer: 3000,
    showConfirmButton: false
});
</script>
<?php endif; ?>

<h1 class="mt-4">Nuevo Colaborador</h1>

<form action="colaboradores.php?accion=store" method="POST" enctype="multipart/form-data" class="card p-4">

    <h5 class="mb-3">Datos Personales</h5>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="primer_nombre" class="form-label">Primer Nombre</label>
            <input type="text" name="primer_nombre" id="primer_nombre" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['primer_nombre'] ?? '') ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
            <input type="text" name="segundo_nombre" id="segundo_nombre" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['segundo_nombre'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label for="primer_apellido" class="form-label">Primer Apellido</label>
            <input type="text" name="primer_apellido" id="primer_apellido" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['primer_apellido'] ?? '') ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
            <input type="text" name="segundo_apellido" id="segundo_apellido" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['segundo_apellido'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label for="sexo" class="form-label">Sexo</label>
            <select name="sexo" id="sexo" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="M" <?= ($_SESSION['old']['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= ($_SESSION['old']['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label for="cedula" class="form-label">Cédula</label>
            <input type="text" name="cedula" id="cedula" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['cedula'] ?? '') ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['fecha_nacimiento'] ?? '') ?>" required>
        </div>
    </div>

    <h5 class="mb-3">Contacto</h5>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['telefono'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label for="celular" class="form-label">Celular</label>
            <input type="text" name="celular" id="celular" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['celular'] ?? '') ?>">
        </div>
        <div class="col-md-12 mb-3">
            <label for="correo_personal" class="form-label">Correo Personal</label>
            <input type="email" name="correo_personal" id="correo_personal" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['correo_personal'] ?? '') ?>">
        </div>
        <div class="col-md-12 mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea name="direccion" id="direccion" class="form-control" rows="2"><?= htmlspecialchars($_SESSION['old']['direccion'] ?? '') ?></textarea>
        </div>
    </div>

    <h5 class="mb-3">Datos Laborales</h5>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="sueldo" class="form-label">Sueldo ($)</label>
            <input type="number" name="sueldo" id="sueldo" step="0.01" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['sueldo'] ?? '') ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label for="departamento_id" class="form-label">Departamento</label>
            <select name="departamento_id" id="departamento_id" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="1" <?= ($_SESSION['old']['departamento_id'] ?? '') == 1 ? 'selected' : '' ?>>Recursos Humanos</option>
                <option value="2" <?= ($_SESSION['old']['departamento_id'] ?? '') == 2 ? 'selected' : '' ?>>Administración</option>
                <option value="3" <?= ($_SESSION['old']['departamento_id'] ?? '') == 3 ? 'selected' : '' ?>>Finanzas</option>
                <option value="4" <?= ($_SESSION['old']['departamento_id'] ?? '') == 4 ? 'selected' : '' ?>>Tecnología</option>
                <option value="5" <?= ($_SESSION['old']['departamento_id'] ?? '') == 5 ? 'selected' : '' ?>>Operaciones</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label for="fecha_contratacion" class="form-label">Fecha de Contratación</label>
            <input type="date" name="fecha_contratacion" id="fecha_contratacion" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['fecha_contratacion'] ?? '') ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="tipo_empleado" class="form-label">Tipo Empleado</label>
            <select name="tipo_empleado" id="tipo_empleado" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="Permanente" <?= ($_SESSION['old']['tipo_empleado'] ?? '') === 'Permanente' ? 'selected' : '' ?>>Permanente</option>
                <option value="Eventual" <?= ($_SESSION['old']['tipo_empleado'] ?? '') === 'Eventual' ? 'selected' : '' ?>>Eventual</option>
                <option value="Interno" <?= ($_SESSION['old']['tipo_empleado'] ?? '') === 'Interno' ? 'selected' : '' ?>>Interno</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="ocupacion" class="form-label">Ocupación</label>
            <input type="text" name="ocupacion" id="ocupacion" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['ocupacion'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label for="cargo_actual_id" class="form-label">Cargo Inicial</label>
            <select name="cargo_actual_id" id="cargo_actual_id" class="form-select" required>
                <option value="">Seleccione un cargo</option>
                <?php foreach ($cargos as $cargo): ?>
                    <option value="<?= $cargo['id']; ?>"
                        <?= ($_SESSION['old']['cargo_actual_id'] ?? '') == $cargo['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cargo['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <h5 class="mb-3">Archivos</h5>

    <div class="mb-3">
        <label for="foto_perfil" class="form-label">Foto de Perfil (JPG/PNG, máx 2MB)</label>
        <input type="file" name="foto_perfil" id="foto_perfil" class="form-control" accept=".jpg,.jpeg,.png">
    </div>

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="toggleHistorial"
               <?= isset($_SESSION['old']['tipo_documento']) ? 'checked' : '' ?>>
        <label class="form-check-label" for="toggleHistorial">
            ¿Desea subir historial académico?
        </label>
    </div>

    <div id="historialAcademicoCampos" style="<?= isset($_SESSION['old']['tipo_documento']) ? 'display:block;' : 'display:none;' ?>">
        <div class="mb-3">
            <label for="tipo_documento" class="form-label">Tipo de Documento</label>
            <select name="tipo_documento" id="tipo_documento" class="form-select">
                <option value="">Seleccione...</option>
                <option value="Diploma" <?= ($_SESSION['old']['tipo_documento'] ?? '') === 'Diploma' ? 'selected' : '' ?>>Diploma</option>
                <option value="Certificado" <?= ($_SESSION['old']['tipo_documento'] ?? '') === 'Certificado' ? 'selected' : '' ?>>Certificado</option>
                <option value="Titulo" <?= ($_SESSION['old']['tipo_documento'] ?? '') === 'Titulo' ? 'selected' : '' ?>>Título</option>
                <option value="Transcript" <?= ($_SESSION['old']['tipo_documento'] ?? '') === 'Transcript' ? 'selected' : '' ?>>Transcript</option>
                <option value="Otro" <?= ($_SESSION['old']['tipo_documento'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="nombre_documento" class="form-label">Nombre del Documento</label>
            <input type="text" name="nombre_documento" id="nombre_documento" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['nombre_documento'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="institucion" class="form-label">Institución</label>
            <input type="text" name="institucion" id="institucion" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['institucion'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="fecha_emision" class="form-label">Fecha de Emisión</label>
            <input type="date" name="fecha_emision" id="fecha_emision" class="form-control"
                   value="<?= htmlspecialchars($_SESSION['old']['fecha_emision'] ?? '') ?>">
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar
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

<?php unset($_SESSION['old']); ?>
<?php include 'views/layouts/footer.php'; ?>
