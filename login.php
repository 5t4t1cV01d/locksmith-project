<?php
// ARCHIVO: login.php
// Muestra el formulario de login y maneja errores de autentificación.
require_once 'seguridad.php';

// Determinar URL de redirección
$redirect_url = 'index.php'; // Redirección por defecto (página principal)
if (isset($_GET['redirect'])) {
    $redirect_url = basename($_GET['redirect']);
}

// Redirige si ya está autenticado
if (is_logged_in()) {
    if (is_logged_in('admin')) {
        header("Location: dashboard_ver.php");
    } else {
        header("Location: " . $redirect_url);
    }
    exit();
}

$error_message = "";
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'credenciales_invalidas') {
        $error_message = "Email o contraseña incorrectos.";
    } elseif ($_GET['error'] == 'campos_vacios') {
        $error_message = "Por favor, ingresa tu email y contraseña.";
    } elseif ($_GET['error'] == 'error_db') {
        $error_message = "Error en la conexión con la base de datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LA GANZUA</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-background">

    <img src="img/full_logo_tr.svg" alt="Logo LA GANZUA" class="center-image">

    <div class="login-page-content animate-blur">
        <h1>Cerrajería Profesional</h1>
        <h2>Panel de Gestión</h2>

        <?php if ($error_message): ?>
            <div class="status-alert error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="autentificar.php" method="POST" class="login-form" novalidate>
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirect_url); ?>">
            <div class="input-wrapper">
                <label for="email">
                    <img src="img/icons/person.svg" alt="">
                </label>
                <input type="email" name="email" id="email" placeholder="Correo Electrónico">
            </div>

            <div class="input-wrapper">
                <label for="password">
                    <img src="img/icons/lock.svg" alt="">
                </label>
                <input type="password" name="password" id="password" placeholder="Contraseña">
            </div>

            <button type="submit" class="option-btn hero-btn">Entrar</button>
        </form>

    </div>
    <script src="js/login.js"></script>
</body>

</html>