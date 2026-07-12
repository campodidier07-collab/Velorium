<?php 
require_once 'config/autoload.php';

$pedido_id = intval($_GET['id'] ?? 0);

if ($pedido_id <= 0) {
    setFlash('error', 'Pedido no válido');
    redirect(baseUrl('public/pages/mis-pedidos.php'));
}

$controller = new PedidoController($db);
$controller->detalle($pedido_id);
