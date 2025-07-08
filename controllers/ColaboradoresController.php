<?php

// =====================================================
// controllers/ColaboradoresController.php - Controlador
// =====================================================

class ColaboradoresController
{
    private $colaborador;
    private $auth;

    public function __construct()
    {
        $this->colaborador = new Colaborador();
        $this->auth = new AuthController();
    }

    // Listar colaboradores
    public function index()
    {
        // Verificar permisos
        if (!$this->auth->tienePermiso('colaboradores.leer')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $filtros = $_GET;
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $porPagina = 20;

        $resultado = $this->colaborador->obtenerLista($filtros, $pagina, $porPagina);
        $departamentos = $this->colaborador->obtenerDepartamentos();

        include 'views/colaboradores/index.php';
    }

    // Mostrar formulario de creación
    public function crear()
    {
        if (!$this->auth->tienePermiso('colaboradores.crear')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $departamentos = $this->colaborador->obtenerDepartamentos();
        include 'views/colaboradores/crear.php';
    }

    // Procesar creación
    public function store()
    {
        if (!$this->auth->tienePermiso('colaboradores.crear')) {
            http_response_code(403);
            return;
        }

        $datos = $_POST;
        $errores = $this->validarDatos($datos);

        if (empty($errores)) {
            // Manejar upload de foto
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $foto = $this->procesarFoto($_FILES['foto_perfil']);
                if ($foto) {
                    $datos['foto_perfil'] = $foto;
                }
            }

            $id = $this->colaborador->crear($datos);

            if ($id) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Colaborador creado exitosamente'];
                header('Location: colaboradores.php?ver=' . $id);
                exit();
            } else {
                $errores[] = 'Error al crear el colaborador';
            }
        }

        $_SESSION['errores'] = $errores;
        $_SESSION['datos_anteriores'] = $datos;
        header('Location: colaboradores.php?accion=crear');
        exit();
    }

    // Mostrar detalles del colaborador
    public function ver($id)
    {
        if (!$this->auth->tienePermiso('colaboradores.leer')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $colaborador = $this->colaborador->obtenerPorId($id);

        if (!$colaborador) {
            http_response_code(404);
            include 'views/404.php';
            return;
        }

        $historial = $this->colaborador->obtenerHistorial($id);
        include 'views/colaboradores/ver.php';
    }

    // Mostrar formulario de edición
    public function editar($id)
    {
        if (!$this->auth->tienePermiso('colaboradores.actualizar')) {
            http_response_code(403);
            include 'views/403.php';
            return;
        }

        $colaborador = $this->colaborador->obtenerPorId($id);

        if (!$colaborador) {
            http_response_code(404);
            include 'views/404.php';
            return;
        }

        $departamentos = $this->colaborador->obtenerDepartamentos();
        include 'views/colaboradores/editar.php';
    }

    // Procesar actualización
    public function actualizar($id)
    {
        if (!$this->auth->tienePermiso('colaboradores.actualizar')) {
            http_response_code(403);
            return;
        }

        $datos = $_POST;
        $errores = $this->validarDatos($datos, $id);

        if (empty($errores)) {
            // Manejar upload de foto
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $foto = $this->procesarFoto($_FILES['foto_perfil']);
                if ($foto) {
                    $datos['foto_perfil'] = $foto;
                }
            }

            $result = $this->colaborador->actualizar($id, $datos);

            if ($result) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Colaborador actualizado exitosamente'];
                header('Location: colaboradores.php?ver=' . $id);
                exit();
            } else {
                $errores[] = 'Error al actualizar el colaborador';
            }
        }

        $_SESSION['errores'] = $errores;
        $_SESSION['datos_anteriores'] = $datos;
        header('Location: colaboradores.php?editar=' . $id);
        exit();
    }

    // Desactivar colaborador
    public function desactivar($id)
    {
        if (!$this->auth->tienePermiso('colaboradores.eliminar')) {
            http_response_code(403);
            return;
        }

        $motivo = $_POST['motivo'] ?? '';

        if ($this->colaborador->desactivar($id, $motivo)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Colaborador desactivado exitosamente'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al desactivar colaborador'];
        }

        header('Location: colaboradores.php');
        exit();
    }

    // Validar datos del formulario
    private function validarDatos($datos, $excluirId = null)
    {
        $errores = [];

        // Campos requeridos
        $requeridos = [
            'primer_nombre',
            'primer_apellido',
            'cedula',
            'sexo',
            'sueldo',
            'departamento_id',
            'fecha_contratacion',
            'tipo_empleado'
        ];

        foreach ($requeridos as $campo) {
            if (empty($datos[$campo])) {
                $errores[] = "El campo " . ucfirst(str_replace('_', ' ', $campo)) . " es requerido";
            }
        }

        // Validar cédula única
        if (!empty($datos['cedula']) && $this->colaborador->existeCedula($datos['cedula'], $excluirId)) {
            $errores[] = "La cédula ya está registrada";
        }

        // Validar email
        if (!empty($datos['correo_personal']) && !filter_var($datos['correo_personal'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo electrónico no es válido";
        }

        // Validar sueldo
        if (!empty($datos['sueldo']) && (!is_numeric($datos['sueldo']) || $datos['sueldo'] <= 0)) {
            $errores[] = "El sueldo debe ser un número positivo";
        }

        return $errores;
    }

    // Procesar upload de foto
    private function procesarFoto($archivo)
    {
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $tamanoMaximo = 2 * 1024 * 1024; // 2MB

        $nombre = $archivo['name'];
        $tamaño = $archivo['size'];
        $tmp = $archivo['tmp_name'];
        $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            return false;
        }

        if ($tamaño > $tamanoMaximo) {
            return false;
        }

        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
        $rutaDestino = 'uploads/fotos/' . $nombreUnico;

        // Crear directorio si no existe
        if (!is_dir('uploads/fotos/')) {
            mkdir('uploads/fotos/', 0777, true);
        }

        if (move_uploaded_file($tmp, $rutaDestino)) {
            return $nombreUnico;
        }

        return false;
    }
}
