<?php 
require_once 'config/autoload.php';
$controller = new PedidoController($db);
$controller->listar();
