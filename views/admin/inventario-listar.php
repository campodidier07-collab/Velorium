<?php 
$titulo = 'Inventario - Admin';
requireAdmin();
require_once 'views/layouts/header.php';
?>

<div class="admin-container">
    <section class="admin-content">
        <h1>Gestión de Inventario</h1>
        
        <div class="estadisticas-inventario">
            <div class="stat-mini">
                <strong>Total Productos:</strong> <?php echo $estadisticas['total_productos']; ?>
            </div>
            <div class="stat-mini">
                <strong>Stock Total:</strong> <?php echo $estadisticas['stock_total']; ?> unidades
            </div>
            <div class="stat-mini">
                <strong>Valor Inventario:</strong> <?php echo formatPrice($estadisticas['valor_inventario']); ?>
            </div>
        </div>
        
        <?php if (!empty($items)): ?>
            <div class="tabla-responsive">
                <table class="admin-tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Disponible</th>
                            <th>Vendidas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr class="<?php echo ($item['cantidad_disponible'] ?? 0) == 0 ? 'row-agotado' : ''; ?>">
                                <td><?php echo e($item['nombre']); ?></td>
                                <td><?php echo $item['cantidad_disponible'] ?? 0; ?></td>
                                <td><?php echo $item['cantidad_vendida'] ?? 0; ?></td>
                                <td>
                                    <form method="POST" action="<?php echo baseUrl('admin/inventario/actualizar'); ?>" style="display: inline;">
                                        <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="cantidad" value="<?php echo $item['cantidad_disponible']; ?>" style="width: 60px;">
                                        <input type="hidden" name="tipo" value="disponible">
                                        <button type="submit" class="btn-icon"><i class="fas fa-save"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No hay items en el inventario.</p>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
