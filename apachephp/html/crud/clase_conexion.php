<?php

require_once __DIR__ . '/config.php';

class Conexion
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $db = DB_NAME;
    private $port = DB_PORT;
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port);

        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");
    }

    public function getConexion()
    {
        return $this->conn;
    }

    public function cerrar()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
