<?php
// =====================================================
// models/Colaborador.php - Modelo de Colaborador
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

    // Crear nuevo colaborador
    public function crear($datos)
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                     cedula, sexo, fecha_nacimiento, telefono, celular, direccion, 
                     correo_personal, sueldo, departamento_id, fecha_contratacion,
                     tipo_empleado, ocupacion, foto_perfil, empleado_activo) 
                    VALUES 
                    (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido,
                     :cedula, :sexo, :fecha_nacimiento, :telefono, :celular, :direccion,
                     :correo_personal, :sueldo, :departamento_id, :fecha_contratacion,
                     :tipo_empleado, :ocupacion, :foto_perfil, :empleado_activo)";

            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                'primer_nombre' => $datos['primer_nombre'],
                'segundo_nombre' => $datos['segundo_nombre'] ?: null,
                'primer_apellido' => $datos['primer_apellido'],
                'segundo_apellido' => $datos['segundo_apellido'] ?: null,
                'cedula' => $datos['cedula'],
                'sexo' => $datos['sexo'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?: null,
                'telefono' => $datos['telefono'] ?: null,
                'celular' => $datos['celular'] ?: null,
                'direccion' => $datos['direccion'] ?: null,
                'correo_personal' => $datos['correo_personal'] ?: null,
                'sueldo' => $datos['sueldo'],
                'departamento_id' => $datos['departamento_id'],
                'fecha_contratacion' => $datos['fecha_contratacion'],
                'tipo_empleado' => $datos['tipo_empleado'],
                'ocupacion' => $datos['ocupacion'] ?: null,
                'foto_perfil' => $datos['foto_perfil'] ?: null,
                'empleado_activo' => $datos['empleado_activo'] ?? 1
            ]);

            if ($result) {
                $colaborador_id = $this->db->lastInsertId();

                // Registrar en historial de cargos (contratación inicial)
                $this->registrarHistorialCargo($colaborador_id, [
                    'tipo_movimiento' => 'Contratacion',
                    'cargo_nuevo' => $datos['ocupacion'],
                    'sueldo_nuevo' => $datos['sueldo'],
                    'departamento_nuevo_id' => $datos['departamento_id'],
                    'fecha_efectiva' => $datos['fecha_contratacion'],
                    'motivo' => 'Contratación inicial'
                ]);

                return $colaborador_id;
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error al crear colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Obtener colaborador por ID
    public function obtenerPorId($id)
    {
        try {
            $sql = "SELECT c.*, d.nombre as departamento_nombre 
                    FROM {$this->table} c
                    LEFT JOIN departamentos d ON c.departamento_id = d.id
                    WHERE c.id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar colaborador
    public function actualizar($id, $datos)
    {
        try {
            // Obtener datos actuales para el historial
            $colaboradorActual = $this->obtenerPorId($id);

            $sql = "UPDATE {$this->table} SET
                    primer_nombre = :primer_nombre,
                    segundo_nombre = :segundo_nombre,
                    primer_apellido = :primer_apellido,
                    segundo_apellido = :segundo_apellido,
                    cedula = :cedula,
                    sexo = :sexo,
                    fecha_nacimiento = :fecha_nacimiento,
                    telefono = :telefono,
                    celular = :celular,
                    direccion = :direccion,
                    correo_personal = :correo_personal,
                    sueldo = :sueldo,
                    departamento_id = :departamento_id,
                    tipo_empleado = :tipo_empleado,
                    ocupacion = :ocupacion,
                    foto_perfil = :foto_perfil,
                    empleado_activo = :empleado_activo,
                    updated_at = NOW()
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                'id' => $id,
                'primer_nombre' => $datos['primer_nombre'],
                'segundo_nombre' => $datos['segundo_nombre'] ?: null,
                'primer_apellido' => $datos['primer_apellido'],
                'segundo_apellido' => $datos['segundo_apellido'] ?: null,
                'cedula' => $datos['cedula'],
                'sexo' => $datos['sexo'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?: null,
                'telefono' => $datos['telefono'] ?: null,
                'celular' => $datos['celular'] ?: null,
                'direccion' => $datos['direccion'] ?: null,
                'correo_personal' => $datos['correo_personal'] ?: null,
                'sueldo' => $datos['sueldo'],
                'departamento_id' => $datos['departamento_id'],
                'tipo_empleado' => $datos['tipo_empleado'],
                'ocupacion' => $datos['ocupacion'] ?: null,
                'foto_perfil' => $datos['foto_perfil'] ?: $colaboradorActual['foto_perfil'],
                'empleado_activo' => $datos['empleado_activo'] ?? 1
            ]);

            // Registrar cambios significativos en historial
            if ($result && $colaboradorActual) {
                $haycambios = false;
                $tipoMovimiento = 'Ajuste_Salarial';
                $motivo = 'Actualización de datos';

                if ($colaboradorActual['ocupacion'] != $datos['ocupacion']) {
                    $tipoMovimiento = 'Promocion';
                    $motivo = 'Cambio de cargo';
                    $hayChangios = true;
                }

                if ($colaboradorActual['departamento_id'] != $datos['departamento_id']) {
                    $tipoMovimiento = 'Traslado';
                    $motivo = 'Cambio de departamento';
                    $hayChangios = true;
                }

                if ($colaboradorActual['sueldo'] != $datos['sueldo']) {
                    $hayChangios = true;
                }

                if ($hayChangios) {
                    $this->registrarHistorialCargo($id, [
                        'tipo_movimiento' => $tipoMovimiento,
                        'cargo_anterior' => $colaboradorActual['ocupacion'],
                        'cargo_nuevo' => $datos['ocupacion'],
                        'sueldo_anterior' => $colaboradorActual['sueldo'],
                        'sueldo_nuevo' => $datos['sueldo'],
                        'departamento_anterior_id' => $colaboradorActual['departamento_id'],
                        'departamento_nuevo_id' => $datos['departamento_id'],
                        'fecha_efectiva' => date('Y-m-d'),
                        'motivo' => $motivo
                    ]);
                }
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error al actualizar colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Obtener lista de colaboradores con filtros y paginación
    public function obtenerLista($filtros = [], $pagina = 1, $porPagina = 20)
    {
        try {
            $where = ["c.empleado_activo = 1"]; // Solo activos por defecto
            $params = [];

            // Aplicar filtros
            if (!empty($filtros['busqueda'])) {
                $where[] = "(c.primer_nombre LIKE :busqueda OR c.primer_apellido LIKE :busqueda OR c.cedula LIKE :busqueda)";
                $params['busqueda'] = '%' . $filtros['busqueda'] . '%';
            }

            if (!empty($filtros['sexo'])) {
                $where[] = "c.sexo = :sexo";
                $params['sexo'] = $filtros['sexo'];
            }

            if (!empty($filtros['departamento'])) {
                $where[] = "c.departamento_id = :departamento";
                $params['departamento'] = $filtros['departamento'];
            }

            if (!empty($filtros['tipo_empleado'])) {
                $where[] = "c.tipo_empleado = :tipo_empleado";
                $params['tipo_empleado'] = $filtros['tipo_empleado'];
            }

            if (!empty($filtros['edad_min']) && !empty($filtros['edad_max'])) {
                $where[] = "TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) BETWEEN :edad_min AND :edad_max";
                $params['edad_min'] = $filtros['edad_min'];
                $params['edad_max'] = $filtros['edad_max'];
            }

            if (isset($filtros['empleado_activo'])) {
                $where[0] = "c.empleado_activo = :empleado_activo"; // Reemplazar condición por defecto
                $params['empleado_activo'] = $filtros['empleado_activo'];
            }

            $whereClause = implode(' AND ', $where);

            // Contar total de registros
            $sqlCount = "SELECT COUNT(*) as total 
                        FROM {$this->table} c 
                        LEFT JOIN departamentos d ON c.departamento_id = d.id 
                        WHERE {$whereClause}";

            $stmtCount = $this->db->prepare($sqlCount);
            $stmtCount->execute($params);
            $total = $stmtCount->fetch()['total'];

            // Calcular offset
            $offset = ($pagina - 1) * $porPagina;

            // Obtener registros paginados
            $sql = "SELECT c.id, c.primer_nombre, c.segundo_nombre, c.primer_apellido, c.segundo_apellido,
                           c.cedula, c.sexo, c.fecha_nacimiento, c.telefono, c.celular, c.correo_personal,
                           c.sueldo, c.fecha_contratacion, c.tipo_empleado, c.ocupacion, c.empleado_activo,
                           c.foto_perfil, d.nombre as departamento_nombre,
                           TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) as edad,
                           CONCAT(c.primer_nombre, ' ', IFNULL(c.segundo_nombre, ''), ' ', 
                                  c.primer_apellido, ' ', IFNULL(c.segundo_apellido, '')) as nombre_completo
                    FROM {$this->table} c
                    LEFT JOIN departamentos d ON c.departamento_id = d.id
                    WHERE {$whereClause}
                    ORDER BY c.primer_apellido, c.primer_nombre
                    LIMIT :offset, :porPagina";

            $stmt = $this->db->prepare($sql);

            // Agregar parámetros de paginación
            $params['offset'] = $offset;
            $params['porPagina'] = $porPagina;

            // Bind de parámetros
            foreach ($params as $key => $value) {
                if (in_array($key, ['offset', 'porPagina'])) {
                    $stmt->bindValue(":$key", (int)$value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":$key", $value);
                }
            }

            $stmt->execute();
            $colaboradores = $stmt->fetchAll();

            return [
                'datos' => $colaboradores,
                'total' => $total,
                'pagina' => $pagina,
                'porPagina' => $porPagina,
                'totalPaginas' => ceil($total / $porPagina)
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener lista de colaboradores: " . $e->getMessage());
            return ['datos' => [], 'total' => 0, 'pagina' => 1, 'porPagina' => $porPagina, 'totalPaginas' => 0];
        }
    }

    // Desactivar colaborador (no eliminar)
    public function desactivar($id, $motivo = '')
    {
        try {
            $sql = "UPDATE {$this->table} SET empleado_activo = 0, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['id' => $id]);

            if ($result) {
                // Registrar en historial
                $this->registrarHistorialCargo($id, [
                    'tipo_movimiento' => 'Desvinculacion',
                    'cargo_anterior' => null,
                    'cargo_nuevo' => 'DESVINCULADO',
                    'fecha_efectiva' => date('Y-m-d'),
                    'motivo' => $motivo ?: 'Desvinculación del colaborador'
                ]);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error al desactivar colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Verificar si cédula ya existe
    public function existeCedula($cedula, $excluirId = null)
    {
        try {
            $sql = "SELECT id FROM {$this->table} WHERE cedula = :cedula";
            $params = ['cedula' => $cedula];

            if ($excluirId) {
                $sql .= " AND id != :excluir_id";
                $params['excluir_id'] = $excluirId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Error al verificar cédula: " . $e->getMessage());
            return false;
        }
    }

    // Obtener historial de cargos de un colaborador
    public function obtenerHistorial($colaborador_id)
    {
        try {
            $sql = "SELECT ch.*, 
                           da.nombre as departamento_anterior,
                           dn.nombre as departamento_nuevo,
                           u.username as usuario_registro
                    FROM cargos_historico ch
                    LEFT JOIN departamentos da ON ch.departamento_anterior_id = da.id
                    LEFT JOIN departamentos dn ON ch.departamento_nuevo_id = dn.id
                    LEFT JOIN usuarios u ON ch.usuario_registro_id = u.id
                    WHERE ch.colaborador_id = :colaborador_id AND ch.activo = 1
                    ORDER BY ch.fecha_efectiva DESC, ch.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['colaborador_id' => $colaborador_id]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener historial: " . $e->getMessage());
            return [];
        }
    }

    // Registrar cambio en historial de cargos
    private function registrarHistorialCargo($colaborador_id, $datos)
    {
        try {
            $sql = "INSERT INTO cargos_historico 
                    (colaborador_id, cargo_anterior, cargo_nuevo, sueldo_anterior, sueldo_nuevo,
                     departamento_anterior_id, departamento_nuevo_id, tipo_movimiento, 
                     fecha_efectiva, motivo, usuario_registro_id)
                    VALUES 
                    (:colaborador_id, :cargo_anterior, :cargo_nuevo, :sueldo_anterior, :sueldo_nuevo,
                     :departamento_anterior_id, :departamento_nuevo_id, :tipo_movimiento,
                     :fecha_efectiva, :motivo, :usuario_registro_id)";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                'colaborador_id' => $colaborador_id,
                'cargo_anterior' => $datos['cargo_anterior'] ?? null,
                'cargo_nuevo' => $datos['cargo_nuevo'] ?? null,
                'sueldo_anterior' => $datos['sueldo_anterior'] ?? null,
                'sueldo_nuevo' => $datos['sueldo_nuevo'] ?? null,
                'departamento_anterior_id' => $datos['departamento_anterior_id'] ?? null,
                'departamento_nuevo_id' => $datos['departamento_nuevo_id'] ?? null,
                'tipo_movimiento' => $datos['tipo_movimiento'],
                'fecha_efectiva' => $datos['fecha_efectiva'],
                'motivo' => $datos['motivo'] ?? null,
                'usuario_registro_id' => $_SESSION['user_id'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error al registrar historial: " . $e->getMessage());
            return false;
        }
    }

    // Obtener departamentos para selects
    public function obtenerDepartamentos()
    {
        try {
            $sql = "SELECT id, nombre FROM departamentos WHERE activo = 1 ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener departamentos: " . $e->getMessage());
            return [];
        }
    }

    // Obtener estadísticas básicas
    public function obtenerEstadisticas()
    {
        try {
            $stats = [];

            // Total colaboradores activos
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE empleado_activo = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats['total_activos'] = $stmt->fetch()['total'];

            // Por sexo
            $sql = "SELECT sexo, COUNT(*) as cantidad 
                    FROM {$this->table} 
                    WHERE empleado_activo = 1 
                    GROUP BY sexo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $porSexo = $stmt->fetchAll();

            foreach ($porSexo as $row) {
                $stats['por_sexo'][$row['sexo']] = $row['cantidad'];
            }

            // Por departamento
            $sql = "SELECT d.nombre, COUNT(c.id) as cantidad 
                    FROM departamentos d 
                    LEFT JOIN {$this->table} c ON d.id = c.departamento_id AND c.empleado_activo = 1
                    WHERE d.activo = 1
                    GROUP BY d.id, d.nombre 
                    ORDER BY cantidad DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats['por_departamento'] = $stmt->fetchAll();

            return $stats;
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }
}
