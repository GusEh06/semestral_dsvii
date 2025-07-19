<?php
require_once 'middleware/auth_middleware.php';
requireLogin();

$auth = new AuthController();
$usuarioActual = $auth->obtenerUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capital Humano</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-users me-2"></i>Capital Humano
        </a>

        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>
                    <?= htmlspecialchars($usuarioActual['username']) ?>
                    <small class="text-light">(<?= htmlspecialchars($usuarioActual['rol_nombre']) ?>)</small>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="perfil.php">
                        <i class="fas fa-user-edit me-2"></i>Mi Perfil
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<main class="container-fluid mt-4">
