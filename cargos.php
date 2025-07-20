<?php
require_once 'controllers/CargosController.php';

$controller = new CargosController();

$accion = $_GET['accion'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($accion) {
    case 'crear':
        $controller->crear();
        break;
    case 'store':
        $controller->store();
        break;
    case 'ver':
        $controller->ver($id);
        break;
    default:
        $controller->index();
        break;
}
?>
