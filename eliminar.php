<?php
// ARCHIVO: eliminar.php
require 'conexion.php';
require 'seguridad.php';

// Solo administradores pueden eliminar
require_admin_login(); 

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: dashboard_ver.php");
    exit();
}

$id = $_GET['id'];
$type = $_GET['type'];
$db = getDB();

try {
    if ($type === 'reserva') {
        $query = "DELETE FROM reservas WHERE id = :id";
        $success_msg = "Reserva #$id eliminada correctamente.";
    } else {
        throw new Exception("Tipo de registro no válido para eliminar.");
    }
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute() && $stmt->rowCount() > 0) {
        header("Location: dashboard_ver.php?status_msg=" . urlencode($success_msg) . "&status_action=eliminada");
    } else {
        throw new Exception("El registro no existe o no se pudo eliminar.");
    }

} catch (Exception $e) {
    header("Location: dashboard_ver.php?status_msg=" . urlencode("Error: " . $e->getMessage()) . "&status_action=error");
}

exit();
?>