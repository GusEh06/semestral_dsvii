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
                    tipo_empleado, ocupacion, cargo_actual_id, empleado_activo, foto_perfil)
                    VALUES
                    (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :sexo, :cedula, :fecha_nacimiento,
                    :telefono, :celular, :direccion, :correo_personal, :sueldo, :departamento_id, :fecha_contratacion,
                    :tipo_empleado, :ocupacion, :cargo_actual_id, :empleado_activo, :foto_perfil)";
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
                    ocupacion = :ocupacion,
                    cargo_actual_id = :cargo_actual_id";

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
            $sql = "SELECT c.*, d.nombre AS departamento_nombre, ca.nombre AS cargo_nombre
                    FROM colaboradores c
                    JOIN departamentos d ON c.departamento_id = d.id
                    LEFT JOIN cargos ca ON c.cargo_actual_id = ca.id
                    WHERE c.id = :id";
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

    public function obtenerCargo($colaborador_id)
    {
        $sql = "SELECT id, fecha_efectiva AS fecha, tipo_movimiento AS descripcion, 'cargo' AS tipo
                FROM cargos_historico
                WHERE colaborador_id = :colaborador_id
                ORDER BY fecha_efectiva DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['colaborador_id' => $colaborador_id]);
        return $stmt->fetchAll();
    }

    public function obtenerEstatus($colaborador_id)
    {
        $sql = "SELECT id, fecha_inicio AS fecha, estatus AS descripcion, 'estatus' AS tipo
                FROM estatus_colaborador
                WHERE colaborador_id = :colaborador_id
                ORDER BY fecha_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['colaborador_id' => $colaborador_id]);
        return $stmt->fetchAll();
    }

    // Obtener último ID insertado
    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }

    public function obtenerPorCedula($cedula)
    {
        $sql = "SELECT id FROM colaboradores WHERE cedula = :cedula LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetch();
    }

    public function obtenerCargos()
    {
        $sql = "SELECT id, nombre FROM cargos ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }



    public function registrarPrimerMovimiento($colaborador_id, $cargo_id, $sueldo, $departamento_id, $fecha, $usuario_id)
    {

        $datos_firma = [
            'colaborador_id'        => $colaborador_id,
            'cargo_nuevo_id'        => $cargo_id,
            'sueldo_nuevo'          => $sueldo,
            'departamento_nuevo_id' => $departamento_id,
            'tipo_movimiento'       => 'Contratacion',
            'fecha_efectiva'        => $fecha
        ];

        $stringFirma = $this->generarStringFirma($datos_firma);
        $firma = $this->firmarDatos($stringFirma);

        $sql = "INSERT INTO cargos_historico
            (colaborador_id, cargo_nuevo_id, sueldo_nuevo, departamento_nuevo_id,
            tipo_movimiento, fecha_efectiva, usuario_registro_id, firma_digital, activo)
            VALUES
            (:colaborador_id, :cargo_nuevo_id, :sueldo_nuevo, :departamento_nuevo_id,
            :tipo_movimiento, :fecha_efectiva, :usuario_registro_id, :firma_digital, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'colaborador_id'        => $colaborador_id,
            'cargo_nuevo_id'        => $cargo_id,
            'sueldo_nuevo'          => $sueldo,
            'departamento_nuevo_id' => $departamento_id,
            'tipo_movimiento'       => 'Contratacion',
            'fecha_efectiva'        => $fecha,
            'usuario_registro_id'   => $usuario_id,
            'firma_digital'         => $firma
        ]);
    }


    private function firmarDatos($datos)
    {
        $privateKey = file_get_contents(__DIR__ . '/../keys/private.pem');
        $pkeyid = openssl_pkey_get_private($privateKey);

        openssl_sign($datos, $firma_binaria, $pkeyid, OPENSSL_ALGO_SHA256);

        return base64_encode($firma_binaria);
    }

        private function generarStringFirma($data)
    {
        return trim(
            $data['colaborador_id'] . '|' .
            $data['cargo_nuevo_id'] . '|' .
            number_format($data['sueldo_nuevo'], 2, '.', '') . '|' .
            $data['departamento_nuevo_id'] . '|' .
            $data['tipo_movimiento'] . '|' .
            date('Y-m-d', strtotime($data['fecha_efectiva']))
        );
    }

}
