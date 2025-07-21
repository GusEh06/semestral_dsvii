<?php

class Estadistica
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function colaboradoresPorSexo()
    {
        $sql = "SELECT sexo, COUNT(*) AS total FROM colaboradores GROUP BY sexo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function colaboradoresPorRangoEdad()
    {
        $sql = "
            SELECT 
              CASE 
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 24 THEN '18-24'
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 25 AND 30 THEN '25-30'
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 31 AND 40 THEN '31-40'
                ELSE '40+' 
              END AS rango_edad,
              COUNT(*) AS total
            FROM colaboradores
            GROUP BY rango_edad
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function colaboradoresPorDireccion()
    {
        $sql = "SELECT direccion, COUNT(*) AS total FROM colaboradores GROUP BY direccion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
