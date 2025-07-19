<?php
require_once 'models/Usuario.php';
require_once 'controllers/AuthController.php';
require_once 'utils/Sanitizador.php';

class UsuariosController
{
    private $usuario;
    private $auth;

    public function __construct()
    {
        $this->usuario = new Usuario();
        $this->auth = new AuthController();
    }

    // Verificar permiso único para todo el módulo
    private function verificarAcceso()
    {
        if (!$this->auth->tienePermiso('usuarios.acceso')) {
            http_response_code(403);
            include 'views/layouts/403.php';
            exit();
        }
    }

    // Listar usuarios
    public function index()
    {
        $this->verificarAcceso();

        $usuarios = $this->usuario->obtenerTodos();
        include 'views/usuarios/index.php';
    }

    // Mostrar formulario para crear usuario
    public function crear()
    {
        $this->verificarAcceso();

        $roles = $this->usuario->obtenerRoles();
        include 'views/usuarios/crear.php';
    }

    // Guardar nuevo usuario
    public function store()
    {
        $this->verificarAcceso();

        // Sanitizar datos
        $username = Sanitizador::texto($_POST['username']);
        $email = Sanitizador::email($_POST['email']);
        $rol_id = Sanitizador::entero($_POST['rol_id']);
        $activo = isset($_POST['activo']) ? Sanitizador::entero($_POST['activo']) : 1;

        // Bloquear asignación de Super_Admin si no eres Super_Admin
        if ($rol_id == 1 && $_SESSION['rol_nombre'] !== 'Super_Admin') {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No puedes asignar rol Super_Admin'];
            header('Location: usuarios.php');
            exit();
        }

        // Validar correo
        if (!Sanitizador::validarEmail($email)) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Correo electrónico inválido'];
            header('Location: usuarios.php?accion=crear');
            exit();
        }

        // Crear datos
        $datos = [
            'username' => $username,
            'email' => $email,
            'rol_id' => $rol_id,
            'activo' => $activo,
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];

        if ($this->usuario->crear($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario creado exitosamente'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al crear usuario'];
        }
        header('Location: usuarios.php');
        exit();
    }

    // Mostrar formulario para editar usuario
    public function editar($id)
    {
        $this->verificarAcceso();

        $usuario = $this->usuario->obtenerPorId($id);
        $roles = $this->usuario->obtenerRoles();
        if (!$usuario) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Usuario no encontrado'];
            header('Location: usuarios.php');
            exit();
        }
        include 'views/usuarios/editar.php';
    }

    // Actualizar usuario existente
    public function update($id)
    {
        $this->verificarAcceso();

        // Sanitizar datos
        $username = Sanitizador::texto($_POST['username']);
        $email = Sanitizador::email($_POST['email']);
        $rol_id = Sanitizador::entero($_POST['rol_id']);
        $activo = isset($_POST['activo']) ? Sanitizador::entero($_POST['activo']) : 1;

        // Bloquear asignación de Super_Admin si no eres Super_Admin
        if ($rol_id == 1 && $_SESSION['rol_nombre'] !== 'Super_Admin') {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No puedes asignar rol Super_Admin'];
            header('Location: usuarios.php');
            exit();
        }

        // Validar correo
        if (!Sanitizador::validarEmail($email)) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Correo electrónico inválido'];
            header("Location: usuarios.php?accion=editar&id=$id");
            exit();
        }

        $datos = [
            'username' => $username,
            'email' => $email,
            'rol_id' => $rol_id,
            'activo' => $activo
        ];

        // Actualizar contraseña solo si se ingresa una nueva
        if (!empty($_POST['password'])) {
            $datos['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if ($this->usuario->actualizar($id, $datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario actualizado correctamente'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al actualizar usuario'];
        }
        header('Location: usuarios.php');
        exit();
    }

    // Desactivar usuario
    public function desactivar($id)
    {
        $this->verificarAcceso();

        if ($this->usuario->desactivar($id)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario desactivado'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al desactivar usuario'];
        }
        header('Location: usuarios.php');
        exit();
    }

    // Activar usuario
    public function activar($id)
    {
        $this->verificarAcceso();

        if ($this->usuario->activar($id)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario activado'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al activar usuario'];
        }
        header('Location: usuarios.php');
        exit();
    }

    // Ver detalle de usuario
    public function ver($id)
    {
        $this->verificarAcceso();

        $usuario = $this->usuario->obtenerPorId($id);
        if (!$usuario) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Usuario no encontrado'];
            header('Location: usuarios.php');
            exit();
        }
        include 'views/usuarios/ver.php';
    }
}
