<?php
/**
 * Modelo de Usuario
 * Gestiona todas las operaciones relacionadas con usuarios
 */
class Usuario {
    private $conn;
    private $table = 'usuarios';
    
    public $id;
    public $nombre;
    public $email;
    public $contraseña;
    public $rol;
    public $creado_en;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crear nuevo usuario
     */
    public function crear() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, email, contraseña, rol) 
                  VALUES (:nombre, :email, :password, :rol)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash de contraseña
        $hashed_password = password_hash($this->contraseña, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':rol', $this->rol);
        
        return $stmt->execute();
    }
    
    /**
     * Verificar credenciales de login
     */
    public function login($email, $password) {
        $query = "SELECT id, nombre, email, contraseña, rol 
                  FROM " . $this->table . " 
                  WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && password_verify($password, $row['contraseña'])) {
            $this->id = $row['id'];
            $this->nombre = $row['nombre'];
            $this->email = $row['email'];
            $this->rol = $row['rol'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verificar si email existe
     */
    public function emailExiste($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Obtener todos los clientes
     */
    public function obtenerClientes($filtros = []) {
        $where = ["rol = 'cliente'"];
        $params = [];
        
        if (!empty($filtros['search'])) {
            $where[] = "(nombre LIKE :search OR email LIKE :search)";
            $params[':search'] = "%{$filtros['search']}%";
        }
        
        if (!empty($filtros['fecha'])) {
            $where[] = "DATE(creado_en) = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT u.*, 
                  COUNT(DISTINCT p.id) as total_pedidos,
                  COALESCE(SUM(CASE WHEN p.estado != 'cancelado' THEN p.total ELSE 0 END), 0) as total_gastado,
                  MAX(p.fecha_pedido) as ultima_compra
                  FROM " . $this->table . " u 
                  LEFT JOIN pedidos p ON u.id = p.cliente_id 
                  WHERE {$where_clause}
                  GROUP BY u.id
                  ORDER BY u.creado_en DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, email = :email 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar usuario
     */
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Contar clientes
     */
    public function contarClientes() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE rol = 'cliente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
