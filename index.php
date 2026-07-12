<?php
/**
 * Router Principal - Time & Style Relojería
 * Punto de entrada del sistema MVC
 * 
 * Este archivo actúa como controlador frontal (Front Controller Pattern)
 * Todas las peticiones se redirigen aquí para ser procesadas
 */

// Cargar configuración y autoload
require_once 'config/autoload.php';

// Obtener la conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Obtener la ruta solicitada
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = str_replace('/index.php', '', $request);
$request = trim($request, '/');

try {
    // Rutas específicas del sistema MVC
    switch ($request) {
        // ==================== AUTENTICACIÓN ====================
        case 'login':
        case 'public/auth/login':
            $controller = new AuthController($db);
            $controller->login();
            break;
            
        case 'registro':
        case 'public/auth/registro':
            $controller = new AuthController($db);
            $controller->register();
            break;
            
        case 'logout':
        case 'public/auth/logout':
            $controller = new AuthController($db);
            $controller->logout();
            break;
        
        // ==================== TIENDA ====================
        case '':
        case 'index':
            $controller = new ShopController($db);
            $controller->index();
            break;
            
        case 'catalogo':
        case 'public/pages/catalogo':
            $controller = new ShopController($db);
            $controller->catalogo();
            break;
            
        // ==================== PRODUCTOS ====================
        case (strpos($request, 'producto/') === 0 ? $request : null):
            $id = intval(substr($request, 8));
            $controller = new ProductoController($db);
            $controller->detalle($id);
            break;
            
        case 'buscar':
            $controller = new ProductoController($db);
            $controller->buscar();
            break;
        
        // ==================== CARRITO ====================
        case 'carrito':
        case 'public/pages/carrito':
            $controller = new CarritoController($db);
            $action = $_GET['action'] ?? 'ver';
            
            switch ($action) {
                case 'agregar':
                    $controller->agregar();
                    break;
                case 'eliminar':
                    $controller->eliminar();
                    break;
                case 'actualizar':
                    $controller->actualizar();
                    break;
                case 'vaciar':
                    $controller->vaciar();
                    break;
                default:
                    $controller->ver();
            }
            break;
            
        // ==================== PERFIL ====================
        case 'perfil':
            $controller = new AuthController($db);
            $controller->perfil();
            break;

        // ==================== PEDIDOS CLIENTE ====================
        case 'pedidos/crear':
            $controller = new PedidoController($db);
            $controller->crear();
            break;
            
        case 'pedidos/listar':
        case 'mis-pedidos':
        case 'public/pages/mis-pedidos':
            $controller = new PedidoController($db);
            $controller->listar();
            break;
            
        case (strpos($request, 'pedidos/') === 0 ? $request : null):
            $id = intval(substr($request, 8));
            $controller = new PedidoController($db);
            $controller->detalle($id);
            break;
        
        // ==================== ADMIN ====================
        case 'admin':
        case 'admin/dashboard':
            $controller = new AdminController($db);
            $controller->dashboard();
            break;
            
        case 'admin/reportes':
            $controller = new AdminController($db);
            $controller->reportes();
            break;
        
        // ==================== ADMIN - PRODUCTOS ====================
        case 'admin/productos':
            $controller = new AdminProductoController($db);
            $controller->listar();
            break;
            
        case 'admin/productos/crear':
            $controller = new AdminProductoController($db);
            $controller->crear();
            break;
            
        case (strpos($request, 'admin/productos/editar/') === 0 ? $request : null):
            $id = intval(substr($request, 23));
            $controller = new AdminProductoController($db);
            $controller->editar($id);
            break;
        
        // ==================== ADMIN - PEDIDOS ====================
        case 'admin/pedidos':
            $controller = new AdminPedidoController($db);
            $controller->listar();
            break;
            
        case (strpos($request, 'admin/pedidos/') === 0 ? $request : null):
            $id = intval(substr($request, 14));
            $controller = new AdminPedidoController($db);
            $controller->detalle($id);
            break;
        
        // ==================== ADMIN - CLIENTES ====================
        case 'admin/clientes':
            $controller = new AdminClienteController($db);
            $controller->listar();
            break;
            
        case 'admin/clientes/crear':
            $controller = new AdminClienteController($db);
            $controller->crear();
            break;
            
        case (strpos($request, 'admin/clientes/editar/') === 0 ? $request : null):
            $id = intval(substr($request, 22));
            $controller = new AdminClienteController($db);
            $controller->editar($id);
            break;
        
        // ==================== ADMIN - INVENTARIO ====================
        case 'admin/inventario':
            $controller = new AdminInventarioController($db);
            $controller->listar();
            break;
            
        case 'admin/inventario/bajo':
            $controller = new AdminInventarioController($db);
            $controller->bajo();
            break;
        
        // ==================== 404 ====================
        default:
            http_response_code(404);
            require_once 'views/errors/404.php';
    }
    
} catch (Exception $e) {
    // Manejo de excepciones
    http_response_code(500);
    echo '<div style="padding: 20px; background: #fee; color: #c00; font-family: monospace;">';
    echo '<h2>⚠️ Error del Sistema</h2>';
    echo '<p>' . e($e->getMessage()) . '</p>';
    echo '</div>';
    
    // Log del error
    @file_put_contents('storage/logs/errors.log', 
        date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . " (Archivo: " . $e->getFile() . ", Línea: " . $e->getLine() . ")\n", 
        FILE_APPEND
    );
}
