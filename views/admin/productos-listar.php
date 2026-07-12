<?php 
$titulo = 'Productos - Admin';
requireAdmin();
require_once 'views/layouts/header.php';
?>

<div class="admin-container">
    <section class="admin-content">
        <div class="admin-header">
            <h1>Gestión de Productos</h1>
            <a href="<?php echo baseUrl('admin/productos/crear'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </div>
        
        <?php if (!empty($productos)): ?>
            <div class="tabla-responsive">
                <table class="admin-tabla">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Marca</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo e($producto['nombre']); ?></td>
                                <td><?php echo e($producto['marca']); ?></td>
                                <td><?php echo ucfirst(e($producto['categoria'])); ?></td>
                                <td><?php echo formatPrice($producto['precio']); ?></td>
                                <td><?php echo $producto['cantidad_disponible'] ?? 0; ?></td>
                                <td>
                                    <a href="<?php echo baseUrl('admin/productos/editar/' . $producto['id']); ?>" class="btn-icon">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?php echo baseUrl('admin/productos/eliminar'); ?>" style="display: inline;" onsubmit="return confirmarAccion(event, this, '¿Estás seguro de que deseas eliminar este producto?');">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn-icon" style="color: #c00;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No hay productos en la base de datos.</p>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
