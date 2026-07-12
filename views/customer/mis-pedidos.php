<?php 
$titulo = 'Mis Pedidos - Velorium';
requireAuth();
require_once 'views/layouts/header.php';
?>

<div class="bg-[#f8fafc] min-h-screen pb-16">
    <!-- Hero Header -->
    <div class="bg-navy-dark pt-12 pb-20 relative overflow-hidden">
        <!-- Decoración de fondo -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1549970924-4f24f0a202c4?auto=format&fit=crop&q=80&w=2000" alt="Fondo" class="w-full h-full object-cover opacity-10 mix-blend-luminosity">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-navy-dark"></div>
        </div>
        
        <div class="container mx-auto px-4 max-w-6xl relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <div class="flex items-center mb-3">
                        <span class="w-8 h-[1px] bg-gold inline-block mr-4"></span>
                        <span class="text-gold text-xs tracking-[0.2em] uppercase font-bold">Su Cuenta</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-serif font-bold text-white mb-2">
                        Historial de Pedidos
                    </h1>
                    <p class="text-slate-400 font-light text-sm md:text-base max-w-xl">
                        Consulte el seguimiento y los detalles de sus adquisiciones en Velorium.
                    </p>
                </div>
                
                <a href="<?php echo baseUrl('catalogo'); ?>" class="group inline-flex items-center justify-center bg-transparent border border-gold text-gold hover:bg-gold hover:text-navy-dark font-medium py-3 px-6 rounded transition-all duration-300 transform hover:-translate-y-1 shadow-[0_0_15px_rgba(212,175,55,0.1)] hover:shadow-[0_0_20px_rgba(212,175,55,0.3)]">
                    <i class="fas fa-shopping-bag mr-2 group-hover:animate-bounce"></i> 
                    Continuar Comprando
                </a>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="container mx-auto px-4 max-w-6xl -mt-8 relative z-20">
        <?php if (empty($pedidos)): ?>
            <div class="bg-white rounded-xl shadow-xl border border-slate-100 p-16 text-center flex flex-col items-center justify-center min-h-[400px] transform transition-all hover:shadow-2xl">
                <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center mb-6 shadow-inner">
                    <i class="fas fa-box-open text-5xl text-slate-300"></i>
                </div>
                <h2 class="text-2xl font-serif font-bold text-navy-dark mb-3">Aún no tiene pedidos</h2>
                <p class="text-slate-500 mb-8 max-w-md">Su historial de compras está vacío. Descubra nuestra exclusiva colección de relojes y encuentre la pieza perfecta para usted.</p>
                <a href="<?php echo baseUrl('catalogo'); ?>" class="bg-navy hover:bg-navy-light text-white font-medium py-3 px-8 rounded transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Explorar Colección
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Pedido</th>
                                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Fecha</th>
                                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Total</th>
                                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Estado</th>
                                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-navy group-hover:text-gold transition-colors">
                                                <i class="fas fa-receipt text-sm"></i>
                                            </div>
                                            <span class="font-bold text-navy-dark">#<?php echo str_pad($pedido['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex flex-col">
                                            <span class="text-slate-700 font-medium"><?php echo date('d M, Y', strtotime($pedido['fecha_pedido'])); ?></span>
                                            <span class="text-slate-400 text-xs"><?php echo date('H:i', strtotime($pedido['fecha_pedido'])); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-5 font-bold text-navy-dark text-lg">
                                        <?php echo formatPrice($pedido['total']); ?>
                                    </td>
                                    <td class="p-5">
                                        <?php 
                                            $estado = strtolower($pedido['estado']);
                                            $estadoClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                            $icono = 'fa-clock';
                                            
                                            if(in_array($estado, ['completado', 'entregado'])) {
                                                $estadoClass = 'bg-[#f0fdf4] text-[#166534] border-[#bbf7d0]';
                                                $icono = 'fa-check-circle';
                                            } elseif($estado == 'pendiente') {
                                                $estadoClass = 'bg-[#fefce8] text-[#854d0e] border-[#fef08a]';
                                                $icono = 'fa-hourglass-half';
                                            } elseif($estado == 'cancelado') {
                                                $estadoClass = 'bg-[#fef2f2] text-[#991b1b] border-[#fecaca]';
                                                $icono = 'fa-times-circle';
                                            }
                                        ?>
                                        <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide inline-flex items-center gap-2 border <?php echo $estadoClass; ?>">
                                            <i class="fas <?php echo $icono; ?>"></i>
                                            <?php echo e($pedido['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="p-5 text-center">
                                        <a href="<?php echo baseUrl('pedidos/' . $pedido['id']); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-navy hover:border-navy hover:text-white transition-all duration-300 transform hover:scale-110 shadow-sm hover:shadow-md" title="Ver Detalles">
                                            <i class="fas fa-chevron-right text-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
