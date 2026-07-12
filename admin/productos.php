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

// Crear directorio de imágenes si no existe
$upload_dir = '../assets/images/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Crear imagen por defecto si no existe
$default_image_path = '../assets/images/';
if (!file_exists($default_image_path)) {
    // Crear una imagen por defecto simple
    $img = imagecreate(300, 300);
    $bg = imagecolorallocate($img, 240, 240, 240);
    $text_color = imagecolorallocate($img, 100, 100, 100);
    imagestring($img, 5, 80, 140, 'Sin Imagen', $text_color);
    imagejpeg($img, $default_image_path);
    imagedestroy($img);
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add' || $action == 'edit') {
        $id = $_POST['id'] ?? null;
        $nombre = trim($_POST['nombre']);
        $marca = trim($_POST['marca']);
        $categoria = $_POST['categoria'];
        $descripcion = trim($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $material = $_POST['material'];
        $genero = $_POST['genero'];
        $estado = $_POST['estado'];
        $stock = intval($_POST['stock']);
        
        // Manejar la imagen
        $imagen_nombre = ''; // Imagen por defecto
        $imagen_subida = false;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $archivo = $_FILES['imagen'];
            $nombre_archivo = $archivo['name'];
            $tipo_archivo = $archivo['type'];
            $tamaño_archivo = $archivo['size'];
            $archivo_temporal = $archivo['tmp_name'];
            
            // Validar tipo de archivo
            $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png'];
            $extensiones_permitidas = ['jpg', 'jpeg', 'png'];
            
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            
            if (in_array($tipo_archivo, $tipos_permitidos) && in_array($extension, $extensiones_permitidas)) {
                // Validar tamaño (máximo 5MB)
                if ($tamaño_archivo <= 5000000) {
                    $directorio_destino = '../assets/images/';
                    $imagen_nombre = uniqid() . '_' . basename($nombre_archivo); // Nombre único para evitar conflictos

                    if (move_uploaded_file($archivo_temporal, $directorio_destino . $imagen_nombre)) {
                        $imagen_subida = true;
                    } else {
                        $imagen_subida = false;
                        // Manejar el error, por ejemplo: mostrar un mensaje
                    }
                } else {
                    $error = 'La imagen es demasiado grande. Máximo 5MB permitido';
                }
            } else {
                $error = 'Tipo de archivo no permitido. Solo JPG, JPEG y PNG';
            }
        } elseif ($action == 'edit' && $id) {
            // Si es edición y no se subió nueva imagen, mantener la actual
            $query = "SELECT imagen FROM relojes WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $producto_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($producto_actual) {
                $imagen_nombre = $producto_actual['imagen'];
                $imagen_subida = true; // Para edición, consideramos imagen válida
            }
        } else {
            // Si es alta y no se subió imagen, solo permitimos default-watch.png
            $imagen_subida = true;
        }
        // Si el usuario intentó subir una imagen y falló, no permitir guardar
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0 && !$imagen_subida) {
            $error = $error ?: 'No se pudo subir la imagen.';
        }
        if (empty($nombre) || empty($marca) || $precio <= 0) {
            $error = 'Por favor complete todos los campos obligatorios';
        }
        if (!empty($error) || !$imagen_subida) {
            // Mostrar el error y no guardar el producto
        } elseif (empty($error) && $imagen_subida) {
            try {
                $db->beginTransaction();
                if ($action == 'add') {
                    // Al agregar un producto, si no se subió imagen, guarda 'default-watch.png' como nombre de imagen
                    if (!$imagen_nombre) {
                        $imagen_nombre = 'default-watch.png';
                    }
                    $query = "INSERT INTO relojes (nombre, marca, categoria, descripcion, precio, material, genero, estado, imagen) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$nombre, $marca, $categoria, $descripcion, $precio, $material, $genero, $estado, $imagen_nombre]);
                    $reloj_id = $db->lastInsertId();
                    $query = "INSERT INTO inventario (reloj_id, cantidad_disponible) VALUES (?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$reloj_id, $stock]);
                    $message = 'Producto agregado exitosamente';
                    // Limpiar formulario y recargar para evitar duplicados
                    // echo '<script>window.location.href = "productos.php?success=1";</script>';
                    // exit();
                } else {
                    $query = "UPDATE relojes SET nombre=?, marca=?, categoria=?, descripcion=?, precio=?, material=?, genero=?, estado=?, imagen=? WHERE id=?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$nombre, $marca, $categoria, $descripcion, $precio, $material, $genero, $estado, $imagen_nombre, $id]);
                    $query = "UPDATE inventario SET cantidad_disponible=? WHERE reloj_id=?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$stock, $id]);
                    $message = 'Producto actualizado exitosamente';
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Error al procesar: ' . $e->getMessage();
            }
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        try {
            // Obtener nombre de imagen antes de eliminar
            $query = "SELECT imagen FROM relojes WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Eliminar producto
            $query = "DELETE FROM relojes WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            

            
            $message = 'Producto eliminado exitosamente';
        } catch (Exception $e) {
            $error = 'Error al eliminar: ' . $e->getMessage();
        }
    }
}

// Función para redimensionar imágenes
function redimensionar_imagen($origen, $destino, $ancho_max, $alto_max) {
    $info = getimagesize($origen);
    if (!$info) return false;
    
    $ancho_orig = $info[0];
    $alto_orig = $info[1];
    $tipo = $info[2];
    
    // Calcular nuevas dimensiones manteniendo proporción
    $ratio = min($ancho_max / $ancho_orig, $alto_max / $alto_orig);
    $ancho_nuevo = $ancho_orig * $ratio;
    $alto_nuevo = $alto_orig * $ratio;
    
    // Crear imagen desde el archivo original
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagen_orig = imagecreatefromjpeg($origen);
            break;
        case IMAGETYPE_PNG:
            $imagen_orig = imagecreatefrompng($origen);
            break;
        default:
            return false;
    }
    
    // Crear nueva imagen redimensionada
    $imagen_nueva = imagecreatetruecolor($ancho_nuevo, $alto_nuevo);
    
    // Preservar transparencia para PNG
    if ($tipo == IMAGETYPE_PNG) {
        imagealphablending($imagen_nueva, false);
        imagesavealpha($imagen_nueva, true);
    }
    
    imagecopyresampled($imagen_nueva, $imagen_orig, 0, 0, 0, 0, $ancho_nuevo, $alto_nuevo, $ancho_orig, $alto_orig);
    
    // Guardar imagen redimensionada
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            imagejpeg($imagen_nueva, $destino, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($imagen_nueva, $destino);
            break;
    }
    
    imagedestroy($imagen_orig);
    imagedestroy($imagen_nueva);
    
    return true;
}

// Obtener productos con filtros
$search = $_GET['search'] ?? '';
$categoria_filter = $_GET['categoria'] ?? '';
$estado_filter = $_GET['estado'] ?? '';

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(r.nombre LIKE ? OR r.marca LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($categoria_filter) {
    $where_conditions[] = "r.categoria = ?";
    $params[] = $categoria_filter;
}

if ($estado_filter) {
    $where_conditions[] = "r.estado = ?";
    $params[] = $estado_filter;
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$query = "SELECT r.*, i.cantidad_disponible 
          FROM relojes r 
          LEFT JOIN inventario i ON r.id = i.reloj_id 
          $where_clause 
          ORDER BY r.creado_en DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Eliminar la obtención de $producto_edit y la apertura automática del modal de edición
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
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('https://images.unsplash.com/photo-1622434641406-a158123450f9?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center 30%; opacity: 0.15; mix-blend-mode: luminosity;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,0.9) 100%);"></div>
            
            <div class="position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <span style="width: 30px; height: 1px; background-color: var(--gold); display: inline-block; margin-right: 15px;"></span>
                        <span style="color: var(--gold); font-size: 0.65rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 800;">Módulo de Catálogo</span>
                    </div>
                    <h1 class="display-5 mb-1" style="font-family: 'Playfair Display', serif; font-weight: 800; color: white;">Gestión de Productos</h1>
                    <p class="mb-0" style="color: #94a3b8; font-weight: 300; font-size: 0.95rem;">Administra el inventario maestro de relojería de alta gama.</p>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-light d-md-none" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Aquí iba el botón de nuevo producto. Se puede restaurar si el usuario lo desea. -->
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

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-search me-2"></i>Buscar Producto
                        </label>
                        <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Nombre o marca...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fas fa-tags me-2"></i>Categoría
                        </label>
                        <select class="form-select" name="categoria">
                            <option value="">Todas las categorías</option>
                            <option value="digital" <?php echo $categoria_filter == 'digital' ? 'selected' : ''; ?>>Digital</option>
                            <option value="deportivo" <?php echo $categoria_filter == 'deportivo' ? 'selected' : ''; ?>>Deportivo</option>
                            <option value="lujo" <?php echo $categoria_filter == 'lujo' ? 'selected' : ''; ?>>Lujo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fas fa-toggle-on me-2"></i>Estado
                        </label>
                        <select class="form-select" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="disponible" <?php echo $estado_filter == 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                            <option value="agotado" <?php echo $estado_filter == 'agotado' ? 'selected' : ''; ?>>Agotado</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-image me-2"></i>Imagen</th>
                                <th><i class="fas fa-clock me-2"></i>Producto</th>
                                <th><i class="fas fa-tags me-2"></i>Categoría</th>
                                <th><i class="fas fa-dollar-sign me-2"></i>Precio</th>
                                <th><i class="fas fa-boxes me-2"></i>Stock</th>
                                <th><i class="fas fa-toggle-on me-2"></i>Estado</th>
                                <th><i class="fas fa-cogs me-2"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($productos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clock fa-4x mb-3 opacity-50"></i>
                                            <h5>No hay productos registrados</h5>
                                            <p>Comienza agregando tu primer producto</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td>
                                            <img src="../assets/images/<?php echo (!empty($producto['imagen']) && file_exists(__DIR__ . '/../assets/images/' . $producto['imagen']) ? $producto['imagen'] : 'default-watch.png'); ?>" 
                                                 class="product-image" 
                                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                                 onerror="this.src='../assets/images/default-watch.png'">
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-1 fw-semibold"><?php echo htmlspecialchars($producto['nombre']); ?></h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-copyright me-1"></i><?php echo htmlspecialchars($producto['marca']); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo ucfirst($producto['categoria']); ?></span>
                                            <br><small class="text-muted mt-1">
                                                <i class="fas fa-venus-mars me-1"></i><?php echo ucfirst($producto['genero']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <h6 class="mb-1 text-success fw-bold">$<?php echo number_format($producto['precio'], 2); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-gem me-1"></i><?php echo ucfirst($producto['material']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo ($producto['cantidad_disponible'] ?? 0) > 5 ? 'success' : (($producto['cantidad_disponible'] ?? 0) > 0 ? 'warning' : 'danger'); ?>">
                                                <i class="fas fa-boxes me-1"></i><?php echo $producto['cantidad_disponible'] ?? 0; ?> unidades
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $producto['estado'] == 'disponible' ? 'success' : 'danger'; ?>">
                                                <i class="fas fa-<?php echo $producto['estado'] == 'disponible' ? 'check' : 'times'; ?> me-1"></i>
                                                <?php echo ucfirst($producto['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Botón de editar eliminado -->
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(<?php echo $producto['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

    <!-- Modal Producto -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clock me-2"></i>
                        Nuevo Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <!-- Información Básica -->
                        <div class="form-section">
                            <h6 class="section-title">
                                <i class="fas fa-info-circle"></i>Información Básica
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" name="nombre" 
                                           placeholder="Ej: Rolex Submariner" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Marca *</label>
                                    <select class="form-select" name="marca" required>
                                        <option value="Rolex">Rolex</option>
                                        <option value="Casio">Casio</option>
                                        <option value="Seiko">Seiko</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descripción *</label>
                                <textarea class="form-control" name="descripcion" rows="4" 
                                          placeholder="Describe las características principales del reloj..." required></textarea>
                            </div>
                        </div>
                        
                        <!-- Especificaciones -->
                        <div class="form-section">
                            <h6 class="section-title">
                                <i class="fas fa-cogs"></i>Especificaciones
                            </h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Categoría *</label>
                                    <select class="form-select" name="categoria" required>
                                        <option value="">Seleccionar categoría</option>
                                        <option value="digital">Digital</option>
                                        <option value="deportivo">Deportivo</option>
                                        <option value="lujo">Lujo</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Material *</label>
                                    <select class="form-select" name="material" required>
                                        <option value="oro">Oro</option>
                                        <option value="plata">Plata</option>
                                        <option value="acero inoxidable">Acero Inoxidable</option>
                                        <option value="titanio">Titanio</option>
                                        <option value="ceramica">Cerámica</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Género *</label>
                                    <select class="form-select" name="genero" required>
                                        <option value="">Seleccionar género</option>
                                        <option value="dama">Dama</option>
                                        <option value="caballero">Caballero</option>
                                        <option value="unisex">Unisex</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Precio e Inventario -->
                        <div class="form-section">
                            <h6 class="section-title">
                                <i class="fas fa-dollar-sign"></i>Precio e Inventario
                            </h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Precio Unitario *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="precio" id="precio" step="0.01" min="0" 
                                               placeholder="0.00" required oninput="calcularPrecioPromocion()">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Stock inicial *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="stock" min="0" 
                                               placeholder="0" required>
                                        <span class="input-group-text">unidades</span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Estado *</label>
                                    <select class="form-select" name="estado" required>
                                        <option value="disponible">Disponible</option>
                                        <option value="agotado">Agotado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Imagen del Producto -->
                        <div class="form-section">
                            <h6 class="section-title">
                                <i class="fas fa-camera"></i>Imagen del Producto
                            </h6>
                            <div class="image-upload-area" onclick="document.getElementById('imagen').click()">
                                <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png" onchange="previewImage(this)">
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <h6 class="mb-2">Arrastra tu imagen aquí o haz clic para seleccionar</h6>
                                    <p class="text-muted mb-0">Formatos soportados: JPG, JPEG, PNG</p>
                                    <small class="text-muted">Tamaño máximo: 5MB</small>
                                </div>
                            </div>
                            <img id="image-preview" class="image-preview" alt="Vista previa">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteProduct(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: '¿Desea eliminar este producto? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d4af37',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#ffffff',
                color: '#0A192F'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const uploadArea = document.querySelector('.image-upload-area');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    
                    // Actualizar el área de upload
                    uploadArea.innerHTML = `
                        <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png" onchange="previewImage(this)">
                        <div class="upload-content">
                            <i class="fas fa-check-circle upload-icon text-success"></i>
                            <h6 class="mb-2 text-success">¡Imagen seleccionada!</h6>
                            <p class="text-muted mb-0">${input.files[0].name}</p>
                            <small class="text-muted">Haz clic para cambiar la imagen</small>
                        </div>
                    `;
                    uploadArea.style.borderColor = '#10b981';
                    uploadArea.style.background = 'linear-gradient(135deg, #ecfdf5, #d1fae5)';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Drag and drop functionality
        const uploadArea = document.querySelector('.image-upload-area');
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('imagen').files = files;
                previewImage(document.getElementById('imagen'));
            }
        });

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
