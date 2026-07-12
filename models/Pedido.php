<?php
/**
 * Modelo de Pedido
 * Gestiona todas las operaciones relacionadas con pedidos
 */
class Pedido {
    private $conn;
    private $table = 'pedidos';
    
    public $id;
    public $cliente_id;
    public $metodo_pago_id;
    public $estado;
    public $total;
    public $direccion_envio;
    public $notas;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crear nuevo pedido (con items array)
     */
    public function crear($items = []) {
        try {
            $this->conn->beginTransaction();
            
            // Crear pedido
            $query = "INSERT INTO " . $this->table . " 
                      (cliente_id, metodo_pago_id, estado, total, direccion_envio, notas) 
                      VALUES (:cliente_id, :metodo_pago_id, :estado, :total, :direccion, :notas)";
            
            $stmt = $this->conn->prepare($query);
            $estado = 'pendiente';
            
            $stmt->bindParam(':cliente_id', $this->cliente_id);
            $stmt->bindParam(':metodo_pago_id', $this->metodo_pago_id);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':total', $this->total);
            $stmt->bindParam(':direccion', $this->direccion_envio);
            $stmt->bindParam(':notas', $this->notas);
            
            $stmt->execute();
            $pedido_id = $this->conn->lastInsertId();
            
            // Agregar items del pedido si se proporcionan
            if (!empty($items)) {
                foreach ($items as $item) {
                    $query = "INSERT INTO items_pedido 
                              (pedido_id, reloj_id, cliente_id, cantidad, precio_unitario, subtotal) 
                              VALUES (:pedido_id, :reloj_id, :cliente_id, :cantidad, :precio, :subtotal)";
                    
                    $stmt = $this->conn->prepare($query);
                    
                    $subtotal = $item['cantidad'] * $item['precio_unitario'];
                    
                    $stmt->bindParam(':pedido_id', $pedido_id);
                    $stmt->bindParam(':reloj_id', $item['reloj_id']);
                    $stmt->bindParam(':cliente_id', $this->cliente_id);
                    $stmt->bindParam(':cantidad', $item['cantidad']);
                    $stmt->bindParam(':precio', $item['precio_unitario']);
                    $stmt->bindParam(':subtotal', $subtotal);
                    
                    $stmt->execute();
                    
                    // Actualizar inventario
                    $query = "UPDATE inventario SET 
                              cantidad_disponible = cantidad_disponible - :cantidad,
                              cantidad_vendida = cantidad_vendida + :cantidad
                              WHERE reloj_id = :reloj_id";
                    
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':cantidad', $item['cantidad']);
                    $stmt->bindParam(':reloj_id', $item['reloj_id']);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            return $pedido_id;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener pedidos por cliente
     */
    public function obtenerPorCliente($cliente_id, $estado = null) {
        $where = "p.cliente_id = :cliente_id";
        $params = [':cliente_id' => $cliente_id];
        
        if ($estado) {
            $where .= " AND p.estado = :estado";
            $params[':estado'] = $estado;
        }
        
        $query = "SELECT p.*, mp.nombre as metodo_pago_nombre
                  FROM " . $this->table . " p
                  LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                  WHERE {$where}
                  ORDER BY p.fecha_pedido DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener pedido por ID
     */
    public function obtenerPorId($id, $cliente_id = null) {
        $where = "p.id = :id";
        $params = [':id' => $id];
        
        if ($cliente_id) {
            $where .= " AND p.cliente_id = :cliente_id";
            $params[':cliente_id'] = $cliente_id;
        }
        
        $query = "SELECT p.*, mp.nombre as metodo_pago_nombre, u.nombre as cliente_nombre, u.email as cliente_email
                  FROM " . $this->table . " p
                  LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                  LEFT JOIN usuarios u ON p.cliente_id = u.id
                  WHERE {$where}";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener items de un pedido
     */
    public function obtenerItems($pedido_id) {
        $query = "SELECT ip.*, r.nombre, r.marca, r.imagen
                  FROM items_pedido ip
                  JOIN relojes r ON ip.reloj_id = r.id
                  WHERE ip.pedido_id = :pedido_id
                  ORDER BY ip.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pedido_id', $pedido_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Actualizar estado del pedido
     */
    public function actualizarEstado($id, $estado, $cliente_id = null) {
        $where = "id = :id";
        $params = [':id' => $id, ':estado' => $estado];
        
        if ($cliente_id) {
            $where .= " AND cliente_id = :cliente_id";
            $params[':cliente_id'] = $cliente_id;
        }
        
        $query = "UPDATE " . $this->table . " SET estado = :estado WHERE {$where}";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Cancelar pedido
     */
    public function cancelar($id, $cliente_id) {
        $query = "UPDATE " . $this->table . " 
                  SET estado = 'cancelado' 
                  WHERE id = :id AND cliente_id = :cliente_id AND estado = 'pendiente'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':cliente_id', $cliente_id);
        
        return $stmt->execute();
    }
    
    /**
     * Contar pedidos por estado
     */
    public function contarPorEstado($cliente_id) {
        $query = "SELECT estado, COUNT(*) as cantidad
                  FROM " . $this->table . "
                  WHERE cliente_id = :cliente_id
                  GROUP BY estado";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['estado']] = $row['cantidad'];
        }
        
        return $result;
    }
    
    /**
     * Obtener todos los pedidos (Admin)
     */
    public function obtenerTodos($filtros = []) {
        $where = [];
        $params = [];
        
        if (!empty($filtros['estado'])) {
            $where[] = "p.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['fecha'])) {
            $where[] = "DATE(p.fecha_pedido) = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }
        
        if (!empty($filtros['cliente'])) {
            $where[] = "(u.nombre LIKE :cliente OR u.email LIKE :cliente)";
            $params[':cliente'] = "%{$filtros['cliente']}%";
        }
        
        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $query = "SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email,
                  COUNT(ip.id) as total_items,
                  mp.nombre as metodo_pago_nombre
                  FROM " . $this->table . " p 
                  JOIN usuarios u ON p.cliente_id = u.id 
                  LEFT JOIN items_pedido ip ON p.id = ip.pedido_id
                  LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                  {$where_clause}
                  GROUP BY p.id
                  ORDER BY p.fecha_pedido DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Agregar item a pedido (método simplificado)
     */
    public function agregarItem($pedido_id, $reloj_id, $cantidad, $precio_unitario) {
        $query = "INSERT INTO items_pedido 
                  (pedido_id, reloj_id, cliente_id, cantidad, precio_unitario, subtotal) 
                  VALUES (:pedido_id, :reloj_id, :cliente_id, :cantidad, :precio, :subtotal)";
        
        $stmt = $this->conn->prepare($query);
        
        $subtotal = $cantidad * $precio_unitario;
        
        $stmt->bindParam(':pedido_id', $pedido_id);
        $stmt->bindParam(':reloj_id', $reloj_id);
        $stmt->bindParam(':cliente_id', $this->cliente_id);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':precio', $precio_unitario);
        $stmt->bindParam(':subtotal', $subtotal);
        
        if ($stmt->execute()) {
            // Actualizar inventario
            $query = "UPDATE inventario SET 
                      cantidad_disponible = cantidad_disponible - :cantidad,
                      cantidad_vendida = cantidad_vendida + :cantidad
                      WHERE reloj_id = :reloj_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':reloj_id', $reloj_id);
            
            return $stmt->execute();
        }
        
        return false;
    }
    
    /**
     * Crear pedido (versión simplificada sin items array)
     */
    public function crearPedido() {
        $query = "INSERT INTO " . $this->table . " 
                  (cliente_id, metodo_pago_id, estado, total, direccion_envio, notas) 
                  VALUES (:cliente_id, :metodo_pago_id, :estado, :total, :direccion, :notas)";
        
        $stmt = $this->conn->prepare($query);
        
        $estado = 'pendiente';
        
        $stmt->bindParam(':cliente_id', $this->cliente_id);
        $stmt->bindParam(':metodo_pago_id', $this->metodo_pago_id);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':direccion', $this->direccion_envio);
        $stmt->bindParam(':notas', $this->notas);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Contar total de pedidos
     */
    public function contarTotal() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Contar pedidos por estado (admin)
     */
    public function contarPorEstadoAdmin($estado) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE estado = :estado";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Obtener pedidos recientes
     */
    public function obtenerRecientes($limit = 10) {
        $query = "SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email,
                  mp.nombre as metodo_pago_nombre
                  FROM " . $this->table . " p 
                  JOIN usuarios u ON p.cliente_id = u.id 
                  LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                  ORDER BY p.fecha_pedido DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener ventas últimas N horas
     */
    public function obtenerVentasUltimas($horas = 24) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE estado IN ('completado', 'entregado') 
                  AND fecha_pedido >= DATE_SUB(NOW(), INTERVAL :horas HOUR)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':horas', $horas, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Obtener ingresos últimas N horas
     */
    public function obtenerIngresosUltimos($horas = 24) {
        $query = "SELECT COALESCE(SUM(total), 0) as ingresos FROM " . $this->table . " 
                  WHERE estado IN ('completado', 'entregado') 
                  AND fecha_pedido >= DATE_SUB(NOW(), INTERVAL :horas HOUR)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':horas', $horas, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['ingresos'];
    }
    
    /**
     * Obtener pedidos por estado (para admin)
     */
    public function obtenerPorEstadoAdmin($estado) {
        $query = "SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email,
                  COUNT(ip.id) as total_items,
                  mp.nombre as metodo_pago_nombre
                  FROM " . $this->table . " p 
                  JOIN usuarios u ON p.cliente_id = u.id 
                  LEFT JOIN items_pedido ip ON p.id = ip.pedido_id
                  LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                  WHERE p.estado = :estado
                  GROUP BY p.id
                  ORDER BY p.fecha_pedido DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
