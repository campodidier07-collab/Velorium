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

// Procesar actualización de stock
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_stock') {
        $reloj_id = $_POST['reloj_id'];
        $nuevo_stock = intval($_POST['nuevo_stock']);
        
        try {
            $query = "UPDATE inventario SET cantidad_disponible = ? WHERE reloj_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$nuevo_stock, $reloj_id]);
            $message = 'Stock actualizado exitosamente';
        } catch (Exception $e) {
            $error = 'Error al actualizar: ' . $e->getMessage();
        }
    }
    // Procesar alta de producto
    if ($_POST['action'] == 'add') {
        $nombre = trim($_POST['nombre']);
        $marca = $_POST['marca'];
        $categoria = $_POST['categoria'];
        $descripcion = trim($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $material = $_POST['material'];
        $genero = $_POST['genero'];
        $estado = $_POST['estado'];
        $stock = intval($_POST['stock']);
        $imagen_nombre = '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $archivo = $_FILES['imagen'];
            $nombre_archivo = $archivo['name'];
            $tipo_archivo = $archivo['type'];
            $tamaño_archivo = $archivo['size'];
            $archivo_temporal = $archivo['tmp_name'];
            $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png'];
            $extensiones_permitidas = ['jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            if (in_array($tipo_archivo, $tipos_permitidos) && in_array($extension, $extensiones_permitidas)) {
                if ($tamaño_archivo <= 5000000) {
                    $directorio_destino = '../assets/images/';
                    $imagen_nombre = uniqid() . '_' . basename($nombre_archivo);
                    if (!move_uploaded_file($archivo_temporal, $directorio_destino . $imagen_nombre)) {
                        $imagen_nombre = 'default-watch.png';
                    }
                } else {
                    $imagen_nombre = 'default-watch.png';
                }
            } else {
                $imagen_nombre = 'default-watch.png';
            }
        } else {
            $imagen_nombre = 'default-watch.png';
        }
        try {
            $db->beginTransaction();
            $query = "INSERT INTO relojes (nombre, marca, categoria, descripcion, precio, material, genero, estado, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$nombre, $marca, $categoria, $descripcion, $precio, $material, $genero, $estado, $imagen_nombre]);
            $reloj_id = $db->lastInsertId();
            $query = "INSERT INTO inventario (reloj_id, cantidad_disponible) VALUES (?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$reloj_id, $stock]);
            $db->commit();
            $message = 'Producto agregado exitosamente';
            // Redirigir para evitar reenvío del formulario
            echo '<script>window.location.href = "inventario.php?success=1";</script>';
            exit();
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Error al agregar producto: ' . $e->getMessage();
        }
    }
    // Procesar edición de producto
    if ($_POST['action'] == 'edit') {
        $reloj_id = intval($_POST['reloj_id']);
        $nombre = trim($_POST['nombre']);
        $marca = $_POST['marca'];
        $categoria = $_POST['categoria'];
        $descripcion = trim($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $material = $_POST['material'];
        $genero = $_POST['genero'];
        $estado = $_POST['estado'];
        $stock = intval($_POST['stock']);
        $imagen_nombre = '';
        // Obtener imagen actual
        $query = "SELECT imagen FROM relojes WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$reloj_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        $imagen_actual = $producto ? $producto['imagen'] : 'default-watch.png';
        // Procesar nueva imagen si se sube
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $archivo = $_FILES['imagen'];
            $nombre_archivo = $archivo['name'];
            $tipo_archivo = $archivo['type'];
            $tamaño_archivo = $archivo['size'];
            $archivo_temporal = $archivo['tmp_name'];
            $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png'];
            $extensiones_permitidas = ['jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            if (in_array($tipo_archivo, $tipos_permitidos) && in_array($extension, $extensiones_permitidas)) {
                if ($tamaño_archivo <= 5000000) {
                    $directorio_destino = '../assets/images/';
                    $imagen_nombre = uniqid() . '_' . basename($nombre_archivo);
                    if (move_uploaded_file($archivo_temporal, $directorio_destino . $imagen_nombre)) {
                        // Eliminar imagen anterior si no es la default
                        if ($imagen_actual && $imagen_actual != 'default-watch.png' && file_exists($directorio_destino . $imagen_actual)) {
                            @unlink($directorio_destino . $imagen_actual);
                        }
                    } else {
                        $imagen_nombre = $imagen_actual;
                    }
                } else {
                    $imagen_nombre = $imagen_actual;
                }
            } else {
                $imagen_nombre = $imagen_actual;
            }
        } else {
            $imagen_nombre = $imagen_actual;
        }
        try {
            $query = "UPDATE relojes SET nombre=?, marca=?, categoria=?, descripcion=?, precio=?, material=?, genero=?, estado=?, imagen=? WHERE id=?";
            $stmt = $db->prepare($query);
            $stmt->execute([$nombre, $marca, $categoria, $descripcion, $precio, $material, $genero, $estado, $imagen_nombre, $reloj_id]);
            $query = "UPDATE inventario SET cantidad_disponible=? WHERE reloj_id=?";
            $stmt = $db->prepare($query);
            $stmt->execute([$stock, $reloj_id]);
            echo '<script>window.location.href = "inventario.php?success=2";</script>';
            exit();
        } catch (Exception $e) {
            $error = 'Error al editar producto: ' . $e->getMessage();
        }
    }
}

// Filtros
$categoria_filter = $_GET['categoria'] ?? '';
$estado_filter = $_GET['estado'] ?? '';
$stock_filter = $_GET['stock'] ?? '';

$where_conditions = [];
$params = [];

if ($categoria_filter) {
    $where_conditions[] = "r.categoria = ?";
    $params[] = $categoria_filter;
}

if ($estado_filter) {
    $where_conditions[] = "r.estado = ?";
    $params[] = $estado_filter;
}

if ($stock_filter) {
    switch ($stock_filter) {
        case 'agotado':
            $where_conditions[] = "i.cantidad_disponible = 0";
            break;
        case 'bajo':
            $where_conditions[] = "i.cantidad_disponible > 0 AND i.cantidad_disponible <= 5";
            break;
        case 'normal':
            $where_conditions[] = "i.cantidad_disponible > 5";
            break;
    }
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Obtener inventario
$query = "SELECT r.*, i.cantidad_disponible, i.cantidad_vendida,
          COALESCE(i.cantidad_vendida, 0) * r.precio as valor_vendido
          FROM relojes r 
          LEFT JOIN inventario i ON r.id = i.reloj_id 
          $where_clause
          ORDER BY i.cantidad_disponible ASC, r.nombre ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$stats = [
    'total_productos' => 0,
    'productos_agotados' => 0,
    'stock_bajo' => 0,
    'valor_total' => 0
];

if ($db) {
    $stmt = $db->query("SELECT COUNT(*) FROM relojes WHERE estado = 'disponible'");
    $stats['total_productos'] = $stmt->fetchColumn();
    
    $stmt = $db->query("SELECT COUNT(*) FROM inventario WHERE cantidad_disponible = 0");
    $stats['productos_agotados'] = $stmt->fetchColumn();
    
    $stmt = $db->query("SELECT COUNT(*) FROM inventario WHERE cantidad_disponible > 0 AND cantidad_disponible <= 5");
    $stats['stock_bajo'] = $stmt->fetchColumn();
    
    $stmt = $db->query("SELECT SUM(r.precio * i.cantidad_disponible) FROM relojes r JOIN inventario i ON r.id = i.reloj_id WHERE r.estado = 'disponible'");
    $stats['valor_total'] = $stmt->fetchColumn() ?? 0;
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Gestión de Inventario</h1>
            <div class="d-flex gap-2">
                <!-- Botón de exportar eliminado -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </button>
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

        <?php if (isset($_GET['success'])): ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Operación Exitosa!',
                        text: '<?php echo $_GET['success'] == 1 ? "Producto agregado exitosamente." : "Producto editado exitosamente."; ?>',
                        confirmButtonColor: '#d4af37',
                        background: '#ffffff',
                        color: '#0A192F'
                    });
                    if (window.history.replaceState) {
                        window.history.replaceState(null, null, window.location.pathname);
                    }
                });
            </script>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Productos</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['total_productos']); ?></h3>
                        </div>
                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Agotados</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['productos_agotados']); ?></h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Stock Bajo</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['stock_bajo']); ?></h3>
                            <small class="opacity-75">≤ 5 unidades</small>
                        </div>
                        <i class="fas fa-exclamation fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Valor Total</h6>
                            <h3 class="mb-0">$<?php echo number_format($stats['valor_total'], 2); ?></h3>
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
                    <div class="col-md-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria">
                            <option value="">Todas</option>
                            <option value="digital" <?php echo $categoria_filter == 'digital' ? 'selected' : ''; ?>>Digital</option>
                            <option value="deportivo" <?php echo $categoria_filter == 'deportivo' ? 'selected' : ''; ?>>Deportivo</option>
                            <option value="lujo" <?php echo $categoria_filter == 'lujo' ? 'selected' : ''; ?>>Lujo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <option value="disponible" <?php echo $estado_filter == 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                            <option value="agotado" <?php echo $estado_filter == 'agotado' ? 'selected' : ''; ?>>Agotado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nivel de Stock</label>
                        <select class="form-select" name="stock">
                            <option value="">Todos</option>
                            <option value="agotado" <?php echo $stock_filter == 'agotado' ? 'selected' : ''; ?>>Agotado (0)</option>
                            <option value="bajo" <?php echo $stock_filter == 'bajo' ? 'selected' : ''; ?>>Bajo (1-5)</option>
                            <option value="normal" <?php echo $stock_filter == 'normal' ? 'selected' : ''; ?>>Normal (>5)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Inventario -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock Actual</th>
                                <th>Vendidos</th>
                                <th>Valor Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($inventario)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay productos en inventario</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($inventario as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/images/<?php echo $item['imagen']; ?>" 
                                                     class="product-image me-3" 
                                                     alt="<?php echo htmlspecialchars($item['nombre']); ?>"
                                                     onerror="this.src='../assets/images/default-watch.png'">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($item['marca']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo ucfirst($item['categoria']); ?></span>
                                            <br><small class="text-muted"><?php echo ucfirst($item['genero']); ?></small>
                                        </td>
                                        <td>
                                            <strong class="text-success">$<?php echo number_format($item['precio'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_stock">
                                                <input type="hidden" name="reloj_id" value="<?php echo $item['id']; ?>">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control stock-input" name="nuevo_stock" 
                                                           value="<?php echo $item['cantidad_disponible'] ?? 0; ?>" min="0">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $item['cantidad_vendida'] ?? 0; ?></span>
                                            <br><small class="text-success">$<?php echo number_format($item['valor_vendido'], 2); ?></small>
                                        </td>
                                        <td>
                                            <strong>$<?php echo number_format($item['precio'] * ($item['cantidad_disponible'] ?? 0), 2); ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $stock = $item['cantidad_disponible'] ?? 0;
                                            if ($stock == 0) {
                                                echo '<span class="badge bg-danger">Agotado</span>';
                                            } elseif ($stock <= 5) {
                                                echo '<span class="badge bg-warning">Stock Bajo</span>';
                                            } else {
                                                echo '<span class="badge bg-success">Disponible</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $item['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <!-- Botón de historial eliminado -->
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

    <!-- Modal para agregar producto -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" id="productModalLabel">Agregar Nuevo Producto</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="action" value="add">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Marca</label>
                  <select class="form-select" name="marca" required>
                    <option value="Rolex">Rolex</option>
                    <option value="Casio">Casio</option>
                    <option value="Seiko">Seiko</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Categoría</label>
                  <select class="form-select" name="categoria" required>
                    <option value="digital">Digital</option>
                    <option value="deportivo">Deportivo</option>
                    <option value="lujo">Lujo</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Material</label>
                  <select class="form-select" name="material" required>
                    <option value="oro">Oro</option>
                    <option value="plata">Plata</option>
                    <option value="acero inoxidable">Acero Inoxidable</option>
                    <option value="titanio">Titanio</option>
                    <option value="ceramica">Cerámica</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Género</label>
                  <select class="form-select" name="genero" required>
                    <option value="dama">Dama</option>
                    <option value="caballero">Caballero</option>
                    <option value="unisex">Unisex</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Estado</label>
                  <select class="form-select" name="estado" required>
                    <option value="disponible">Disponible</option>
                    <option value="agotado">Agotado</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Precio</label>
                  <input type="number" class="form-control" name="precio" step="0.01" min="0" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Stock</label>
                  <input type="number" class="form-control" name="stock" min="0" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Descripción</label>
                  <textarea class="form-control" name="descripcion" rows="2" required></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Imagen</label>
                  <input type="file" class="form-control" name="imagen" accept="image/*">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar Producto</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php foreach ($inventario as $item): ?>
    <!-- Modal de edición de producto -->
    <div class="modal fade" id="editProductModal<?php echo $item['id']; ?>" tabindex="-1" aria-labelledby="editProductModalLabel<?php echo $item['id']; ?>" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" id="editProductModalLabel<?php echo $item['id']; ?>">Editar Producto</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="reloj_id" value="<?php echo $item['id']; ?>">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($item['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Marca</label>
                  <select class="form-select" name="marca" required>
                    <option value="Rolex" <?php echo $item['marca']=='Rolex'?'selected':''; ?>>Rolex</option>
                    <option value="Casio" <?php echo $item['marca']=='Casio'?'selected':''; ?>>Casio</option>
                    <option value="Seiko" <?php echo $item['marca']=='Seiko'?'selected':''; ?>>Seiko</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Categoría</label>
                  <select class="form-select" name="categoria" required>
                    <option value="digital" <?php echo $item['categoria']=='digital'?'selected':''; ?>>Digital</option>
                    <option value="deportivo" <?php echo $item['categoria']=='deportivo'?'selected':''; ?>>Deportivo</option>
                    <option value="lujo" <?php echo $item['categoria']=='lujo'?'selected':''; ?>>Lujo</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Material</label>
                  <select class="form-select" name="material" required>
                    <option value="oro" <?php echo $item['material']=='oro'?'selected':''; ?>>Oro</option>
                    <option value="plata" <?php echo $item['material']=='plata'?'selected':''; ?>>Plata</option>
                    <option value="acero inoxidable" <?php echo $item['material']=='acero inoxidable'?'selected':''; ?>>Acero Inoxidable</option>
                    <option value="titanio" <?php echo $item['material']=='titanio'?'selected':''; ?>>Titanio</option>
                    <option value="ceramica" <?php echo $item['material']=='ceramica'?'selected':''; ?>>Cerámica</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Género</label>
                  <select class="form-select" name="genero" required>
                    <option value="dama" <?php echo $item['genero']=='dama'?'selected':''; ?>>Dama</option>
                    <option value="caballero" <?php echo $item['genero']=='caballero'?'selected':''; ?>>Caballero</option>
                    <option value="unisex" <?php echo $item['genero']=='unisex'?'selected':''; ?>>Unisex</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Estado</label>
                  <select class="form-select" name="estado" required>
                    <option value="disponible" <?php echo $item['estado']=='disponible'?'selected':''; ?>>Disponible</option>
                    <option value="agotado" <?php echo $item['estado']=='agotado'?'selected':''; ?>>Agotado</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Precio</label>
                  <input type="number" class="form-control" name="precio" step="0.01" min="0" value="<?php echo $item['precio']; ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Stock</label>
                  <input type="number" class="form-control" name="stock" min="0" value="<?php echo $item['cantidad_disponible'] ?? 0; ?>" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Descripción</label>
                  <textarea class="form-control" name="descripcion" rows="2" required><?php echo htmlspecialchars($item['descripcion']); ?></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Imagen actual</label><br>
                  <img src="../assets/images/<?php echo $item['imagen'] ? $item['imagen'] : 'default-watch.png'; ?>" alt="Imagen actual" class="img-thumbnail mb-2" style="max-width: 180px;">
                  <input type="file" class="form-control mt-2" name="imagen" accept="image/*">
                  <small class="text-muted">Formatos soportados: JPG, JPEG, PNG. Tamaño máximo: 5MB.</small>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportInventory() {
            Swal.fire('Atención', 'Funcionalidad de exportación en desarrollo', 'info');
        }

        function showHistory(productId) {
            Swal.fire('Historial', 'Historial del producto ID: ' + productId + '<br>(Funcionalidad en desarrollo)', 'info');
        }

        // Confirmar cambios de stock
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.querySelector('input[name="action"]')?.value === 'update_stock' && !this.dataset.confirmed) {
                    e.preventDefault();
                    const newStock = this.querySelector('input[name="nuevo_stock"]').value;
                    Swal.fire({
                        title: '¿Confirmar cambio?',
                        text: `¿Desea cambiar el stock a ${newStock} unidades?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d4af37',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar',
                        background: '#ffffff',
                        color: '#0A192F'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.dataset.confirmed = true;
                            this.submit();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
