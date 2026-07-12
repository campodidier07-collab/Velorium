<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
require_once '../models/Pedido.php';
require_once '../models/Reloj.php';

$database = new Database();
$db = $database->getConnection();

$pedido = new Pedido($db);
$reloj = new Reloj($db);

$periodo = isset($_GET['periodo']) ? (int)$_GET['periodo'] : 30;
$horas = $periodo * 24;

// Obtener datos para gráficos
$ventasPorDia = [];
$ventasPorCategoria = [];
$topVendidos = [];
$totalIngresos = 0;
$totalPedidos = 0;

if ($db) {
    try {
        $ventasPorDia = $pedido->obtenerVentasPorDia($periodo);
        $ventasPorCategoria = $pedido->obtenerVentasPorCategoria($periodo);
        $topVendidos = $reloj->obtenerTopVendidos(5, $periodo);
        $totalIngresos = $pedido->obtenerIngresosUltimos($horas);
        // Usar obtenerVentasUltimas para contar pedidos en ese periodo
        $totalPedidos = $pedido->obtenerVentasUltimas($horas);
    } catch (Exception $e) {
        error_log("Error en reportes: " . $e->getMessage());
    }
}

// Preparar para JavaScript
$chartDias = [];
$chartIngresos = [];
foreach ($ventasPorDia as $v) {
    $chartDias[] = $v['fecha'];
    $chartIngresos[] = $v['ingresos'];
}

$chartCategorias = [];
$chartVentasCat = [];
foreach ($ventasPorCategoria as $v) {
    $chartCategorias[] = ucfirst($v['categoria']);
    $chartVentasCat[] = $v['total_vendidos'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Velorium Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* Estilos específicos para la exportación a PDF */
        .html2pdf__page-break {
            page-break-before: always !important;
        }
        #areaImprimir tr, #areaImprimir .card, #areaImprimir .stat-card {
            page-break-inside: avoid !important;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Hero Header -->
        <div class="p-4 p-md-5 mb-4 rounded-4 position-relative overflow-hidden shadow-lg" style="background-color: var(--navy-dark); color: white;">
            <!-- Decoración de fondo -->
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('https://images.unsplash.com/photo-1547996160-81dfa63595aa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center; opacity: 0.08; mix-blend-mode: luminosity;"></div>
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,0.8) 100%);"></div>
            
            <div class="position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <span style="width: 30px; height: 1px; background-color: var(--gold); display: inline-block; margin-right: 15px;"></span>
                        <span style="color: var(--gold); font-size: 0.65rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 800;">Panel de Control Administrativo</span>
                    </div>
                    <h1 class="display-5 mb-1" style="font-family: 'Playfair Display', serif; font-weight: 800; color: white;">Reportes y Estadísticas</h1>
                    <p class="mb-0" style="color: #94a3b8; font-weight: 300; font-size: 0.95rem;">Visualización de datos y rendimiento de la tienda.</p>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <form method="GET" action="reportes.php" class="m-0">
                        <select name="periodo" class="form-select text-white bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-25" style="border-radius: 8px;" onchange="this.form.submit()">
                            <option value="1" class="text-dark" <?php echo $periodo == 1 ? 'selected' : ''; ?>>Hoy</option>
                            <option value="7" class="text-dark" <?php echo $periodo == 7 ? 'selected' : ''; ?>>Últimos 7 días</option>
                            <option value="30" class="text-dark" <?php echo $periodo == 30 ? 'selected' : ''; ?>>Últimos 30 días</option>
                            <option value="365" class="text-dark" <?php echo $periodo == 365 ? 'selected' : ''; ?>>Este año</option>
                        </select>
                    </form>
                    <button id="btnExportar" class="btn text-white bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-25 shadow-sm px-4 py-2" style="border-radius: 8px;">
                        <i class="fas fa-file-pdf text-danger me-2"></i>Exportar PDF
                    </button>
                    <button class="btn btn-outline-light d-md-none" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Área Imprimible -->
        <div id="areaImprimir" class="p-2">
            
            <!-- Encabezado exclusivo para PDF (Oculto por defecto) -->
            <div id="pdfHeader" style="display: none; margin-bottom: 30px; text-align: center;">
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #C5A880; padding-bottom: 15px; margin-bottom: 25px; align-items: flex-end;">
                    <div style="text-align: left;">
                        <h2 style="color: #0A192F; font-family: 'Playfair Display', serif; font-weight: 800; margin: 0;">VELORIUM</h2>
                        <span style="color: #64748b; font-size: 0.85rem; letter-spacing: 2px;">SISTEMA PREMIUM DE RELOJERÍA</span>
                    </div>
                    <div style="text-align: right;">
                        <h4 style="color: #0A192F; font-family: 'Playfair Display', serif; font-weight: bold; margin: 0;">Reporte de Ventas</h4>
                        <span style="color: #64748b; font-size: 0.85rem;">Generado: <?php echo date('d/m/Y'); ?></span>
                    </div>
                </div>
            </div>

        <!-- Estadísticas Principales -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Ingresos Totales (<?php echo $periodo; ?> días)</h6>
                            <h2 class="mb-0 fw-bold">$<?php echo number_format($totalIngresos, 2); ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Pedidos Totales</h6>
                            <h2 class="mb-0 fw-bold"><?php echo number_format($totalPedidos); ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Promedio por Pedido</h6>
                            <h2 class="mb-0 fw-bold">$<?php echo $totalPedidos > 0 ? number_format($totalIngresos / $totalPedidos, 2) : '0.00'; ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <!-- Gráfico de Líneas -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="mb-0 fw-bold" style="color: var(--navy-dark);"><i class="fas fa-chart-area me-2" style="color: var(--gold);"></i>Evolución de Ingresos (Últimos <?php echo $periodo; ?> días)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div style="height: 300px; width: 100%;">
                            <canvas id="chartIngresos"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de Pastel -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="mb-0 fw-bold" style="color: var(--navy-dark);"><i class="fas fa-chart-pie me-2" style="color: var(--gold);"></i>Ventas por Categoría</h5>
                    </div>
                    <div class="card-body p-4 d-flex justify-content-center align-items-center">
                        <div style="height: 250px; width: 100%;">
                            <canvas id="chartCategorias"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla Top Vendidos -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold" style="color: var(--navy-dark);"><i class="fas fa-trophy me-2" style="color: var(--gold);"></i>Top 5 Relojes Más Vendidos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                            <tr>
                                <th class="px-4 py-3 border-0 text-secondary">Producto</th>
                                <th class="px-4 py-3 border-0 text-secondary text-center">Categoría</th>
                                <th class="px-4 py-3 border-0 text-secondary text-center">Unidades Vendidas</th>
                                <th class="px-4 py-3 border-0 text-secondary text-center">Stock Actual</th>
                                <th class="px-4 py-3 border-0 text-secondary text-end">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topVendidos)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay datos de ventas disponibles.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($topVendidos as $reloj): ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-3 overflow-hidden d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #f8f9fa; border: 1px solid #e9ecef; margin-right: 15px;">
                                                <?php if($reloj['imagen']): ?>
                                                    <img src="../assets/images/<?php echo htmlspecialchars($reloj['imagen']); ?>" alt="<?php echo htmlspecialchars($reloj['nombre']); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                <?php else: ?>
                                                    <i class="fas fa-clock text-muted"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold" style="color: var(--navy-dark);"><?php echo htmlspecialchars($reloj['nombre']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($reloj['marca']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-light text-dark border text-capitalize px-3 py-2 rounded-pill"><?php echo htmlspecialchars($reloj['categoria']); ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <h5 class="mb-0 text-success fw-bold"><?php echo $reloj['cantidad_vendida']; ?></h5>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <h5 class="mb-0 fw-bold <?php echo $reloj['cantidad_disponible'] <= 5 ? 'text-danger' : 'text-secondary'; ?>"><?php echo $reloj['cantidad_disponible']; ?></h5>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <strong style="color: var(--navy-dark);">$<?php echo number_format($reloj['precio'], 2); ?></strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        </div> <!-- Fin de área imprimible -->

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- html2pdf.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Custom Scripts -->
    <script src="main.js"></script>
    
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

        // 3. Funcionalidad de Exportar a PDF
        document.getElementById('btnExportar').addEventListener('click', function() {
            // Cambiar aspecto del botón mientras se genera
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';
            btn.disabled = true;

            const element = document.getElementById('areaImprimir');
            const pdfHeader = document.getElementById('pdfHeader');
            
            // Mostrar encabezado y preparar diseño para el PDF (forzar ancho)
            pdfHeader.style.display = 'block';
            const originalWidth = element.style.width;
            element.style.width = '1100px';
            element.style.maxWidth = '1100px';
            element.style.margin = '0 auto';
            
            // Opciones para la librería html2pdf
            const opt = {
                margin:       [15, 10, 15, 10], // top, left, bottom, right
                filename:     'Reporte_Velorium_' + new Date().toISOString().split('T')[0] + '.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Generar y descargar el PDF
            html2pdf().set(opt).from(element).save().then(function() {
                // Restaurar el botón, ocultar encabezado y volver al diseño normal
                pdfHeader.style.display = 'none';
                element.style.width = originalWidth;
                element.style.maxWidth = '';
                element.style.margin = '';
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

    });
    </script>
</body>
</html>
