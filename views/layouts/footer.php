<?php
/**
 * Footer compartido - Migrado a Tailwind CSS
 */
?>
</div><!-- End container -->

<footer class="bg-navy-dark text-slate-300 pt-16 pb-8 border-t-4 border-gold">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <!-- Sobre Nosotros -->
            <div>
                <h3 class="text-xl font-serif font-bold text-white mb-6 relative inline-block">
                    Sobre Nosotros
                    <span class="absolute -bottom-2 left-0 w-12 h-0.5 bg-gold"></span>
                </h3>
                <p class="leading-relaxed text-sm text-slate-400">
                    Velorium. Donde el tiempo refleja la excelencia. Ofrecemos las mejores marcas internacionales con garantía y servicio excepcional.
                </p>
            </div>
            
            <!-- Enlaces Rápidos -->
            <div>
                <h3 class="text-xl font-serif font-bold text-white mb-6 relative inline-block">
                    Enlaces Rápidos
                    <span class="absolute -bottom-2 left-0 w-12 h-0.5 bg-gold"></span>
                </h3>
                <ul class="space-y-3">
                    <li><a href="<?php echo baseUrl('catalogo'); ?>" class="hover:text-gold transition-colors flex items-center gap-2 text-sm"><i class="fas fa-chevron-right text-[10px] text-gold"></i> Catálogo</a></li>
                    <li><a href="<?php echo baseUrl(); ?>" class="hover:text-gold transition-colors flex items-center gap-2 text-sm"><i class="fas fa-chevron-right text-[10px] text-gold"></i> Inicio</a></li>
                    <li><a href="#" class="hover:text-gold transition-colors flex items-center gap-2 text-sm"><i class="fas fa-chevron-right text-[10px] text-gold"></i> Contáctanos</a></li>
                    <li><a href="#" class="hover:text-gold transition-colors flex items-center gap-2 text-sm"><i class="fas fa-chevron-right text-[10px] text-gold"></i> Términos y Condiciones</a></li>
                </ul>
            </div>
            
            <!-- Contacto -->
            <div>
                <h3 class="text-xl font-serif font-bold text-white mb-6 relative inline-block">
                    Contacto
                    <span class="absolute -bottom-2 left-0 w-12 h-0.5 bg-gold"></span>
                </h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="bg-navy p-2 rounded text-gold"><i class="fas fa-phone w-4 h-4 flex items-center justify-center"></i></div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Llámanos</p>
                            <p class="text-sm">+1 (555) 123-4567</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="bg-navy p-2 rounded text-gold"><i class="fas fa-envelope w-4 h-4 flex items-center justify-center"></i></div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Correo</p>
                            <p class="text-sm">info@timeandstyle.com</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="bg-navy p-2 rounded text-gold"><i class="fas fa-map-marker-alt w-4 h-4 flex items-center justify-center"></i></div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Ubicación</p>
                            <p class="text-sm">Ciudad, País</p>
                        </div>
                    </li>
                </ul>
            </div>
            
            <!-- Redes Sociales -->
            <div>
                <h3 class="text-xl font-serif font-bold text-white mb-6 relative inline-block">
                    Síguenos
                    <span class="absolute -bottom-2 left-0 w-12 h-0.5 bg-gold"></span>
                </h3>
                <p class="text-sm text-slate-400 mb-6">Mantente al tanto de nuestras últimas colecciones y ofertas exclusivas.</p>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 rounded bg-navy flex items-center justify-center text-slate-300 hover:bg-gold hover:text-navy-dark transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 rounded bg-navy flex items-center justify-center text-slate-300 hover:bg-gold hover:text-navy-dark transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-10 h-10 rounded bg-navy flex items-center justify-center text-slate-300 hover:bg-gold hover:text-navy-dark transition-colors"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
        
        <div class="border-t border-navy-light pt-8 mt-8 text-center flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-500">&copy; 2024 Velorium. Todos los derechos reservados.</p>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <span>Diseñado con</span> <i class="fas fa-heart text-red-500"></i> <span>para coleccionistas</span>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
