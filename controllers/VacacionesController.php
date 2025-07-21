<?php
// =====================================================
// controllers/VacacionesController.php - Controlador de Vacaciones
// =====================================================
require_once 'models/Vacacion.php';
require_once 'controllers/AuthController.php';
require_once 'utils/Sanitizador.php';
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;


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

        // ✅ Aquí se asigna la variable que necesita la vista
        $vacaciones = $resultado['datos'];

        // También puedes pasar info de paginación si la usas
        $totalPaginas = $resultado['total_paginas'] ?? 1;
        $paginaActual = $pagina;

        
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

           
        // Cargar días ganados para cada colaborador sin referencia
        foreach ($colaboradores as $key => $colaborador) {
            $colaboradores[$key]['dias_disponibles'] = $this->vacacion->calcularDiasDisponibles($colaborador['id']);
        }


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

                if ($accion === 'aprobar') {
                    $solicitud = $this->vacacion->obtenerPorId($id);
                    $colaborador = $solicitud['colaborador_nombre'] ?? 'Desconocido';
                    $fecha_inicio = $solicitud['fecha_inicio'];
                    $fecha_fin = $solicitud['fecha_fin'];
                    $dias_solicitados = $solicitud['dias_solicitados'];

                    $options = new Options();
                    $options->set('defaultFont', 'Arial');
                    $dompdf = new Dompdf($options);

                    $html = "
                        <h1>Resolución de Vacaciones</h1>
                        <p><strong>Colaborador:</strong> $colaborador</p>
                        <p><strong>Desde:</strong> $fecha_inicio</p>
                        <p><strong>Hasta:</strong> $fecha_fin</p>
                        <p><strong>Días solicitados:</strong> $dias_solicitados</p>
                        <p><strong>Estado:</strong> Aprobado</p>
                        <p><strong>Generado por:</strong> Admin del sistema</p>
                    ";

                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    $nombreArchivo = "resolucion_vacaciones_" . preg_replace('/[^A-Za-z0-9]/', '_', $colaborador) . ".pdf";
                    $dompdf->stream($nombreArchivo, ['Attachment' => false]);
                    exit;
                }
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



    public function verPDF($id)
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

        $colaborador = $solicitud['colaborador_nombre'];
        $fecha_inicio = $solicitud['fecha_inicio'];
        $fecha_fin = $solicitud['fecha_fin'];
        $dias_solicitados = $solicitud['dias_solicitados'];
        $estado = $solicitud['estado'];
        $aprobador = $solicitud['aprobado_por'] ?? 'No aprobado aún';


        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $aprobadorSeguro = htmlspecialchars($aprobador);

        $html = "
            <h1>Resolución de Vacaciones</h1>
            <p><strong>Colaborador:</strong> $colaborador</p>
            <p><strong>Desde:</strong> $fecha_inicio</p>
            <p><strong>Hasta:</strong> $fecha_fin</p>
            <p><strong>Días solicitados:</strong> $dias_solicitados</p>
            <p><strong>Estado:</strong> $estado</p>
            <p><strong>Aprobado por:</strong> $aprobadorSeguro</p>
        ";


        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Mostrar en navegador (inline)
        $dompdf->stream("resolucion_vacaciones_{$id}.pdf", ['Attachment' => false]);
        exit;
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

        // Validar mínimo de días
        if (!empty($datos['dias_solicitados']) && $datos['dias_solicitados'] < 7) {
            $errores[] = "No se pueden solicitar menos de 7 días de vacaciones";
        }


        return $errores;
    }
}
