<?php 
$titulo = 'Dashboard Corporativo - Velorium';
requireAdmin();
require_once 'views/layouts/header.php';
?>

<div class="bg-slate-50 min-h-screen pb-16">
    
    <!-- Hero Header Administrativo -->
    <div class="bg-navy-dark pt-12 pb-24 relative overflow-hidden">
        <!-- Decoración de fondo -->
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1547996160-81dfa63595aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')] bg-cover bg-center opacity-5 mix-blend-luminosity"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent to-navy-dark/90"></div>
        
        <div class="container mx-auto px-4 max-w-7xl relative z-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-3 mb-2">
                        <span class="w-6 h-px bg-gold"></span>
                        <span class="text-gold tracking-widest text-[10px] uppercase font-bold">Panel de Control</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-serif font-bold text-white mb-2">Dashboard Corporativo</h1>
                    <p class="text-slate-400 font-light text-sm">Resumen de operaciones y estado general de la joyería.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="<?php echo baseUrl('admin/relojes/nuevo'); ?>" class="bg-gold hover:bg-gold-light text-navy-dark font-bold py-2.5 px-6 rounded-md transition-all shadow-lg hover:shadow-xl text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nueva Pieza
                    </a>
                    <a href="<?php echo baseUrl('admin/reportes'); ?>" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold py-2.5 px-6 rounded-md transition-all text-xs uppercase tracking-wider flex items-center gap-2 backdrop-blur-sm">
                        <i class="fas fa-download"></i> Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 max-w-7xl -mt-12 relative z-20">
        
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            
            <!-- Órdenes Totales -->
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center gap-6 hover:-translate-y-1 transition-transform duration-300">
                <div class="w-16 h-16 rounded-full bg-navy/5 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shopping-bag text-2xl text-navy"></i>
                </div>
                <div>
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Total de Pedidos</h3>
                    <div class="flex items-end gap-3">
                        <p class="text-3xl font-bold text-navy-dark leading-none"><?php echo $stats['total_pedidos']; ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Pedidos Pendientes -->
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center gap-6 hover:-translate-y-1 transition-transform duration-300">
                <div class="w-16 h-16 rounded-full bg-yellow-500/10 flex items-center justify-center flex-shrink-0 border border-yellow-500/20">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Órdenes Pendientes</h3>
                    <p class="text-3xl font-bold text-navy-dark leading-none"><?php echo $stats['pedidos_pendientes']; ?></p>
                </div>
            </div>
            
            <!-- Clientes -->
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center gap-6 hover:-translate-y-1 transition-transform duration-300">
                <div class="w-16 h-16 rounded-full bg-gold/10 flex items-center justify-center flex-shrink-0 border border-gold/20">
                    <i class="fas fa-user-tie text-2xl text-gold-dark"></i>
                </div>
                <div>
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Cartera de Clientes</h3>
                    <p class="text-3xl font-bold text-navy-dark leading-none"><?php echo $stats['total_clientes']; ?></p>
                </div>
            </div>
            
            <!-- Productos Activos -->
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center gap-6 hover:-translate-y-1 transition-transform duration-300">
                <div class="w-16 h-16 rounded-full bg-navy/5 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-gem text-2xl text-navy"></i>
                </div>
                <div>
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Piezas en Catálogo</h3>
                    <p class="text-3xl font-bold text-navy-dark leading-none"><?php echo $stats['total_productos']; ?></p>
                </div>
            </div>
            
            <!-- Completados -->
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center gap-6 hover:-translate-y-1 transition-transform duration-300">
                <div class="w-16 h-16 rounded-full bg-green-500/10 flex items-center justify-center flex-shrink-0 border border-green-500/20">
                    <i class="fas fa-check-double text-2xl text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Pedidos Entregados</h3>
                    <p class="text-3xl font-bold text-navy-dark leading-none"><?php echo $stats['pedidos_completados']; ?></p>
                </div>
            </div>
            
            <!-- Alertas / Agotados -->
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center gap-6 hover:-translate-y-1 transition-transform duration-300">
                <div class="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center flex-shrink-0 border border-red-500/20">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Piezas Agotadas</h3>
                    <p class="text-3xl font-bold text-navy-dark leading-none"><?php echo $stats['productos_agotados']; ?></p>
                </div>
            </div>

        </div>
        
        <!-- Últimas ventas -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white">
                <h2 class="text-lg font-serif font-bold text-navy-dark flex items-center gap-3">
                    <i class="fas fa-history text-gold"></i> Órdenes Recientes
                </h2>
                <a href="<?php echo baseUrl('admin/pedidos'); ?>" class="text-[10px] font-bold uppercase tracking-widest text-navy bg-slate-50 hover:bg-slate-100 px-4 py-2 rounded-md transition-colors">
                    Ver todo el historial
                </a>
            </div>
            
            <?php if (!empty($pedidos_recientes)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f8f9fa] text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="p-5 font-bold">Folio</th>
                                <th class="p-5 font-bold">Cliente</th>
                                <th class="p-5 font-bold">Monto Total</th>
                                <th class="p-5 font-bold">Estado Actual</th>
                                <th class="p-5 font-bold">Fecha de Ingreso</th>
                                <th class="p-5 text-center font-bold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            <?php foreach ($pedidos_recientes as $pedido): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="p-5">
                                        <span class="font-mono font-bold text-navy-dark bg-slate-100 px-2 py-1 rounded text-xs">
                                            #<?php echo str_pad($pedido['id'], 5, '0', STR_PAD_LEFT); ?>
                                        </span>
                                    </td>
                                    <td class="p-5 font-medium text-slate-700 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-navy/5 text-navy flex items-center justify-center font-bold text-xs uppercase">
                                            <?php echo substr(e($pedido['cliente_nombre'] ?? 'N'), 0, 1); ?>
                                        </div>
                                        <?php echo e($pedido['cliente_nombre'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="p-5 font-bold text-navy"><?php echo formatPrice($pedido['total']); ?></td>
                                    <td class="p-5">
                                        <?php 
                                            $estadoClass = 'bg-slate-100 text-slate-500 border border-slate-200';
                                            if($pedido['estado'] == 'completado' || $pedido['estado'] == 'entregado') $estadoClass = 'bg-green-50 text-green-700 border border-green-200';
                                            if($pedido['estado'] == 'pendiente') $estadoClass = 'bg-yellow-50 text-yellow-700 border border-yellow-200';
                                            if($pedido['estado'] == 'cancelado') $estadoClass = 'bg-red-50 text-red-700 border border-red-200';
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest inline-flex items-center gap-1.5 shadow-sm <?php echo $estadoClass; ?>">
                                            <?php if($pedido['estado'] == 'completado' || $pedido['estado'] == 'entregado'): ?>
                                                <i class="fas fa-check-circle"></i>
                                            <?php elseif($pedido['estado'] == 'pendiente'): ?>
                                                <i class="fas fa-clock"></i>
                                            <?php elseif($pedido['estado'] == 'cancelado'): ?>
                                                <i class="fas fa-times-circle"></i>
                                            <?php else: ?>
                                                <i class="fas fa-circle"></i>
                                            <?php endif; ?>
                                            <?php echo ucfirst(e($pedido['estado'])); ?>
                                        </span>
                                    </td>
                                    <td class="p-5 text-slate-500 text-xs font-medium"><?php echo formatDate($pedido['fecha_pedido']); ?></td>
                                    <td class="p-5 text-center">
                                        <a href="<?php echo baseUrl('admin/pedidos/' . $pedido['id']); ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-slate-200 text-navy hover:bg-navy hover:text-white hover:border-navy transition-all shadow-sm hover:shadow-md" title="Inspeccionar Orden">
                                            <i class="fas fa-search text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-16 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                        <i class="fas fa-inbox text-2xl text-slate-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-navy-dark mb-1">Sin historial operativo</h3>
                    <p class="text-slate-500 text-sm">No existen órdenes registradas en la base de datos hasta el momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
