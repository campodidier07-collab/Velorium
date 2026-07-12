<?php 
require_once 'config/autoload.php';
$controller = new ShopController($db);
$controller->catalogo();
