<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4">Detalles de Solicitud de Vacaciones</h1>

<a href="vacaciones.php" class="btn btn-secondary mb-3">← Volver al listado</a>

<div class="card mb-4">
    <div class="card-header">
        <strong>Empleado:</strong> <?= htmlspecialchars($solicitud['colaborador_nombre']); ?>
        (<?= htmlspecialchars($solicitud['colaborador_cedula']); ?>) <br>
        <strong>Departamento:</strong> <?= htmlspecialchars($solicitud['departamento']); ?>
    </div>
    <div class="card-body">
        <p><strong>Fecha de Solicitud:</strong> <?= htmlspecialchars($solicitud['fecha_solicitud']); ?></p>
        <p><strong>Fecha Inicio:</strong> <?= htmlspecialchars($solicitud['fecha_inicio']); ?></p>
        <p><strong>Fecha Fin:</strong> <?= htmlspecialchars($solicitud['fecha_fin']); ?></p>
        <p><strong>Días Solicitados:</strong> <?= htmlspecialchars($solicitud['dias_solicitados']); ?></p>
        <p><strong>Días Disponibles al Momento:</strong> <?= htmlspecialchars($solicitud['dias_disponibles_al_momento']); ?></p>
        <p><strong>Estado:</strong> 
            <?php 
                $estado = $solicitud['estado'];
                $clase = '';
                if ($estado === 'Aprobada') $clase = 'text-success';
                else if ($estado === 'Pendiente') $clase = 'text-warning';
                else if ($estado === 'Rechazada') $clase = 'text-danger';
            ?>
            <span class="<?= $clase; ?>"><?= htmlspecialchars($estado); ?></span>
        </p>
        <?php if ($solicitud['estado'] === 'Rechazada'): ?>
            <p><strong>Motivo de Rechazo:</strong> <?= nl2br(htmlspecialchars($solicitud['motivo_rechazo'])); ?></p>
        <?php endif; ?>
        <p><strong>Observaciones:</strong> <?= nl2br(htmlspecialchars($solicitud['observaciones'] ?? 'Ninguna')); ?></p>

        <p><strong>Aprobada por:</strong> <?= htmlspecialchars($solicitud['aprobado_por'] ?? 'N/A'); ?></p>

        <p><strong>Días Disponibles Actualmente:</strong> <?= $dias_disponibles ?? 'N/A'; ?></p>
    </div>
</div>

<?php if ($solicitud['estado'] === 'Pendiente' && $auth->tienePermiso('vacaciones.aprobar')): ?>
    <div class="card">
        <div class="card-header">
            <strong>Procesar Solicitud</strong>
        </div>
        <div class="card-body">
            <form action="vacaciones.php?accion=procesar&id=<?= $solicitud['id']; ?>" method="POST">
                <div class="mb-3">
                    <label for="accion" class="form-label">Acción</label>
                    <select name="accion" id="accion" class="form-select" required>
                        <option value="">Seleccione una acción</option>
                        <option value="aprobar">Aprobar</option>
                        <option value="rechazar">Rechazar</option>
                    </select>
                </div>
                <div class="mb-3" id="motivoRechazoDiv" style="display:none;">
                    <label for="motivo_rechazo" class="form-label">Motivo de Rechazo</label>
                    <textarea name="motivo_rechazo" id="motivo_rechazo" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Procesar</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('accion').addEventListener('change', function() {
            var motivoDiv = document.getElementById('motivoRechazoDiv');
            if (this.value === 'rechazar') {
                motivoDiv.style.display = 'block';
                document.getElementById('motivo_rechazo').setAttribute('required', 'required');
            } else {
                motivoDiv.style.display = 'none';
                document.getElementById('motivo_rechazo').removeAttribute('required');
            }
        });
    </script>
<?php endif; ?>

<?php include 'views/layouts/footer.php'; ?>
