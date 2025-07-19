<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/database.php';
// =====================================================
// middleware/auth_middleware.php - Middleware de Autenticación
// =====================================================

function requireLogin()
{
    $auth = new AuthController();

    if (!$auth->estaLogueado() || !$auth->verificarTimeout()) {
        header('Location: /login.php');
        exit();
    }
}

function requirePermission($permiso)
{
    $auth = new AuthController();

    if (!$auth->tienePermiso($permiso)) {
        http_response_code(403);
        include 'views/403.php';
        exit();
    }
}
