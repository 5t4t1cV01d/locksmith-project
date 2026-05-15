<?php
// ARCHIVO: php/procesar_solicitud.php
require_once '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = getDB();

    try {
        // 1. Obtener datos
        $phone = trim($_POST['telefono'] ?? '');
        $name = trim($_POST['nombre'] ?? '');
        $address = trim($_POST['domicilio'] ?? '');
        $service = $_POST['tipo_servicio'] ?? '';
        $notes = trim($_POST['notas'] ?? '');
        $hora = $_POST['hora_cita'] ?? 'Inmediato';

        if (!$phone || !$service || !$notes) throw new Exception("Faltan datos obligatorios.");

        $cita = ($service === 'Urgencias 24/7' || $hora === 'Inmediato') ? "🚨 URGENCIA: INMEDIATA" : "📅 CITA: {$_POST['fecha_cita']} a las $hora";
        $full_notes = "$cita\n\n--- CLIENTE ---\n$name ($address)\n\n--- NOTAS ---\n$notes";

        // 4. Insertar en service_requests (Tabla unificada)
        $query_service = "INSERT INTO service_requests (client_phone, service_type, service_address, status, notes, service_date) 
                          VALUES (:phone, :type, :address, 'Pendiente', :notes, NOW())";
        
        $stmt_service = $db->prepare($query_service);
        $stmt_service->bindParam(':phone', $phone);
        $stmt_service->bindParam(':type', $service_type);
        $stmt_service->bindParam(':address', $address);
        $stmt_service->bindParam(':notes', $full_notes);
        
        if ($stmt_service->execute()) {
            header("Location: ../solicitar.php?status=success");
        } else {
            throw new Exception("No se pudo registrar la solicitud.");
        }

    } catch (Exception $e) {
        header("Location: ../solicitar.php?status=error&msg=" . urlencode($e->getMessage()));
    }
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
?>
