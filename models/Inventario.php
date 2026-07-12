<?php
/**
 * Modelo de Inventario
 * Gestiona el stock de productos
 */
class Inventario {
    private $conn;
    private $table = 'inventario';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener inventario completo
     */
    public function obtenerTodo($filtros = []) {
        $where = [];
        $params = [];
        
        if (!empty($filtros['categoria'])) {
            $where[] = "r.categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }
        
        if (!empty($filtros['estado'])) {
            $where[] = "r.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['stock'])) {
            switch ($filtros['stock']) {
                case 'agotado':
                    $where[] = "i.cantidad_disponible = 0";
                    break;
                case 'bajo':
                    $where[] = "i.cantidad_disponible > 0 AND i.cantidad_disponible <= 5";
                    break;
                case 'normal':
                    $where[] = "i.cantidad_disponible > 5";
                    break;
            }
        }
        
        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $query = "SELECT r.*, i.cantidad_disponible, i.cantidad_vendida,
                  COALESCE(i.cantidad_vendida, 0) * r.precio as valor_vendido
                  FROM relojes r 
                  LEFT JOIN " . $this->table . " i ON r.id = i.reloj_id 
                  {$where_clause}
                  ORDER BY i.cantidad_disponible ASC, r.nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Actualizar stock
     */
    public function actualizarStock($reloj_id, $cantidad) {
        $query = "UPDATE " . $this->table . " 
                  SET cantidad_disponible = :cantidad 
                  WHERE reloj_id = :reloj_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':reloj_id', $reloj_id);
        
        return $stmt->execute();
    }
    
    /**
     * Crear registro de inventario
     */
    public function crear($reloj_id, $cantidad) {
        $query = "INSERT INTO " . $this->table . " 
                  (reloj_id, cantidad_disponible) 
                  VALUES (:reloj_id, :cantidad)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reloj_id', $reloj_id);
        $stmt->bindParam(':cantidad', $cantidad);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener estadísticas de inventario
     */
    public function obtenerEstadisticas() {
        $stats = [
            'total_productos' => 0,
            'productos_agotados' => 0,
            'stock_bajo' => 0,
            'valor_total' => 0
        ];
        
        $stmt = $this->conn->query("SELECT COUNT(*) FROM relojes WHERE estado = 'disponible'");
        $stats['total_productos'] = $stmt->fetchColumn();
        
        $stmt = $this->conn->query("SELECT COUNT(*) FROM " . $this->table . " WHERE cantidad_disponible = 0");
        $stats['productos_agotados'] = $stmt->fetchColumn();
        
        $stmt = $this->conn->query("SELECT COUNT(*) FROM " . $this->table . " WHERE cantidad_disponible > 0 AND cantidad_disponible <= 5");
        $stats['stock_bajo'] = $stmt->fetchColumn();
        
        $stmt = $this->conn->query("SELECT SUM(r.precio * i.cantidad_disponible) FROM relojes r JOIN " . $this->table . " i ON r.id = i.reloj_id WHERE r.estado = 'disponible'");
        $stats['valor_total'] = $stmt->fetchColumn() ?? 0;
        
        return $stats;
    }

    public $reloj_id;
    public $cantidad_disponible;
    
    /**
     * Actualizar cantidad disponible
     */
    public function actualizarDisponible($reloj_id, $cantidad) {
        $query = "UPDATE " . $this->table . " 
                  SET cantidad_disponible = :cantidad 
                  WHERE reloj_id = :reloj_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':reloj_id', $reloj_id);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar cantidad vendida
     */
    public function actualizarVendida($reloj_id, $cantidad) {
        $query = "UPDATE " . $this->table . " 
                  SET cantidad_vendida = cantidad_vendida + :cantidad 
                  WHERE reloj_id = :reloj_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':reloj_id', $reloj_id);
        
        return $stmt->execute();
    }
    
    /**
     * Contar productos agotados
     */
    public function contarAgotados() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE cantidad_disponible = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Obtener stock total
     */
    public function obtenerStockTotal() {
        $query = "SELECT COALESCE(SUM(cantidad_disponible), 0) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Obtener valor del inventario
     */
    public function obtenerValorInventario() {
        $query = "SELECT COALESCE(SUM(r.precio * i.cantidad_disponible), 0) as valor 
                  FROM relojes r 
                  LEFT JOIN " . $this->table . " i ON r.id = i.reloj_id 
                  WHERE r.estado = 'disponible'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['valor'];
    }
    
    /**
     * Obtener stock bajo (menos de N unidades)
     */
    public function obtenerStockBajo($limite = 10) {
        $query = "SELECT r.*, i.cantidad_disponible, i.cantidad_vendida
                  FROM relojes r 
                  LEFT JOIN " . $this->table . " i ON r.id = i.reloj_id 
                  WHERE i.cantidad_disponible < :limite AND r.estado = 'disponible'
                  ORDER BY i.cantidad_disponible ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear método simplificado
     */
    public function crearInventario() {
        $query = "INSERT INTO " . $this->table . " 
                  (reloj_id, cantidad_disponible) 
                  VALUES (:reloj_id, :cantidad)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reloj_id', $this->reloj_id);
        $stmt->bindParam(':cantidad', $this->cantidad_disponible);
        
        return $stmt->execute();
    }
}
