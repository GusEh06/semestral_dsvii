<?php
// =====================================================
// models/Usuario.php - Modelo de Usuario
// =====================================================

class Usuario
{
    private $db;
    private $table = 'usuarios';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Autenticar usuario
    public function autenticar($username, $password)
    {
        try {
            $sql = "SELECT u.*, r.nombre as rol_nombre 
                    FROM {$this->table} u 
                    JOIN roles r ON u.rol_id = r.id 
                    WHERE u.username = :username AND u.activo = 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['username' => $username]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                // Actualizar último login
                $this->actualizarUltimoLogin($usuario['id']);

                // Resetear intentos fallidos
                $this->resetearIntentosFallidos($usuario['id']);

                return $usuario;
            }

            // Incrementar intentos fallidos
            $this->incrementarIntentosFallidos($username);
            return false;
        } catch (PDOException $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            return false;
        }
    }

    // Crear nuevo usuario
    public function crear($datos)
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (username, password_hash, email, rol_id, activo) 
                    VALUES (:username, :password_hash, :email, :rol_id, :activo)";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                'username' => $datos['username'],
                'password_hash' => password_hash($datos['password'], PASSWORD_DEFAULT),
                'email' => $datos['email'],
                'rol_id' => $datos['rol_id'],
                'activo' => $datos['activo'] ?? 1
            ]);
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    // Obtener permisos del usuario
    public function obtenerPermisos($usuario_id)
    {
        try {
            $sql = "SELECT p.nombre, p.modulo 
                    FROM usuarios u
                    JOIN rol_permisos rp ON u.rol_id = rp.rol_id
                    JOIN permisos p ON rp.permiso_id = p.id
                    WHERE u.id = :usuario_id AND u.activo = 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['usuario_id' => $usuario_id]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener permisos: " . $e->getMessage());
            return [];
        }
    }

    // Verificar si usuario tiene permiso específico
    public function tienePermiso($usuario_id, $permiso)
    {
        $permisos = $this->obtenerPermisos($usuario_id);
        foreach ($permisos as $p) {
            if ($p['nombre'] === $permiso) {
                return true;
            }
        }
        return false;
    }

    // Actualizar último login
    private function actualizarUltimoLogin($usuario_id)
    {
        $sql = "UPDATE {$this->table} SET ultimo_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $usuario_id]);
    }

    // Incrementar intentos fallidos
    private function incrementarIntentosFallidos($username)
    {
        $sql = "UPDATE {$this->table} 
                SET intentos_fallidos = intentos_fallidos + 1,
                    bloqueado_hasta = CASE 
                        WHEN intentos_fallidos >= 4 THEN DATE_ADD(NOW(), INTERVAL 30 MINUTE)
                        ELSE bloqueado_hasta 
                    END
                WHERE username = :username";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
    }

    // Resetear intentos fallidos
    private function resetearIntentosFallidos($usuario_id)
    {
        $sql = "UPDATE {$this->table} 
                SET intentos_fallidos = 0, bloqueado_hasta = NULL 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $usuario_id]);
    }

    // Verificar si usuario está bloqueado
    public function estaBloqueado($username)
    {
        $sql = "SELECT bloqueado_hasta FROM {$this->table} 
                WHERE username = :username AND bloqueado_hasta > NOW()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);

        return $stmt->fetch() !== false;
    }
}
