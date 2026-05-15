<?php
require_once 'seguridad.php';

if (!isset($current_page)) {
    $current_page = 'index';
}
$is_dashboard = (strpos($current_page, 'dashboard') !== false);

// Si es Dashboard (admin), el logo va a dashboard_ver.php. Si no, va a index.php.
$home_url = $is_dashboard ? 'dashboard_ver.php' : 'index.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LA GANZUA - Cerrajería Profesional | <?php echo strtoupper(str_replace(['s_', '_'], ' ', $current_page)); ?>
    </title>
    <link rel="stylesheet" href="css/style.css">
    <?php if ($current_page == 'index'): ?>
        <script src="js/index.js"></script>
    <?php endif; ?>
</head>

<body <?php echo ($current_page == 'login') ? 'class="login-background"' : ''; ?>>

    <?php if ($current_page != 'login'): ?>
        <header>
            <nav class="main-nav">
                <div class="logo">
                    <img src="img/full_logo_tr.svg" alt="Logo LA GANZUA">
                </div>

                <?php if (!$is_dashboard): ?>
                    <ul class="nav-links">
                        <li class="dropdown">
                            <a href="index.php"
                                class="<?php echo ($current_page == 'index') ? 'active-link' : 'dropbtn'; ?>">INICIO</a>
                            <div class="dropdown-content">
                                <a href="index.php#about-us">Nosotros</a>
                                <a href="index.php#location">Ubicación</a>
                            </div>
                        </li>
                        <li class="dropdown">
                            <a href="servicios.php"
                                class="<?php echo ($current_page == 'servicios') ? 'active-link' : 'dropbtn'; ?>">SERVICIOS</a>
                            <div class="dropdown-content">
                                <a href="servicios.php#precios">Precios</a>
                                <a href="servicios.php#opiniones">Opiniones</a>
                                <a href="servicios.php#proceso">Nuestro Proceso</a>
                            </div>
                        </li>
                        <li><a href="solicitar.php"
                                class="<?php echo ($current_page == 'solicitar') ? 'active-link' : ''; ?>">SOLICITAR</a></li>
                        <li><a href="contacto.php"
                                class="<?php echo ($current_page == 'contacto') ? 'active-link' : ''; ?>">CONTACTO</a></li>
                    </ul>
                <?php else: ?>
                    <ul class="nav-links">
                        <li><a href="dashboard_ver.php"
                                class="<?php echo ($current_page == 'dashboard_ver') ? 'active-link' : ''; ?>">GESTIÓN DE
                                SERVICIOS</a></li>
                        <li><a href="dashboard_crear.php"
                                class="<?php echo ($current_page == 'dashboard_crear') ? 'active-link' : ''; ?>">NUEVA ORDEN DE TRABAJO</a></li>
                    </ul>
                <?php endif; ?>

                <?php if (is_logged_in('admin')): ?>
                    <div class="view-switcher">
                        <?php if ($is_dashboard): ?>
                            <a href="index.php?v=s" class="switch-btn client-view" title="Ver Vista Cliente">
                                <img src="img/icons/visibility_off.svg" alt="" class="switch-icon icon-default">
                                <img src="img/icons/visibility.svg" alt="" class="switch-icon icon-hover">
                            </a>
                        <?php else: ?>
                            <a href="dashboard_ver.php?v=s" class="switch-btn admin-view" title="Volver al Panel Admin">
                                <img src="img/icons/visibility.svg" alt="" class="switch-icon icon-default">
                                <img src="img/icons/visibility_off.svg" alt="" class="switch-icon icon-hover">
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </nav>

            <?php include 'includes/admin_bar.php'; ?>

        </header>
    <?php endif; ?>

    <main class="main-content <?php echo isset($_GET['v']) ? 'animate-blur' : 'animate-slide'; ?>">