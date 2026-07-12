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

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($error)) {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'toggle_status') {
        $id = $_POST['id'];
        $nuevo_estado = $_POST['nuevo_estado'] == '1' ? 1 : 0;
        
        try {
            $query = "UPDATE metodos_pago SET activo = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$nuevo_estado, $id]);
            
            $message = 'Estado del método de pago actualizado exitosamente';
        } catch (Exception $e) {
            $error = 'Error al actualizar estado: ' . $e->getMessage();
        }
    } elseif ($action == 'add' || $action == 'edit') {
        $id = $_POST['id'] ?? null;
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $activo = isset($_POST['activo']) ? 1 : 0;
        
        if (empty($nombre)) {
            $error = 'El nombre del método de pago es obligatorio';
        } else {
            try {
                if ($action == 'add') {
                    $query = "INSERT INTO metodos_pago (nombre, descripcion, activo) VALUES (?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$nombre, $descripcion, $activo]);
                    $message = 'Método de pago agregado exitosamente';
                } else {
                    $query = "UPDATE metodos_pago SET nombre=?, descripcion=?, activo=? WHERE id=?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$nombre, $descripcion, $activo, $id]);
                    $message = 'Método de pago actualizado exitosamente';
                }
            } catch (Exception $e) {
                $error = 'Error al procesar: ' . $e->getMessage();
            }
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        try {
            $query = "DELETE FROM metodos_pago WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $message = 'Método de pago eliminado exitosamente';
        } catch (Exception $e) {
            $error = 'Error al eliminar: ' . $e->getMessage();
        }
    }
}

// Obtener métodos de pago
$metodos = [];
try {
    $query = "SELECT * FROM metodos_pago ORDER BY creado_en DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error al obtener métodos de pago: ' . $e->getMessage();
}

// Obtener método para editar
$metodo_edit = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    try {
        $query = "SELECT * FROM metodos_pago WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$edit_id]);
        $metodo_edit = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = 'Error al obtener método para editar: ' . $e->getMessage();
    }
}

// Estadísticas
$total_metodos = count($metodos);
$metodos_activos = count(array_filter($metodos, function($m) { return $m['activo']; }));
$metodos_inactivos = $total_metodos - $metodos_activos;
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
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center; opacity: 0.15; mix-blend-mode: luminosity;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,0.9) 100%);"></div>
            
            <div class="position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <span style="width: 30px; height: 1px; background-color: var(--gold); display: inline-block; margin-right: 15px;"></span>
                        <span style="color: var(--gold); font-size: 0.65rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 800;">Configuración Financiera</span>
                    </div>
                    <h1 class="display-5 mb-1" style="font-family: 'Playfair Display', serif; font-weight: 800; color: white;">Métodos de Pago</h1>
                    <p class="mb-0" style="color: #94a3b8; font-weight: 300; font-size: 0.95rem;">Administre las pasarelas de transacción y opciones de compra.</p>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-light d-md-none" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button class="btn btn-primary" style="background-color: var(--gold); border: none; color: var(--navy-dark); font-weight: bold; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 6px rgba(212, 175, 55, 0.2);" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fas fa-plus me-2"></i>Nuevo Método
                    </button>
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
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-credit-card fa-3x mb-3"></i>
                        <h3 class="mb-1"><?php echo $total_metodos; ?></h3>
                        <p class="mb-0">Total Métodos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card success">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h3 class="mb-1"><?php echo $metodos_activos; ?></h3>
                        <p class="mb-0">Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card danger">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-3x mb-3"></i>
                        <h3 class="mb-1"><?php echo $metodos_inactivos; ?></h3>
                        <p class="mb-0">Inactivos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métodos de Pago -->
        <div class="row">
            <?php if (empty($metodos)): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-credit-card fa-4x mb-3 text-muted opacity-50"></i>
                            <h5>No hay métodos de pago configurados</h5>
                            <p class="text-muted">Comienza agregando tu primer método de pago</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($metodos as $metodo): ?>
                    <?php 
                    $config = json_decode($metodo['configuracion'] ?? '{}', true);
                    $tiempo_entrega = $config['tiempo_entrega'] ?? 'No especificado';
                    $instrucciones = $config['instrucciones'] ?? '';
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card payment-method-card <?php echo $metodo['activo'] ? 'active' : 'inactive'; ?>">
                            <div class="card-body text-center">
                                <div class="payment-method-icon">
                                    <i class="<?php echo $metodo['icono']; ?> <?php echo $metodo['activo'] ? 'text-success' : 'text-danger'; ?>"></i>
                                </div>
                                <h5 class="card-title mb-2"><?php echo htmlspecialchars($metodo['nombre']); ?></h5>
                                <p class="card-text text-muted mb-3"><?php echo htmlspecialchars($metodo['descripcion']); ?></p>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock me-1"></i>Entrega: <?php echo $tiempo_entrega; ?>
                                    </small>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-<?php echo $metodo['activo'] ? 'success' : 'danger'; ?>">
                                        <i class="fas fa-<?php echo $metodo['activo'] ? 'check' : 'times'; ?> me-1"></i>
                                        <?php echo $metodo['activo'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </div>
                                
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleStatus(<?php echo $metodo['id']; ?>, <?php echo $metodo['activo'] ? '0' : '1'; ?>)">
                                        <i class="fas fa-<?php echo $metodo['activo'] ? 'pause' : 'play'; ?>"></i>
                                    </button>
                                    <a href="?edit=<?php echo $metodo['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteMethod(<?php echo $metodo['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Método de Pago -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card me-2"></i>
                        <?php echo $metodo_edit ? 'Editar Método de Pago' : 'Nuevo Método de Pago'; ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $metodo_edit ? 'edit' : 'add'; ?>">
                        <?php if ($metodo_edit): ?>
                            <input type="hidden" name="id" value="<?php echo $metodo_edit['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nombre del Método *</label>
                                <input type="text" class="form-control" name="nombre" 
                                       value="<?php echo $metodo_edit ? htmlspecialchars($metodo_edit['nombre']) : ''; ?>" 
                                       placeholder="Ej: Pago Contra Entrega" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Icono</label>
                                <select class="form-select" name="icono">
                                    <option value="fas fa-truck" <?php echo ($metodo_edit && $metodo_edit['icono'] == 'fas fa-truck') ? 'selected' : ''; ?>>🚚 Entrega</option>
                                    <option value="fas fa-credit-card" <?php echo ($metodo_edit && $metodo_edit['icono'] == 'fas fa-credit-card') ? 'selected' : ''; ?>>💳 Tarjeta</option>
                                    <option value="fas fa-money-bill" <?php echo ($metodo_edit && $metodo_edit['icono'] == 'fas fa-money-bill') ? 'selected' : ''; ?>>💵 Efectivo</option>
                                    <option value="fas fa-university" <?php echo ($metodo_edit && $metodo_edit['icono'] == 'fas fa-university') ? 'selected' : ''; ?>>🏦 Transferencia</option>
                                    <option value="fas fa-mobile-alt" <?php echo ($metodo_edit && $metodo_edit['icono'] == 'fas fa-mobile-alt') ? 'selected' : ''; ?>>📱 Móvil</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3" 
                                      placeholder="Describe cómo funciona este método de pago..."><?php echo $metodo_edit ? htmlspecialchars($metodo_edit['descripcion']) : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tiempo de Entrega</label>
                                <input type="text" class="form-control" name="tiempo_entrega" 
                                       value="<?php echo $metodo_edit ? htmlspecialchars($metodo_edit['config']['tiempo_entrega'] ?? '') : ''; ?>" 
                                       placeholder="Ej: 2-5 días hábiles">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Instrucciones para el Cliente</label>
                            <textarea class="form-control" name="instrucciones" rows="4" 
                                      placeholder="Instrucciones detalladas sobre cómo usar este método de pago..."><?php echo $metodo_edit ? htmlspecialchars($metodo_edit['config']['instrucciones'] ?? '') : ''; ?></textarea>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="activo" id="activo" 
                                   <?php echo ($metodo_edit && $metodo_edit['activo']) || !$metodo_edit ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="activo">
                                Método activo (disponible para los clientes)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?php echo $metodo_edit ? 'Actualizar Método' : 'Guardar Método'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        function toggleStatus(id, newStatus) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="nuevo_estado" value="${newStatus}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function deleteMethod(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: '¿Está seguro de eliminar este método de pago? Esta acción no se puede deshacer.',
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

        // Auto-abrir modal si hay método para editar
        <?php if ($metodo_edit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('paymentModal')).show();
            });
        <?php endif; ?>

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert && alert.parentNode) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>
