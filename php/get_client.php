<?php
// ARCHIVO: php/get_client.php
require '../conexion.php';
require '../seguridad.php';

header('Content-Type: application/json');

// header('Content-Type: application/json'); // Ya está arriba
// Eliminamos restricción de admin para permitir autocompletado al cliente público

$phone = $_GET['phone'] ?? '';

if (!empty($phone)) {
    $db = getDB();
    try {
        $query = "SELECT name, address FROM client WHERE phone = :phone LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            echo json_encode(['success' => true, 'client' => $client]);
        } else {
            echo json_encode(['success' => false]);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error de BD']);
    }
} else {
    echo json_encode(['error' => 'Teléfono vacío']);
}
?>
