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
        $fecha_cita = !empty($_POST['fecha_cita']) ? trim($_POST['fecha_cita']) : null;
        $hora_cita = !empty($_POST['hora_cita']) && $_POST['hora_cita'] !== 'Inmediato' ? trim($_POST['hora_cita']) : null;

        if (!$phone || !$service || !$notes) throw new Exception("Faltan datos obligatorios.");

        // Recuperar edad del cliente existente
        $stmt_age = $db->prepare("SELECT age FROM client WHERE phone = :phone");
        $stmt_age->execute([':phone' => $phone]);
        $client_data = $stmt_age->fetch(PDO::FETCH_ASSOC);
        $age = $client_data ? (int)$client_data['age'] : 0;

        // 4. Insertar en service_requests
        $query_service = "INSERT INTO service_requests (client_phone, age, service_type, service_address, appointment_date, appointment_time, status, notes, service_date) 
                          VALUES (:phone, :age, :type, :address, :fecha_cita, :hora_cita, 'Pendiente', :notes, NOW())";
        
        $stmt_service = $db->prepare($query_service);
        $stmt_service->bindParam(':phone', $phone);
        $stmt_service->bindParam(':age', $age);
        $stmt_service->bindParam(':type', $service);
        $stmt_service->bindParam(':address', $address);
        $stmt_service->bindParam(':fecha_cita', $fecha_cita);
        $stmt_service->bindParam(':hora_cita', $hora_cita);
        $stmt_service->bindParam(':notes', $notes);
        
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
