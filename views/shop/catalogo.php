<?php 
$titulo = 'Catálogo - Velorium';
require_once 'views/layouts/header.php';
$filtros = $filtros ?? ['categoria' => '', 'genero' => '', 'orden' => 'nombre'];
?>

<!-- Hero Banner del Catálogo -->
<div class="relative bg-navy-dark py-24 overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1547996160-81dfa63595aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')] bg-cover bg-center opacity-20 mix-blend-luminosity"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-transparent to-transparent"></div>
    <div class="container mx-auto px-4 lg:px-8 relative z-10 text-center">
        <div class="inline-flex items-center gap-3 mb-4">
            <span class="w-8 h-px bg-gold"></span>
            <span class="text-gold tracking-widest text-xs uppercase font-semibold">Descubra la perfección</span>
            <span class="w-8 h-px bg-gold"></span>
        </div>
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-white mb-4">Catálogo Velorium</h1>
        <p class="text-slate-300 max-w-xl mx-auto font-light">Explore nuestra cuidada selección de guardatiempos excepcionales, donde la alta ingeniería suiza se encuentra con el diseño atemporal.</p>
    </div>
</div>

<div class="bg-slate-50 pb-20 min-h-screen">
    <div class="container mx-auto px-4 lg:px-8">
        
        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- Sidebar Filtros -->
            <aside class="w-full lg:w-1/4">
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8 sticky top-28">
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                        <h3 class="text-lg font-serif font-bold text-navy-dark flex items-center gap-3">
                            <i class="fas fa-sliders-h text-gold"></i> Filtros
                        </h3>
                        <?php if(!empty(array_filter($filtros))): ?>
                            <a href="<?php echo baseUrl('catalogo'); ?>" class="text-[10px] uppercase tracking-wider text-slate-400 hover:text-navy transition-colors font-bold">Limpiar</a>
                        <?php endif; ?>
                    </div>
                    
                    <form method="GET" class="space-y-8">
                        <!-- Categoría -->
                        <div>
                            <h4 class="font-bold text-navy-dark mb-4 text-xs uppercase tracking-widest">Categoría</h4>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="categoria" value="" <?php echo empty($filtros['categoria']) ? 'checked' : ''; ?> class="form-radio text-gold focus:ring-gold h-4 w-4 border-slate-300"> 
                                    <span class="text-slate-600 text-sm group-hover:text-gold-dark transition-colors font-medium">Todas las colecciones</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="categoria" value="clasico" <?php echo ($filtros['categoria'] ?? '') === 'clasico' ? 'checked' : ''; ?> class="form-radio text-gold focus:ring-gold h-4 w-4 border-slate-300"> 
                                    <span class="text-slate-600 text-sm group-hover:text-gold-dark transition-colors font-medium">Clásicos</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="categoria" value="deportivo" <?php echo ($filtros['categoria'] ?? '') === 'deportivo' ? 'checked' : ''; ?> class="form-radio text-gold focus:ring-gold h-4 w-4 border-slate-300"> 
                                    <span class="text-slate-600 text-sm group-hover:text-gold-dark transition-colors font-medium">Deportivos</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="categoria" value="lujo" <?php echo ($filtros['categoria'] ?? '') === 'lujo' ? 'checked' : ''; ?> class="form-radio text-gold focus:ring-gold h-4 w-4 border-slate-300"> 
                                    <span class="text-slate-600 text-sm group-hover:text-gold-dark transition-colors font-medium">Alta Relojería (Lujo)</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Género -->
                        <div>
                            <h4 class="font-bold text-navy-dark mb-4 text-xs uppercase tracking-widest">Género</h4>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="genero" value="" <?php echo empty($filtros['genero']) ? 'checked' : ''; ?> class="form-radio text-gold focus:ring-gold h-4 w-4 border-slate-300"> 
                                    <span class="text-slate-600 text-sm group-hover:text-gold-dark transition-colors font-medium">Todos</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="genero" value="dama" <?php echo ($filtros['genero'] ?? '') === 'dama' ? 'checked' : ''; ?> class="form-radio text-gold focus:ring-gold h-4 w-4 border-slate-300"> 
                                    <span class="text-slate-600 text-sm group-hover:text-gold-dark transition-colors font-medium">Dama</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="genero" value="caballero" <?php echo ($filtros['genero'] ?? '') === 'caballero' ? 'checked' : ''; ?> class="form-radio text-gold focus:ring-gold h-4 w-4 border-slate-300"> 
                                    <span class="text-slate-600 text-sm group-hover:text-gold-dark transition-colors font-medium">Caballero</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="genero" value="unisex" <?php echo ($filtros['genero'] ?? '') === 'unisex' ? 'checked' : ''; ?> class="form-radio text-gold focus:ring-gold h-4 w-4 border-slate-300"> 
                                    <span class="text-slate-600 text-sm group-hover:text-gold-dark transition-colors font-medium">Unisex</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Ordenar por -->
                        <div>
                            <h4 class="font-bold text-navy-dark mb-4 text-xs uppercase tracking-widest">Ordenar por</h4>
                            <div class="relative">
                                <select name="orden" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gold focus:border-gold cursor-pointer transition-colors hover:bg-slate-100">
                                    <option value="nombre" <?php echo ($filtros['orden'] ?? '') === 'nombre' ? 'selected' : ''; ?>>Orden Alfabético</option>
                                    <option value="precio_asc" <?php echo ($filtros['orden'] ?? '') === 'precio_asc' ? 'selected' : ''; ?>>Precio: Menor a Mayor</option>
                                    <option value="precio_desc" <?php echo ($filtros['orden'] ?? '') === 'precio_desc' ? 'selected' : ''; ?>>Precio: Mayor a Menor</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-6 border-t border-slate-100">
                            <button type="submit" class="w-full bg-navy text-white text-xs tracking-widest uppercase font-bold py-4 px-4 rounded-md hover:bg-navy-light transition-all shadow-md hover:shadow-lg flex justify-center items-center gap-2">
                                Aplicar Filtros <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </aside>
            
            <!-- Grid de Productos -->
            <main class="w-full lg:w-3/4">
                
                <!-- Barra de resultados superior -->
                <div class="flex justify-between items-center mb-8 text-sm text-slate-500">
                    <p>Mostrando <span class="font-bold text-navy-dark"><?php echo count($productos); ?></span> piezas exclusivas</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $producto): ?>
                            <div class="group bg-white rounded-xl hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 overflow-hidden relative flex flex-col h-full border border-slate-100 hover:border-gold/30 -translate-y-0 hover:-translate-y-2">
                                
                                <!-- Ribbon/Etiqueta -->
                                <?php if(($producto['cantidad_disponible'] ?? 0) <= 0): ?>
                                    <div class="absolute top-4 right-4 z-10 bg-slate-800 text-white text-[9px] font-bold px-3 py-1 uppercase tracking-wider rounded-sm">Agotado</div>
                                <?php elseif(isset($producto['destacado']) && $producto['destacado']): ?>
                                    <div class="absolute top-4 left-4 z-10 bg-gold text-navy-dark text-[9px] font-bold px-3 py-1 uppercase tracking-wider rounded-sm shadow-sm">Premium</div>
                                <?php endif; ?>

                                <!-- Imagen del Producto -->
                                <div class="relative h-80 overflow-hidden bg-gradient-to-b from-[#fdfdfd] to-[#f8f9fa] flex items-center justify-center p-8 group-hover:bg-white transition-colors duration-500">
                                    <div class="absolute inset-0 bg-navy/5 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                        <a href="<?php echo baseUrl('producto/' . $producto['id']); ?>" class="w-14 h-14 rounded-full bg-white text-navy flex items-center justify-center hover:bg-gold hover:text-white hover:scale-110 transition-all shadow-xl translate-y-4 group-hover:translate-y-0 duration-300">
                                            <i class="fas fa-expand"></i>
                                        </a>
                                    </div>
                                    <img src="<?php echo asset('images/' . e($producto['imagen'])); ?>" alt="<?php echo e($producto['nombre']); ?>" class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-110 group-hover:drop-shadow-2xl transition-all duration-700">
                                </div>
                                
                                <!-- Info del Producto -->
                                <div class="p-6 flex flex-col flex-grow bg-white relative z-20">
                                    <p class="text-gold-dark font-bold text-[10px] uppercase tracking-widest mb-2 flex items-center justify-between">
                                        <?php echo e($producto['marca']); ?>
                                        <span class="text-slate-300 font-normal"><?php echo e($producto['categoria']); ?></span>
                                    </p>
                                    <h3 class="text-lg font-serif font-bold text-navy-dark mb-4 line-clamp-2 leading-tight group-hover:text-gold transition-colors" title="<?php echo e($producto['nombre']); ?>">
                                        <a href="<?php echo baseUrl('producto/' . $producto['id']); ?>"><?php echo e($producto['nombre']); ?></a>
                                    </h3>
                                    
                                    <div class="mt-auto flex items-center justify-between pt-4 border-t border-slate-50">
                                        <div class="flex flex-col">
                                            <?php if(isset($producto['precio_final']) && $producto['precio_final'] < $producto['precio']): ?>
                                                <span class="text-xs text-slate-400 line-through mb-0.5"><?php echo formatPrice($producto['precio']); ?></span>
                                            <?php endif; ?>
                                            <span class="text-xl font-bold text-navy tracking-tight"><?php echo formatPrice($producto['precio_final'] ?? $producto['precio']); ?></span>
                                        </div>
                                        
                                        <form method="POST" action="<?php echo baseUrl('carrito?action=agregar'); ?>">
                                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                            <input type="hidden" name="cantidad" value="1">
                                            <button type="submit" class="w-12 h-12 rounded-full bg-slate-50 text-navy flex items-center justify-center hover:bg-navy hover:text-white transition-all disabled:opacity-50 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed shadow-sm hover:shadow-md" <?php echo ($producto['cantidad_disponible'] ?? 0) <= 0 ? 'disabled title="Agotado"' : 'title="Añadir a su colección"'; ?>>
                                                <i class="fas fa-shopping-bag"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full flex flex-col items-center justify-center py-32 bg-white rounded-xl border border-slate-100 shadow-sm text-center px-4">
                            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-search text-4xl text-slate-300"></i>
                            </div>
                            <h3 class="text-2xl font-serif font-bold text-navy-dark mb-2">Sin resultados</h3>
                            <p class="text-slate-500 max-w-md mx-auto">No hemos encontrado piezas que coincidan con sus exigentes criterios. Por favor, intente ajustar los filtros.</p>
                            <a href="<?php echo baseUrl('catalogo'); ?>" class="mt-8 bg-gold hover:bg-gold-light text-navy-dark font-bold py-3 px-8 rounded transition-colors text-sm uppercase tracking-wider">
                                Ver todo el catálogo
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
