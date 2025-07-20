<?php
require_once 'models/Cargo.php';
require_once 'controllers/AuthController.php';
require_once 'utils/Sanitizador.php';

class CargosController
{
    private $cargo;
    private $auth;

    public function __construct()
    {
        $this->cargo = new Cargo();
        $this->auth = new AuthController();
    }

    private function verificarAcceso()
    {
        if (!$this->auth->tienePermiso('cargos.acceso')) {
            http_response_code(403);
            include 'views/layouts/403.php';
            exit();
        }
    }

    public function index()
    {
        $this->verificarAcceso();

        $filtros = [
            'direccion' => $_GET['direccion'] ?? null,
            'departamento' => $_GET['departamento'] ?? null,
            'tipo_movimiento' => $_GET['tipo_movimiento'] ?? null,
        ];

        $cargos = $this->cargo->obtenerTodos($filtros);
        $departamentos = $this->cargo->obtenerDepartamentos();

        include 'views/cargos/index.php';
    }

    public function crear()
    {
        $this->verificarAcceso();

        $departamentos = $this->cargo->obtenerDepartamentos();
        $cargos = $this->cargo->obtenerCargos();

        include 'views/cargos/crear.php';
    }

    public function store()
    {
        $this->verificarAcceso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            exit();
        }

        try {
            $cedula = Sanitizador::texto($_POST['colaborador_cedula']);
            $colaborador = $this->cargo->buscarColaboradorPorCedula($cedula);

            if (!$colaborador) {
                echo json_encode(['status' => 'error', 'message' => 'Colaborador no encontrado']);
                exit();
            }

            $cargoNuevo = Sanitizador::entero($_POST['cargo_nuevo_id']);
            $sueldoNuevo = Sanitizador::flotante($_POST['sueldo_nuevo']);
            $departamentoNuevo = Sanitizador::entero($_POST['departamento_nuevo_id']);

            // Validación: comparar con datos actuales del colaborador
            if (
                $colaborador['cargo_actual_id'] == $cargoNuevo &&
                floatval($colaborador['sueldo']) == $sueldoNuevo &&
                $colaborador['departamento_id'] == $departamentoNuevo
            ) {
                echo json_encode(['status' => 'error', 'message' => 'Los datos nuevos son iguales a los actuales. No se puede registrar el movimiento.']);
                exit();
            }

            $datos = [
                'colaborador_id'         => $colaborador['id'], 
                'cargo_nuevo_id'         => $cargoNuevo,
                'sueldo_nuevo'           => $sueldoNuevo,
                'departamento_nuevo_id'  => $departamentoNuevo,
                'tipo_movimiento'        => Sanitizador::texto($_POST['tipo_movimiento']),
                'fecha_efectiva'         => Sanitizador::texto($_POST['fecha_efectiva']),
                'motivo'                 => Sanitizador::texto($_POST['motivo']),
                'usuario_registro_id'    => $_SESSION['user_id']
            ];

            if ($this->cargo->crear($datos)) {
                echo json_encode(['status' => 'success', 'message' => 'Movimiento registrado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al registrar movimiento']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function ver($id)
    {
        $this->verificarAcceso();
        $cargo = $this->cargo->obtenerPorIdConNombres($id);
        if (!$cargo) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Movimiento no encontrado'];
            header('Location: cargos.php');
            exit();
        }

        $firmaValida = $this->cargo->verificarFirma($cargo);
        include 'views/cargos/ver.php';
    }
}
?>
