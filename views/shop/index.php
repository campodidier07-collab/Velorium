<?php 
$titulo = 'Inicio - Velorium';
require_once 'views/layouts/header.php';
?>

<!-- Hero Section -->
<section class="relative bg-navy-dark text-white overflow-hidden min-h-[90vh] flex items-center">
    <!-- Imagen de fondo premium -->
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1547996160-81dfa63595aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')] bg-cover bg-center opacity-40 mix-blend-luminosity scale-105 animate-[pulse_20s_ease-in-out_infinite_alternate]"></div>
    <!-- Gradientes para legibilidad -->
    <div class="absolute inset-0 bg-gradient-to-r from-navy-dark/90 via-navy/60 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-navy-dark via-transparent to-transparent"></div>

    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-3 mb-6 opacity-0 animate-[fade-in-up_1s_ease-out_0.3s_forwards]">
                <span class="w-12 h-px bg-gold"></span>
                <span class="text-gold tracking-[0.2em] text-sm uppercase font-semibold">Colección Exclusiva</span>
            </div>
            
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-serif font-bold mb-6 text-white tracking-wide leading-[1.1] opacity-0 animate-[fade-in-up_1s_ease-out_0.5s_forwards]">
                Donde el tiempo <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-light via-gold to-gold-dark italic pr-2">refleja la excelencia</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-300 mb-10 font-light leading-relaxed max-w-2xl opacity-0 animate-[fade-in-up_1s_ease-out_0.7s_forwards]">
                Descubra obras maestras de la ingeniería suiza. Relojes diseñados no solo para dar la hora, sino para trascender generaciones.
            </p>
            
            <div class="flex flex-wrap items-center gap-6 opacity-0 animate-[fade-in-up_1s_ease-out_0.9s_forwards]">
                <a href="<?php echo baseUrl('catalogo'); ?>" class="relative group inline-flex items-center justify-center bg-gold text-navy-dark font-bold py-4 px-10 overflow-hidden transition-all hover:shadow-[0_0_20px_rgba(212,175,55,0.4)]">
                    <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                    <span class="relative flex items-center gap-2">Explorar Catálogo <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i></span>
                </a>
                
                <a href="#featured" class="text-white hover:text-gold transition-colors font-medium flex items-center gap-2 group">
                    <span class="w-10 h-10 rounded-full border border-slate-500 flex items-center justify-center group-hover:border-gold transition-colors">
                        <i class="fas fa-play text-xs ml-0.5"></i>
                    </span>
                    Ver el video
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-0 animate-[fade-in_1s_ease-out_1.5s_forwards]">
        <span class="text-xs text-slate-400 tracking-widest uppercase">Descubrir</span>
        <div class="w-px h-16 bg-gradient-to-b from-gold to-transparent relative overflow-hidden">
            <div class="absolute top-0 w-full h-1/2 bg-white animate-[scroll-down_2s_ease-in-out_infinite]"></div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-navy border-b border-navy-light/30">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 divide-y md:divide-y-0 md:divide-x divide-navy-light/30">
            <div class="flex items-start gap-4 text-slate-300 pt-6 md:pt-0">
                <i class="fas fa-gem text-3xl text-gold mt-1"></i>
                <div>
                    <h4 class="text-white font-bold mb-2 uppercase tracking-wide">Calidad Insuperable</h4>
                    <p class="text-sm font-light leading-relaxed">Piezas ensambladas a mano por maestros relojeros suizos garantizando una precisión milimétrica.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 text-slate-300 pt-6 md:pt-0 md:pl-8">
                <i class="fas fa-shield-alt text-3xl text-gold mt-1"></i>
                <div>
                    <h4 class="text-white font-bold mb-2 uppercase tracking-wide">Garantía Internacional</h4>
                    <p class="text-sm font-light leading-relaxed">Todos nuestros relojes incluyen certificación de autenticidad y 5 años de garantía mundial.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 text-slate-300 pt-6 md:pt-0 md:pl-8">
                <i class="fas fa-shipping-fast text-3xl text-gold mt-1"></i>
                <div>
                    <h4 class="text-white font-bold mb-2 uppercase tracking-wide">Envío Asegurado</h4>
                    <p class="text-sm font-light leading-relaxed">Transporte discreto, rápido y totalmente asegurado hasta la puerta de su residencia o empresa.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<section id="featured" class="py-24 bg-slate-50 relative">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <div class="inline-flex items-center gap-3 mb-4">
                    <span class="w-8 h-px bg-gold"></span>
                    <span class="text-gold tracking-wider text-xs uppercase font-semibold">Selección de expertos</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-serif font-bold text-navy-dark relative inline-block">
                    Piezas Destacadas
                </h2>
            </div>
            <a href="<?php echo baseUrl('catalogo'); ?>" class="text-navy font-semibold hover:text-gold transition-colors flex items-center gap-2 group">
                Ver toda la colección <i class="fas fa-long-arrow-alt-right group-hover:translate-x-2 transition-transform"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="group bg-white rounded-xl hover:shadow-2xl transition-all duration-500 overflow-hidden relative flex flex-col h-full border border-slate-100 hover:border-gold/20 -translate-y-0 hover:-translate-y-2">
                        
                        <!-- Ribbon/Etiqueta opcional -->
                        <?php if(isset($producto['destacado']) && $producto['destacado']): ?>
                            <div class="absolute top-4 left-4 z-10 bg-navy text-white text-[10px] font-bold px-3 py-1 uppercase tracking-wider rounded-sm">Premium</div>
                        <?php endif; ?>

                        <!-- Imagen del Producto -->
                        <div class="relative h-80 overflow-hidden bg-[#f8f9fa] flex items-center justify-center p-8 group-hover:bg-white transition-colors duration-500">
                            <!-- Overlay de botones on hover -->
                            <div class="absolute inset-0 bg-navy/5 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
                                <a href="<?php echo baseUrl('producto/' . $producto['id']); ?>" class="w-12 h-12 rounded-full bg-white text-navy flex items-center justify-center hover:bg-gold hover:text-white hover:scale-110 transition-all shadow-lg translate-y-4 group-hover:translate-y-0 duration-300">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                            <img src="<?php echo asset('images/' . e($producto['imagen'])); ?>" alt="<?php echo e($producto['nombre']); ?>" class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-110 group-hover:drop-shadow-xl transition-all duration-700">
                        </div>
                        
                        <!-- Info del Producto -->
                        <div class="p-6 flex flex-col flex-grow bg-white relative z-20">
                            <p class="text-gold-dark font-bold text-[10px] uppercase tracking-widest mb-2"><?php echo e($producto['marca']); ?></p>
                            <h3 class="text-lg font-serif font-bold text-navy-dark mb-3 line-clamp-2 leading-tight group-hover:text-gold transition-colors" title="<?php echo e($producto['nombre']); ?>">
                                <a href="<?php echo baseUrl('producto/' . $producto['id']); ?>"><?php echo e($producto['nombre']); ?></a>
                            </h3>
                            
                            <div class="mt-auto flex items-center justify-between pt-4 border-t border-slate-100">
                                <span class="text-xl font-bold text-navy tracking-tight"><?php echo formatPrice($producto['precio_final'] ?? $producto['precio']); ?></span>
                                
                                <form method="POST" action="<?php echo baseUrl('carrito?action=agregar'); ?>">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="w-10 h-10 rounded-full bg-slate-50 text-navy flex items-center justify-center hover:bg-navy hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed" <?php echo ($producto['cantidad_disponible'] ?? 0) <= 0 ? 'disabled title="Agotado"' : 'title="Agregar al carrito"'; ?>>
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full flex flex-col items-center justify-center text-center py-20 bg-white rounded-xl border border-slate-100 shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-box-open text-3xl text-slate-300"></i>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-navy mb-2">Colección en Preparación</h3>
                    <p class="text-slate-500 max-w-md">Nuestros curadores están seleccionando nuevas piezas exclusivas. Vuelva a visitarnos pronto.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Seccion Dual Colecciones -->
<section class="py-0 overflow-hidden">
    <div class="flex flex-col md:flex-row h-auto md:h-[600px]">
        <!-- Coleccion Clásica -->
        <div class="w-full md:w-1/2 relative group overflow-hidden h-[400px] md:h-full">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80')] bg-cover bg-center transition-transform duration-1000 group-hover:scale-110"></div>
            <div class="absolute inset-0 bg-navy-dark/60 group-hover:bg-navy-dark/40 transition-colors duration-500"></div>
            <div class="absolute inset-0 p-12 flex flex-col justify-end">
                <span class="text-gold tracking-widest text-xs uppercase font-bold mb-3">Estilo Atemporal</span>
                <h3 class="text-4xl font-serif font-bold text-white mb-4">Colección Clásica</h3>
                <p class="text-slate-300 font-light mb-8 max-w-md">Relojes automáticos con esferas tradicionales y correas de cuero de la más alta calidad.</p>
                <div>
                    <a href="<?php echo baseUrl('catalogo?categoria=clasico'); ?>" class="inline-block border border-white text-white hover:bg-white hover:text-navy-dark uppercase tracking-widest text-xs font-bold py-3 px-8 transition-colors">
                        Descubrir
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Coleccion Deportiva -->
        <div class="w-full md:w-1/2 relative group overflow-hidden h-[400px] md:h-full">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80')] bg-cover bg-center transition-transform duration-1000 group-hover:scale-110"></div>
            <div class="absolute inset-0 bg-navy-dark/60 group-hover:bg-navy-dark/40 transition-colors duration-500"></div>
            <div class="absolute inset-0 p-12 flex flex-col justify-end">
                <span class="text-gold tracking-widest text-xs uppercase font-bold mb-3">Rendimiento Extremo</span>
                <h3 class="text-4xl font-serif font-bold text-white mb-4">Línea Deportiva</h3>
                <p class="text-slate-300 font-light mb-8 max-w-md">Cronógrafos de alta precisión diseñados para soportar las condiciones más exigentes.</p>
                <div>
                    <a href="<?php echo baseUrl('catalogo?categoria=deportivo'); ?>" class="inline-block border border-white text-white hover:bg-white hover:text-navy-dark uppercase tracking-widest text-xs font-bold py-3 px-8 transition-colors">
                        Explorar
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estilos para animaciones custom -->
<style>
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scroll-down {
        0% { transform: translateY(-100%); }
        100% { transform: translateY(200%); }
    }
</style>

<?php require_once 'views/layouts/footer.php'; ?>
