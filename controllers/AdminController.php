<?php
/**
 * Controlador de Admin
 * Gestiona el dashboard y funciones administrativas generales
 */
class AdminController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Dashboard principal
     */
    public function dashboard() {
        requireAdmin();
        
        // Obtener estadísticas
        $pedido = new Pedido($this->db);
        $usuario = new Usuario($this->db);
        $reloj = new Reloj($this->db);
        $inventario = new Inventario($this->db);
        
        $stats = [
            'total_pedidos' => $pedido->contarTotal(),
            'pedidos_pendientes' => $pedido->contarPorEstadoAdmin('pendiente'),
            'pedidos_completados' => $pedido->contarPorEstadoAdmin('completado'),
            'total_clientes' => $usuario->contarClientes(),
            'total_productos' => count($reloj->obtenerTodos()),
            'productos_agotados' => $inventario->contarAgotados(),
            'ventas_ultimas_24h' => $pedido->obtenerVentasUltimas(24),
            'ingresos_ultimas_24h' => $pedido->obtenerIngresosUltimos(24)
        ];
        
        $pedidos_recientes = $pedido->obtenerRecientes(10);
        
        require_once 'views/admin/dashboard.php';
    }
    
    /**
     * Reportes
     */
    public function reportes() {
        requireAdmin();
        
        $titulo = 'Reportes y Estadísticas - Velorium';
        require_once 'views/admin/reportes.php';
    }
}
