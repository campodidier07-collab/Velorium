<?php 
$titulo = 'Clientes - Admin';
requireAdmin();
require_once 'views/layouts/header.php';
?>

<div class="admin-container">
    <section class="admin-content">
        <div class="admin-header">
            <h1>Gestión de Clientes</h1>
            <a href="<?php echo baseUrl('admin/clientes/crear'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </a>
        </div>
        
        <?php if (!empty($clientes)): ?>
            <div class="tabla-responsive">
                <table class="admin-tabla">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Pedidos</th>
                            <th>Gastado</th>
                            <th>Última Compra</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo e($cliente['nombre']); ?></td>
                                <td><?php echo e($cliente['email']); ?></td>
                                <td><?php echo $cliente['total_pedidos']; ?></td>
                                <td><?php echo formatPrice($cliente['total_gastado']); ?></td>
                                <td><?php echo $cliente['ultima_compra'] ? formatDate($cliente['ultima_compra']) : 'N/A'; ?></td>
                                <td>
                                    <a href="<?php echo baseUrl('admin/clientes/editar/' . $cliente['id']); ?>" class="btn-icon">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No hay clientes registrados.</p>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
