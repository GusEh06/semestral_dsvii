<?php
class Reporte
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerColaboradores($filtros, $limite = null)
    {
        $sql = "SELECT c.id, c.primer_nombre, c.segundo_nombre, c.primer_apellido, c.segundo_apellido, 
                    c.sexo, c.cedula, c.sueldo, c.fecha_nacimiento
                FROM colaboradores c
                WHERE 1=1";

        $params = [];

        if ($filtros['sexo']) {
            $sql .= " AND c.sexo = :sexo";
            $params['sexo'] = $filtros['sexo'];
        }

        if ($filtros['edad']) {
            $sql .= " AND YEAR(CURDATE()) - YEAR(c.fecha_nacimiento) >= :edad";
            $params['edad'] = $filtros['edad'];
        }

        if ($filtros['nombre']) {
            $sql .= " AND c.primer_nombre LIKE :nombre";
            $params['nombre'] = "%" . $filtros['nombre'] . "%";
        }

        if ($filtros['apellido']) {
            $sql .= " AND c.primer_apellido LIKE :apellido";
            $params['apellido'] = "%" . $filtros['apellido'] . "%";
        }

        if ($filtros['salario']) {
            $sql .= " AND c.sueldo >= :salario";
            $params['salario'] = $filtros['salario'];
        }

        // Paginación
        if ($limite) {
            $offset = ($filtros['pagina'] - 1) * $limite;
            $sql .= " LIMIT :offset, :limite";
            $params['offset'] = $offset;
            $params['limite'] = $limite;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function contarColaboradores($filtros)
    {
        $sql = "SELECT COUNT(c.id) 
                FROM colaboradores c
                WHERE 1=1";

        $params = [];

        if ($filtros['sexo']) {
            $sql .= " AND c.sexo = :sexo";
            $params['sexo'] = $filtros['sexo'];
        }

        if ($filtros['edad']) {
            $sql .= " AND YEAR(CURDATE()) - YEAR(c.fecha_nacimiento) >= :edad";
            $params['edad'] = $filtros['edad'];
        }

        if ($filtros['nombre']) {
            $sql .= " AND c.primer_nombre LIKE :nombre";
            $params['nombre'] = "%" . $filtros['nombre'] . "%";
        }

        if ($filtros['apellido']) {
            $sql .= " AND c.primer_apellido LIKE :apellido";
            $params['apellido'] = "%" . $filtros['apellido'] . "%";
        }

        if ($filtros['salario']) {
            $sql .= " AND c.sueldo >= :salario";
            $params['salario'] = $filtros['salario'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function generarExcel($colaboradores)
    {
        // Incluir la librería PhpSpreadsheet para crear el archivo Excel
        require_once 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Establecer encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Apellido');
        $sheet->setCellValue('D1', 'Cédula');
        $sheet->setCellValue('E1', 'Sexo');
        $sheet->setCellValue('F1', 'Salario');
        $sheet->setCellValue('G1', 'Fecha de Nacimiento');

        // Llenar los datos de los colaboradores
        $row = 2;
        foreach ($colaboradores as $colaborador) {
            $sheet->setCellValue('A' . $row, $colaborador['id']);
            $sheet->setCellValue('B' . $row, $colaborador['primer_nombre'] . ' ' . $colaborador['segundo_nombre']);
            $sheet->setCellValue('C' . $row, $colaborador['primer_apellido'] . ' ' . $colaborador['segundo_apellido']);
            $sheet->setCellValue('D' . $row, $colaborador['cedula']);
            $sheet->setCellValue('E' . $row, $colaborador['sexo']);
            $sheet->setCellValue('F' . $row, $colaborador['sueldo']);
            $sheet->setCellValue('G' . $row, $colaborador['fecha_nacimiento']);
            $row++;
        }

        // Guardar el archivo Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Reporte_Colaboradores_" . date('Y-m-d_H-i-s') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
?>
