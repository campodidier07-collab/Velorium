<?php
/**
 * Header/Navbar compartido - Migrado a Tailwind CSS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($titulo ?? 'Velorium - Donde el tiempo refleja la excelencia.'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo asset('images/favicon.svg'); ?>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            light: '#112240',
                            DEFAULT: '#0A192F',
                            dark: '#020C1B'
                        },
                        gold: {
                            light: '#F3E5AB',
                            DEFAULT: '#D4AF37',
                            dark: '#AA8C2C'
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmarAccion(event, element, mensaje) {
        event.preventDefault();
        Swal.fire({
            title: '¿Estás seguro?',
            text: mensaje || 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d4af37',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            background: '#ffffff',
            color: '#0A192F'
        }).then((result) => {
            if (result.isConfirmed) {
                if (element.tagName === 'FORM') {
                    element.submit();
                } else if (element.tagName === 'A') {
                    window.location.href = element.href;
                }
            }
        });
        return false;
    }
    </script>
    
    <style>
        /* Estilos base */
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .nav-link-underline::after {
            content: ''; position: absolute; width: 0; height: 2px; bottom: -4px; left: 50%;
            background-color: #D4AF37; transition: all 0.2s ease; transform: translateX(-50%);
        }
        .nav-link-underline:hover::after { width: 100%; }
    </style>
</head>
<body class="pt-16 bg-slate-50 text-slate-800">

<nav class="fixed top-0 w-full z-50 bg-navy text-white shadow-md border-b border-navy-light">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <!-- Brand -->
        <a href="<?php echo baseUrl(); ?>" class="text-xl font-serif font-bold text-gold flex items-center gap-2 hover:text-gold-light transition-colors">
            <i class="fas fa-hourglass"></i> Velorium
        </a>
        
        <!-- Menú -->
        <div class="flex items-center gap-6 text-sm font-medium">
            <a href="<?php echo baseUrl(''); ?>" class="text-slate-300 hover:text-gold transition-colors relative nav-link-underline">Inicio</a>
            <a href="<?php echo baseUrl('catalogo'); ?>" class="text-slate-300 hover:text-gold transition-colors relative nav-link-underline">Catálogo</a>
            
            <a href="<?php echo baseUrl('carrito'); ?>" class="text-slate-300 hover:text-gold transition-colors flex items-center gap-1 relative nav-link-underline">
                <i class="fas fa-shopping-cart"></i> Carrito
                <?php if (!empty($_SESSION['carrito'])): ?>
                    <span class="bg-gold text-navy-dark text-[10px] font-bold px-1.5 py-0.5 rounded-full absolute -top-2 -right-3"><?php echo count($_SESSION['carrito']); ?></span>
                <?php endif; ?>
            </a>
            
            <?php if (isLoggedIn()): ?>
                <a href="<?php echo baseUrl('mis-pedidos'); ?>" class="text-slate-300 hover:text-gold transition-colors relative nav-link-underline">Mis Pedidos</a>
                
                <?php if (isAdmin()): ?>
                    <a href="<?php echo baseUrl('admin/dashboard'); ?>" class="text-gold hover:text-gold-light transition-colors flex items-center gap-1">
                        <i class="fas fa-cog"></i> Admin
                    </a>
                <?php endif; ?>
                
                <!-- Dropdown Usuario -->
                <div class="relative group ml-2">
                    <button class="flex items-center gap-1 text-slate-300 hover:text-gold transition-colors focus:outline-none">
                        <i class="fas fa-user-circle"></i> <?php echo e($_SESSION['nombre']); ?>
                        <i class="fas fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <!-- Menú desplegable -->
                    <div class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="<?php echo baseUrl('perfil'); ?>" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-navy transition-colors rounded-t-md border-b border-slate-100">Mi Perfil</a>
                        <a href="<?php echo baseUrl('logout'); ?>" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-red-600 transition-colors rounded-b-md">Cerrar Sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-3 ml-4">
                    <a href="<?php echo baseUrl('login'); ?>" class="text-slate-300 hover:text-white transition-colors">Entrar</a>
                    <a href="<?php echo baseUrl('registro'); ?>" class="bg-gold text-navy-dark hover:bg-gold-light font-semibold py-2 px-5 rounded transition-colors shadow-sm">Registrarse</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Flash Messages con SweetAlert2 -->
<?php 
$flash = getFlash();
if ($flash): 
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: '<?php echo $flash['type']; ?>', // 'success' o 'error'
                title: '<?php echo $flash['type'] === 'success' ? '¡Éxito!' : 'Atención'; ?>',
                text: '<?php echo e($flash['message']); ?>',
                confirmButtonColor: '#d4af37',
                background: '#ffffff',
                color: '#0A192F'
            });
        });
    </script>
<?php endif; ?>

<div class="container mx-auto px-4 py-6">

