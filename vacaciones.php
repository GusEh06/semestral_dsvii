<?php
require_once 'controllers/VacacionesController.php';
require_once 'middleware/auth_middleware.php';

// Proteger todas las rutas: solo usuarios logueados
requireLogin();

$controller = new VacacionesController();
$accion = $_GET['accion'] ?? 'index';
$id = $_GET['id'] ?? null;
$pagina = $_GET['pagina'] ?? 1;

switch ($accion) {
    case 'index':
        $controller->index($pagina);
        break;

    case 'crear':
        $controller->crear();
        break;

    case 'store':
        $controller->store();
        break;

    case 'ver':
        if ($id) {
            $controller->ver($id);
        } else {
            header('Location: vacaciones.php');
        }
        break;
    
    case 'verPDF':
        if ($id) {
            $controller->verPDF($id);
        }
        break;

    case 'procesar':
        if ($id) {
            $controller->procesar($id);
        } else {
            header('Location: vacaciones.php');
        }
        break;

    default:
        $controller->index($pagina);
        break;
}
