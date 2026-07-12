<?php
/**
 * Autoload - Carga automática de clases
 * Simplifica la inclusión de modelos y controladores
 */

// Función de autoload personalizada
spl_autoload_register(function ($class_name) {
    $directories = [
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir configuración de base de datos
require_once __DIR__ . '/database.php';

// Funciones helper globales
require_once __DIR__ . '/helpers.php';
