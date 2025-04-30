<?php

session_start();

require_once __DIR__ . '/../../php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['email' => $correo]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($password, $usuario['contraseña_hash'])) {

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['tipo'] = $usuario['tipo'];
            $_SESSION['email'] = $usuario['email'];

            if ($usuario['tipo'] === 'administrativo') {
                header("Location: ../../web/welcome/dashboard.php");
                exit();
            } else {
                header("Location: ../../web/welcome/welcome.php");
                exit();
            }

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
