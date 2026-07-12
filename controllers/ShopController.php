<?php
/**
 * Controlador de Tienda
 * Gestiona el catálogo y página principal
 */
class ShopController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Página principal con productos destacados
     */
    public function index() {
        $reloj = new Reloj($this->db);
        $productos = $reloj->obtenerDestacados(8);
        
        require_once 'views/shop/index.php';
    }
    
    /**
     * Catálogo completo con filtros
     */
    public function catalogo() {
        $reloj = new Reloj($this->db);
        
        $filtros = [
            'categoria' => $_GET['categoria'] ?? '',
            'genero' => $_GET['genero'] ?? '',
            'marca' => $_GET['marca'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? '',
            'orden' => $_GET['orden'] ?? 'nombre'
        ];
        
        $productos = $reloj->obtenerTodos($filtros);
        
        require_once 'views/shop/catalogo.php';
    }
}
