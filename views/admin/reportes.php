<?php 
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
        <!-- Tarjetas KPI -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Ingresos Totales (30 días)</p>
                    <h3 class="text-3xl font-serif font-bold text-navy-dark"><?php echo formatPrice($totalIngresos); ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Pedidos Totales</p>
                    <h3 class="text-3xl font-serif font-bold text-navy-dark"><?php echo number_format($totalPedidos); ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Promedio por Pedido</p>
                    <h3 class="text-3xl font-serif font-bold text-navy-dark">
                        <?php echo $totalPedidos > 0 ? formatPrice($totalIngresos / $totalPedidos) : formatPrice(0); ?>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-gold-light/20 text-gold-dark flex items-center justify-center text-xl">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Gráfico de Ventas (Líneas) -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-serif font-bold text-navy-dark">Evolución de Ingresos (Últimos 30 días)</h3>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="chartIngresos"></canvas>
                </div>
            </div>

            <!-- Gráfico de Categorías (Pastel) -->
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-serif font-bold text-navy-dark">Ventas por Categoría</h3>
                </div>
                <div class="relative h-64 w-full flex items-center justify-center">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla Top Vendidos -->
        <div class="bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-lg font-serif font-bold text-navy-dark">Top 5 Relojes Más Vendidos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-4 px-6 bg-white text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">Producto</th>
                            <th class="py-4 px-6 bg-white text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 text-center">Categoría</th>
                            <th class="py-4 px-6 bg-white text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 text-center">Unidades Vendidas</th>
                            <th class="py-4 px-6 bg-white text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 text-center">Stock Actual</th>
                            <th class="py-4 px-6 bg-white text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 text-right">Precio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($topVendidos)): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">No hay datos de ventas disponibles.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($topVendidos as $reloj): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 flex-shrink-0">
                                            <?php if($reloj['imagen']): ?>
                                                <img src="<?php echo asset('images/' . e($reloj['imagen'])); ?>" alt="<?php echo e($reloj['nombre']); ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <i class="fas fa-clock text-slate-300 text-xl w-full h-full flex items-center justify-center"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-navy-dark text-sm"><?php echo e($reloj['nombre']); ?></p>
                                            <p class="text-xs text-slate-500"><?php echo e($reloj['marca']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 capitalize">
                                        <?php echo e($reloj['categoria']); ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="font-bold text-emerald-600"><?php echo $reloj['cantidad_vendida']; ?></span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="font-medium <?php echo $reloj['cantidad_disponible'] > 5 ? 'text-slate-600' : 'text-red-500'; ?>">
                                        <?php echo $reloj['cantidad_disponible']; ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-navy-dark">
                                    <?php echo formatPrice($reloj['precio']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Configuración general de Chart.js
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    
    // Datos PHP a JS
    const chartDias = <?php echo json_encode($chartDias); ?>;
    const chartIngresos = <?php echo json_encode($chartIngresos); ?>;
    
    const chartCategorias = <?php echo json_encode($chartCategorias); ?>;
    const chartVentasCat = <?php echo json_encode($chartVentasCat); ?>;
    
    // 1. Gráfico de Evolución de Ingresos (Líneas)
    if (document.getElementById('chartIngresos')) {
        const ctxIngresos = document.getElementById('chartIngresos').getContext('2d');
        
        // Gradiente para el área bajo la línea
        let gradient = ctxIngresos.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(197, 168, 128, 0.5)'); // gold-light semi-transparente
        gradient.addColorStop(1, 'rgba(197, 168, 128, 0.0)');
        
        new Chart(ctxIngresos, {
            type: 'line',
            data: {
                labels: chartDias,
                datasets: [{
                    label: 'Ingresos ($)',
                    data: chartIngresos,
                    borderColor: '#C5A880', // gold-light
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#C5A880',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Suavizar curva
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        padding: 12,
                        titleFont: { size: 13, family: "'Inter', sans-serif" },
                        bodyFont: { size: 14, weight: 'bold' },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed.y;
                                return '$' + value.toLocaleString('en-US', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Categorías (Pastel/Doughnut)
    if (document.getElementById('chartCategorias')) {
        const ctxCategorias = document.getElementById('chartCategorias').getContext('2d');
        
        new Chart(ctxCategorias, {
            type: 'doughnut',
            data: {
                labels: chartCategorias,
                datasets: [{
                    data: chartVentasCat,
                    backgroundColor: [
                        '#1E293B', // navy-dark
                        '#C5A880', // gold-light
                        '#94a3b8'  // slate-400
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed + ' unidades';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>
