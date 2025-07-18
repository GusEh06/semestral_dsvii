<?php
// =====================================================
// models/Colaborador.php - Modelo para colaboradores
// =====================================================

class Colaborador
{
    private $db;
    private $table = 'colaboradores';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Crear un nuevo colaborador
    public function crear($datos)
    {
        try {
            $sql = "INSERT INTO {$this->table}
                    (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, sexo, cedula, fecha_nacimiento,
                    telefono, celular, direccion, correo_personal, sueldo, departamento_id, fecha_contratacion,
                    tipo_empleado, ocupacion, empleado_activo, foto_perfil)
                    VALUES
                    (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :sexo, :cedula, :fecha_nacimiento,
                    :telefono, :celular, :direccion, :correo_personal, :sueldo, :departamento_id, :fecha_contratacion,
                    :tipo_empleado, :ocupacion, :empleado_activo, :foto_perfil)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($datos);
        } catch (PDOException $e) {
            error_log("Error al crear colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar colaborador
    public function actualizar($id, $datos)
    {
        try {
            $sql = "UPDATE {$this->table} SET
                    primer_nombre = :primer_nombre,
                    segundo_nombre = :segundo_nombre,
                    primer_apellido = :primer_apellido,
                    segundo_apellido = :segundo_apellido,
                    sexo = :sexo,
                    cedula = :cedula,
                    fecha_nacimiento = :fecha_nacimiento,
                    telefono = :telefono,
                    celular = :celular,
                    direccion = :direccion,
                    correo_personal = :correo_personal,
                    sueldo = :sueldo,
                    departamento_id = :departamento_id,
                    fecha_contratacion = :fecha_contratacion,
                    tipo_empleado = :tipo_empleado,
                    ocupacion = :ocupacion";

            if (!empty($datos['foto_perfil'])) {
                $sql .= ", foto_perfil = :foto_perfil";
            }

            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $datos['id'] = $id;

            return $stmt->execute($datos);
        } catch (PDOException $e) {
            error_log("Error al actualizar colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Obtener todos los colaboradores
    public function obtenerTodos()
    {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY primer_apellido, primer_nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener colaboradores: " . $e->getMessage());
            return [];
        }
    }

    // Obtener un colaborador por ID
    public function obtenerPorId($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener colaborador por ID: " . $e->getMessage());
            return false;
        }
    }

    // Desactivar colaborador
    public function desactivar($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET empleado_activo = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al desactivar colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Activar colaborador
    public function activar($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET empleado_activo = 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al activar colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Obtener documentos académicos
    public function obtenerDocumentos($colaborador_id)
    {
        try {
            $sql = "SELECT * FROM documentos_academicos WHERE colaborador_id = :colaborador_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['colaborador_id' => $colaborador_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener documentos académicos: " . $e->getMessage());
            return [];
        }
    }

    // Guardar documento académico
    public function guardarDocumento($doc)
    {
        try {
            $sql = "INSERT INTO documentos_academicos
                (colaborador_id, tipo_documento, nombre_documento, institucion, fecha_emision, archivo_pdf, verificado, observaciones)
                VALUES
                (:colaborador_id, :tipo_documento, :nombre_documento, :institucion, :fecha_emision, :archivo_pdf, :verificado, :observaciones)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($doc);
        } catch (PDOException $e) {
            error_log("Error al guardar documento académico: " . $e->getMessage());
            return false;
        }
    }

    // Obtener último ID insertado
    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }
}
