<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

// Procesar cambio de estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_status') {
        $pedido_id = $_POST['pedido_id'];
        $nuevo_estado = $_POST['estado'];
        
        try {
            $query = "UPDATE pedidos SET estado = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$nuevo_estado, $pedido_id]);
            $message = 'Estado del pedido actualizado exitosamente';
        } catch (Exception $e) {
            $error = 'Error al actualizar: ' . $e->getMessage();
        }
    }
}

// Filtros
$estado_filter = $_GET['estado'] ?? '';
$fecha_filter = $_GET['fecha'] ?? '';
$cliente_filter = $_GET['cliente'] ?? '';

$where_conditions = [];
$params = [];

if ($estado_filter) {
    $where_conditions[] = "p.estado = ?";
    $params[] = $estado_filter;
}

if ($fecha_filter) {
    $where_conditions[] = "DATE(p.fecha_pedido) = ?";
    $params[] = $fecha_filter;
}

if ($cliente_filter) {
    $where_conditions[] = "(u.nombre LIKE ? OR u.email LIKE ?)";
    $params[] = "%$cliente_filter%";
    $params[] = "%$cliente_filter%";
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener pedidos
$query = "SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email,
          COUNT(ip.id) as total_items,
          mp.nombre as metodo_pago_nombre
          FROM pedidos p 
          JOIN usuarios u ON p.cliente_id = u.id 
          LEFT JOIN items_pedido ip ON p.id = ip.pedido_id
          LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
          $where_clause
          GROUP BY p.id
          ORDER BY p.fecha_pedido DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener detalles de pedido específico
$pedido_detalle = null;
$items_pedido = [];
if (isset($_GET['detalle'])) {
    $pedido_id = $_GET['detalle'];
    
    // Obtener pedido
    $query = "SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email, u.id as cliente_id,
              mp.nombre as metodo_pago_nombre
              FROM pedidos p 
              JOIN usuarios u ON p.cliente_id = u.id 
              LEFT JOIN metodos_pago mp ON p.metodo_pago_id = mp.id
              WHERE p.id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$pedido_id]);
    $pedido_detalle = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener items del pedido
    if ($pedido_detalle) {
        $query = "SELECT ip.*, r.nombre as producto_nombre, r.marca as producto_marca, r.imagen as producto_imagen
                  FROM items_pedido ip
                  JOIN relojes r ON ip.reloj_id = r.id
                  WHERE ip.pedido_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$pedido_id]);
        $items_pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Estadísticas rápidas
$stats = [
    'pendiente' => 0,
    'completado' => 0,
    'cancelado' => 0,
    'total_hoy' => 0
];

if ($db) {
    $stmt = $db->query("SELECT estado, COUNT(*) as count FROM pedidos GROUP BY estado");
    $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($estados as $estado) {
        $stats[$estado['estado']] = $estado['count'];
    }
    
    $stmt = $db->query("SELECT COUNT(*) FROM pedidos WHERE DATE(fecha_pedido) = CURRENT_DATE()");
    $stats['total_hoy'] = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métodos de Pago - Admin</title>
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
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('https://images.unsplash.com/photo-1594534475808-b18fc33b045e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center 60%; opacity: 0.15; mix-blend-mode: luminosity;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,0.9) 100%);"></div>
            
            <div class="position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <span style="width: 30px; height: 1px; background-color: var(--gold); display: inline-block; margin-right: 15px;"></span>
                        <span style="color: var(--gold); font-size: 0.65rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 800;">Operaciones Financieras</span>
                    </div>
                    <h1 class="display-5 mb-1" style="font-family: 'Playfair Display', serif; font-weight: 800; color: white;">Gestión de Pedidos</h1>
                    <p class="mb-0" style="color: #94a3b8; font-weight: 300; font-size: 0.95rem;">Monitorización en tiempo real del flujo de transacciones de la boutique.</p>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-light d-md-none" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Botón de reportes eliminado -->
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '<?php echo $message; ?>',
                        confirmButtonColor: '#d4af37',
                        background: '#ffffff',
                        color: '#0A192F'
                    });
                });
            </script>
        <?php endif; ?>

        <?php if ($error): ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '<?php echo $error; ?>',
                        confirmButtonColor: '#d4af37',
                        background: '#ffffff',
                        color: '#0A192F'
                    });
                });
            </script>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Pendientes</h6>
                            <h3 class="mb-0"><?php echo $stats['pendiente']; ?></h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Completados</h6>
                            <h3 class="mb-0"><?php echo $stats['completado']; ?></h3>
                        </div>
                        <i class="fas fa-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Cancelados</h6>
                            <h3 class="mb-0"><?php echo $stats['cancelado']; ?></h3>
                        </div>
                        <i class="fas fa-times fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Hoy</h6>
                            <h3 class="mb-0"><?php echo $stats['total_hoy']; ?></h3>
                        </div>
                        <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <option value="pendiente" <?php echo $estado_filter == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="completado" <?php echo $estado_filter == 'completado' ? 'selected' : ''; ?>>Completado</option>
                            <option value="cancelado" <?php echo $estado_filter == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="fecha" value="<?php echo htmlspecialchars($fecha_filter); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cliente</label>
                        <input type="text" class="form-control" name="cliente" value="<?php echo htmlspecialchars($cliente_filter); ?>" placeholder="Nombre o email...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Pedidos -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pedidos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay pedidos registrados</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($pedido['cliente_nombre']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($pedido['cliente_email']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $pedido['total_items']; ?> items</span>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($pedido['metodo_pago_nombre'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td><strong class="text-success">$<?php echo number_format($pedido['total'], 2); ?></strong></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                                <select class="form-select form-select-sm" name="estado" onchange="this.form.submit()">
                                                    <option value="pendiente" <?php echo $pedido['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                    <option value="completado" <?php echo $pedido['estado'] == 'completado' ? 'selected' : ''; ?>>Completado</option>
                                                    <option value="cancelado" <?php echo $pedido['estado'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <div>
                                                <?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?>
                                                <br><small class="text-muted"><?php echo date('H:i', strtotime($pedido['fecha_pedido'])); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="?detalle=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle Pedido -->
    <?php if ($pedido_detalle): ?>
    <div class="modal fade" id="detalleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Detalle del Pedido #<?php echo $pedido_detalle['id']; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Información del Cliente</h6>
                            <p class="mb-1"><strong><?php echo htmlspecialchars($pedido_detalle['cliente_nombre']); ?></strong></p>
                            <p class="mb-1"><?php echo htmlspecialchars($pedido_detalle['cliente_email']); ?></p>
                            <p class="mb-0"><small class="text-muted">ID Cliente: <?php echo $pedido_detalle['cliente_id']; ?></small></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Información del Pedido</h6>
                            <p class="mb-1"><strong>Estado:</strong> 
                                <span class="badge bg-<?php echo $pedido_detalle['estado'] == 'pendiente' ? 'warning' : ($pedido_detalle['estado'] == 'completado' ? 'success' : 'danger'); ?>">
                                    <?php echo ucfirst($pedido_detalle['estado']); ?>
                                </span>
                            </p>
                            <p class="mb-1"><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido_detalle['fecha_pedido'])); ?></p>
                            <p class="mb-0"><strong>Método de Pago:</strong> <?php echo htmlspecialchars($pedido_detalle['metodo_pago_nombre'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Dirección de Envío</h6>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($pedido_detalle['direccion_envio'])); ?></p>
                    </div>
                    
                    <h6>Productos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items_pedido as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/images/relojes/<?php echo $item['producto_imagen']; ?>" 
                                                     class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;"
                                                     onerror="this.src='../assets/images/default-watch.jpg'">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['producto_nombre']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($item['producto_marca']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $item['cantidad']; ?></td>
                                        <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                        <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>$<?php echo number_format($pedido_detalle['total'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-abrir modal si hay detalle
        <?php if ($pedido_detalle): ?>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('detalleModal')).show();
            });
        <?php endif; ?>
    </script>
</body>
</html>
