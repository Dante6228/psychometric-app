<?php

session_start();

require_once __DIR__ . '/../conexion.php';

$id = $_POST['id_usuario'] ?? null;
$nombre = trim($_POST['nombre'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? null;
$confirm = $_POST['confirm_password'] ?? null;

if (!$id || !$nombre || !$email) {
    $_SESSION['error'] = "Datos incompletos";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;
}

$conexion = new Conexion();
$conn = $conexion->connection();

try {
    if ($password && $password === $confirm) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, contraseña_hash = ? WHERE id_usuario = ?");
        $stmt->execute([$nombre, $email, $hash, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id_usuario = ?");
        $stmt->execute([$nombre, $email, $id]);
    }

    $_SESSION['success'] = "Alumno actualizado correctamente";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar alumno";
}

header("Location: ../../web/welcome/crud_alumnos.php");

exit;
