<?php
require_once 'controllers/ReportesController.php';

$controller = new ReportesController();

$accion = $_GET['accion'] ?? 'index';

switch ($accion) {
    case 'exportar':
        $controller->exportarExcel();
        break;
    case 'index':
    default:
        $controller->index();
        break;
}
?>
