<?php
// ARCHIVO: includes/admin_bar.php
// Barra lateral flotante (Login/Logout).

if ($current_page != 'login'): 
    $is_logged = is_logged_in();
?>
<div class="admin-bar">
    <div class="log">
        <?php if ($is_logged): ?>
            <a href="autentificar.php?logout=true" title="Cerrar Sesión"> 
                <img src="img/icons/exit.svg" alt="Salir">
            </a>
        <?php else: ?>
            <a href="login.php" title="Iniciar Sesión">
                <img src="img/icons/login.svg" alt="Admin">
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>