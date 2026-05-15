<?php
// ARCHIVO: php/procesar_solicitud.php
require_once '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = getDB();

    try {
        // 1. Obtener datos
        $phone = htmlspecialchars(trim($_POST['telefono'] ?? ''));
        $name = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $address = htmlspecialchars(trim($_POST['domicilio'] ?? ''));
        $service_type = htmlspecialchars(trim($_POST['tipo_servicio'] ?? ''));
        $notes = htmlspecialchars(trim($_POST['notas'] ?? ''));
        
        // Datos de la cita
        $fecha_cita = $_POST['fecha_cita'] ?? '';
        $hora_cita = $_POST['hora_cita'] ?? 'Inmediato';

        // 2. Validación básica
        if (empty($phone) || empty($service_type) || empty($notes)) {
            throw new Exception("Faltan datos obligatorios para procesar su solicitud.");
        }

        // 3. Preparar la información de la cita para las notas
        $cita_info = "";
        if ($service_type === 'Urgencias 24/7' || $hora_cita === 'Inmediato') {
            $cita_info = "🚨 URGENCIA: ATENCIÓN INMEDIATA";
        } else {
            $cita_info = "📅 CITA PROGRAMADA: $fecha_cita a las $hora_cita";
        }

        $full_notes = "$cita_info \n\n--- DETALLES DEL CLIENTE ---\nNombre: $name \nDirección: $address \n\n--- NOTAS DEL PROBLEMA ---\n$notes";

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
