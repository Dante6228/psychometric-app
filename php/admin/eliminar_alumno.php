<?php

session_start();

require_once __DIR__ . '/../conexion.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID no válido";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;
}

$id = intval($_GET['id']);

$conexion = new Conexion();
$conn = $conexion->connection();

try {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Alumno eliminado correctamente";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al eliminar alumno";
}

header("Location: ../../web/welcome/crud_alumnos.php");

exit;
