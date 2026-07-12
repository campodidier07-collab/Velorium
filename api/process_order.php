<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'cliente') {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['cart']) || !isset($input['direccion']) || !isset($input['metodo_pago'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    $db->beginTransaction();
    
    $cart = $input['cart'];
    $direccion = trim($input['direccion']);
    $metodo_pago_id = intval($input['metodo_pago']);
    $cliente_id = $_SESSION['usuario_id'];
    
    // Calcular total
    $total = 0;
    $items_pedido = [];
    
    foreach ($cart as $item) {
        $reloj_id = intval($item['id']);
        $cantidad = intval($item['quantity']);
        $precio_unitario = floatval($item['price']);
        
        // Verificar stock
        $query = "SELECT cantidad_disponible FROM inventario WHERE reloj_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$reloj_id]);
        $stock = $stmt->fetchColumn();
        
        if ($stock < $cantidad) {
            throw new Exception("Stock insuficiente para el producto ID: $reloj_id");
        }
        
        $subtotal = $precio_unitario * $cantidad;
        $total += $subtotal;
        
        $items_pedido[] = [
            'reloj_id' => $reloj_id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $subtotal
        ];
    }
    
    // Crear pedido
    $query = "INSERT INTO pedidos (cliente_id, metodo_pago_id, estado, total, direccion_envio) 
              VALUES (?, ?, 'pendiente', ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$cliente_id, $metodo_pago_id, $total, $direccion]);
    $pedido_id = $db->lastInsertId();
    
    // Crear items del pedido
    foreach ($items_pedido as $item) {
        $query = "INSERT INTO items_pedido (pedido_id, reloj_id, cliente_id, cantidad, precio_unitario, subtotal) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $pedido_id,
            $item['reloj_id'],
            $cliente_id,
            $item['cantidad'],
            $item['precio_unitario'],
            $item['subtotal']
        ]);
        
        // Actualizar inventario
        $query = "UPDATE inventario SET 
                  cantidad_disponible = cantidad_disponible - ?,
                  cantidad_vendida = cantidad_vendida + ?
                  WHERE reloj_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$item['cantidad'], $item['cantidad'], $item['reloj_id']]);
    }
    
    // Crear registro de venta
    $query = "INSERT INTO ventas (pedido_id, cliente_id, ganancia_neta, total_venta, metodo_pago) 
              VALUES (?, ?, ?, ?, (SELECT nombre FROM metodos_pago WHERE id = ?))";
    $stmt = $db->prepare($query);
    $stmt->execute([$pedido_id, $cliente_id, $total * 0.3, $total, $metodo_pago_id]); // 30% de ganancia
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Pedido creado exitosamente',
        'pedido_id' => $pedido_id
    ]);
    
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
