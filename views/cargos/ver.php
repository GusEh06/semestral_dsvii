<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Detalle del Movimiento</h1>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-user-tag"></i> Información del Movimiento
    </div>
    <div class="card-body">
        <p><strong>Colaborador:</strong> <?= htmlspecialchars($cargo['colaborador_nombre']) . ' (' . htmlspecialchars($cargo['colaborador_cedula']) . ')'; ?></p>

        <p><strong>Cargo Anterior:</strong> 
            <?= isset($cargo['cargo_anterior']) ? htmlspecialchars($cargo['cargo_anterior']) : '<span class="text-muted">Sin datos previos disponibles</span>'; ?>
        </p>
        <p><strong>Cargo Nuevo:</strong> <?= htmlspecialchars($cargo['cargo_nombre']); ?></p>

        <p><strong>Sueldo Anterior:</strong> 
            <?= isset($cargo['sueldo_anterior']) ? '$' . number_format($cargo['sueldo_anterior'], 2) : '<span class="text-muted">Sin datos previos disponibles</span>'; ?>
        </p>
        <p><strong>Sueldo Nuevo:</strong> $<?= htmlspecialchars(number_format($cargo['sueldo_nuevo'], 2)); ?></p>

        <p><strong>Departamento Anterior:</strong> 
            <?= isset($cargo['departamento_anterior']) ? htmlspecialchars($cargo['departamento_anterior']) : '<span class="text-muted">Sin datos previos disponibles</span>'; ?>
        </p>
        <p><strong>Departamento Nuevo:</strong> <?= htmlspecialchars($cargo['departamento_nombre']); ?></p>

        <p><strong>Tipo de Movimiento:</strong> <?= htmlspecialchars($cargo['tipo_movimiento']); ?></p>
        <p><strong>Fecha Efectiva:</strong> <?= htmlspecialchars($cargo['fecha_efectiva']); ?></p>
        <p><strong>Motivo:</strong> <?= htmlspecialchars($cargo['motivo'] ?: 'No especificado'); ?></p>
        <p><strong>Registrado Por Usuario ID:</strong> <?= htmlspecialchars($cargo['usuario_registro_id'] ?? 'Desconocido'); ?></p>
        <p><strong>Fecha de Registro:</strong> <?= htmlspecialchars($cargo['created_at']); ?></p>

        <p><strong>Validación de Firma:</strong>
            <?php if ($firmaValida): ?>
                <span class="badge bg-success">Firma válida</span>
            <?php else: ?>
                <span class="badge bg-danger">Firma corrupta</span>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="mt-4">
    <a href="cargos.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<?php include 'views/layouts/footer.php'; ?>
