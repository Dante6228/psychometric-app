<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/output.css">
    <title>Index</title>
</head>
<body class="bg-gray-200 text-gray-900 font-sans flex flex-col items-center justify-center gap-6 min-h-screen p-4">

    <main class="flex flex-col items-center justify-center gap-8 w-full max-w-3xl">
        <h1 class="font-bold text-4xl text-center">Index de prueba de la aplicación PHP</h1>

        <div class="conexion">
            <?php
                require_once __DIR__ . '/php/conexion.php';
                try {
                    $conexion = new Conexion();
                    $conexion->connection();
                    echo "<p class='text-green-600 font-medium'>✔ Conexión exitosa a la base de datos</p>";
                } catch (Exception $e) {
                    echo "<p class='text-red-600 font-medium'>✖ Error al conectar: " . $e->getMessage() . "</p>";
                }
            ?>
        </div>

        <section class="bg-white p-6 rounded-xl shadow-md w-full">
            <h2 class="font-semibold text-xl mb-2">Instalar Tailwind:</h2>
            <code class="bg-gray-100 p-2 block rounded text-sm">npm install -D tailwindcss@3</code>
        </section>

        <section class="bg-white p-6 rounded-xl shadow-md w-full">
            <h2 class="font-semibold text-xl mb-2">Correr Tailwind:</h2>
            <code class="bg-gray-100 p-2 block rounded text-sm mb-2">npx tailwindcss -i ./styles/input.css -o ./styles/output.css --watch</code>
        </section>
    </main>
    
</body>
</html>
