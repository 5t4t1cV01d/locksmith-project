<?php
// ARCHIVO: php/guardar_opinion.php
require_once '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = getDB();

    try {
        // 1. Capturar datos del formulario
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = htmlspecialchars(trim($_POST['comentario'] ?? ''));
        $location = htmlspecialchars(trim($_POST['ubicacion'] ?? 'Mérida'));
        $is_anonymous = isset($_POST['anonimo']) ? 1 : 0;
        
        $display_name = ($is_anonymous || empty($name = trim($_POST['nombre'] ?? ''))) ? "Cliente Anónimo" : htmlspecialchars($name);

        // 2. Validación mínima
        if ($rating < 1 || $rating > 5 || empty($comment)) {
            throw new Exception("Por favor, califique el servicio y escriba un comentario.");
        }

        // 3. Insertar en la tabla reviews (Estructura simplificada sin moderación)
        $query = "INSERT INTO reviews (rating, comment, client_name, is_anonymous, service_location, created_at) 
                  VALUES (:rating, :comment, :name, :anon, :location, NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':name', $display_name);
        $stmt->bindParam(':anon', $is_anonymous);
        $stmt->bindParam(':location', $location);

        if ($stmt->execute()) {
            // Éxito: Redirigir con mensaje positivo
            header("Location: ../contacto.php?status=success_opinion");
        } else {
            throw new Exception("No pudimos guardar tu opinión en este momento.");
        }

    } catch (Exception $e) {
        header("Location: ../contacto.php?status=error&msg=" . urlencode($e->getMessage()));
    }
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
?>
