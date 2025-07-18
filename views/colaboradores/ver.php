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
            </div>
            <div class="col-md-9">
                <p><strong>Nombre:</strong> <?= htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['primer_apellido']); ?></p>
                <p><strong>Cédula:</strong> <?= htmlspecialchars($colaborador['cedula']); ?></p>
                <p><strong>Correo:</strong> <?= htmlspecialchars($colaborador['correo_personal']); ?></p>
                <p><strong>Estado:</strong> <?= $colaborador['empleado_activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>'; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-file-pdf"></i> Historial Académico
    </div>
    <div class="card-body">
        <?php if (!empty($documentos)): ?>
            <ul class="list-group">
                <?php foreach ($documentos as $doc): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($doc['nombre_documento']); ?></strong><br>
                            <small><?= htmlspecialchars($doc['tipo_documento']); ?> - <?= htmlspecialchars($doc['institucion']); ?></small>
                        </div>
                        <div>
                            <a href="<?= htmlspecialchars($doc['archivo_pdf']); ?>" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="<?= htmlspecialchars($doc['archivo_pdf']); ?>" download class="btn btn-sm btn-secondary">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                            <button onclick="confirmarEliminarDoc(<?= $doc['id']; ?>)" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No hay documentos académicos registrados.</p>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4">
    <a href="colaboradores.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<script>
function confirmarEliminarDoc(docId) {
    Swal.fire({
        title: '¿Eliminar documento?',
        text: "Esta acción eliminará permanentemente el PDF.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'documentos.php?accion=eliminar&id=' + docId;
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
