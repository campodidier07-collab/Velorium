<?php 
require_once 'config/autoload.php';

// Destruir sesión
session_destroy();
setFlash('success', 'Sesión cerrada correctamente');

// Redirigir al inicio
redirect(baseUrl());
