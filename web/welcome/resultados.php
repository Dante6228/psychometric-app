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
        'description' => $resultado['d_total'] > 0 ?
            'Le apasionan los retos. Puede ser considerado temerario por los demás. Siempre listo a la competencia. Cuando algo esta en juego, sale lo mejor de él. Tiene respeto por aquellos que ganan contra todas las expectativas. Se desempeña mejor cuanto tiene autonomía.' :
            'Son personas apacibles que buscan la paz y la armonía. En donde existen problemas, ellos preferirán que sean otros los que inicien la acción, quizá hasta sacrificando su propio interés para adaptarse a las soluciones impuestas. La humildad es una virtud.'
    ],
    'I' => [
        'title' => 'Influencia',
        'description' => $resultado['i_total'] > 0 ?
            'Abierto, persuasivo y sociable. Generalmente optimista, puede ver algo bueno en cualquier situación. Interesado principalmente en la gente, sus problemas y actividades. Dispuesto a ayudar a otros a promover sus proyectos, así como los suyos propios.' :
            'Lógicas y objetivas en todo lo que hacen, con frecuencia se acusa a estas personas de no gustar de la gente. El problema no es de sentir atracción o afecto, sino lo que hacen al respecto. Socialmente pasivos, frecuentemente asumen el rol de observador en cualquier ambiente social, incluso ante los conflictos.'
    ],
    'S' => [
        'title' => 'Constancia',
        'description' => $resultado['s_total'] > 0 ?
            'Generalmente amable, tranquilo y llevadero. Es poco demostrativo y controlado, ya que no es de naturaleza explosiva de pronta reacción; puede ocultar sus sentimientos y ser rencoroso. Gusta de establecer relaciones amistosas cercanas con un grupo relativamente pequeño de personas.' :
            'Flexibles, variables y activos. Estas personas ponen las cosas en movimiento. La variedad es el condimento de la vida; además, es difícil pegarle a un blanco en constante movimiento. Estas personas se sienten cómodas con un alto ritmo de cambios de actividad y de rutina.'
    ],
    'C' => [
        'title' => 'Cumplimiento',
        'description' => $resultado['c_total'] > 0 ?
            'Es generalmente pacifico y se adapta a las situaciones con el fin de evitar antagonismos. Siendo sensible, busca apreciación y es fácilmente herido por otros. Es humilde leal y dócil, tratando de hacer siempre las cosas lo mejor posible.' :
            '"Independientes, desinhibidos y aventureros; estos espíritus libres disfrutan de la vida. Cualquier cosa nueva y diferente les emociona.
            Debido a que prefieren campos nuevos y mares desconocidos, con frecuencia estas personas preocupan a las más conservadores por su constante innovación."'
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
<html lang="es" class="selection:bg-slate-200">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/output.css">
    <link rel="stylesheet" href="../../styles/general.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Resultados Test Cleaver</title>
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
                    <table class="min-w-full bg-white border border-gray-200 p-4">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 border-b text-left">Factor</th>
                                <th class="py-3 px-4 border-b text-left">Nivel</th>
                                <th class="py-3 px-4 border-b text-left">Puntuación</th>
                                <th class="py-3 px-4 border-b text-left">Porcentaje</th>
                                <th class="py-3 px-4 border-b text-left">Características</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Definimos las descripciones completas para Alto/Bajo
                            $detalles_cleaver = [
                                'D' => [
                                    'alto' => 'Le apasionan los retos. Puede ser considerado temerario. Siempre listo a la competencia.',
                                    'bajo' => 'Personas apacibles que buscan la paz y la armonía. Prefieren que otros inicien la acción.'
                                ],
                                'I' => [
                                    'alto' => 'Abierto, persuasivo y sociable. Optimista, puede ver algo bueno en cualquier situación.',
                                    'bajo' => 'Lógicas y objetivas. Socialmente pasivas, frecuentemente asumen el rol de observador.'
                                ],
                                'S' => [
                                    'alto' => 'Amable, tranquilo y llevadero. Gusta de relaciones cercanas con un grupo pequeño.',
                                    'bajo' => 'Flexibles y activos. Se sienten cómodos con un alto ritmo de cambios.'
                                ],
                                'C' => [
                                    'alto' => 'Pacífico y adaptable. Sensible, busca apreciación y trata de hacer siempre lo mejor posible.',
                                    'bajo' => 'Independientes y aventureros. Prefieren campos nuevos y mares desconocidos.'
                                ]
                            ];
                            
                            foreach (['D', 'I', 'S', 'C'] as $factor):
                                $puntaje = $resultado[strtolower($factor) . '_total'];
                                $nivel = $puntaje > 0 ? 'Alto' : 'Bajo';
                                $signo = $puntaje > 0 ? '+' : '-';
                            ?>
                                <tr class="<?php echo $factor == $perfil_dominante ? 'bg-blue-50' : ''; ?>">
                                    <td class="py-3 px-4 border-b font-medium">
                                        <?php echo $descripciones[$factor]['title']; ?> (<?php echo $factor; ?>)
                                    </td>
                                    <td class="py-3 px-4 border-b font-semibold text-<?php echo $nivel == 'Alto' ? 'green' : 'blue'; ?>-600">
                                        <?php echo $nivel; ?><?php echo $signo; ?>
                                    </td>
                                    <td class="py-3 px-4 border-b"><?php echo $puntaje; ?></td>
                                    <td class="py-3 px-4 border-b">
                                        <span class="text-sm text-gray-600"><?php echo ${strtolower($factor) . '_percent'}; ?>%</span>
                                    </td>
                                    <td class="py-3 px-4 border-b text-sm text-gray-600">
                                        <?php echo $detalles_cleaver[$factor][strtolower($nivel)]; ?>
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
                
                <?php
                // Definimos recomendaciones específicas para Alto/Bajo en cada factor
                $recomendaciones = [
                    'D' => [
                        'alto' => [
                            'Practica la paciencia y escucha activa con colegas',
                            'Considera más opiniones antes de tomar decisiones importantes',
                            'Aprende a delegar tareas efectivamente',
                            'Controla tu impulsividad en situaciones tensas'
                        ],
                        'bajo' => [
                            'Toma iniciativa en situaciones que requieren liderazgo',
                            'Desarrolla mayor confianza en tus decisiones',
                            'Atrévete a expresar tus opiniones con firmeza',
                            'Establece metas más desafiantes para ti mismo'
                        ]
                    ],
                    'I' => [
                        'alto' => [
                            'Enfócate en completar tareas antes de empezar nuevas',
                            'Desarrolla mayor organización en tus actividades',
                            'Practica la escucha activa sin interrumpir',
                            'Controla tu entusiasmo en situaciones formales'
                        ],
                        'bajo' => [
                            'Participa más activamente en interacciones sociales',
                            'Expresa tus emociones con mayor libertad',
                            'Practica habilidades de persuasión',
                            'Comparte tus ideas con mayor entusiasmo'
                        ]
                    ],
                    'S' => [
                        'alto' => [
                            'Expresa tus opiniones con mayor asertividad',
                            'Abrete a cambios graduales en tu rutina',
                            'Establece límites claros en tus relaciones',
                            'Toma decisiones con mayor rapidez cuando sea necesario'
                        ],
                        'bajo' => [
                            'Desarrolla mayor consistencia en tus acciones',
                            'Cultiva la paciencia en procesos largos',
                            'Establece rutinas más estables',
                            'Practica la escucha activa sin necesidad de responder'
                        ]
                    ],
                    'C' => [
                        'alto' => [
                            'Practica la flexibilidad ante cambios inesperados',
                            'Prioriza lo importante sobre lo perfecto',
                            'Comparte tus análisis de forma más concisa',
                            'Acepta que algunos errores son parte del aprendizaje'
                        ],
                        'bajo' => [
                            'Desarrolla mayor atención a los detalles',
                            'Establece estándares más altos de calidad',
                            'Sigue procedimientos establecidos cuando sea necesario',
                            'Documenta más tus procesos y decisiones'
                        ]
                    ]
                ];
                
                // Mostramos recomendaciones para cada factor
                foreach (['D', 'I', 'S', 'C'] as $factor):
                    $puntaje = $resultado[strtolower($factor) . '_total'];
                    $nivel = $puntaje > 0 ? 'alto' : 'bajo';
                ?>
                    <div class="mb-4">
                        <h4 class="font-medium text-blue-700">
                            <?php echo $descripciones[$factor]['title']; ?> (<?php echo $factor; ?><?php echo $puntaje > 0 ? '+' : '-'; ?>):
                        </h4>
                        <ul class="list-disc pl-5 text-blue-700 space-y-1 ml-2">
                            <?php foreach ($recomendaciones[$factor][$nivel] as $recomendacion): ?>
                                <li><?php echo $recomendacion; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
                
                <!-- Recomendación especial para el perfil dominante -->
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <h4 class="font-medium text-blue-800">Recomendación clave para tu perfil dominante (<?php echo $perfil_dominante; ?>):</h4>
                    <p class="text-blue-700 mt-1 pl-2"><?php
                        echo $recomendaciones[$perfil_dominante][$resultado[strtolower($perfil_dominante) . '_total'] > 0 ? 'alto' : 'bajo'][0];
                    ?></p>
                </div>
            </div>

            <!-- Botón de acción -->
            <div class="flex justify-center mt-8">
                <a href="../../web/welcome/welcome.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                    Volver
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
