<?php
require_once 'middleware/auth_middleware.php';
requireLogin();

$auth = new AuthController();
$usuario = $auth->obtenerUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Capital Humano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-users me-2"></i>Capital Humano
        </a>
        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>
                    <?= htmlspecialchars($usuario['username']) ?>
                    <small class="text-light">(<?= htmlspecialchars($usuario['rol_nombre']) ?>)</small>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user-edit me-2"></i>Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h2>Bienvenido al Dashboard</h2>
            <div class="row mt-4">

                <?php if ($auth->tienePermiso('usuarios.acceso')): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-user-cog fa-3x text-warning mb-3"></i>
                                <h5>Usuarios</h5>
                                <a href="usuarios.php" class="btn btn-warning">Administrar</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($auth->tienePermiso('colaboradores.acceso')): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h5>Colaboradores</h5>
                                <a href="colaboradores.php" class="btn btn-primary">Gestionar</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($auth->tienePermiso('vacaciones.acceso')): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                                <h5>Vacaciones</h5>
                                <a href="vacaciones.php" class="btn btn-info">Gestionar</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($auth->tienePermiso('cargos.acceso')): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card border-secondary">
                            <div class="card-body text-center">
                                <i class="fas fa-briefcase fa-3x text-secondary mb-3"></i>
                                <h5>Cargos / Movimientos</h5>
                                <a href="cargos.php" class="btn btn-secondary">Gestionar</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($auth->tienePermiso('reportes.acceso')): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                                <h5>Reportes</h5>
                                <a href="reportes.php" class="btn btn-success">Ver Reportes</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($auth->tienePermiso('estadisticas.acceso')): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card border-dark">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-pie fa-3x text-dark mb-3"></i>
                                <h5>Estadísticas</h5>
                                <a href="estadisticas.php" class="btn btn-dark">Ver Estadísticas</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
