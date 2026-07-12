<?php 
$titulo = 'Su Carrito - Velorium';
require_once 'views/layouts/header.php';
?>

<!-- Hero Banner del Carrito -->
<div class="relative bg-navy-dark py-16 overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1547996160-81dfa63595aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')] bg-cover bg-center opacity-10 mix-blend-luminosity"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-transparent to-transparent"></div>
    <div class="container mx-auto px-4 lg:px-8 relative z-10 text-center">
        <h1 class="text-3xl md:text-4xl font-serif font-bold text-white mb-2">Su Colección Personal</h1>
        <p class="text-slate-400 font-light text-sm uppercase tracking-widest">Proceda a asegurar sus piezas</p>
    </div>
</div>

<div class="bg-slate-50 pb-20 min-h-screen">
    <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
        
        <?php if (empty($productos_detalle)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-16 text-center flex flex-col items-center justify-center min-h-[50vh]">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-shopping-bag text-4xl text-slate-300"></i>
                </div>
                <h2 class="text-2xl font-serif font-bold text-navy-dark mb-3">Su colección está vacía</h2>
                <p class="text-slate-500 mb-8 max-w-md mx-auto text-sm leading-relaxed">Aún no ha seleccionado ninguna pieza. Explore nuestro catálogo corporativo y descubra el reloj que elevará su estilo profesional.</p>
                <a href="<?php echo baseUrl('catalogo'); ?>" class="bg-gold hover:bg-gold-light text-navy-dark font-bold py-3 px-8 rounded transition-colors text-sm uppercase tracking-wider shadow-sm">
                    Ir al Catálogo
                </a>
            </div>
        <?php else: ?>
            <div class="flex flex-col lg:flex-row gap-10 mt-8">
                
                <!-- Lista de Productos -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                        
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-xl font-serif font-bold text-navy-dark">Artículos en Revisión</h2>
                            <span class="bg-navy text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider"><?php echo count($productos_detalle ?? []); ?> Piezas</span>
                        </div>

                        <!-- Items -->
                        <div class="divide-y divide-slate-100">
                            <?php foreach ($productos_detalle as $producto): ?>
                                <div class="p-6 hover:bg-slate-50/50 transition-colors flex flex-col sm:flex-row gap-6 items-center sm:items-start relative group">
                                    
                                    <!-- Botón Eliminar Móvil -->
                                    <form method="POST" action="<?php echo baseUrl('carrito?action=eliminar'); ?>" class="absolute top-4 right-4 sm:hidden">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="w-8 h-8 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center shadow-sm" title="Eliminar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>

                                    <!-- Imagen -->
                                    <div class="w-32 h-32 bg-[#f8f9fa] rounded-lg flex-shrink-0 flex items-center justify-center p-4">
                                        <img src="<?php echo asset('images/' . e($producto['imagen'])); ?>" alt="<?php echo e($producto['nombre']); ?>" class="max-h-full object-contain filter drop-shadow-md">
                                    </div>
                                    
                                    <!-- Detalles del Producto -->
                                    <div class="flex-grow w-full flex flex-col h-full justify-between">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-[10px] text-gold-dark font-bold uppercase tracking-widest mb-1"><?php echo e($producto['marca']); ?></p>
                                                <h4 class="text-lg font-serif font-bold text-navy-dark line-clamp-2 leading-tight pr-8 sm:pr-0 mb-2">
                                                    <a href="<?php echo baseUrl('producto/' . $producto['id']); ?>" class="hover:text-gold transition-colors"><?php echo e($producto['nombre']); ?></a>
                                                </h4>
                                                <div class="text-sm font-medium text-slate-500 mb-4 sm:mb-0">
                                                    <?php echo formatPrice($producto['precio_final']); ?> <span class="text-xs font-normal">/ unidad</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Eliminar Desktop -->
                                            <form method="POST" action="<?php echo baseUrl('carrito?action=eliminar'); ?>" class="hidden sm:block">
                                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                                <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors p-2 text-sm" title="Remover pieza">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-50 w-full">
                                            <!-- Cantidad -->
                                            <form method="POST" action="<?php echo baseUrl('carrito?action=actualizar'); ?>" class="flex items-center gap-3">
                                                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest hidden sm:inline-block">Cant:</span>
                                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                                <div class="w-20">
                                                    <input type="number" name="cantidad" value="<?php echo $producto['cantidad_carrito']; ?>" min="1" class="w-full h-10 border border-slate-200 rounded-md text-center focus:ring-1 focus:ring-gold focus:border-gold outline-none text-sm bg-white transition-colors" onchange="this.form.submit()">
                                                </div>
                                                <button type="submit" class="hidden">Actualizar</button>
                                            </form>
                                            
                                            <!-- Subtotal -->
                                            <div class="text-right">
                                                <span class="block sm:hidden text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Subtotal</span>
                                                <span class="font-bold text-navy-dark text-lg sm:text-xl"><?php echo formatPrice($producto['subtotal']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen de Orden -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8 sticky top-28">
                        <h3 class="text-xl font-serif font-bold text-navy-dark mb-6 flex items-center gap-3">
                            <i class="fas fa-file-invoice-dollar text-gold"></i> Resumen
                        </h3>
                        
                        <div class="space-y-4 mb-8 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span class="font-medium">Subtotal</span>
                                <span class="font-bold text-navy-dark"><?php echo formatPrice($total ?? 0); ?></span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span class="font-medium">Envío Asegurado</span>
                                <span class="font-bold text-navy-dark"><?php echo formatPrice(10); ?></span>
                            </div>
                            <div class="flex justify-between text-slate-600 border-b border-slate-100 pb-4">
                                <span class="font-medium">Impuestos</span>
                                <span class="text-xs font-bold text-gold bg-gold/10 px-2 py-0.5 rounded-sm uppercase tracking-wider">Incluidos</span>
                            </div>
                            
                            <div class="flex justify-between items-end pt-2">
                                <span class="text-lg font-serif font-bold text-slate-800">Total a Pagar</span>
                                <span class="text-2xl font-bold text-navy"><?php echo formatPrice(($total ?? 0) + 10); ?></span>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <?php if (isLoggedIn()): ?>
                                <a href="<?php echo baseUrl('pedidos/crear'); ?>" class="w-full bg-navy text-white text-xs tracking-widest uppercase font-bold py-4 px-4 rounded-md hover:bg-navy-light transition-all shadow-md hover:shadow-lg flex justify-center items-center gap-3">
                                    <i class="fas fa-lock"></i> Proceder al Pago Seguro
                                </a>
                            <?php else: ?>
                                <a href="<?php echo baseUrl('login'); ?>" class="w-full bg-navy text-white text-xs tracking-widest uppercase font-bold py-4 px-4 rounded-md hover:bg-navy-light transition-all shadow-md hover:shadow-lg flex justify-center items-center gap-3">
                                    <i class="fas fa-user-circle"></i> Iniciar Sesión para Pagar
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo baseUrl('catalogo'); ?>" class="w-full bg-white text-navy-dark text-xs tracking-widest uppercase font-bold py-4 px-4 rounded-md hover:bg-slate-50 border border-slate-200 transition-colors flex justify-center items-center">
                                Continuar Explorando
                            </a>
                        </div>
                        
                        <!-- Badges de seguridad corporativa -->
                        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-4">Pagos 100% Seguros</p>
                            <div class="flex justify-center gap-6 text-slate-300">
                                <i class="fab fa-cc-visa text-3xl hover:text-slate-400 transition-colors"></i>
                                <i class="fab fa-cc-mastercard text-3xl hover:text-slate-400 transition-colors"></i>
                                <i class="fab fa-cc-amex text-3xl hover:text-slate-400 transition-colors"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
