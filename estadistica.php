<?php
require_once 'controllers/EstadisticaController.php';
require_once 'middleware/auth_middleware.php';

// Proteger todas las rutas: solo usuarios logueados
requireLogin();

$controller = new EstadisticasController();
$accion = $_GET['accion'] ?? 'index';

switch ($accion) {
    case 'index':
        $controller->index();
        break;

    // case 'detalle':
    //     $controller->detalle();
    //     break;

    default:
        $controller->index();
        break;
}
