<?php 
require_once 'config/autoload.php';
require_once 'views/layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2><i class="fas fa-user-plus"></i> Registrarse</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo e($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo e($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo baseUrl('index.php?route=registro'); ?>" class="form-auth">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••" minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••" minlength="6">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
        </form>
        
        <p class="auth-footer">
            ¿Ya tienes cuenta? <a href="<?php echo baseUrl('index.php?route=login'); ?>">Inicia sesión aquí</a>
        </p>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
