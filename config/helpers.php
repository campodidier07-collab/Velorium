<?php

/**
 * Funciones Helper - Utilidades globales del sistema
 */

/**
 * Redireccionar a una URL
 */
function redirect($url)
{
    header("Location: $url");
    exit();
}

/**
 * Verificar si el usuario está autenticado
 */
function isLoggedIn()
{
    return isset($_SESSION['usuario_id']);
}

/**
 * Verificar si el usuario es administrador
 */
function isAdmin()
{
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador';
}

/**
 * Verificar si el usuario es cliente
 */
function isCliente()
{
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente';
}

/**
 * Requerir autenticación
 */
function requireAuth()
{
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

/**
 * Requerir rol de administrador
 */
function requireAdmin()
{
    if (!isAdmin()) {
        redirect('/index.php');
    }
}

/**
 * Escapar salida HTML
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear precio
 */
function formatPrice($price)
{
    return '$' . number_format($price, 2);
}

/**
 * Formatear fecha
 */
function formatDate($date, $format = 'd/m/Y')
{
    return date($format, strtotime($date));
}

/**
 * Formatear fecha y hora
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i')
{
    return date($format, strtotime($datetime));
}

/**
 * Obtener usuario actual
 */
function currentUser()
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['nombre'],
        'email' => $_SESSION['email'],
        'rol' => $_SESSION['rol']
    ];
}

/**
 * Establecer mensaje flash
 */
function setFlash($type, $message)
{
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Obtener y limpiar mensaje flash
 */
function getFlash()
{
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];

        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);

        return $flash;
    }

    return null;
}

/**
 * Obtener URL base
 */
function baseUrl($path = '')
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);

    return $protocol . '://' . $host . $script . '/' . ltrim($path, '/');
}

/**
 * Obtener URL de asset
 */
function asset($path)
{
    if (strpos($path, 'images/http') === 0) {
        return htmlspecialchars_decode(substr($path, 7));
    }
    if (strpos($path, 'http') === 0) {
        return htmlspecialchars_decode($path);
    }
    return baseUrl('assets/' . ltrim($path, '/'));
}

/**
 * Subir archivo
 */
function uploadFile($file, $directory = 'uploads/', $allowed_types = ['jpg', 'jpeg', 'png'])
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error al subir el archivo'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
    }

    if ($file['size'] > 5000000) { // 5MB
        return ['success' => false, 'message' => 'El archivo es demasiado grande'];
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $destination = $directory . $filename;

    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename, 'path' => $destination];
    }

    return ['success' => false, 'message' => 'No se pudo mover el archivo'];
}

/**
 * Validar email
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generar CSRF token
 */
function generateCsrfToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar CSRF token
 */
function verifyCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitizar entrada
 */
function sanitize($data)
{
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Debug - Solo en desarrollo
 */
function dd($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}

/**
 * Obtener iniciales de un nombre
 */
function getInitials($name)
{
    $words = explode(' ', $name);
    $initials = '';

    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper($word[0]);
        }
    }

    return substr($initials, 0, 2);
}
