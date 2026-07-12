<?php
// Sidebar Admin Panel
?>
<!-- SweetAlert2 CDN para el Panel Administrativo -->
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

<nav class="sidebar" id="sidebar">
    <div class="p-3">
        <div class="text-center mb-4">
            <h4 class="text-white mb-3">
                <i class="fas fa-clock me-2"></i>Admin Panel
            </h4>
            <div class="text-white">
                <div class="bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-user-shield fa-2x"></i>
                </div>
                <p class="mb-0 mt-2 fw-bold"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Admin'); ?></p>
                <small class="text-light opacity-75">Administrador</small>
            </div>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'dashboard.php') echo ' active'; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'productos.php') echo ' active'; ?>" href="productos.php">
                    <i class="fas fa-clock me-2"></i>Productos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'pedidos.php') echo ' active'; ?>" href="pedidos.php">
                    <i class="fas fa-shopping-cart me-2"></i>Pedidos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'clientes.php') echo ' active'; ?>" href="clientes.php">
                    <i class="fas fa-users me-2"></i>Clientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'inventario.php') echo ' active'; ?>" href="inventario.php">
                    <i class="fas fa-boxes me-2"></i>Inventario
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'metodos_pago.php') echo ' active'; ?>" href="metodos_pago.php">
                    <i class="fas fa-credit-card me-2"></i>Métodos de Pago
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php if(basename($_SERVER['PHP_SELF']) == 'reportes.php') echo ' active'; ?>" href="reportes.php">
                    <i class="fas fa-chart-line me-2"></i>Reportes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php" onclick="return confirmarAccion(event, this, '¿Está seguro de cerrar sesión?')">
                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
</nav> 