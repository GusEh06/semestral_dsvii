<?php
// =====================================================
// models/Vacacion.php - Modelo de Vacaciones
// =====================================================

class Vacacion
{
    private $db;
    private $table = 'vacaciones';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Crear solicitud de vacaciones
    public function crearSolicitud($datos)
    {
        try {
            // Verificar que el colaborador tenga días disponibles
            $colaborador = $this->obtenerColaborador($datos['colaborador_id']);
            if (!$colaborador) {
                return ['success' => false, 'message' => 'Colaborador no encontrado'];
            }

            // Verificar antigüedad (mínimo 11 meses)
            $meses_trabajados = $this->calcularMesesTrabajados($colaborador['fecha_contratacion']);
            if ($meses_trabajados < 11) {
                return ['success' => false, 'message' => 'El colaborador debe tener al menos 11 meses de antigüedad'];
            }

            // Calcular días disponibles
            $dias_disponibles = $this->calcularDiasDisponibles($datos['colaborador_id']);

            if ($datos['dias_solicitados'] > $dias_disponibles) {
                return ['success' => false, 'message' => "Solo tiene {$dias_disponibles} días disponibles"];
            }

            // Verificar que no haya solapamiento de fechas
            if ($this->hayConflictoFechas($datos['colaborador_id'], $datos['fecha_inicio'], $datos['fecha_fin'])) {
                return ['success' => false, 'message' => 'Ya tiene vacaciones programadas en esas fechas'];
            }

            $sql = "INSERT INTO {$this->table} 
                    (colaborador_id, fecha_solicitud, fecha_inicio, fecha_fin, 
                     dias_solicitados, dias_disponibles_al_momento, estado, observaciones) 
                    VALUES 
                    (:colaborador_id, CURDATE(), :fecha_inicio, :fecha_fin, 
                     :dias_solicitados, :dias_disponibles, 'Pendiente', :observaciones)";

            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                'colaborador_id' => $datos['colaborador_id'],
                'fecha_inicio' => $datos['fecha_inicio'],
                'fecha_fin' => $datos['fecha_fin'],
                'dias_solicitados' => $datos['dias_solicitados'],
                'dias_disponibles' => $dias_disponibles,
                'observaciones' => $datos['observaciones'] ?? null
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Solicitud creada exitosamente', 'id' => $this->db->lastInsertId()];
            }

            return ['success' => false, 'message' => 'Error al crear la solicitud'];
        } catch (PDOException $e) {
            error_log("Error al crear solicitud de vacaciones: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del sistema'];
        }
    }

    // Aprobar o rechazar solicitud
    public function procesarSolicitud($id, $accion, $motivo_rechazo = '', $usuario_id = null)
    {
        try {
            $estado = $accion === 'aprobar' ? 'Aprobada' : 'Rechazada';

            $sql = "UPDATE {$this->table} 
                    SET estado = :estado, 
                        motivo_rechazo = :motivo_rechazo,
                        aprobada_por_usuario_id = :usuario_id,
                        fecha_aprobacion = NOW(),
                        updated_at = NOW()
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                'id' => $id,
                'estado' => $estado,
                'motivo_rechazo' => $estado === 'Rechazada' ? $motivo_rechazo : null,
                'usuario_id' => $usuario_id
            ]);

            if ($result && $estado === 'Aprobada') {
                // Actualizar estado del colaborador
                $this->actualizarEstatusColaborador($id);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error al procesar solicitud: " . $e->getMessage());
            return false;
        }
    }

    // Obtener solicitudes con filtros
    public function obtenerSolicitudes($filtros = [], $pagina = 1, $porPagina = 20)
    {
        try {
            $where = ["1=1"];
            $params = [];

            // Aplicar filtros
            if (!empty($filtros['colaborador_id'])) {
                $where[] = "v.colaborador_id = :colaborador_id";
                $params['colaborador_id'] = $filtros['colaborador_id'];
            }

            if (!empty($filtros['estado'])) {
                $where[] = "v.estado = :estado";
                $params['estado'] = $filtros['estado'];
            }

            if (!empty($filtros['departamento'])) {
                $where[] = "c.departamento_id = :departamento";
                $params['departamento'] = $filtros['departamento'];
            }

            if (!empty($filtros['año'])) {
                $where[] = "YEAR(v.fecha_inicio) = :año";
                $params['año'] = $filtros['año'];
            }

            if (!empty($filtros['mes'])) {
                $where[] = "MONTH(v.fecha_inicio) = :mes";
                $params['mes'] = $filtros['mes'];
            }

            $whereClause = implode(' AND ', $where);

            // Contar total
            $sqlCount = "SELECT COUNT(*) as total 
                        FROM {$this->table} v 
                        JOIN colaboradores c ON v.colaborador_id = c.id 
                        JOIN departamentos d ON c.departamento_id = d.id 
                        WHERE {$whereClause}";

            $stmtCount = $this->db->prepare($sqlCount);
            $stmtCount->execute($params);
            $total = $stmtCount->fetch()['total'];

            // Obtener registros paginados
            $offset = ($pagina - 1) * $porPagina;

            $sql = "SELECT v.*, 
                           CONCAT(c.primer_nombre, ' ', c.primer_apellido) as colaborador_nombre,
                           c.cedula as colaborador_cedula,
                           d.nombre as departamento,
                           ua.username as aprobado_por
                    FROM {$this->table} v
                    JOIN colaboradores c ON v.colaborador_id = c.id
                    JOIN departamentos d ON c.departamento_id = d.id
                    LEFT JOIN usuarios ua ON v.aprobada_por_usuario_id = ua.id
                    WHERE {$whereClause}
                    ORDER BY v.fecha_solicitud DESC, v.created_at DESC
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
            $solicitudes = $stmt->fetchAll();

            return [
                'datos' => $solicitudes,
                'total' => $total,
                'pagina' => $pagina,
                'porPagina' => $porPagina,
                'totalPaginas' => ceil($total / $porPagina)
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes: " . $e->getMessage());
            return ['datos' => [], 'total' => 0, 'pagina' => 1, 'porPagina' => $porPagina, 'totalPaginas' => 0];
        }
    }

    // Obtener solicitud por ID
    public function obtenerPorId($id)
    {
        try {
            $sql = "SELECT v.*, 
                           CONCAT(c.primer_nombre, ' ', c.primer_apellido) as colaborador_nombre,
                           c.cedula as colaborador_cedula,
                           c.fecha_contratacion,
                           d.nombre as departamento,
                           ua.username as aprobado_por
                    FROM {$this->table} v
                    JOIN colaboradores c ON v.colaborador_id = c.id
                    JOIN departamentos d ON c.departamento_id = d.id
                    LEFT JOIN usuarios ua ON v.aprobada_por_usuario_id = ua.id
                    WHERE v.id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener solicitud: " . $e->getMessage());
            return false;
        }
    }

    // Calcular días de vacaciones disponibles para un colaborador
    public function calcularDiasDisponibles($colaborador_id)
    {
        try {
            $colaborador = $this->obtenerColaborador($colaborador_id);
            if (!$colaborador) return 0;

            // Calcular días ganados desde la contratación
            $dias_ganados = $this->calcularDiasGanados($colaborador['fecha_contratacion']);

            // Restar días ya tomados (aprobados)
            $sql = "SELECT COALESCE(SUM(dias_solicitados), 0) as dias_tomados 
                    FROM {$this->table} 
                    WHERE colaborador_id = :colaborador_id 
                    AND estado = 'Aprobada'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['colaborador_id' => $colaborador_id]);
            $dias_tomados = $stmt->fetch()['dias_tomados'];

            $dias_disponibles = max(0, $dias_ganados - $dias_tomados);

            // Actualizar en la tabla de colaboradores
            $this->actualizarDiasAcumulados($colaborador_id, $dias_disponibles);

            return $dias_disponibles;
        } catch (PDOException $e) {
            error_log("Error al calcular días disponibles: " . $e->getMessage());
            return 0;
        }
    }

    // Calcular días ganados según fecha de contratación (1 día por cada 11 trabajados)
    private function calcularDiasGanados($fecha_contratacion)
    {
        $contratacion = new DateTime($fecha_contratacion);
        $hoy = new DateTime();
        $dias_trabajados = $hoy->diff($contratacion)->days;

        return floor($dias_trabajados / 11);
    }

    // Calcular meses trabajados
    private function calcularMesesTrabajados($fecha_contratacion)
    {
        $contratacion = new DateTime($fecha_contratacion);
        $hoy = new DateTime();
        $diferencia = $hoy->diff($contratacion);

        return ($diferencia->y * 12) + $diferencia->m;
    }

    // Verificar conflicto de fechas
    private function hayConflictoFechas($colaborador_id, $fecha_inicio, $fecha_fin)
    {
        try {
            $sql = "SELECT COUNT(*) as conflictos 
                    FROM {$this->table} 
                    WHERE colaborador_id = :colaborador_id 
                    AND estado IN ('Pendiente', 'Aprobada')
                    AND (
                        (fecha_inicio <= :fecha_inicio AND fecha_fin >= :fecha_inicio) OR
                        (fecha_inicio <= :fecha_fin AND fecha_fin >= :fecha_fin) OR
                        (fecha_inicio >= :fecha_inicio AND fecha_fin <= :fecha_fin)
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'colaborador_id' => $colaborador_id,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);

            return $stmt->fetch()['conflictos'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar conflicto de fechas: " . $e->getMessage());
            return true; // Por seguridad, asumir que hay conflicto
        }
    }

    // Obtener información del colaborador
    private function obtenerColaborador($colaborador_id)
    {
        try {
            $sql = "SELECT * FROM colaboradores WHERE id = :id AND empleado_activo = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $colaborador_id]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener colaborador: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar días acumulados en tabla colaboradores
    private function actualizarDiasAcumulados($colaborador_id, $dias_disponibles)
    {
        try {
            $sql = "UPDATE colaboradores 
                    SET dias_vacaciones_acumulados = :dias,
                        ultima_actualizacion_vacaciones = CURDATE()
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id' => $colaborador_id,
                'dias' => $dias_disponibles
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar días acumulados: " . $e->getMessage());
        }
    }

    // Actualizar estatus del colaborador cuando se aprueban vacaciones
    private function actualizarEstatusColaborador($vacacion_id)
    {
        try {
            // Obtener datos de la vacación
            $vacacion = $this->obtenerPorId($vacacion_id);
            if (!$vacacion) return false;

            // Insertar registro en estatus_colaborador
            $sql = "INSERT INTO estatus_colaborador 
                    (colaborador_id, estatus, fecha_inicio, fecha_fin, observaciones, usuario_registro_id)
                    VALUES 
                    (:colaborador_id, 'Vacaciones', :fecha_inicio, :fecha_fin, 
                     'Vacaciones aprobadas automáticamente', :usuario_id)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'colaborador_id' => $vacacion['colaborador_id'],
                'fecha_inicio' => $vacacion['fecha_inicio'],
                'fecha_fin' => $vacacion['fecha_fin'],
                'usuario_id' => $_SESSION['user_id'] ?? null
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar estatus: " . $e->getMessage());
            return false;
        }
    }

    // Obtener calendario de vacaciones
    public function obtenerCalendario($año = null, $mes = null)
    {
        try {
            $año = $año ?? date('Y');
            $where = ["v.estado = 'Aprobada'", "YEAR(v.fecha_inicio) = :año"];
            $params = ['año' => $año];

            if ($mes) {
                $where[] = "MONTH(v.fecha_inicio) = :mes";
                $params['mes'] = $mes;
            }

            $whereClause = implode(' AND ', $where);

            $sql = "SELECT v.*, 
                           CONCAT(c.primer_nombre, ' ', c.primer_apellido) as colaborador_nombre,
                           c.cedula,
                           d.nombre as departamento
                    FROM {$this->table} v
                    JOIN colaboradores c ON v.colaborador_id = c.id
                    JOIN departamentos d ON c.departamento_id = d.id
                    WHERE {$whereClause}
                    ORDER BY v.fecha_inicio";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener calendario: " . $e->getMessage());
            return [];
        }
    }

    // Estadísticas de vacaciones
    public function obtenerEstadisticas($año = null)
    {
        try {
            $año = $año ?? date('Y');

            $stats = [];

            // Total solicitudes por estado
            $sql = "SELECT estado, COUNT(*) as cantidad 
                    FROM {$this->table} 
                    WHERE YEAR(fecha_solicitud) = :año 
                    GROUP BY estado";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['año' => $año]);
            $stats['por_estado'] = $stmt->fetchAll();

            // Días por departamento
            $sql = "SELECT d.nombre as departamento, 
                           COUNT(v.id) as solicitudes,
                           SUM(CASE WHEN v.estado = 'Aprobada' THEN v.dias_solicitados ELSE 0 END) as dias_aprobados
                    FROM departamentos d
                    LEFT JOIN colaboradores c ON d.id = c.departamento_id
                    LEFT JOIN {$this->table} v ON c.id = v.colaborador_id AND YEAR(v.fecha_inicio) = :año
                    WHERE d.activo = 1
                    GROUP BY d.id, d.nombre
                    ORDER BY dias_aprobados DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['año' => $año]);
            $stats['por_departamento'] = $stmt->fetchAll();

            // Tendencia mensual
            $sql = "SELECT MONTH(fecha_inicio) as mes, 
                           COUNT(*) as solicitudes,
                           SUM(dias_solicitados) as dias_totales
                    FROM {$this->table} 
                    WHERE YEAR(fecha_inicio) = :año AND estado = 'Aprobada'
                    GROUP BY MONTH(fecha_inicio)
                    ORDER BY mes";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['año' => $año]);
            $stats['por_mes'] = $stmt->fetchAll();

            return $stats;
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }

    // Obtener departamentos para filtros
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

    // Obtener colaboradores activos para selects
    public function obtenerColaboradores($departamento_id = null)
    {
        try {
            $where = "c.empleado_activo = 1";
            $params = [];

            if ($departamento_id) {
                $where .= " AND c.departamento_id = :departamento_id";
                $params['departamento_id'] = $departamento_id;
            }

            $sql = "SELECT c.id, 
                           CONCAT(c.primer_nombre, ' ', c.primer_apellido) as nombre_completo,
                           c.cedula,
                           c.fecha_contratacion,
                           d.nombre as departamento
                    FROM colaboradores c
                    JOIN departamentos d ON c.departamento_id = d.id
                    WHERE {$where}
                    ORDER BY c.primer_apellido, c.primer_nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener colaboradores: " . $e->getMessage());
            return [];
        }
    }
}
