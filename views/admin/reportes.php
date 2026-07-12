<?php 
$titulo = 'Reportes y Estadísticas - Velorium';
require_once 'views/layouts/header.php';
?>

<div class="bg-slate-50 min-h-screen pb-16">
    
    <!-- Hero Header Administrativo -->
    <div class="bg-navy-dark pt-12 pb-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1547996160-81dfa63595aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')] bg-cover bg-center opacity-5 mix-blend-luminosity"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent to-navy-dark/90"></div>
        
        <div class="container mx-auto px-4 max-w-7xl relative z-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-3 mb-2">
                        <span class="w-6 h-px bg-gold"></span>
                        <span class="text-gold tracking-widest text-[10px] uppercase font-bold">Panel de Control</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-serif font-bold text-white mb-2">Reportes y Estadísticas</h1>
                    <p class="text-slate-400 font-light text-sm">Visualización de datos y rendimiento de la tienda.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="<?php echo baseUrl('admin/dashboard'); ?>" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold py-2.5 px-6 rounded-md transition-all text-xs uppercase tracking-wider flex items-center gap-2 backdrop-blur-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 max-w-7xl -mt-12 relative z-20">
        <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-12 text-center">
            <div class="w-24 h-24 mx-auto rounded-full bg-gold/10 flex items-center justify-center border border-gold/20 mb-6">
                <i class="fas fa-chart-line text-4xl text-gold-dark"></i>
            </div>
            <h2 class="text-2xl font-serif font-bold text-navy-dark mb-4">Módulo en Construcción</h2>
            <p class="text-slate-500 max-w-lg mx-auto leading-relaxed">
                El módulo avanzado de reportes y estadísticas en tiempo real se encuentra actualmente en desarrollo y estará disponible en una futura actualización.
            </p>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
