<?php
// =====================================================
// colaboradores.php - Archivo principal de routing
// =====================================================

require_once 'config/database.php';
require_once 'models/Colaborador.php';
require_once 'controllers/ColaboradoresController.php';
require_once 'middleware/auth_middleware.php';

// Verificar login
requireLogin();

$controller = new ColaboradoresController();

// Routing simple
if (isset($_GET['crear'])) {
    $controller->crear();
} elseif (isset($_GET['ver'])) {
    $controller->ver($_GET['ver']);
} elseif (isset($_GET['editar'])) {
    $controller->editar($_GET['editar']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['_method']) && $_POST['_method'] === 'PUT' && isset($_POST['id'])) {
        $controller->actualizar($_POST['id']);
    } elseif (isset($_POST['_method']) && $_POST['_method'] === 'DELETE' && isset($_POST['id'])) {
        $controller->desactivar($_POST['id']);
    } else {
        $controller->store();
    }
} else {
    $controller->index();
}
