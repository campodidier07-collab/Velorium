<?php 
$titulo = '404 - Página no encontrada';
require_once 'views/layouts/header.php';
?>

<section class="error-container">
    <div class="error-content">
        <h1>404</h1>
        <p>Página no encontrada</p>
        <p class="error-message">Parece que la página que buscas no existe.</p>
        
        <a href="<?php echo baseUrl(); ?>" class="btn btn-primary">
            <i class="fas fa-home"></i> Volver al Inicio
        </a>
    </div>
</section>

<?php require_once 'views/layouts/footer.php'; ?>
