<?php
require_once 'controllers/ColaboradoresController.php';
require_once 'middleware/auth_middleware.php';

requireLogin();

$controller = new ColaboradoresController();
$accion = $_GET['accion'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($accion) {
    case 'index':
        $controller->index();
        break;

    case 'crear':
        $controller->crear();
        break;

    case 'store':
        $controller->store();
        break;

    case 'editar':
        if ($id) {
            $controller->editar($id);
        } else {
            header('Location: colaboradores.php');
        }
        break;

    case 'update':
        if ($id) {
            $controller->update($id);
        } else {
            header('Location: colaboradores.php');
        }
        break;

    case 'ver':
        if ($id) {
            $controller->ver($id);
        } else {
            header('Location: colaboradores.php');
        }
        break;

    case 'desactivar':
        if ($id) {
            $controller->desactivar($id);
        } else {
            header('Location: colaboradores.php');
        }
        break;

    case 'activar':
        if ($id) {
            $controller->activar($id);
        } else {
            header('Location: colaboradores.php');
        }
        break;

    default:
        $controller->index();
        break;
}
