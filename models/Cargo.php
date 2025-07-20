<?php
class Cargo
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerTodos($filtros = [])
    {
        $sql = "SELECT ch.*, 
            c.cedula,
            CONCAT(c.primer_nombre, ' ', c.primer_apellido) AS colaborador_nombre,
            ca.nombre AS cargo_nombre,
            d.nombre AS departamento_nombre
        FROM cargos_historico ch
        JOIN colaboradores c ON ch.colaborador_id = c.id
        JOIN cargos ca ON ch.cargo_nuevo_id = ca.id
        JOIN departamentos d ON ch.departamento_nuevo_id = d.id";

        $params = [];

        if (!empty($filtros['direccion'])) {
            $sql .= " AND c.direccion LIKE :direccion";
            $params['direccion'] = '%' . $filtros['direccion'] . '%';
        }

        if (!empty($filtros['departamento'])) {
            $sql .= " AND ch.departamento_nuevo_id = :departamento";
            $params['departamento'] = $filtros['departamento'];
        }

        if (!empty($filtros['tipo_movimiento'])) {
            $sql .= " AND ch.tipo_movimiento = :tipo_movimiento";
            $params['tipo_movimiento'] = $filtros['tipo_movimiento'];
        }

        $sql .= " ORDER BY ch.fecha_efectiva DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function obtenerUltimoMovimiento($colaborador_id)
    {
        $sql = "SELECT * FROM cargos_historico 
                WHERE colaborador_id = :colaborador_id
                ORDER BY fecha_efectiva DESC, id DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['colaborador_id' => $colaborador_id]);
        return $stmt->fetch();
    }

    public function crear($datos)
    {
        $anterior = $this->obtenerUltimoMovimiento($datos['colaborador_id']);

        $stringFirma = $this->generarStringFirma($datos);

        file_put_contents('string_firma.txt', $stringFirma);

        // Cargar clave privada
        $privateKeyPath = __DIR__ . '/../keys/private.pem';
        if (!file_exists($privateKeyPath)) {
            throw new Exception('Clave privada no encontrada');
        }
        $pkeyid = openssl_pkey_get_private(file_get_contents($privateKeyPath));

        // Firmar datos
        openssl_sign($stringFirma, $firma, $pkeyid, OPENSSL_ALGO_SHA256);
        $firmaBase64 = base64_encode($firma);

        // Guardar en DB
        $sql = "INSERT INTO cargos_historico
                (colaborador_id, cargo_anterior_id, cargo_nuevo_id,
                 sueldo_anterior, sueldo_nuevo,
                 departamento_anterior_id, departamento_nuevo_id,
                 tipo_movimiento, fecha_efectiva, motivo, usuario_registro_id, activo, firma_digital, created_at)
                VALUES
                (:colaborador_id, :cargo_anterior_id, :cargo_nuevo_id,
                 :sueldo_anterior, :sueldo_nuevo,
                 :departamento_anterior_id, :departamento_nuevo_id,
                 :tipo_movimiento, :fecha_efectiva, :motivo, :usuario_registro_id, 1, :firma_digital, NOW())";

        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([
            'colaborador_id'          => $datos['colaborador_id'],
            'cargo_anterior_id'       => $anterior['cargo_nuevo_id'] ?? null,
            'cargo_nuevo_id'          => $datos['cargo_nuevo_id'],
            'sueldo_anterior'         => $anterior['sueldo_nuevo'] ?? null,
            'sueldo_nuevo'            => $datos['sueldo_nuevo'],
            'departamento_anterior_id'=> $anterior['departamento_nuevo_id'] ?? null,
            'departamento_nuevo_id'   => $datos['departamento_nuevo_id'],
            'tipo_movimiento'         => $datos['tipo_movimiento'],
            'fecha_efectiva'          => $datos['fecha_efectiva'],
            'motivo'                  => $datos['motivo'],
            'usuario_registro_id'     => $datos['usuario_registro_id'],
            'firma_digital'           => $firmaBase64
        ]);

        if ($resultado) {
            $this->actualizarDatosColaborador($datos['colaborador_id'], [
                'cargo_actual_id'    => $datos['cargo_nuevo_id'],
                'sueldo'             => $datos['sueldo_nuevo'],
                'departamento_id'    => $datos['departamento_nuevo_id']
            ]);
        }

        return $resultado;
    }

    private function actualizarDatosColaborador($colaborador_id, $nuevosDatos)
    {
        $sql = "UPDATE colaboradores SET 
                    cargo_actual_id = :cargo_actual_id,
                    sueldo = :sueldo,
                    departamento_id = :departamento_id
                WHERE id = :colaborador_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'cargo_actual_id'   => $nuevosDatos['cargo_actual_id'],
            'sueldo'            => $nuevosDatos['sueldo'],
            'departamento_id'   => $nuevosDatos['departamento_id'],
            'colaborador_id'    => $colaborador_id
        ]);
    }

    public function verificarFirma($cargo)
    {
        $stringFirma = $this->generarStringFirma([
            'colaborador_id'        => $cargo['colaborador_id'],
            'cargo_nuevo_id'        => $cargo['cargo_nuevo_id'],
            'sueldo_nuevo'          => $cargo['sueldo_nuevo'],
            'departamento_nuevo_id' => $cargo['departamento_nuevo_id'],
            'tipo_movimiento'       => $cargo['tipo_movimiento'],
            'fecha_efectiva'        => $cargo['fecha_efectiva']
        ]);

        $publicKeyPath = __DIR__ . '/../keys/public.pem';
        if (!file_exists($publicKeyPath)) {
            return false;
        }

        $pubkeyid = openssl_pkey_get_public(file_get_contents($publicKeyPath));
        return openssl_verify($stringFirma, base64_decode($cargo['firma_digital']), $pubkeyid, OPENSSL_ALGO_SHA256) === 1;
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

    public function obtenerPorIdConNombres($id)
    {
        $sql = "SELECT ch.*,
                CONCAT(c.primer_nombre, ' ', c.primer_apellido) AS colaborador_nombre,
                c.cedula AS colaborador_cedula,
                ca.nombre AS cargo_nombre,
                d.nombre AS departamento_nombre,
                ca_ant.nombre AS cargo_anterior,
                d_ant.nombre AS departamento_anterior,
                ch_ant.sueldo_nuevo AS sueldo_anterior
            FROM cargos_historico ch
            JOIN colaboradores c ON ch.colaborador_id = c.id
            JOIN cargos ca ON ch.cargo_nuevo_id = ca.id
            JOIN departamentos d ON ch.departamento_nuevo_id = d.id
            LEFT JOIN cargos_historico ch_ant
                ON ch_ant.id = (
                    SELECT MAX(id)
                    FROM cargos_historico
                    WHERE colaborador_id = ch.colaborador_id
                    AND id < ch.id
                )
            LEFT JOIN cargos ca_ant ON ch_ant.cargo_nuevo_id = ca_ant.id
            LEFT JOIN departamentos d_ant ON ch_ant.departamento_nuevo_id = d_ant.id
            WHERE ch.id = :id
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function buscarColaboradorPorNombre($nombre)
    {
        $sql = "SELECT id FROM colaboradores
                WHERE CONCAT(primer_nombre, ' ', primer_apellido) = :nombre
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nombre' => $nombre]);
        return $stmt->fetch();
    }

    public function buscarColaboradorPorCedula($cedula)
    {
        $sql = "SELECT * FROM colaboradores WHERE cedula = :cedula LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetch();
    }

    public function obtenerDepartamentos()
    {
        $stmt = $this->db->prepare("SELECT id, nombre FROM departamentos WHERE activo = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerCargos()
    {
        $stmt = $this->db->prepare("SELECT id, nombre FROM cargos ORDER BY nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
