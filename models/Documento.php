<?php
// =====================================================
// models/Documento.php - Modelo para documentos académicos
// =====================================================

class Documento
{
    private $db;
    private $table = 'documentos_academicos';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerPorId($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener documento: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar documento: " . $e->getMessage());
            return false;
        }
    }
}
