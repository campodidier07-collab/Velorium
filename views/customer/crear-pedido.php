<?php 
$titulo = 'Crear Pedido - Velorium';
requireAuth();
require_once 'views/layouts/header.php';
?>

<div class="bg-slate-50 py-10 min-h-screen">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <div class="bg-white rounded shadow-sm border border-slate-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-navy p-6 border-b-2 border-gold text-center relative">
                <h1 class="text-2xl font-serif font-bold text-white relative z-10">Confirmación de Pedido</h1>
                <p class="text-slate-300 relative z-10 text-sm mt-1">Complete los detalles para finalizar su compra</p>
            </div>
            
            <div class="p-8">
                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-sm" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo e($error); ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="direccion" class="block text-sm font-semibold text-navy-dark mb-2 uppercase tracking-wide">Dirección de Envío *</label>
                        <textarea id="direccion" name="direccion" required rows="4" placeholder="Ingrese su dirección completa" class="w-full border border-slate-300 rounded p-3 focus:ring-1 focus:ring-gold outline-none text-sm bg-slate-50 text-slate-700"></textarea>
                    </div>
                    
                    <div>
                        <label for="metodo_pago" class="block text-sm font-semibold text-navy-dark mb-2 uppercase tracking-wide">Método de Pago *</label>
                        <div class="relative">
                            <select id="metodo_pago" name="metodo_pago" required class="w-full border border-slate-300 rounded p-3 focus:ring-1 focus:ring-gold outline-none text-sm bg-slate-50 text-slate-700 appearance-none">
                                <option value="">-- Seleccione un método --</option>
                                <?php foreach ($metodos as $metodo): ?>
                                    <option value="<?php echo $metodo['id']; ?>">
                                        <?php echo e($metodo['nombre']); ?> - <?php echo e($metodo['descripcion']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="notas" class="block text-sm font-semibold text-navy-dark mb-2 uppercase tracking-wide">Notas Adicionales</label>
                        <textarea id="notas" name="notas" rows="3" placeholder="Instrucciones especiales para la entrega (opcional)" class="w-full border border-slate-300 rounded p-3 focus:ring-1 focus:ring-gold outline-none text-sm bg-slate-50 text-slate-700"></textarea>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-100 flex gap-4">
                        <a href="<?php echo baseUrl('carrito'); ?>" class="w-1/3 bg-white text-slate-600 border border-slate-300 font-medium py-3 rounded hover:bg-slate-50 transition-colors text-center text-sm flex items-center justify-center">
                            Volver al Carrito
                        </a>
                        <button type="submit" class="w-2/3 bg-navy hover:bg-navy-light text-white font-medium py-3 rounded transition-colors text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Confirmar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
