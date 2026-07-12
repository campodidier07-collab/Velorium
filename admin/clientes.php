<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Filtros
$search = $_GET['search'] ?? '';
$fecha_filter = $_GET['fecha'] ?? '';

$where_conditions = ["rol = 'cliente'"];
$params = [];

if ($search) {
    $where_conditions[] = "(nombre LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($fecha_filter) {
    $where_conditions[] = "DATE(creado_en) = ?";
    $params[] = $fecha_filter;
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Obtener clientes con estadísticas
$query = "SELECT u.*, 
          COUNT(DISTINCT p.id) as total_pedidos,
          COALESCE(SUM(CASE WHEN p.estado != 'cancelado' THEN p.total ELSE 0 END), 0) as total_gastado,
          MAX(p.fecha_pedido) as ultima_compra
          FROM usuarios u 
          LEFT JOIN pedidos p ON u.id = p.cliente_id 
          $where_clause
          GROUP BY u.id
          ORDER BY u.creado_en DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas generales
$stats = [
    'total_clientes' => 0,
    'nuevos_mes' => 0,
    'activos' => 0,
    'total_gastado' => 0
];

if ($db) {
    $stmt = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente'");
    $stats['total_clientes'] = $stmt->fetchColumn();
    
    $stmt = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente' AND MONTH(creado_en) = MONTH(CURRENT_DATE()) AND YEAR(creado_en) = YEAR(CURRENT_DATE())");
    $stats['nuevos_mes'] = $stmt->fetchColumn();
    
    $stmt = $db->query("SELECT COUNT(DISTINCT cliente_id) FROM pedidos WHERE fecha_pedido >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)");
    $stats['activos'] = $stmt->fetchColumn();
    
    $stmt = $db->query("SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE estado != 'cancelado'");
    $stats['total_gastado'] = $stmt->fetchColumn();
}

// Obtener detalle de cliente específico
$cliente_detalle = null;
$pedidos_cliente = [];
if (isset($_GET['detalle'])) {
    $cliente_id = $_GET['detalle'];
    
    // Obtener cliente
    $query = "SELECT u.*, 
              COUNT(DISTINCT p.id) as total_pedidos,
              COALESCE(SUM(CASE WHEN p.estado != 'cancelado' THEN p.total ELSE 0 END), 0) as total_gastado,
              MAX(p.fecha_pedido) as ultima_compra
              FROM usuarios u 
              LEFT JOIN pedidos p ON u.id = p.cliente_id 
              WHERE u.id = ? AND u.rol = 'cliente'
              GROUP BY u.id";
    $stmt = $db->prepare($query);
    $stmt->execute([$cliente_id]);
    $cliente_detalle = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener pedidos del cliente
    if ($cliente_detalle) {
        $query = "SELECT p.*, COUNT(ip.id) as total_items
                  FROM pedidos p
                  LEFT JOIN items_pedido ip ON p.id = ip.pedido_id
                  WHERE p.cliente_id = ?
                  GROUP BY p.id
                  ORDER BY p.fecha_pedido DESC
                  LIMIT 10";
        $stmt = $db->prepare($query);
        $stmt->execute([$cliente_id]);
        $pedidos_cliente = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Procesar edición de cliente
if (isset($_POST['editar_cliente'])) {
    $id = $_POST['cliente_id'];
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    // Agrega más campos si es necesario

    $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ? AND rol = 'cliente'");
    $stmt->execute([$nombre, $email, $id]);
    // Puedes agregar un mensaje de éxito
    header("Location: clientes.php?msg=editado");
    exit();
}

// Procesar eliminación de cliente
if (isset($_POST['eliminar_cliente'])) {
    $id = $_POST['cliente_id'];
    // Eliminar pedidos asociados primero (si la base de datos no tiene ON DELETE CASCADE)
    $stmt = $db->prepare("DELETE FROM pedidos WHERE cliente_id = ?");
    $stmt->execute([$id]);
    // Eliminar cliente
    $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ? AND rol = 'cliente'");
    $stmt->execute([$id]);
    header("Location: clientes.php?msg=eliminado");
    exit();
}

// Obtener datos para editar cliente
$cliente_edit = null;
if (isset($_GET['editar'])) {
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ? AND rol = 'cliente'");
    $stmt->execute([$_GET['editar']]);
    $cliente_edit = $stmt->fetch(PDO::FETCH_ASSOC);
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
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center 20%; opacity: 0.15; mix-blend-mode: luminosity;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,0.9) 100%);"></div>
            
            <div class="position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <span style="width: 30px; height: 1px; background-color: var(--gold); display: inline-block; margin-right: 15px;"></span>
                        <span style="color: var(--gold); font-size: 0.65rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 800;">Relaciones Comerciales</span>
                    </div>
                    <h1 class="display-5 mb-1" style="font-family: 'Playfair Display', serif; font-weight: 800; color: white;">Gestión de Clientes</h1>
                    <p class="mb-0" style="color: #94a3b8; font-weight: 300; font-size: 0.95rem;">Directorio y análisis de compradores de la boutique.</p>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-light d-md-none" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Botón de exportar eliminado -->
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Clientes</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['total_clientes']); ?></h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Nuevos este Mes</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['nuevos_mes']); ?></h3>
                        </div>
                        <i class="fas fa-user-plus fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Clientes Activos</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['activos']); ?></h3>
                            <small class="opacity-75">Últimos 30 días</small>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Gastado</h6>
                            <h3 class="mb-0">$<?php echo number_format($stats['total_gastado'], 2); ?></h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Buscar Cliente</label>
                        <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Nombre o email...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha de Registro</label>
                        <input type="date" class="form-control" name="fecha" value="<?php echo htmlspecialchars($fecha_filter); ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="clientes.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-2"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente</th>
                                <th>Email</th>
                                <th>Pedidos</th>
                                <th>Total Gastado</th>
                                <th>Última Compra</th>
                                <th>Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clientes)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay clientes registrados</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="client-avatar me-3">
                                                    <?php echo strtoupper(substr($cliente['nombre'], 0, 2)); ?>
                                                </div>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong>
                                                    <br><small class="text-muted">ID: <?php echo $cliente['id']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo $cliente['total_pedidos']; ?> pedidos</span>
                                        </td>
                                        <td>
                                            <strong class="text-success">$<?php echo number_format($cliente['total_gastado'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($cliente['ultima_compra']): ?>
                                                <?php echo date('d/m/Y', strtotime($cliente['ultima_compra'])); ?>
                                                <br><small class="text-muted"><?php echo date('H:i', strtotime($cliente['ultima_compra'])); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Sin compras</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($cliente['creado_en'])); ?>
                                            <br><small class="text-muted"><?php echo date('H:i', strtotime($cliente['creado_en'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?detalle=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?editar=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="pedidos.php?cliente=<?php echo urlencode($cliente['email']); ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </a>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este cliente? Esta acción no se puede deshacer.');">
                                                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                                    <button type="submit" name="eliminar_cliente" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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

    <!-- Modal Detalle Cliente -->
    <?php if ($cliente_detalle): ?>
    <div class="modal fade" id="clienteModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>
                        Detalle del Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="client-avatar me-3" style="width: 80px; height: 80px; font-size: 2rem;">
                                    <?php echo strtoupper(substr($cliente_detalle['nombre'], 0, 2)); ?>
                                </div>
                                <div>
                                    <h4 class="mb-1"><?php echo htmlspecialchars($cliente_detalle['nombre']); ?></h4>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($cliente_detalle['email']); ?></p>
                                    <small class="text-muted">ID: <?php echo $cliente_detalle['id']; ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h5 class="text-primary"><?php echo $cliente_detalle['total_pedidos']; ?></h5>
                                    <small class="text-muted">Pedidos</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="text-success">$<?php echo number_format($cliente_detalle['total_gastado'], 2); ?></h5>
                                    <small class="text-muted">Total Gastado</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="text-info"><?php echo date('d/m/Y', strtotime($cliente_detalle['creado_en'])); ?></h5>
                                    <small class="text-muted">Registro</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($cliente_detalle['ultima_compra']): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Última compra:</strong> <?php echo date('d/m/Y H:i', strtotime($cliente_detalle['ultima_compra'])); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h6>Historial de Pedidos</h6>
                    <?php if (empty($pedidos_cliente)): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Este cliente no ha realizado pedidos</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidos_cliente as $pedido): ?>
                                        <tr>
                                            <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                                            <td><span class="badge bg-info"><?php echo $pedido['total_items']; ?></span></td>
                                            <td><strong>$<?php echo number_format($pedido['total'], 2); ?></strong></td>
                                            <td>
                                                <span class="badge bg-<?php echo $pedido['estado'] == 'pendiente' ? 'warning' : ($pedido['estado'] == 'completado' ? 'success' : 'danger'); ?>">
                                                    <?php echo ucfirst($pedido['estado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <a href="pedidos.php?cliente=<?php echo urlencode($cliente_detalle['email']); ?>" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Ver Todos los Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($cliente_edit): ?>
<div class="modal fade" id="editarClienteModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="cliente_id" value="<?php echo $cliente_edit['id']; ?>">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente_edit['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente_edit['email']); ?>" required>
                </div>
                <!-- Agrega más campos si tu tabla usuarios tiene más datos editables -->
            </div>
            <div class="modal-footer">
                <button type="submit" name="editar_cliente" class="btn btn-primary">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('editarClienteModal')).show();
    });
</script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-abrir modal si hay detalle
        <?php if ($cliente_detalle): ?>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('clienteModal')).show();
            });
        <?php endif; ?>

        function exportClients() {
            // Simular exportación
            alert('Funcionalidad de exportación en desarrollo');
        }
    </script>
</body>
</html>
