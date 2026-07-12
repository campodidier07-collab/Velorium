<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener estadísticas
$stats = [
    'total_productos' => 0,
    'total_clientes' => 0,
    'total_pedidos' => 0,
    'ventas_mes' => 0,
    'productos_agotados' => 0,
    'pedidos_pendientes' => 0,
    'ventas_hoy' => 0,
    'productos_disponibles' => 0
];

if ($db) {
    try {
        // Total productos
        $stmt = $db->query("SELECT COUNT(*) FROM relojes WHERE estado = 'disponible'");
        $stats['total_productos'] = $stmt->fetchColumn();
        
        // Productos disponibles (con stock)
        $stmt = $db->query("SELECT COUNT(*) FROM relojes r JOIN inventario i ON r.id = i.reloj_id WHERE r.estado = 'disponible' AND i.cantidad_disponible > 0");
        $stats['productos_disponibles'] = $stmt->fetchColumn();
        
        // Total clientes
        $stmt = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente'");
        $stats['total_clientes'] = $stmt->fetchColumn();
        
        // Total pedidos
        $stmt = $db->query("SELECT COUNT(*) FROM pedidos");
        $stats['total_pedidos'] = $stmt->fetchColumn();
        
        // Pedidos pendientes
        $stmt = $db->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'pendiente'");
        $stats['pedidos_pendientes'] = $stmt->fetchColumn();
        
        // Ventas del mes
        $stmt = $db->query("SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE MONTH(fecha_pedido) = MONTH(CURRENT_DATE()) AND YEAR(fecha_pedido) = YEAR(CURRENT_DATE()) AND estado != 'cancelado'");
        $stats['ventas_mes'] = $stmt->fetchColumn();
        
        // Ventas de hoy
        $stmt = $db->query("SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE DATE(fecha_pedido) = CURRENT_DATE() AND estado != 'cancelado'");
        $stats['ventas_hoy'] = $stmt->fetchColumn();
        
        // Productos agotados
        $stmt = $db->query("SELECT COUNT(*) FROM inventario WHERE cantidad_disponible <= 0");
        $stats['productos_agotados'] = $stmt->fetchColumn();
        
    } catch (Exception $e) {
        error_log("Error obteniendo estadísticas: " . $e->getMessage());
    }
}

// Obtener pedidos recientes
$pedidos_recientes = [];
if ($db) {
    try {
        $query = "SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email 
                  FROM pedidos p 
                  JOIN usuarios u ON p.cliente_id = u.id 
                  ORDER BY p.fecha_pedido DESC 
                  LIMIT 8";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $pedidos_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error obteniendo pedidos recientes: " . $e->getMessage());
    }
}

// Obtener productos con bajo stock
$productos_bajo_stock = [];
if ($db) {
    try {
        $query = "SELECT r.nombre, r.marca, i.cantidad_disponible, r.precio
                  FROM relojes r 
                  JOIN inventario i ON r.id = i.reloj_id 
                  WHERE i.cantidad_disponible <= 5 AND r.estado = 'disponible'
                  ORDER BY i.cantidad_disponible ASC 
                  LIMIT 8";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $productos_bajo_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error obteniendo productos bajo stock: " . $e->getMessage());
    }
}

// Obtener ventas por categoría
$ventas_categoria = [];
if ($db) {
    try {
        $query = "SELECT r.categoria, COUNT(ip.id) as cantidad_vendida, SUM(ip.subtotal) as total_ventas
                  FROM items_pedido ip
                  JOIN relojes r ON ip.reloj_id = r.id
                  JOIN pedidos p ON ip.pedido_id = p.id
                  WHERE p.estado != 'cancelado'
                  GROUP BY r.categoria
                  ORDER BY total_ventas DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $ventas_categoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error obteniendo ventas por categoría: " . $e->getMessage());
    }
}

// Obtener actividad reciente
$actividad_reciente = [];
if ($db) {
    try {
        // Combinar pedidos y registros de usuarios recientes
        $query = "(SELECT 'pedido' as tipo, CONCAT('Nuevo pedido #', id, ' por $', FORMAT(total, 2)) as descripcion, fecha_pedido as fecha FROM pedidos ORDER BY fecha_pedido DESC LIMIT 5)
                  UNION ALL
                  (SELECT 'usuario' as tipo, CONCAT('Nuevo cliente: ', nombre) as descripcion, creado_en as fecha FROM usuarios WHERE rol = 'cliente' ORDER BY creado_en DESC LIMIT 3)
                  ORDER BY fecha DESC LIMIT 8";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $actividad_reciente = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error obteniendo actividad reciente: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Hero Header -->
        <div class="p-4 p-md-5 mb-4 rounded-4 position-relative overflow-hidden shadow-lg" style="background-color: var(--navy-dark); color: white;">
            <!-- Decoración de fondo -->
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('https://images.unsplash.com/photo-1547996160-81dfa63595aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center; opacity: 0.08; mix-blend-mode: luminosity;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,0.8) 100%);"></div>
            
            <div class="position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <span style="width: 30px; height: 1px; background-color: var(--gold); display: inline-block; margin-right: 15px;"></span>
                        <span style="color: var(--gold); font-size: 0.65rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 800;">Panel de Control Administrativo</span>
                    </div>
                    <h1 class="display-5 mb-1" style="font-family: 'Playfair Display', serif; font-weight: 800; color: white;">Dashboard Corporativo</h1>
                    <p class="mb-0" style="color: #94a3b8; font-weight: 300; font-size: 0.95rem;">Resumen de operaciones y estado general de la joyería Velorium.</p>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-light d-md-none" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="text-end bg-white bg-opacity-10 rounded-3 p-3 backdrop-blur" style="border: 1px solid rgba(255,255,255,0.05);">
                        <small class="d-block" style="color: #94a3b8; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Sincronizado</small>
                        <strong style="color: var(--gold); font-family: monospace; font-size: 1.1rem;" id="last-update"><?php echo date('H:i:s'); ?></strong>
                        <small class="d-block mt-1" style="color: white; font-size: 0.75rem;"><?php echo date('d/m/Y'); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        <?php if ($stats['productos_agotados'] > 0): ?>
            <div class="alert alert-warning fade-in">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <strong>¡Atención!</strong> Hay <?php echo $stats['productos_agotados']; ?> producto(s) agotado(s).
                        <a href="inventario.php" class="alert-link ms-2">Gestionar inventario <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($stats['pedidos_pendientes'] > 0): ?>
            <div class="alert alert-info fade-in">
                <div class="d-flex align-items-center">
                    <i class="fas fa-clock fa-2x me-3"></i>
                    <div>
                        <strong>Pedidos pendientes:</strong> Tienes <?php echo $stats['pedidos_pendientes']; ?> pedido(s) esperando procesamiento.
                        <a href="pedidos.php?estado=pendiente" class="alert-link ms-2">Ver pedidos <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Estadísticas Principales -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Total Productos</h6>
                            <h2 class="mb-0 fw-bold"><?php echo number_format($stats['total_productos']); ?></h2>
                            <small class="opacity-75"><?php echo $stats['productos_disponibles']; ?> disponibles</small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Total Clientes</h6>
                            <h2 class="mb-0 fw-bold"><?php echo number_format($stats['total_clientes']); ?></h2>
                            <small class="opacity-75">Usuarios registrados</small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Total Pedidos</h6>
                            <h2 class="mb-0 fw-bold"><?php echo number_format($stats['total_pedidos']); ?></h2>
                            <small class="opacity-75"><?php echo $stats['pedidos_pendientes']; ?> pendientes</small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Ventas del Mes</h6>
                            <h2 class="mb-0 fw-bold">$<?php echo number_format($stats['ventas_mes'], 2); ?></h2>
                            <small class="opacity-75">Hoy: $<?php echo number_format($stats['ventas_hoy'], 2); ?></small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos y Datos -->
        <div class="row mb-4">
            <!-- Ventas por Categoría -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Ventas por Categoría
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($ventas_categoria)): ?>
                            <canvas id="categoryChart" width="400" height="200"></canvas>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay datos de ventas disponibles</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Actividad Reciente
                        </h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshActivity()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($actividad_reciente)): ?>
                            <div id="activity-list">
                                <?php foreach ($actividad_reciente as $actividad): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon <?php echo $actividad['tipo']; ?>">
                                            <i class="fas fa-<?php echo $actividad['tipo'] == 'pedido' ? 'shopping-cart' : 'user-plus'; ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1"><?php echo htmlspecialchars($actividad['descripcion']); ?></p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($actividad['fecha'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay actividad reciente</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pedidos Recientes -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Pedidos Recientes
                        </h5>
                        <a href="pedidos.php" class="btn btn-sm btn-primary">
                            Ver Todos <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($pedidos_recientes)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay pedidos recientes</p>
                                <a href="productos.php" class="btn btn-outline-primary">Ver productos</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pedidos_recientes as $pedido): ?>
                                            <tr>
                                                <td>
                                                    <strong>#<?php echo $pedido['id']; ?></strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($pedido['cliente_nombre']); ?></strong>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($pedido['cliente_email']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong class="text-success">$<?php echo number_format($pedido['total'], 2); ?></strong>
                                                </td>
                                                <td>
                                                    <?php
                                                    $badge_class = 'secondary';
                                                    $icon = 'clock';
                                                    switch ($pedido['estado']) {
                                                        case 'pendiente':
                                                            $badge_class = 'warning';
                                                            $icon = 'clock';
                                                            break;
                                                        case 'completado':
                                                            $badge_class = 'success';
                                                            $icon = 'check';
                                                            break;
                                                        case 'cancelado':
                                                            $badge_class = 'danger';
                                                            $icon = 'times';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?php echo $badge_class; ?>">
                                                        <i class="fas fa-<?php echo $icon; ?> me-1"></i>
                                                        <?php echo ucfirst($pedido['estado']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?>
                                                        <br><small class="text-muted"><?php echo date('H:i', strtotime($pedido['fecha_pedido'])); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="pedidos.php?id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Productos con Stock Bajo -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Stock Bajo
                        </h5>
                        <a href="inventario.php" class="btn btn-sm btn-warning">
                            Gestionar <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($productos_bajo_stock)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted mb-0">Todos los productos tienen stock suficiente</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($productos_bajo_stock as $producto): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start px-0">
                                        <div class="flex-grow-1">
                                            <strong class="d-block"><?php echo htmlspecialchars($producto['nombre']); ?></strong>
                                            <small class="text-muted"><?php echo htmlspecialchars($producto['marca']); ?></small>
                                            <div class="mt-1">
                                                <small class="text-success">$<?php echo number_format($producto['precio'], 2); ?></small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?php echo $producto['cantidad_disponible'] == 0 ? 'danger' : 'warning'; ?> rounded-pill">
                                                <?php echo $producto['cantidad_disponible']; ?>
                                            </span>
                                            <div class="mt-1">
                                                <div class="progress" style="width: 60px; height: 4px;">
                                                    <div class="progress-bar bg-<?php echo $producto['cantidad_disponible'] == 0 ? 'danger' : 'warning'; ?>" 
                                                         style="width: <?php echo min(100, ($producto['cantidad_disponible'] / 10) * 100); ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="productos.php?action=add" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-plus fa-2x mb-2"></i>
                                    <span>Agregar Producto</span>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="pedidos.php?estado=pendiente" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <span>Pedidos Pendientes</span>
                                    <?php if ($stats['pedidos_pendientes'] > 0): ?>
                                        <span class="badge bg-light text-dark mt-1"><?php echo $stats['pedidos_pendientes']; ?></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="inventario.php" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-boxes fa-2x mb-2"></i>
                                    <span>Gestionar Inventario</span>
                                    <?php if ($stats['productos_agotados'] > 0): ?>
                                        <span class="badge bg-light text-dark mt-1"><?php echo $stats['productos_agotados']; ?> agotados</span>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Función para alternar sidebar en móvil
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Cerrar sidebar al hacer clic fuera en móvil
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isToggleButton = event.target.closest('.d-md-none');
            
            if (!isClickInsideSidebar && !isToggleButton && window.innerWidth <= 768) {
                sidebar.classList.remove('show');
            }
        });

        // Gráfico de ventas por categoría
        <?php if (!empty($ventas_categoria)): ?>
        const ctx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php foreach ($ventas_categoria as $categoria): ?>
                        '<?php echo ucfirst($categoria['categoria']); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    data: [
                        <?php foreach ($ventas_categoria as $categoria): ?>
                            <?php echo $categoria['total_ventas']; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: [
                        '#3498db',
                        '#e74c3c',
                        '#f39c12',
                        '#27ae60',
                        '#9b59b6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Auto-refresh cada 5 minutos
        setInterval(function() {
            document.getElementById('last-update').textContent = new Date().toLocaleString('es-ES');
        }, 300000);

        // Función para refrescar actividad
        function refreshActivity() {
            const button = event.target.closest('button');
            const icon = button.querySelector('i');
            
            icon.classList.add('fa-spin');
            
            // Simular carga
            setTimeout(() => {
                icon.classList.remove('fa-spin');
                location.reload();
            }, 1000);
        }

        // Notificaciones del navegador
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // Mostrar notificación si hay productos agotados
        <?php if ($stats['productos_agotados'] > 0): ?>
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('Alerta de Inventario', {
                    body: 'Hay <?php echo $stats['productos_agotados']; ?> producto(s) agotado(s)',
                    icon: 'https://cdn-icons-png.flaticon.com/512/1170/1170576.png'
                });
            }
        <?php endif; ?>

        // Animaciones de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card, .stat-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('fade-in');
                }, index * 100);
            });
        });

        // Actualizar hora cada segundo
        setInterval(function() {
            const now = new Date();
            document.getElementById('last-update').textContent = now.toLocaleString('es-ES');
        }, 1000);
    </script>
</body>
</html>
