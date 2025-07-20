<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Detalle del Colaborador</h1>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-user"></i> Información General
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center">
                <?php if (!empty($colaborador['foto_perfil'])): ?>
                    <img src="<?= htmlspecialchars($colaborador['foto_perfil']); ?>" class="img-thumbnail mb-3" style="width: 150px; height: 150px;">
                <?php else: ?>
                    <img src="assets/img/default-avatar.png" class="img-thumbnail mb-3" style="width: 150px; height: 150px;">
                <?php endif; ?>
                <div class="mt-2">
                    <span class="badge <?= $colaborador['empleado_activo'] ? 'bg-success' : 'bg-secondary'; ?>">
                        <?= $colaborador['empleado_activo'] ? 'Activo' : 'Inactivo'; ?>
                    </span>
                    <span class="badge bg-info text-dark">
                        <?= htmlspecialchars($colaborador['tipo_empleado']); ?>
                    </span>
                </div>
            </div>
            <div class="col-md-9">
                <!-- Datos Personales -->
                <h5><i class="fas fa-id-card me-2"></i>Datos Personales</h5>
                <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['segundo_nombre'] . ' ' . $colaborador['primer_apellido'] . ' ' . $colaborador['segundo_apellido']); ?></p>
                <p><strong>Cédula:</strong> <?= htmlspecialchars($colaborador['cedula']); ?></p>
                <p><strong>Sexo:</strong> <?= $colaborador['sexo'] === 'M' ? 'Masculino' : 'Femenino'; ?></p>
                <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($colaborador['fecha_nacimiento']); ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($colaborador['direccion']); ?></p>
                <hr>

                <!-- Información de Contacto -->
                <h5><i class="fas fa-phone-alt me-2"></i>Información de Contacto</h5>
                <p><strong>Correo Personal:</strong> <?= htmlspecialchars($colaborador['correo_personal']); ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($colaborador['telefono']); ?></p>
                <p><strong>Celular:</strong> <?= htmlspecialchars($colaborador['celular']); ?></p>
                <hr>

                <!-- Datos Laborales -->
                <h5><i class="fas fa-briefcase me-2"></i>Datos Laborales</h5>
                <p><strong>Departamento:</strong> <?= htmlspecialchars($colaborador['departamento_nombre'] ?? 'Sin asignar'); ?></p>
                <p><strong>Cargo Actual:</strong> <?= htmlspecialchars($colaborador['cargo_nombre'] ?? 'Sin asignar'); ?></p>
                <p><strong>Ocupación:</strong> <?= htmlspecialchars($colaborador['ocupacion']); ?></p>
                <p><strong>Sueldo:</strong> <span class="badge bg-primary">$<?= htmlspecialchars(number_format($colaborador['sueldo'], 2)); ?></span></p>
                <p><strong>Fecha de Contratación:</strong> <?= htmlspecialchars($colaborador['fecha_contratacion']); ?></p>
                <p><strong>Días Vacaciones Acumulados:</strong> <?= htmlspecialchars($colaborador['dias_vacaciones_acumulados']); ?> días</p>
                <p><strong>Última Actualización Vacaciones:</strong> <?= htmlspecialchars($colaborador['ultima_actualizacion_vacaciones'] ?? 'Sin registro'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="colaboradores.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<?php include 'views/layouts/footer.php'; ?>
