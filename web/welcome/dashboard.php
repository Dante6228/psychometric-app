<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../web/user/index.php?message=errPost");
    exit();
}

if ($_SESSION['tipo'] !== 'administrativo') {
    header("Location: ../../web/user/index.php?message=errUser");
    exit();
}

require_once __DIR__ . '/../../php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->connection();

?>

<!DOCTYPE html>
<html lang="es" class="selection:bg-slate-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/output.css">
    <link rel="stylesheet" href="../../styles/general.css">
    <title>Dashboard</title>
</head>
<body>
    <h1>¡Bienvenido <?php echo $_SESSION['nombre']?>!</h1>
</body>
</html>
