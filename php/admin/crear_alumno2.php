<?php

session_start();

// Verificar autenticación y permisos
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'administrativo') {
    $_SESSION['error'] = "Acceso no autorizado";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;
}

require_once __DIR__ . '/../conexion.php';

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Método no permitido";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;
}

// Validar y sanitizar entradas
$nombre = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8') : '';
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validaciones básicas
if (empty($nombre) || !$email || empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = "Todos los campos son requeridos";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Las contraseñas no coinciden";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;
}

$conexion = new Conexion();
$conn = $conexion->connection();

try {
    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = "El email ya está registrado";
        header("Location: ../../web/welcome/crud_alumnos.php");
        exit;
    }

    // Hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Crear el usuario
    $stmt = $conn->prepare("
        INSERT INTO usuarios (nombre, email, contraseña_hash, tipo)
        VALUES (?, ?, ?, 'alumno')
    ");
    $stmt->execute([$nombre, $email, $passwordHash]);

    $_SESSION['success'] = "Cuenta de alumno creada exitosamente";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;

} catch (PDOException $e) {
    error_log("Error al crear alumno: " . $e->getMessage());
    $_SESSION['error'] = "Error al crear la cuenta. Por favor intente nuevamente.";
    header("Location: ../../web/welcome/crud_alumnos.php");
    exit;
}
