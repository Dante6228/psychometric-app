<?php

session_start();

require_once __DIR__ . '/../conexion.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID no válido";
    header("Location: ../../web/welcome/crud_admins.php");
    exit;
}

$id = intval($_GET['id']);

$conexion = new Conexion();
$conn = $conexion->connection();

try {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ? AND tipo = 'administrativo'");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Administrador eliminado correctamente";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al eliminar administrador";
}

header("Location: ../../web/welcome/crud_admins.php");

exit;
