<?php
// =====================================================
// controllers/VacacionesController.php - Controlador de Vacaciones
// =====================================================

class VacacionesController
{
    private $vacacion;
    private $auth;

    public function __construct()
    {
        $this->vacacion = new Vacacion();
        $this->auth = new AuthController();
    }

    // Listar solicitudes
    public function index()
    {
        if (!$this->auth->tienePermiso('vacaciones.crear')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $filtros = $_GET;
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $porPagina = 20;

        $resultado = $this->vacacion->obtenerSolicitudes($filtros, $pagina, $porPagina);
        $departamentos = $this->vacacion->obtenerDepartamentos();

        include 'views/vacaciones/index.php';
    }

    // Mostrar formulario de solicitud
    public function crear()
    {
        if (!$this->auth->tienePermiso('vacaciones.crear')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $departamentos = $this->vacacion->obtenerDepartamentos();
        $colaboradores = $this->vacacion->obtenerColaboradores();

        include 'views/vacaciones/crear.php';
    }

    // Procesar solicitud
    public function store()
    {
        if (!$this->auth->tienePermiso('vacaciones.crear')) {
            http_response_code(403);
            return;
        }

        $datos = $_POST;
        $errores = $this->validarDatos($datos);

        if (empty($errores)) {
            $resultado = $this->vacacion->crearSolicitud($datos);

            if ($resultado['success']) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => $resultado['message']];
                header('Location: vacaciones.php?ver=' . $resultado['id']);
                exit();
            } else {
                $errores[] = $resultado['message'];
            }
        }

        $_SESSION['errores'] = $errores;
        $_SESSION['datos_anteriores'] = $datos;
        header('Location: vacaciones.php?accion=crear');
        exit();
    }

    // Ver solicitud
    public function ver($id)
    {
        if (!$this->auth->tienePermiso('vacaciones.crear')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $solicitud = $this->vacacion->obtenerPorId($id);

        if (!$solicitud) {
            http_response_code(404);
            include 'views/404.php';
            return;
        }

        // Calcular días disponibles actuales
        $dias_disponibles = $this->vacacion->calcularDiasDisponibles($solicitud['colaborador_id']);

        include 'views/vacaciones/ver.php';
    }

    // Procesar aprobación/rechazo
    public function procesar($id)
    {
        if (!$this->auth->tienePermiso('vacaciones.aprobar')) {
            http_response_code(403);
            return;
        }

        $accion = $_POST['accion'] ?? '';
        $motivo_rechazo = $_POST['motivo_rechazo'] ?? '';

        if (in_array($accion, ['aprobar', 'rechazar'])) {
            $result = $this->vacacion->procesarSolicitud($id, $accion, $motivo_rechazo, $_SESSION['user_id']);

            if ($result) {
                $mensaje = $accion === 'aprobar' ? 'Solicitud aprobada exitosamente' : 'Solicitud rechazada';
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => $mensaje];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al procesar la solicitud'];
            }
        }

        header('Location: vacaciones.php?ver=' . $id);
        exit();
    }

    // Calendario de vacaciones
    public function calendario()
    {
        if (!$this->auth->tienePermiso('vacaciones.crear')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $año = $_GET['año'] ?? date('Y');
        $mes = $_GET['mes'] ?? null;

        $vacaciones = $this->vacacion->obtenerCalendario($año, $mes);

        include 'views/vacaciones/calendario.php';
    }

    // Estadísticas
    public function estadisticas()
    {
        if (!$this->auth->tienePermiso('vacaciones.crear')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $año = $_GET['año'] ?? date('Y');
        $estadisticas = $this->vacacion->obtenerEstadisticas($año);

        include 'views/vacaciones/estadisticas.php';
    }

    // API para obtener colaboradores por departamento
    public function apiColaboradores()
    {
        header('Content-Type: application/json');

        $departamento_id = $_GET['departamento_id'] ?? null;
        $colaboradores = $this->vacacion->obtenerColaboradores($departamento_id);

        echo json_encode($colaboradores);
    }

    // API para obtener días disponibles
    public function apiDiasDisponibles()
    {
        header('Content-Type: application/json');

        $colaborador_id = $_GET['colaborador_id'] ?? null;
        if (!$colaborador_id) {
            echo json_encode(['error' => 'ID de colaborador requerido']);
            return;
        }

        $dias_disponibles = $this->vacacion->calcularDiasDisponibles($colaborador_id);

        echo json_encode([
            'dias_disponibles' => $dias_disponibles,
            'colaborador_id' => $colaborador_id
        ]);
    }

    // Validar datos del formulario
    private function validarDatos($datos)
    {
        $errores = [];

        // Campos requeridos
        $requeridos = ['colaborador_id', 'fecha_inicio', 'fecha_fin', 'dias_solicitados'];

        foreach ($requeridos as $campo) {
            if (empty($datos[$campo])) {
                $errores[] = "El campo " . ucfirst(str_replace('_', ' ', $campo)) . " es requerido";
            }
        }

        // Validar fechas
        if (!empty($datos['fecha_inicio']) && !empty($datos['fecha_fin'])) {
            $inicio = new DateTime($datos['fecha_inicio']);
            $fin = new DateTime($datos['fecha_fin']);
            $hoy = new DateTime();

            if ($inicio < $hoy) {
                $errores[] = "La fecha de inicio no puede ser anterior a hoy";
            }

            if ($fin <= $inicio) {
                $errores[] = "La fecha de fin debe ser posterior a la fecha de inicio";
            }

            // Calcular días entre fechas
            $dias_calculados = $fin->diff($inicio)->days + 1; // +1 para incluir ambos días

            if ($datos['dias_solicitados'] != $dias_calculados) {
                $errores[] = "Los días solicitados no coinciden con el rango de fechas ({$dias_calculados} días)";
            }
        }

        // Validar días solicitados
        if (!empty($datos['dias_solicitados']) && (!is_numeric($datos['dias_solicitados']) || $datos['dias_solicitados'] <= 0)) {
            $errores[] = "Los días solicitados debe ser un número positivo";
        }

        return $errores;
    }
}
