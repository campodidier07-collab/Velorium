<?php
/**
 * Controlador de Administración de Pedidos
 * Gestiona pedidos desde el área administrativa
 */
class AdminPedidoController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Listar todos los pedidos
     */
    public function listar() {
        requireAdmin();
        
        $estado = sanitize($_GET['estado'] ?? '');
        $pedido = new Pedido($this->db);
        
        if (empty($estado)) {
            $pedidos = $pedido->obtenerTodos();
        } else {
            $pedidos = $pedido->obtenerPorEstado($estado);
        }
        
        $estados_disponibles = ['pendiente', 'confirmado', 'enviado', 'entregado', 'completado', 'cancelado'];
        
        require_once 'views/admin/pedidos-listar.php';
    }
    
    /**
     * Ver detalle de pedido
     */
    public function detalle($id) {
        requireAdmin();
        
        $pedido = new Pedido($this->db);
        $pedido_detalle = $pedido->obtenerPorId($id);
        
        if (!$pedido_detalle) {
            setFlash('error', 'Pedido no encontrado');
            redirect('admin/pedidos.php');
        }
        
        $items = $pedido->obtenerItems($id);
        
        // Obtener info del cliente
        $usuario = new Usuario($this->db);
        $cliente = $usuario->obtenerPorId($pedido_detalle['cliente_id']);
        
        require_once 'views/admin/pedidos-detalle.php';
    }
    
    /**
     * Actualizar estado de pedido
     */
    public function actualizarEstado() {
        requireAdmin();
        
        $pedido_id = intval($_POST['pedido_id'] ?? 0);
        $nuevo_estado = sanitize($_POST['estado'] ?? '');
        
        $estados_validos = ['pendiente', 'confirmado', 'enviado', 'entregado', 'completado', 'cancelado'];
        
        if ($pedido_id <= 0 || !in_array($nuevo_estado, $estados_validos)) {
            setFlash('error', 'Datos inválidos');
        } else {
            $pedido = new Pedido($this->db);
            $pedido->id = $pedido_id;
            
            if ($pedido->actualizarEstado($nuevo_estado)) {
                setFlash('success', 'Estado del pedido actualizado');
            } else {
                setFlash('error', 'Error al actualizar el estado');
            }
        }
        
        redirect('admin/pedidos.php');
    }
}
