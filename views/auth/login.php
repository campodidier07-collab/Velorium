<?php 
$titulo = 'Iniciar Sesión - Velorium';
require_once 'views/layouts/header.php';
?>

<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50 relative">
    <!-- Círculos decorativos de fondo -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-gold opacity-5 blur-3xl"></div>
        <div class="absolute bottom-[10%] -right-[5%] w-[40%] h-[40%] rounded-full bg-navy opacity-5 blur-3xl"></div>
    </div>

    <div class="max-w-5xl w-full bg-white rounded-2xl shadow-xl flex overflow-hidden border border-slate-100 relative z-10">
        
        <!-- Panel Izquierdo (Imagen corporativa) -->
        <div class="hidden md:flex md:w-1/2 bg-navy relative items-center justify-center overflow-hidden">
            <!-- Imagen de fondo de reloj de lujo -->
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1523170335258-f5ed11844a49?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80')] bg-cover bg-center opacity-30 mix-blend-luminosity hover:opacity-40 transition-opacity duration-700"></div>
            <!-- Gradiente para oscurecer y dar tono navy -->
            <div class="absolute inset-0 bg-gradient-to-tr from-navy-dark/90 to-navy-light/40"></div>
            
            <div class="relative z-10 p-12 text-center flex flex-col items-center justify-center h-full">
                <div class="w-16 h-16 border border-gold/30 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-hourglass text-2xl text-gold"></i>
                </div>
                <h2 class="text-4xl font-serif font-bold text-white mb-4 tracking-wide">Velorium</h2>
                <div class="w-12 h-0.5 bg-gold mb-6"></div>
                <p class="text-slate-300 font-light text-lg italic leading-relaxed">"Donde el tiempo refleja la excelencia."</p>
            </div>
        </div>
        
        <!-- Panel Derecho (Formulario) -->
        <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-white">
            
            <div class="mb-10 text-center md:text-left">
                <h3 class="text-3xl font-serif font-bold text-navy-dark mb-2">Bienvenido de nuevo</h3>
                <p class="text-slate-500 font-light text-sm">Ingrese sus credenciales para acceder a su cuenta corporativa.</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Acceso',
                            text: '<?php echo e($error); ?>',
                            confirmButtonColor: '#d4af37',
                            background: '#ffffff',
                            color: '#0A192F'
                        });
                    });
                </script>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-xs font-semibold text-navy-light uppercase tracking-wider mb-2">Correo Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-gold transition-colors">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <input type="email" id="email" name="email" required placeholder="tu@email.com" 
                            class="pl-11 w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none transition-all bg-slate-50 focus:bg-white text-slate-800 text-sm">
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-xs font-semibold text-navy-light uppercase tracking-wider">Contraseña</label>
                        <a href="#" class="text-xs text-gold-dark hover:text-navy font-medium transition-colors">¿Olvidó su contraseña?</a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-gold transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" required placeholder="••••••••" 
                            class="pl-11 w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none transition-all bg-slate-50 focus:bg-white text-slate-800 text-sm">
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-navy hover:bg-navy-light text-white font-medium py-3.5 px-4 rounded-lg transition-all shadow-lg hover:shadow-navy/30 flex justify-center items-center gap-2 mt-4 group">
                    Iniciar Sesión 
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>
            
            <div class="mt-12 text-center text-sm">
                <p class="text-slate-500">
                    ¿No tiene cuenta? <a href="<?php echo baseUrl('registro'); ?>" class="text-navy font-bold hover:text-gold-dark transition-colors relative after:content-[''] after:absolute after:w-full after:h-[1px] after:bg-gold-dark after:-bottom-0.5 after:left-0 after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:origin-left">Solicite acceso aquí</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
