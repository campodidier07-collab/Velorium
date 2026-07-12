<?php
/**
 * Script de Prueba de Conexión
 * Verifica que la base de datos esté correctamente configurada
 */

require_once '../../config/database.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<div style='padding: 20px; background: #e8f5e9; border: 1px solid #4caf50; border-radius: 4px; color: #2e7d32;'>";
    echo "<h2>✅ Conexión Exitosa</h2>";
    echo "<p>Base de datos: <strong>" . DB_NAME . "</strong></p>";
    echo "<p>Host: <strong>" . DB_HOST . "</strong></p>";
    echo "<p>Usuario: <strong>" . DB_USER . "</strong></p>";
    echo "</div>";
    
    // Probar algunas queries
    $stmt = $pdo->query("SELECT COUNT(*) as usuarios FROM usuarios");
    $result = $stmt->fetch();
    
    echo "<div style='padding: 20px; margin-top: 20px; background: #f5f5f5; border-radius: 4px;'>";
    echo "<h3>Estadísticas de la BD:</h3>";
    echo "<ul>";
    echo "<li>Usuarios registrados: <strong>" . $result['usuarios'] . "</strong></li>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as productos FROM relojes");
    $result = $stmt->fetch();
    echo "<li>Productos en catálogo: <strong>" . $result['productos'] . "</strong></li>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as pedidos FROM pedidos");
    $result = $stmt->fetch();
    echo "<li>Pedidos totales: <strong>" . $result['pedidos'] . "</strong></li>";
    
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='padding: 20px; background: #ffebee; border: 1px solid #c62828; border-radius: 4px; color: #b71c1c;'>";
    echo "<h2>❌ Error de Conexión</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Verifica la configuración en config/database.php</p>";
    echo "</div>";
}
