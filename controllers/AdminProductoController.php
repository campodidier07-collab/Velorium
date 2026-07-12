<?php
/**
 * Controlador de Administración de Productos
 * Gestiona CRUD de productos en el área admin
 */
class AdminProductoController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Listar todos los productos
     */
    public function listar() {
        requireAdmin();
        
        $reloj = new Reloj($this->db);
        $productos = $reloj->obtenerTodos();
        
        require_once 'views/admin/productos-listar.php';
    }
    
    /**
     * Crear nuevo producto
     */
    public function crear() {
        requireAdmin();
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = sanitize($_POST['nombre'] ?? '');
            $marca = sanitize($_POST['marca'] ?? '');
            $categoria = sanitize($_POST['categoria'] ?? '');
            $descripcion = sanitize($_POST['descripcion'] ?? '');
            $precio = floatval($_POST['precio'] ?? 0);
            $material = sanitize($_POST['material'] ?? '');
            $genero = sanitize($_POST['genero'] ?? '');
            $cantidad = intval($_POST['cantidad'] ?? 0);
            
            if (empty($nombre) || empty($marca) || empty($categoria) || $precio <= 0) {
                $error = 'Por favor complete todos los campos requeridos';
            } else {
                $imagen = 'default-watch.png';
                
                // Manejar subida de imagen
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                    $upload = uploadFile($_FILES['imagen'], 'storage/uploads/products/');
                    
                    if ($upload['success']) {
                        $imagen = $upload['filename'];
                    } else {
                        $error = $upload['message'];
                    }
                }
                
                if (empty($error)) {
                    $reloj = new Reloj($this->db);
                    $reloj->nombre = $nombre;
                    $reloj->marca = $marca;
                    $reloj->categoria = $categoria;
                    $reloj->descripcion = $descripcion;
                    $reloj->precio = $precio;
                    $reloj->material = $material;
                    $reloj->genero = $genero;
                    $reloj->estado = 'disponible';
                    $reloj->imagen = $imagen;
                    
                    $producto_id = $reloj->crear();
                    
                    if ($producto_id) {
                        // Crear inventario
                        $inventario = new Inventario($this->db);
                        $inventario->reloj_id = $producto_id;
                        $inventario->cantidad_disponible = $cantidad;
                        $inventario->crear();
                        
                        setFlash('success', 'Producto creado exitosamente');
                        redirect('admin/productos.php');
                    } else {
                        $error = 'Error al crear el producto';
                    }
                }
            }
        }
        
        require_once 'views/admin/productos-crear.php';
    }
    
    /**
     * Editar producto
     */
    public function editar($id) {
        requireAdmin();
        
        $reloj = new Reloj($this->db);
        $producto = $reloj->obtenerPorId($id);
        
        if (!$producto) {
            setFlash('error', 'Producto no encontrado');
            redirect('admin/productos.php');
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $producto['nombre'] = sanitize($_POST['nombre'] ?? '');
            $producto['marca'] = sanitize($_POST['marca'] ?? '');
            $producto['categoria'] = sanitize($_POST['categoria'] ?? '');
            $producto['descripcion'] = sanitize($_POST['descripcion'] ?? '');
            $producto['precio'] = floatval($_POST['precio'] ?? 0);
            $producto['material'] = sanitize($_POST['material'] ?? '');
            $producto['genero'] = sanitize($_POST['genero'] ?? '');
            
            if (empty($producto['nombre']) || empty($producto['marca']) || $producto['precio'] <= 0) {
                $error = 'Por favor complete todos los campos requeridos';
            } else {
                // Manejar subida de imagen
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                    $upload = uploadFile($_FILES['imagen'], 'storage/uploads/products/');
                    
                    if ($upload['success']) {
                        $producto['imagen'] = $upload['filename'];
                    } else {
                        $error = $upload['message'];
                    }
                }
                
                if (empty($error)) {
                    $reloj->id = $id;
                    $reloj->nombre = $producto['nombre'];
                    $reloj->marca = $producto['marca'];
                    $reloj->categoria = $producto['categoria'];
                    $reloj->descripcion = $producto['descripcion'];
                    $reloj->precio = $producto['precio'];
                    $reloj->material = $producto['material'];
                    $reloj->genero = $producto['genero'];
                    $reloj->imagen = $producto['imagen'];
                    
                    if ($reloj->actualizar()) {
                        setFlash('success', 'Producto actualizado exitosamente');
                        redirect('admin/productos.php');
                    } else {
                        $error = 'Error al actualizar el producto';
                    }
                }
            }
        }
        
        require_once 'views/admin/productos-editar.php';
    }
    
    /**
     * Eliminar producto
     */
    public function eliminar() {
        requireAdmin();
        
        $producto_id = intval($_POST['producto_id'] ?? 0);
        
        if ($producto_id > 0) {
            $reloj = new Reloj($this->db);
            
            if ($reloj->eliminar($producto_id)) {
                setFlash('success', 'Producto eliminado exitosamente');
            } else {
                setFlash('error', 'Error al eliminar el producto');
            }
        }
        
        redirect('admin/productos.php');
    }
}
