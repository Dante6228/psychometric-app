<?php

session_start();

$validMessages = ['errPss', 'errEmail', 'errPost', 'login', 'errUser', 'logout'];
$message = (isset($_GET['message']) && in_array($_GET['message'], $validMessages)) ? $_GET['message'] : null;

$title = '';
$text = '';
$icon = '';

if ($message) {
    switch ($message) {
        case 'errPss':
            $title = 'Contraseña incorrecta';
            $text = 'Verifica que la hayas escrito bien.';
            $icon = 'error';
            break;
        case 'errEmail':
            $title = 'Correo no encontrado';
            $text = 'No hay una cuenta con ese correo.';
            $icon = 'warning';
            break;
        case 'errPost':
            $title = 'Acceso no permitido';
            $text = 'Debes iniciar sesión para acceder.';
            $icon = 'warning';
            break;
        case 'errUser':
            $title = 'Acceso no permitido';
            $text = 'No tienes los permisos necesarios para acceder.';
            $icon = 'warning';
            break;
        case 'logout':
            $title = 'Sesión cerrada';
            $text = 'Has cerrado sesión correctamente.';
            $icon = 'success';
            break;
        default:
            $title = 'Error desconocido';
            $text = 'Ocurrió un error inesperado.';
            $icon = 'error';
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="es" class="selection:bg-slate-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/output.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Iniciar sesión</title>
</head>
<body class="bg-soft-white flex flex-col items-center justify-center min-h-screen">
        <?php

            if (isset($_SESSION['error_test'])) {
                echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 animate-fade-in">';
                echo '<p>'.htmlspecialchars($_SESSION['error_test']).'</p>';
                echo '</div>';
                unset($_SESSION['error_test']);
            }

        ?>
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
        <h1 class="text-3xl font-bold text-center mb-6">Iniciar sesión</h1>


        <form action="../../php/user/procesar.php" method="POST" class="flex flex-col gap-4">
            <div>
                <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                <input type="email" id="email" name="email" required class="mt-1 border border-soft-grey rounded-md p-2 w-full focus:outline-none focus:border-slate-950 focus:border-1" placeholder="tucorreo@email.com">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Contraseña</label>
                <input type="password" id="password" name="password" required class="mt-1 border border-soft-grey rounded-md p-2 w-full focus:outline-none focus:border-slate-950 focus:border-1" placeholder="********">
            </div>
            <button type="submit" class="bg-soft-blue text-white p-2 rounded-md hover:bg-blue-600 transition-all duration-200">Entrar</button>
        </form>
    </div>

    <?php if ($message): ?>
        <script>
            Swal.fire({
                title: '<?= htmlspecialchars($title) ?>',
                text: '<?= htmlspecialchars($text) ?>',
                icon: '<?= htmlspecialchars($icon) ?>',
                confirmButtonColor: '#10B981',
                scrollbarPadding: false
            });
        </script>
    <?php endif; ?>

</body>
</html>
