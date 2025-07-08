<?php
// =====================================================
// logout.php - Cerrar Sesión
// =====================================================

require_once 'controllers/AuthController.php';

$auth = new AuthController();
$result = $auth->logout();

header('Location: login.php?message=logout_success');
exit();
?>

<?php
// =====================================================
// views/403.php - Página de Acceso Denegado
// =====================================================
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                        <h2>Acceso Denegado</h2>
                        <p class="text-muted">No tienes permisos para acceder a esta sección.</p>
                        <a href="dashboard.php" class="btn btn-primary">Volver al Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>