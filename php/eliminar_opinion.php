<?php
// ARCHIVO: php/eliminar_opinion.php
require_once '../conexion.php';
require_once '../seguridad.php';

// 1. Seguridad: Solo el administrador puede borrar
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$db = getDB();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // 2. Ejecutar el borrado
        $query = "DELETE FROM reviews WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            // Éxito: Regresar a la sección de opiniones
            header("Location: ../servicios.php#opiniones");
        } else {
            throw new Exception("No se pudo eliminar la opinión.");
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../servicios.php");
}
?>
