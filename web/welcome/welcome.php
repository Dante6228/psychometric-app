<?php
session_start();

$validMessages = ['errTest'];
$message = (isset($_GET['message']) && in_array($_GET['message'], $validMessages)) ? $_GET['message'] : null;

$title = '';
$text = '';
$icon = '';

if ($message) {
    switch ($message) {
        case 'errTest':
            $title = 'Ya completaste el test';
            $text = 'No puedes hacer el test dos veces.';
            $icon = 'error';
            break;
    }
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../web/user/index.php?message=errPost");
    exit();
}

require_once __DIR__ . '/../../php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->connection();

$nombre_usuario = $_SESSION['nombre'] ?? 'Estudiante';
?>

<!DOCTYPE html>
<html lang="es" class="selection:bg-slate-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/output.css">
    <link rel="stylesheet" href="../../styles/general.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Test Cleaver | Bienvenido</title>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <header class="bg-blue-600 text-white shadow-md">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Test Psicométrico Cleaver</h1>
                    <div class="flex items-center space-x-4">
                        <span class="bg-white/20 rounded-full px-4 py-1 text-sm"><?php echo date('d/m/Y'); ?></span>
                        <form action="../../php/user/cerrar.php" method="post">
                            <button type="submit" class="flex items-center text-white hover:text-gray-200 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="flex-grow container mx-auto px-4 py-8 max-w-4xl">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <!-- Banner superior -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6">
                    <h2 class="text-2xl font-bold">¡Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?>!</h2>
                    <p class="mt-2 opacity-90">Estás a punto de realizar el Test de Perfilamiento Cleaver</p>
                </div>

                <!-- Sección de información -->
                <div class="p-6">
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">¿Qué es el Test Cleaver?</h3>
                        <div class="space-y-4 text-gray-700">
                            <p>El test Cleaver es una herramienta psicométrica basada en el modelo DISC que evalúa cuatro dimensiones principales de tu personalidad:</p>
                            
                            <ul class="list-disc pl-5 space-y-2">
                                <li><span class="font-medium text-red-600">Dominancia (D):</span> Cómo abordas los desafíos y problemas</li>
                                <li><span class="font-medium text-yellow-500">Influencia (I):</span> Cómo interactúas con los demás e influyes en ellos</li>
                                <li><span class="font-medium text-green-600">Estabilidad (S):</span> Cómo respondes al cambio y al ritmo del entorno</li>
                                <li><span class="font-medium text-indigo-600">Cumplimiento (C):</span> Cómo manejas las reglas y procedimientos</li>
                            </ul>
                            
                            <p>El test consiste en una serie de grupos de cuatro atributos cada uno. En cada grupo deberás seleccionar el que más se parezca a ti y el que menos se parezca.</p>
                            
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mt-4">
                                <p class="font-medium text-blue-800">Importante:</p>
                                <p>No hay respuestas correctas o incorrectas. Sé espontáneo y responde con honestidad para obtener un perfil preciso.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Instrucciones -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Instrucciones</h3>
                        <ol class="list-decimal pl-5 space-y-3 text-gray-700">
                            <li>Lee cada grupo de 4 palabras cuidadosamente</li>
                            <li>Selecciona <span class="font-medium">la palabra que mejor te describe</span> (MÁS)</li>
                            <li>Selecciona <span class="font-medium">la palabra que menos te describe</span> (MENOS)</li>
                            <li>No dejes ningún grupo sin responder</li>
                            <li>El test toma aproximadamente 15-20 minutos</li>
                        </ol>
                    </div>

                    <!-- Botón de acción -->
                    <div class="flex justify-center mt-8">
                        <a href="test.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg text-lg transition duration-300 transform hover:scale-105 shadow-md">
                            Comenzar Test Cleaver
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-100 border-t py-4 mt-8">
            <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
                <p>Este test es confidencial y los resultados serán utilizados únicamente con fines de desarrollo personal.</p>
                <p class="mt-1">© <?php echo date('Y'); ?> Sistema de Evaluación Psicométrica</p>
            </div>
        </footer>
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