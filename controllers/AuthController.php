<?php

// =====================================================
// controllers/AuthController.php - Controlador de Autenticación
// =====================================================

class AuthController
{
    private $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();

        // Configurar sesiones seguras
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
    }

    // Procesar login
    public function login($username, $password)
    {
        // Validaciones básicas
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Usuario y contraseña son requeridos'];
        }

        // Verificar si está bloqueado
        if ($this->usuario->estaBloqueado($username)) {
            return ['success' => false, 'message' => 'Usuario bloqueado temporalmente. Intente más tarde.'];
        }

        // Autenticar
        $userData = $this->usuario->autenticar($username, $password);

        if ($userData) {
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);

            // Guardar datos en sesión
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['rol_id'] = $userData['rol_id'];
            $_SESSION['rol_nombre'] = $userData['rol_nombre'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();

            // Cargar permisos
            $_SESSION['permisos'] = $this->usuario->obtenerPermisos($userData['id']);

            return ['success' => true, 'message' => 'Login exitoso'];
        }

        return ['success' => false, 'message' => 'Credenciales incorrectas'];
    }

    // Cerrar sesión
    public function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }

            session_destroy();
        }

        return ['success' => true, 'message' => 'Sesión cerrada'];
    }

    // Verificar si está logueado
    public function estaLogueado()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // Verificar timeout de sesión
    public function verificarTimeout($timeout = 3600)
    { // 1 hora por defecto
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $timeout) {
                $this->logout();
                return false;
            }
            $_SESSION['last_activity'] = time();
        }
        return true;
    }

    // Verificar permiso
    public function tienePermiso($permiso)
    {
        if (!$this->estaLogueado()) {
            return false;
        }

        $permisos = $_SESSION['permisos'] ?? [];
        foreach ($permisos as $p) {
            if ($p['nombre'] === $permiso) {
                return true;
            }
        }
        return false;
    }

    // Obtener datos del usuario actual
    public function obtenerUsuarioActual()
    {
        if (!$this->estaLogueado()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'rol_id' => $_SESSION['rol_id'],
            'rol_nombre' => $_SESSION['rol_nombre'],
            'email' => $_SESSION['email']
        ];
    }
}
