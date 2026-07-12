<?php
require_once 'config/database.php';

class Pedido {
    private $conn;
    private $table_name = "pedidos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear($cliente_id, $total, $metodo_pago_id, $direccion_envio = null, $notas = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (cliente_id, total, metodo_pago_id, direccion_envio, notas) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cliente_id, $total, $metodo_pago_id, $direccion_envio, $notas]);
        
        return $this->conn->lastInsertId();
    }

    public function obtenerPorCliente($cliente_id, $estado = null) {
        $query = "SELECT p.*, mp.nombre as metodo_pago_nombre
                  FROM " . $this->table_name . " p
                  LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                  WHERE p.cliente_id = ?";
        
        $params = [$cliente_id];
        
        if ($estado) {
            $query .= " AND p.estado = ?";
            $params[] = $estado;
        }
        
        $query .= " ORDER BY p.fecha_pedido DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id, $cliente_id = null) {
        $query = "SELECT p.*, mp.nombre as metodo_pago_nombre, u.nombre as cliente_nombre, u.email as cliente_email
                  FROM " . $this->table_name . " p
                  LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
                  LEFT JOIN usuarios u ON p.cliente_id = u.id
                  WHERE p.id = ?";
        
        $params = [$id];
        
        if ($cliente_id) {
            $query .= " AND p.cliente_id = ?";
            $params[] = $cliente_id;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalle($pedido_id) {
        $query = "SELECT dp.*, r.nombre, r.marca, r.imagen, r.precio as precio_actual
                  FROM items_pedido dp
                  JOIN relojes r ON dp.reloj_id = r.id
                  WHERE dp.pedido_id = ?
                  ORDER BY dp.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregarDetalle($pedido_id, $reloj_id, $cantidad, $precio_unitario) {
        $subtotal = $cantidad * $precio_unitario;
        
        $query = "INSERT INTO items_pedido (pedido_id, reloj_id, cantidad, precio_unitario, subtotal) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$pedido_id, $reloj_id, $cantidad, $precio_unitario, $subtotal]);
    }

    public function actualizarEstado($id, $estado, $cliente_id = null) {
        $query = "UPDATE " . $this->table_name . " SET estado = ? WHERE id = ?";
        $params = [$estado, $id];
        
        if ($cliente_id) {
            $query .= " AND cliente_id = ?";
            $params[] = $cliente_id;
        }
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    public function cancelar($id, $cliente_id) {
        // Solo se puede cancelar si está en estado 'pendiente'
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = 'cancelado' 
                  WHERE id = ? AND cliente_id = ? AND estado = 'pendiente'";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id, $cliente_id]);
    }

    public function contarPorEstado($cliente_id) {
        $query = "SELECT estado, COUNT(*) as cantidad
                  FROM " . $this->table_name . "
                  WHERE cliente_id = ?
                  GROUP BY estado";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cliente_id]);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['estado']] = $row['cantidad'];
        }
        
        return $result;
    }
}
?>