<?php
// ARCHIVO: seguridad.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in($role = null) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    if ($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    return true;
}

function require_admin_login() {
    if (!is_logged_in('admin')) {
        header("Location: login.php");
        exit();
    }
}

function require_user_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}
?>