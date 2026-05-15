<?php
// ARCHIVO: autentificar.php
// Usa la conexión PDO definida en conexion.php
require 'conexion.php'; 
require 'seguridad.php';

// =====================================
// Lógica de Cerrar Sesión (Logout)
// =====================================
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location: index.php?v=s"); 
    exit();
}
// =====================================


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Las validaciones de campos vacíos se manejan ahora vía JavaScript (js/login.js)
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 🚨 CORRECCIÓN 2: Obtener la conexión PDO
    $db = getDB();

    try {
        // Consulta segura para buscar el usuario por email en la tabla admin
        $query = "SELECT id, name, email, password FROM admin WHERE email = :email LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = 'admin'; // Forzamos rol admin para esta tabla

            header("Location: dashboard_ver.php?v=s");
            
        } else {
            // Falla la autenticación
            header("Location: login.php?error=credenciales_invalidas");
        }

    } catch (PDOException $e) {
        // Error de BD (conexión o consulta)
        header("Location: login.php?error=error_db");
    }
}

exit();
?>