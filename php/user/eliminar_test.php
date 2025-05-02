<?php

session_start();

// Verificar autenticación y permisos
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'administrativo') {
    header("HTTP/1.1 403 Forbidden");
    exit("Acceso denegado");
}

require_once __DIR__ . '/../conexion.php';

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    exit("Método no permitido");
}

// Validar y sanitizar entrada
$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
if (!$id_usuario || $id_usuario <= 0) {
    header("HTTP/1.1 400 Bad Request");
    exit("ID de usuario inválido");
}

$conexion = new Conexion();
$conn = $conexion->connection();

try {
    // Iniciar transacción
    $conn->beginTransaction();

    // 1. Eliminar respuestas del usuario
    $stmt = $conn->prepare("DELETE FROM respuestas WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);

    // 2. Eliminar resultados DISC
    $stmt = $conn->prepare("DELETE FROM resultados_disc WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);

    $conn->commit();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Test eliminado correctamente',
        'redirect' => 'dashboard.php?success=deleted'
    ]);

} catch (PDOException $e) {
    $conn->rollBack();

    error_log("Error al eliminar test: " . $e->getMessage());
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar el test: ' . $e->getMessage()
    ]);
    exit;
}
