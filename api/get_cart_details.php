<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['ids']) || !is_array($input['ids'])) {
    http_response_code(400);
    echo json_encode(['error' => 'IDs de productos requeridos']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$ids = array_map('intval', $input['ids']);
$placeholders = str_repeat('?,', count($ids) - 1) . '?';

$query = "SELECT r.*, p.porcentaje_descuento,
          CASE 
            WHEN p.id IS NOT NULL AND p.activo = 1 AND NOW() BETWEEN p.fecha_inicio AND p.fecha_fin 
            THEN r.precio * (1 - p.porcentaje_descuento/100)
            ELSE r.precio 
          END as precio_final
          FROM relojes r 
          LEFT JOIN promociones p ON r.id = p.reloj_id AND p.activo = 1 AND NOW() BETWEEN p.fecha_inicio AND p.fecha_fin
          WHERE r.id IN ($placeholders) AND r.estado = 'disponible'";

$stmt = $db->prepare($query);
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
?>
