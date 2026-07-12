<?php 
$titulo = 'Pedidos - Admin';
requireAdmin();
require_once 'views/layouts/header.php';
?>

<div class="admin-container">
    <section class="admin-content">
        <h1>Gestión de Pedidos</h1>
        
        <div class="filtros-admin">
            <?php foreach ($estados_disponibles as $estado): ?>
                <a href="<?php echo baseUrl('admin/pedidos?estado=' . $estado); ?>" class="btn-filtro">
                    <?php echo ucfirst($estado); ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($pedidos)): ?>
            <div class="tabla-responsive">
                <table class="admin-tabla">
                    <thead>
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
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>#<?php echo $pedido['id']; ?></td>
                                <td><?php echo e($pedido['cliente_nombre'] ?? 'N/A'); ?></td>
                                <td><?php echo formatPrice($pedido['total']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($pedido['estado']); ?>">
                                        <?php echo ucfirst(e($pedido['estado'])); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($pedido['fecha_pedido']); ?></td>
                                <td>
                                    <a href="<?php echo baseUrl('admin/pedidos/' . $pedido['id']); ?>" class="btn-icon">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No hay pedidos para mostrar.</p>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
