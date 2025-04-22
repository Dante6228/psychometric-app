<?php
session_start();

// Verificar si el usuario completó el test
if (!isset($_SESSION['test_completed']) || !$_SESSION['test_completed']) {
    header('Location: test.php');
    exit();
}

require_once __DIR__ . '/../../php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->connection();

// Obtener los resultados del usuario
$id_usuario = $_SESSION['id_usuario'];
$stmt = $conn->prepare("SELECT * FROM resultados_disc WHERE id_usuario = ? ORDER BY id_resultado DESC LIMIT 1");
$stmt->execute([$id_usuario]);
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resultado) {
    $_SESSION['error'] = "No se encontraron resultados para mostrar";
    header('Location: test.php');
    exit();
}

// Calcular porcentajes
$total = abs($resultado['d_total']) + abs($resultado['i_total']) + abs($resultado['s_total']) + abs($resultado['c_total']);
$d_percent = round(abs($resultado['d_total']) / $total * 100);
$i_percent = round(abs($resultado['i_total']) / $total * 100);
$s_percent = round(abs($resultado['s_total']) / $total * 100);
$c_percent = round(abs($resultado['c_total']) / $total * 100);

// Determinar perfil dominante
$perfiles = [
    'D' => $d_percent,
    'I' => $i_percent,
    'S' => $s_percent,
    'C' => $c_percent
];
arsort($perfiles);
$perfil_dominante = key($perfiles);

// Descripciones de los perfiles
$descripciones = [
    'D' => [
        'title' => 'Dominancia',
        'description' => 'Personas decisivas, enfocadas en resultados, directas y competitivas. Les gusta el control y los desafíos.'
    ],
    'I' => [
        'title' => 'Influencia',
        'description' => 'Personas entusiastas, sociables, persuasivas y optimistas. Disfrutan trabajando en equipo y motivando a otros.'
    ],
    'S' => [
        'title' => 'Estabilidad',
        'description' => 'Personas pacientes, predecibles, estables y buen oyentes. Valoran la seguridad y la cooperación.'
    ],
    'C' => [
        'title' => 'Cumplimiento',
        'description' => 'Personas precisas, analíticas, sistemáticas y detallistas. Siguen procedimientos y normas establecidas.'
    ]
];

// Mensaje especial si cumple con el perfil ideal
$mensaje_perfil = $resultado['perfil_ideal'] ?
    "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded'>
        <p class='font-bold'>¡Felicidades!</p>
        <p>Tu perfil cumple con las características ideales para esta posición.</p>
    </div>" :
    "";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados Test Cleaver</title>
    <link rel="stylesheet" href="../../styles/output.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 rounded-t-xl shadow">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">Resultados de tu Test Psicométrico</h1>
                    <p class="mt-2">Perfil DISC - <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></p>
                </div>
                <div class="bg-white/20 rounded-full px-4 py-1 text-sm font-medium">
                    <?php echo date('d/m/Y'); ?>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="bg-white p-6 rounded-b-xl shadow-md">
            <?php echo $mensaje_perfil; ?>

            <!-- Resumen de puntajes -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Tu Perfil Dominante:
                    <span class="text-blue-600"><?php echo $descripciones[$perfil_dominante]['title']; ?></span>
                </h2>
                <p class="text-gray-600 mb-6"><?php echo $descripciones[$perfil_dominante]['description']; ?></p>

                <!-- Gráfico de radar -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-8">
                    <canvas id="radarChart" height="300"></canvas>
                </div>

                <!-- Tabla de resultados -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 border-b text-left">Factor</th>
                                <th class="py-3 px-4 border-b text-left">Puntuación</th>
                                <th class="py-3 px-4 border-b text-left">Porcentaje</th>
                                <th class="py-3 px-4 border-b text-left">Características</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (['D', 'I', 'S', 'C'] as $factor): ?>
                                <tr class="<?php echo $factor == $perfil_dominante ? 'bg-blue-50' : ''; ?>">
                                    <td class="py-3 px-4 border-b font-medium">
                                        <?php echo $descripciones[$factor]['title']; ?> (<?php echo $factor; ?>)
                                    </td>
                                    <td class="py-3 px-4 border-b"><?php echo $resultado[strtolower($factor) . '_total']; ?></td>
                                    <td class="py-3 px-4 border-b">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-<?php echo $factor == 'D' ? 'red' : ($factor == 'I' ? 'yellow' : ($factor == 'S' ? 'green' : 'indigo')); ?>-500 h-2.5 rounded-full"
                                                style="width: <?php echo ${strtolower($factor) . '_percent'}; ?>%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600"><?php echo ${strtolower($factor) . '_percent'}; ?>%</span>
                                    </td>
                                    <td class="py-3 px-4 border-b text-sm text-gray-600">
                                        <?php echo $descripciones[$factor]['description']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recomendaciones -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-8">
                <h3 class="font-bold text-lg text-blue-800 mb-2">Recomendaciones para tu desarrollo</h3>
                <?php if ($perfil_dominante == 'D'): ?>
                    <ul class="list-disc pl-5 text-blue-700 space-y-1">
                        <li>Practica la paciencia y escucha activa con colegas</li>
                        <li>Considera más opiniones antes de tomar decisiones</li>
                        <li>Delega tareas para no sobrecargarte</li>
                    </ul>
                <?php elseif ($perfil_dominante == 'I'): ?>
                    <ul class="list-disc pl-5 text-blue-700 space-y-1">
                        <li>Enfócate en completar tareas antes de empezar nuevas</li>
                        <li>Toma notas durante reuniones importantes</li>
                        <li>Practica la escucha sin interrumpir</li>
                    </ul>
                <?php elseif ($perfil_dominante == 'S'): ?>
                    <ul class="list-disc pl-5 text-blue-700 space-y-1">
                        <li>Expresa tus opiniones con mayor asertividad</li>
                        <li>Abrete a cambios graduales</li>
                        <li>Establece límites claros</li>
                    </ul>
                <?php else: ?>
                    <ul class="list-disc pl-5 text-blue-700 space-y-1">
                        <li>Practica la flexibilidad ante cambios</li>
                        <li>Prioriza lo importante sobre lo perfecto</li>
                        <li>Comparte tus análisis de forma más concisa</li>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Botón de acción -->
            <div class="flex justify-center mt-8">
                <a href="../../web/welcome/dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Script para el gráfico -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('radarChart').getContext('2d');
            const radarChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Dominancia (D)', 'Influencia (I)', 'Estabilidad (S)', 'Cumplimiento (C)'],
                    datasets: [{
                        label: 'Tu Perfil DISC',
                        data: [
                            <?php echo $d_percent; ?>,
                            <?php echo $i_percent; ?>,
                            <?php echo $s_percent; ?>,
                            <?php echo $c_percent; ?>
                        ],
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointRadius: 4
                    }]
                },
                options: {
                    scale: {
                        ticks: {
                            beginAtZero: true,
                            max: 100,
                            min: 0,
                            stepSize: 20
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>
