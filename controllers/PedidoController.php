<?php
/**
 * Controlador de Pedidos (Cliente)
 * Gestiona pedidos y órdenes del cliente
 */
class PedidoController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Crear nuevo pedido
     */
    public function crear() {
        requireAuth();
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $carrito = $_SESSION['carrito'] ?? [];
            
            if (empty($carrito)) {
                $error = 'El carrito está vacío';
            } else {
                $direccion = sanitize($_POST['direccion'] ?? '');
                $metodo_pago_id = intval($_POST['metodo_pago'] ?? 0);
                $notas = sanitize($_POST['notas'] ?? '');
                
                if (empty($direccion)) {
                    $error = 'Por favor ingrese una dirección de envío';
                } elseif ($metodo_pago_id <= 0) {
                    $error = 'Por favor seleccione un método de pago';
                } else {
                    // Calcular total
                    $reloj = new Reloj($this->db);
                    $total = 0;
                    $items = [];
                    
                    foreach ($carrito as $producto_id => $cantidad) {
                        $producto = $reloj->obtenerPorId($producto_id);
                        
                        if ($producto && $producto['cantidad_disponible'] >= $cantidad) {
                            $subtotal = $producto['precio_final'] * $cantidad;
                            $total += $subtotal;
                            $items[] = [
                                'reloj_id' => $producto_id,
                                'cantidad' => $cantidad,
                                'precio_unitario' => $producto['precio_final']
                            ];
                        } else {
                            $error = 'Stock insuficiente para el producto';
                            break;
                        }
                    }
                    
                    if (empty($error)) {
                        $pedido = new Pedido($this->db);
                        $pedido->cliente_id = $_SESSION['usuario_id'];
                        $pedido->metodo_pago_id = $metodo_pago_id;
                        $pedido->total = $total;
                        $pedido->direccion_envio = $direccion;
                        $pedido->notas = $notas;
                        $pedido->estado = 'pendiente';
                        
                        $pedido_id = $pedido->crear();
                        
                        if ($pedido_id) {
                            // Agregar items al pedido
                            foreach ($items as $item) {
                                $pedido->agregarItem($pedido_id, $item['reloj_id'], $item['cantidad'], $item['precio_unitario']);
                            }
                            
                            // Limpiar carrito
                            $_SESSION['carrito'] = [];
                            setFlash('success', 'Pedido creado exitosamente');
                            redirect('mis-pedidos.php');
                        } else {
                            $error = 'Error al crear el pedido';
                        }
                    }
                }
            }
        }
        
        $metodo_pago = new MetodoPago($this->db);
        $metodos = $metodo_pago->obtenerActivos();
        
        require_once 'views/customer/crear-pedido.php';
    }
    
    /**
     * Listar pedidos del cliente
     */
    public function listar() {
        requireAuth();
        
        $pedido = new Pedido($this->db);
        $pedidos = $pedido->obtenerPorCliente($_SESSION['usuario_id']);
        
        require_once 'views/customer/mis-pedidos.php';
    }
    
    /**
     * Ver detalle de un pedido
     */
    public function detalle($id) {
        requireAuth();
        
        $pedido = new Pedido($this->db);
        $pedido_detalle = $pedido->obtenerPorId($id);
        
        if (!$pedido_detalle || $pedido_detalle['cliente_id'] != $_SESSION['usuario_id']) {
            setFlash('error', 'Pedido no encontrado');
            redirect('mis-pedidos.php');
        }
        
        $items = $pedido->obtenerItems($id);
        
        require_once 'views/customer/pedido-detalle.php';
    }
    
    /**
     * Cancelar pedido
     */
    public function cancelar() {
        requireAuth();
        
        $pedido_id = intval($_POST['pedido_id'] ?? 0);
        $pedido = new Pedido($this->db);
        $pedido_detalle = $pedido->obtenerPorId($pedido_id);
        
        if (!$pedido_detalle || $pedido_detalle['cliente_id'] != $_SESSION['usuario_id']) {
            setFlash('error', 'Pedido no encontrado');
        } elseif (in_array($pedido_detalle['estado'], ['entregado', 'completado', 'cancelado'])) {
            setFlash('error', 'Este pedido no puede ser cancelado');
        } else {
            $pedido->id = $pedido_id;
            if ($pedido->actualizarEstado('cancelado')) {
                setFlash('success', 'Pedido cancelado exitosamente');
            } else {
                setFlash('error', 'Error al cancelar el pedido');
            }
        }
        
        redirect('mis-pedidos.php');
    }
}
