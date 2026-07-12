<?php 
$titulo = 'Mi Perfil - Velorium';
require_once 'views/layouts/header.php';
?>

<div class="bg-[#f8fafc] min-h-screen pb-16">
    <div class="bg-navy-dark pt-12 pb-20 relative overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1549970924-4f24f0a202c4?auto=format&fit=crop&q=80&w=2000" alt="Fondo" class="w-full h-full object-cover opacity-10 mix-blend-luminosity">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-navy-dark"></div>
        </div>
        
        <div class="container mx-auto px-4 max-w-6xl relative z-10">
            <div class="flex items-center mb-3">
                <span class="w-8 h-[1px] bg-gold inline-block mr-4"></span>
                <span class="text-gold text-xs tracking-[0.2em] uppercase font-bold">Cuenta Personal</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-white mb-2">
                Mi Perfil
            </h1>
            <p class="text-slate-400 font-light text-sm md:text-base max-w-xl">
                Gestione su información personal y detalles de contacto.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 max-w-6xl -mt-8 relative z-20">
        <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 md:p-12">
            <div class="flex flex-col md:flex-row gap-12">
                <div class="md:w-1/3 flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full bg-slate-100 flex items-center justify-center text-5xl font-serif text-navy shadow-inner mb-6 border-4 border-white shadow-[0_0_15px_rgba(0,0,0,0.1)]">
                        <?php echo strtoupper(substr($usuario_data['nombre'], 0, 1)); ?>
                    </div>
                    <h2 class="text-2xl font-bold text-navy-dark mb-1"><?php echo e($usuario_data['nombre']); ?></h2>
                    <p class="text-slate-500 mb-6 text-sm"><?php echo e($usuario_data['email']); ?></p>
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide bg-gold/10 text-gold-dark border border-gold/20">
                        <?php echo e($usuario_data['rol']); ?>
                    </span>
                </div>
                
                <div class="md:w-2/3">
                    <h3 class="text-xl font-serif font-bold text-navy-dark mb-6 border-b border-slate-100 pb-4">Información de la Cuenta</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nombre Completo</label>
                            <div class="p-4 bg-slate-50 rounded-lg border border-slate-100 text-navy font-medium text-sm">
                                <?php echo e($usuario_data['nombre']); ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Correo Electrónico</label>
                            <div class="p-4 bg-slate-50 rounded-lg border border-slate-100 text-navy font-medium text-sm">
                                <?php echo e($usuario_data['email']); ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Fecha de Registro</label>
                            <div class="p-4 bg-slate-50 rounded-lg border border-slate-100 text-navy font-medium text-sm">
                                <?php echo date('d M, Y', strtotime($usuario_data['creado_en'])); ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Estado</label>
                            <div class="p-4 bg-[#f0fdf4] rounded-lg border border-[#bbf7d0] text-[#166534] font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-check-circle"></i> Cuenta Activa
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-[#f8fafc] border border-slate-200 rounded-lg p-5 flex items-start gap-4">
                        <i class="fas fa-info-circle text-navy mt-1"></i>
                        <div>
                            <h4 class="text-sm font-bold text-navy-dark mb-1">Actualización de datos</h4>
                            <p class="text-sm text-slate-600">Para modificar tu información personal o cambiar tu contraseña, por favor contacta a nuestro equipo de soporte o asiste a nuestra boutique.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
