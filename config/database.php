<?php
// =====================================================
// config/database.php - Configuración de Base de Datos
// =====================================================

class Database
{
    private $host = 'localhost';
    private $db_name = 'capital_humano';
    private $username = 'root'; // Cambiar según tu configuración
    private $password = '';     // Cambiar según tu configuración
    private $charset = 'utf8mb4';
    private $pdo;

    public function getConnection()
    {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ];

                $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return $this->pdo;
    }
}
