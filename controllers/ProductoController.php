<?php
/**
 * Controlador de Producto
 * Gestiona detalles de productos individuales
 */
class ProductoController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Mostrar detalles de un producto
     */
    public function detalle($id) {
        $reloj = new Reloj($this->db);
        $producto = $reloj->obtenerPorId($id);
        
        if (!$producto) {
            require_once 'views/shop/no-encontrado.php';
            return;
        }
        
        require_once 'views/shop/producto-detalle.php';
    }
    
    /**
     * Buscar productos
     */
    public function buscar() {
        $termino = sanitize($_GET['q'] ?? '');
        $reloj = new Reloj($this->db);
        
        $productos = [];
        if (!empty($termino)) {
            $productos = $reloj->buscar($termino);
        }
        
        require_once 'views/shop/resultados-busqueda.php';
    }
}
