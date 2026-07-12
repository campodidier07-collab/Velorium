<?php
/**
 * Modelo de Método de Pago
 * Gestiona los métodos de pago disponibles
 */
class MetodoPago {
    private $conn;
    private $table = 'metodos_pago';
    
    public $id;
    public $nombre;
    public $descripcion;
    public $icono;
    public $configuracion;
    public $activo;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todos los métodos de pago
     */
    public function obtenerTodos($solo_activos = false) {
        $where = $solo_activos ? "WHERE activo = 1" : "";
        
        $query = "SELECT * FROM " . $this->table . " {$where} ORDER BY creado_en DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener método de pago por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear método de pago
     */
    public function crear() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, descripcion, icono, configuracion, activo) 
                  VALUES (:nombre, :descripcion, :icono, :configuracion, :activo)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':icono', $this->icono);
        $stmt->bindParam(':configuracion', $this->configuracion);
        $stmt->bindParam(':activo', $this->activo);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar método de pago
     */
    public function actualizar() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, descripcion = :descripcion, 
                      icono = :icono, configuracion = :configuracion, activo = :activo 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':icono', $this->icono);
        $stmt->bindParam(':configuracion', $this->configuracion);
        $stmt->bindParam(':activo', $this->activo);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Cambiar estado (activo/inactivo)
     */
    public function cambiarEstado($id, $estado) {
        $query = "UPDATE " . $this->table . " SET activo = :estado WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar método de pago
     */
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}

    /**
     * Obtener métodos de pago activos
     */
    public function obtenerActivos() {
        return $this->obtenerTodos(true);
    }
}
