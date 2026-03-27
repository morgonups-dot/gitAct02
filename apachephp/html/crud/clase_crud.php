<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clase_conexion.php';

class Crud
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    public function crear($nombre, $apellido, $correo, $celular)
    {
        $conn = $this->conexion->getConexion();
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, celular) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $apellido, $correo, $celular);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function leerTodo($orden = 'id', $direccion = 'DESC', $busqueda = '', $campoBusqueda = 'todos')
    {
        $conn = $this->conexion->getConexion();
        
        $columnasValidas = ['id', 'nombre', 'apellido', 'correo', 'celular'];
        if (!in_array($orden, $columnasValidas)) {
            $orden = 'id';
        }
        $direccion = strtoupper($direccion) === 'ASC' ? 'ASC' : 'DESC';
        
        $columnasBusqueda = ['nombre', 'apellido', 'correo', 'celular'];
        
        if (!empty($busqueda)) {
            $busquedaSegura = "%" . $busqueda . "%";
            
            if ($campoBusqueda === 'todos' || !in_array($campoBusqueda, $columnasBusqueda)) {
                $sql = "SELECT id, nombre, apellido, correo, celular FROM usuarios 
                        WHERE nombre LIKE ? OR apellido LIKE ? OR correo LIKE ? OR celular LIKE ? 
                        ORDER BY " . $orden . " " . $direccion;
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $busquedaSegura, $busquedaSegura, $busquedaSegura, $busquedaSegura);
            } else {
                $sql = "SELECT id, nombre, apellido, correo, celular FROM usuarios 
                        WHERE " . $campoBusqueda . " LIKE ? 
                        ORDER BY " . $orden . " " . $direccion;
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $busquedaSegura);
            }
        } else {
            $sql = "SELECT id, nombre, apellido, correo, celular FROM usuarios ORDER BY " . $orden . " " . $direccion;
            $stmt = $conn->prepare($sql);
        }
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuarios = [];
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila;
            }
        }
        $stmt->close();
        return $usuarios;
    }

    public function leerPorId($id)
    {
        $conn = $this->conexion->getConexion();
        $sql = "SELECT id, nombre, apellido, correo, celular FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = null;
        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
        }
        $stmt->close();
        return $usuario;
    }

    public function actualizar($id, $nombre, $apellido, $correo, $celular)
    {
        $conn = $this->conexion->getConexion();
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, celular = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $apellido, $correo, $celular, $id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function eliminar($id)
    {
        $conn = $this->conexion->getConexion();
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function __destruct()
    {
        $this->conexion->cerrar();
    }
}
