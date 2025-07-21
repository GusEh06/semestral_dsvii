<?php
require_once 'models/Estadistica.php';
require_once 'controllers/AuthController.php';

class EstadisticasController
{
    private $estadistica;
    private $auth;

    public function __construct()
    {
        $this->estadistica = new Estadistica();
        $this->auth = new AuthController();
    }

    public function index()
    {
        // Verificar permisos
        if (!$this->auth->tienePermiso('estadisticas.acceso')) {
            http_response_code(403);
            include 'views/layouts/403.php';
            return;
        }

        // Obtener datos estadísticos
        $porSexo = $this->estadistica->colaboradoresPorSexo();
        $porEdad = $this->estadistica->colaboradoresPorRangoEdad();
        $colaboradoresDireccion = $this->estadistica->colaboradoresPorDireccion();

        // Incluir la vista y pasar las variables
        include 'views/estadistica/index.php';
    }
}
