<?php
// ARCHIVO: php/get_client.php
ob_start();
require_once '../conexion.php';
require_once '../seguridad.php';
ob_clean(); 

header('Content-Type: application/json');

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
            echo json_encode(['success' => false, 'msg' => 'No encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Error de BD']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Teléfono vacío']);
}
exit();
?>
