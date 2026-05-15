<?php
// ARCHIVO: conexion.php
// Configuración de conexión a la BD 'coworking_db' usando PDO.

class Database {
    private $host = "localhost";
    private $db_name = "cerrajeria_db"; // <-- Apuntando a la BD de la captura
    private $username = "root";
    private $password = ""; 
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password, [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ]);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Maneja el error de conexión (aparecerá si la BD no existe o no está activa)
            echo "Error: No se pudo conectar a la base de datos. Verifique 'coworking_db'.";
            exit();
        }
        return $this->conn;
    }
}

function getDB() {
    $database = new Database();
    return $database->getConnection();
}
?>