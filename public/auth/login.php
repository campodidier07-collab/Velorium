<?php 
require_once 'config/autoload.php';
require_once 'views/layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo e($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo baseUrl('index.php?route=login'); ?>" class="form-auth">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
        
        <p class="auth-footer">
            ¿No tienes cuenta? <a href="<?php echo baseUrl('index.php?route=registro'); ?>">Regístrate aquí</a>
        </p>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
