<?php
require_once 'models/Reporte.php';
require_once 'controllers/AuthController.php';
require_once 'utils/Sanitizador.php';

class ReportesController
{
    private $reporte;
    private $auth;

    public function __construct()
    {
        $this->reporte = new Reporte();
        $this->auth = new AuthController();
    }

    private function verificarAcceso()
    {
        if (!$this->auth->tienePermiso('reportes.acceso')) {
            http_response_code(403);
            include 'views/layouts/403.php';
            exit();
        }
    }

    public function index()
    {
        $this->verificarAcceso();

        // Recoger los parámetros del filtro
        $filtros = [
            'sexo'       => $_GET['sexo'] ?? null,
            'edad'       => $_GET['edad'] ?? null,
            'nombre'     => $_GET['nombre'] ?? null,
            'apellido'   => $_GET['apellido'] ?? null,
            'salario'    => $_GET['salario'] ?? null,
            'pagina'     => $_GET['pagina'] ?? 1,
        ];

        // Número de resultados por página
        $resultadosPorPagina = 10;

        // Obtener los datos filtrados y paginados
        $colaboradores = $this->reporte->obtenerColaboradores($filtros, $resultadosPorPagina);

        // Obtener el total de resultados para la paginación
        $totalResultados = $this->reporte->contarColaboradores($filtros);

        // Calcular el número total de páginas
        $totalPaginas = ceil($totalResultados / $resultadosPorPagina);

        // Incluir la vista
        include 'views/reportes/index.php';
    }

    public function exportarExcel()
    {
        $this->verificarAcceso();

        // Recoger los parámetros del filtro
        $filtros = [
            'sexo'       => $_GET['sexo'] ?? null,
            'edad'       => $_GET['edad'] ?? null,
            'nombre'     => $_GET['nombre'] ?? null,
            'apellido'   => $_GET['apellido'] ?? null,
            'salario'    => $_GET['salario'] ?? null,
        ];

        // Verificar si todos los filtros están vacíos
        if (empty($filtros['sexo']) && empty($filtros['edad']) && empty($filtros['nombre']) && empty($filtros['apellido']) && empty($filtros['salario'])) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Debe aplicar al menos un filtro para generar el reporte.'];
            header('Location: reportes.php'); // Redirige a la página de reportes
            exit();
        }

        // Obtener todos los colaboradores con los filtros aplicados
        $colaboradores = $this->reporte->obtenerColaboradores($filtros, null); // Sin paginación

        // Generar el archivo Excel
        $this->reporte->generarExcel($colaboradores);
    }
}
?>
