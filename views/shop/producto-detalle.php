<?php 
$titulo = ($producto['nombre'] ?? 'Producto') . ' - Velorium';
require_once 'views/layouts/header.php';
?>

<div class="bg-slate-50 py-10 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <!-- Breadcrumb -->
        <nav class="flex text-slate-500 text-xs mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="<?php echo baseUrl(); ?>" class="hover:text-navy transition-colors inline-flex items-center gap-1">
                        Inicio
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-[10px] mx-2 text-slate-400"></i>
                        <a href="<?php echo baseUrl('catalogo'); ?>" class="hover:text-navy transition-colors">Catálogo</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-[10px] mx-2 text-slate-400"></i>
                        <span class="text-navy-dark font-medium truncate max-w-[200px] sm:max-w-xs"><?php echo e($producto['nombre']); ?></span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <!-- Contenedor del Producto -->
        <div class="bg-white rounded shadow-sm border border-slate-200 flex flex-col lg:flex-row">
            
            <!-- Imagen -->
            <div class="w-full lg:w-1/2 p-8 lg:p-12 bg-white flex items-center justify-center relative border-b lg:border-b-0 lg:border-r border-slate-100">
                <img src="<?php echo asset('images/' . e($producto['imagen'])); ?>" alt="<?php echo e($producto['nombre']); ?>" class="relative z-10 w-full max-w-sm object-contain">
                <?php if (($producto['cantidad_disponible'] ?? 0) <= 0): ?>
                    <div class="absolute top-6 right-6 bg-slate-800 text-white text-[10px] uppercase font-bold px-2 py-1 rounded">Agotado</div>
                <?php endif; ?>
            </div>
            
            <!-- Info -->
            <div class="w-full lg:w-1/2 p-8 lg:p-12 flex flex-col justify-center">
                <p class="text-gold-dark font-bold uppercase tracking-widest text-xs mb-2"><?php echo e($producto['marca']); ?></p>
                <h1 class="text-2xl md:text-3xl font-serif font-bold text-navy-dark mb-4 leading-tight"><?php echo e($producto['nombre']); ?></h1>
                
                <div class="flex items-center gap-3 mb-6 pb-6 border-b border-slate-100">
                    <div class="flex text-gold text-xs">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-xs text-slate-500">(24 reseñas)</span>
                </div>
                
                <div class="mb-8">
                    <span class="text-3xl font-bold text-navy"><?php echo formatPrice($producto['precio_final'] ?? $producto['precio']); ?></span>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wide">Impuestos incluidos</p>
                </div>
                
                <div class="grid grid-cols-2 gap-y-3 gap-x-6 mb-8 text-sm">
                    <div class="flex flex-col border-b border-slate-50 pb-2">
                        <span class="text-slate-400 text-xs uppercase tracking-wider mb-1">Categoría</span>
                        <span class="font-medium text-navy-dark"><?php echo ucfirst(e($producto['categoria'])); ?></span>
                    </div>
                    <div class="flex flex-col border-b border-slate-50 pb-2">
                        <span class="text-slate-400 text-xs uppercase tracking-wider mb-1">Género</span>
                        <span class="font-medium text-navy-dark"><?php echo ucfirst(e($producto['genero'])); ?></span>
                    </div>
                    <div class="flex flex-col border-b border-slate-50 pb-2">
                        <span class="text-slate-400 text-xs uppercase tracking-wider mb-1">Material</span>
                        <span class="font-medium text-navy-dark"><?php echo e($producto['material']); ?></span>
                    </div>
                    <div class="flex flex-col border-b border-slate-50 pb-2">
                        <span class="text-slate-400 text-xs uppercase tracking-wider mb-1">Disponibilidad</span>
                        <span class="font-medium <?php echo ($producto['cantidad_disponible'] ?? 0) > 0 ? 'text-navy-light' : 'text-slate-500'; ?>">
                            <?php echo ($producto['cantidad_disponible'] ?? 0) > 0 ? 'En Stock' : 'Agotado'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-navy-dark mb-2 uppercase tracking-wider">Descripción</h3>
                    <p class="text-slate-600 leading-relaxed text-sm font-light"><?php echo nl2br(e($producto['descripcion'])); ?></p>
                </div>
                
                <form method="POST" action="<?php echo baseUrl('carrito?action=agregar'); ?>" class="mt-auto">
                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                    
                    <div class="flex flex-col sm:flex-row gap-3 items-end">
                        <div class="w-full sm:w-24">
                            <label for="cantidad" class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wider">Cantidad</label>
                            <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?php echo $producto['cantidad_disponible'] ?? 10; ?>" class="w-full h-10 text-center border border-slate-300 rounded focus:ring-1 focus:ring-gold outline-none text-sm bg-slate-50">
                        </div>
                        
                        <button type="submit" class="w-full sm:flex-1 h-10 bg-navy text-white text-sm font-medium rounded hover:bg-navy-light transition-colors flex justify-center items-center gap-2 disabled:bg-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed" <?php echo ($producto['cantidad_disponible'] ?? 0) <= 0 ? 'disabled' : ''; ?>>
                            <?php echo ($producto['cantidad_disponible'] ?? 0) <= 0 ? 'Agotado' : 'Añadir al Pedido'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
