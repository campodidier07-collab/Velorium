<?php
session_start();

// Si ya está logueado como admin, redirigir al dashboard
if (isset($_SESSION['usuario_id']) && $_SESSION['rol'] == 'administrador') {
    header('Location: dashboard.php');
    exit();
}

// Si no es admin, redirigir al login
header('Location: login.php');
exit();
?>
