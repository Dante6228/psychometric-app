<?php

session_start();

require_once __DIR__ . '/../../php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE correo = :correo";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['correo' => $correo]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($password, $usuario['contraseña'])) {

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['contraseña'] = $usuario['contraseña'];

            header("Location: ../../web/welcome/dashboard.php?message=login");
            exit();
        } else {
            header("Location: ../../web/user/index.php?message=errPss");
            exit();
        }
    } else {
        header("Location: ../../web/user/index.php?message=errEmail");
        exit();
    }
} else {
    header("Location: ../../web/user/index.php?message=errPost");
    exit();
}
