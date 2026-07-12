<?php
/**
 * Controlador de Carrito
 * Gestiona operaciones del carrito de compras
 */
class CarritoController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Mostrar carrito
     */
    public function ver() {
        $carrito = $_SESSION['carrito'] ?? [];
        $total = 0;
        $productos_detalle = [];
        
        if (!empty($carrito)) {
            $reloj = new Reloj($this->db);
            
            foreach ($carrito as $producto_id => $cantidad) {
                $producto = $reloj->obtenerPorId($producto_id);
                
                if ($producto) {
                    $producto['cantidad_carrito'] = $cantidad;
                    $producto['subtotal'] = $producto['precio_final'] * $cantidad;
                    $productos_detalle[] = $producto;
                    $total += $producto['subtotal'];
                }
            }
        }
        
        require_once 'views/shop/carrito.php';
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregar() {
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 1);
        
        if ($producto_id <= 0 || $cantidad <= 0) {
            setFlash('error', 'Producto o cantidad inválida');
            redirect('carrito.php');
        }
        
        // Verificar que el producto exista
        $reloj = new Reloj($this->db);
        $producto = $reloj->obtenerPorId($producto_id);
        
        if (!$producto) {
            setFlash('error', 'Producto no encontrado');
            redirect('carrito.php');
        }
        
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id] += $cantidad;
        } else {
            $_SESSION['carrito'][$producto_id] = $cantidad;
        }
        
        setFlash('success', 'Producto agregado al carrito');
        redirect('carrito.php');
    }
    
    /**
     * Eliminar producto del carrito
     */
    public function eliminar() {
        $producto_id = intval($_POST['producto_id'] ?? 0);
        
        if ($producto_id > 0 && isset($_SESSION['carrito'][$producto_id])) {
            unset($_SESSION['carrito'][$producto_id]);
            setFlash('success', 'Producto eliminado del carrito');
        }
        
        redirect('carrito.php');
    }
    
    /**
     * Actualizar cantidad de producto
     */
    public function actualizar() {
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 0);
        
        if ($producto_id > 0 && $cantidad > 0 && isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id] = $cantidad;
            setFlash('success', 'Carrito actualizado');
        }
        
        redirect('carrito.php');
    }
    
    /**
     * Vaciar carrito
     */
    public function vaciar() {
        $_SESSION['carrito'] = [];
        setFlash('success', 'Carrito vaciado');
        redirect('carrito.php');
    }
}
