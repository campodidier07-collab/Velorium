<?php 
require_once 'config/autoload.php';
$controller = new CarritoController($db);
$controller->ver();
