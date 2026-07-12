<?php
/**
 * Modelo de Reloj (Producto)
 * Gestiona todas las operaciones relacionadas con productos
 */
class Reloj {
    private $conn;
    private $table = 'relojes';
    
    public $id;
    public $nombre;
    public $marca;
    public $categoria;
    public $descripcion;
    public $precio;
    public $material;
    public $genero;
    public $estado;
    public $imagen;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todos los relojes con filtros
     */
    public function obtenerTodos($filtros = []) {
        $where = ["r.estado = 'disponible'"];
        $params = [];
        
        if (!empty($filtros['categoria'])) {
            $where[] = "r.categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }
        
        if (!empty($filtros['genero'])) {
            $where[] = "r.genero = :genero";
            $params[':genero'] = $filtros['genero'];
        }
        
        if (!empty($filtros['marca'])) {
            $where[] = "r.marca = :marca";
            $params[':marca'] = $filtros['marca'];
        }
        
        if (!empty($filtros['busqueda'])) {
            $where[] = "(r.nombre LIKE :busqueda OR r.marca LIKE :busqueda OR r.descripcion LIKE :busqueda)";
            $params[':busqueda'] = "%{$filtros['busqueda']}%";
        }
        
        $where_clause = implode(' AND ', $where);
        
        // Ordenamiento
        $order_by = "r.nombre ASC";
        if (!empty($filtros['orden'])) {
            switch ($filtros['orden']) {
                case 'precio_asc':
                    $order_by = "precio_final ASC";
                    break;
                case 'precio_desc':
                    $order_by = "precio_final DESC";
                    break;
                case 'marca':
                    $order_by = "r.marca ASC";
                    break;
                case 'categoria':
                    $order_by = "r.categoria ASC";
                    break;
            }
        }
        
        $query = "SELECT r.*, p.porcentaje_descuento, p.fecha_fin, i.cantidad_disponible,
                  CASE 
                    WHEN p.id IS NOT NULL AND p.activo = 1 AND NOW() BETWEEN p.fecha_inicio AND p.fecha_fin 
                    THEN r.precio * (1 - p.porcentaje_descuento/100)
                    ELSE r.precio 
                  END as precio_final
                  FROM " . $this->table . " r 
                  LEFT JOIN promociones p ON r.id = p.reloj_id AND p.activo = 1 AND NOW() BETWEEN p.fecha_inicio AND p.fecha_fin
                  LEFT JOIN inventario i ON r.id = i.reloj_id
                  WHERE {$where_clause}
                  ORDER BY {$order_by}";
        
        if (!empty($filtros['limit'])) {
            $query .= " LIMIT " . intval($filtros['limit']);
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener reloj por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT r.*, i.cantidad_disponible, p.porcentaje_descuento,
                  CASE 
                    WHEN p.id IS NOT NULL AND p.activo = 1 AND NOW() BETWEEN p.fecha_inicio AND p.fecha_fin 
                    THEN r.precio * (1 - p.porcentaje_descuento/100)
                    ELSE r.precio 
                  END as precio_final
                  FROM " . $this->table . " r 
                  LEFT JOIN inventario i ON r.id = i.reloj_id
                  LEFT JOIN promociones p ON r.id = p.reloj_id AND p.activo = 1
                  WHERE r.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear nuevo reloj
     */
    public function crear() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, marca, categoria, descripcion, precio, material, genero, estado, imagen) 
                  VALUES (:nombre, :marca, :categoria, :descripcion, :precio, :material, :genero, :estado, :imagen)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':marca', $this->marca);
        $stmt->bindParam(':categoria', $this->categoria);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':material', $this->material);
        $stmt->bindParam(':genero', $this->genero);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':imagen', $this->imagen);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Actualizar reloj
     */
    public function actualizar() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, marca = :marca, categoria = :categoria, 
                      descripcion = :descripcion, precio = :precio, material = :material, 
                      genero = :genero, estado = :estado, imagen = :imagen 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':marca', $this->marca);
        $stmt->bindParam(':categoria', $this->categoria);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':material', $this->material);
        $stmt->bindParam(':genero', $this->genero);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':imagen', $this->imagen);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar reloj
     */
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener productos destacados
     */
    public function obtenerDestacados($limit = 6) {
        return $this->obtenerTodos(['limit' => $limit]);
    }
    
    /**
     * Buscar productos
     */
    public function buscar($termino) {
        return $this->obtenerTodos(['busqueda' => $termino]);
    }
    
    /**
     * Obtener los productos más vendidos
     */
    public function obtenerTopVendidos($limit = 5) {
        $query = "SELECT r.*, i.cantidad_vendida, i.cantidad_disponible 
                  FROM " . $this->table . " r 
                  JOIN inventario i ON r.id = i.reloj_id 
                  WHERE i.cantidad_vendida > 0 
                  ORDER BY i.cantidad_vendida DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
