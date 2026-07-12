<?php 
$titulo = 'Detalle Pedido #' . $pedido_detalle['id'];
requireAuth();
require_once 'views/layouts/header.php';
?>

<div class="bg-slate-50 py-10 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <!-- Breadcrumb -->
        <nav class="flex text-slate-500 text-xs mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="<?php echo baseUrl(); ?>" class="hover:text-navy transition-colors">Inicio</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1 text-slate-400"></i></li>
                <li><a href="<?php echo baseUrl('mis-pedidos'); ?>" class="hover:text-navy transition-colors">Mis Pedidos</a></li>
                <li><i class="fas fa-chevron-right text-[10px] mx-1 text-slate-400"></i></li>
                <li class="text-navy-dark font-medium">Pedido #<?php echo $pedido_detalle['id']; ?></li>
            </ol>
        </nav>

        <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200">
            <h1 class="text-2xl font-serif font-bold text-navy-dark flex items-center gap-3">
                Pedido #<?php echo str_pad($pedido_detalle['id'], 5, '0', STR_PAD_LEFT); ?>
            </h1>
            
            <?php 
                $estadoClass = 'bg-slate-100 text-slate-600';
                if($pedido_detalle['estado'] == 'completado' || $pedido_detalle['estado'] == 'entregado') $estadoClass = 'bg-green-50 text-green-700 border border-green-200';
                if($pedido_detalle['estado'] == 'pendiente') $estadoClass = 'bg-yellow-50 text-yellow-700 border border-yellow-200';
                if($pedido_detalle['estado'] == 'cancelado') $estadoClass = 'bg-red-50 text-red-700 border border-red-200';
            ?>
            <span class="px-3 py-1 rounded text-xs font-bold uppercase tracking-wider inline-block <?php echo $estadoClass; ?>">
                <?php echo e($pedido_detalle['estado']); ?>
            </span>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Info General -->
            <div class="bg-white rounded shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-navy-dark mb-4 uppercase tracking-wider border-b border-slate-100 pb-2">Información del Pedido</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex flex-col"><span class="text-slate-400 text-xs uppercase mb-1">Fecha</span> <span class="font-medium text-slate-800"><?php echo formatDateTime($pedido_detalle['fecha_pedido']); ?></span></li>
                    <li class="flex flex-col"><span class="text-slate-400 text-xs uppercase mb-1">Total</span> <span class="font-bold text-navy text-lg"><?php echo formatPrice($pedido_detalle['total']); ?></span></li>
                </ul>
            </div>
            
            <!-- Envío -->
            <div class="bg-white rounded shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-navy-dark mb-4 uppercase tracking-wider border-b border-slate-100 pb-2">Dirección de Envío</h3>
                <p class="text-sm text-slate-600 leading-relaxed"><?php echo nl2br(e($pedido_detalle['direccion_envio'])); ?></p>
            </div>
            
            <!-- Pago -->
            <div class="bg-white rounded shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-navy-dark mb-4 uppercase tracking-wider border-b border-slate-100 pb-2">Método de Pago</h3>
                <p class="text-sm font-medium text-slate-800 flex items-center gap-2">
                    <i class="fas fa-credit-card text-gold"></i> <?php echo e($pedido_detalle['metodo_pago_nombre'] ?? 'No especificado'); ?>
                </p>
            </div>
        </div>
        
        <div class="bg-white rounded shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h3 class="text-sm font-bold text-navy-dark uppercase tracking-wider">Productos Adquiridos</h3>
            </div>
            
            <?php if (!empty($items)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                                <th class="p-4 pl-6">Producto</th>
                                <th class="p-4 text-center">Cantidad</th>
                                <th class="p-4 text-right">Precio Unitario</th>
                                <th class="p-4 text-right pr-6">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php foreach ($items as $item): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-16 h-16 bg-white border border-slate-100 rounded flex items-center justify-center p-1">
                                                <img src="<?php echo asset('images/' . e($item['imagen'])); ?>" alt="<?php echo e($item['nombre']); ?>" class="max-h-full object-contain">
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gold-dark font-bold uppercase tracking-wider mb-1"><?php echo e($item['marca']); ?></p>
                                                <h4 class="font-medium text-navy-dark"><?php echo e($item['nombre']); ?></h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center font-medium text-slate-700"><?php echo $item['cantidad']; ?></td>
                                    <td class="p-4 text-right text-slate-600"><?php echo formatPrice($item['precio_unitario']); ?></td>
                                    <td class="p-4 text-right pr-6 font-bold text-navy-dark"><?php echo formatPrice($item['subtotal']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td colspan="3" class="p-4 text-right text-sm font-bold text-slate-600 uppercase">Total Pedido:</td>
                                <td class="p-4 pr-6 text-right text-xl font-bold text-navy"><?php echo formatPrice($pedido_detalle['total']); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-8 text-center text-slate-500 text-sm">
                    No hay productos asociados a este pedido.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="flex justify-start">
            <a href="<?php echo baseUrl('mis-pedidos'); ?>" class="bg-white border border-slate-300 text-slate-600 font-medium py-2.5 px-6 rounded hover:bg-slate-50 transition-colors text-sm flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Volver a Mis Pedidos
            </a>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
