<?php
/**
 * Controlador de Administración de Inventario
 * Gestiona stock y control de inventario
 */
class AdminInventarioController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Ver inventario completo
     */
    public function listar() {
        requireAdmin();
        
        $inventario = new Inventario($this->db);
        $items = $inventario->obtenerTodo();
        
        $estadisticas = [
            'total_productos' => count($items),
            'productos_agotados' => $inventario->contarAgotados(),
            'stock_total' => $inventario->obtenerStockTotal(),
            'valor_inventario' => $inventario->obtenerValorInventario()
        ];
        
        require_once 'views/admin/inventario-listar.php';
    }
    
    /**
     * Actualizar cantidad de producto
     */
    public function actualizar() {
        requireAdmin();
        
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $tipo = sanitize($_POST['tipo'] ?? ''); // 'disponible' o 'vendida'
        
        if ($producto_id <= 0 || $cantidad < 0 || empty($tipo)) {
            setFlash('error', 'Datos inválidos');
        } else {
            $inventario = new Inventario($this->db);
            
            if ($tipo === 'disponible') {
                $inventario->actualizarDisponible($producto_id, $cantidad);
            } elseif ($tipo === 'vendida') {
                $inventario->actualizarVendida($producto_id, $cantidad);
            } else {
                setFlash('error', 'Tipo de actualización inválido');
                redirect('admin/inventario.php');
            }
            
            setFlash('success', 'Inventario actualizado');
        }
        
        redirect('admin/inventario.php');
    }
    
    /**
     * Reporte de inventario bajo
     */
    public function bajo() {
        requireAdmin();
        
        $inventario = new Inventario($this->db);
        $items_bajo = $inventario->obtenerStockBajo(10); // Menos de 10 unidades
        
        require_once 'views/admin/inventario-bajo.php';
    }
}
