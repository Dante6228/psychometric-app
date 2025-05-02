<?php

session_start();

// Verificar autenticación y permisos
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

// Validar y obtener ID del estudiante
$id_estudiante = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_estudiante || $id_estudiante <= 0) {
    header("Location: dashboard.php?error=invalid_id");
    exit();
}

// Obtener información del estudiante y sus resultados
$stmt = $conn->prepare("
    SELECT
        u.id_usuario,
        u.nombre,
        u.email,
        r.fecha_resultado,
        r.d_total, r.d_percent,
        r.i_total, r.i_percent,
        r.s_total, r.s_percent,
        r.c_total, r.c_percent,
        r.perfil_dominante,
        r.perfil_ideal
    FROM usuarios u
    JOIN resultados_disc r ON u.id_usuario = r.id_usuario
    WHERE u.id_usuario = ? AND u.tipo = 'alumno'
    LIMIT 1
");

if (!$stmt->execute([$id_estudiante])) {
    header("Location: dashboard.php?error=db_error");
    exit();
}

$estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudiante) {
    header("Location: dashboard.php?error=student_not_found");
    exit();
}

// Obtener las respuestas del estudiante
$stmt = $conn->prepare("
    SELECT p.texto_pregunta, p.factor_disc, r.mas, r.menos, p.grupo_preguntas
    FROM respuestas r
    JOIN preguntas p ON r.id_pregunta = p.id_pregunta
    WHERE r.id_usuario = ?
    ORDER BY p.grupo_preguntas, p.id_pregunta
");

if (!$stmt->execute([$id_estudiante])) {
    header("Location: dashboard.php?error=db_error_responses");
    exit();
}

$respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar respuestas por grupos
$grupos_respuestas = [];
foreach ($respuestas as $respuesta) {
    $grupo = 'Grupo ' . $respuesta['grupo_preguntas'];
    if (!isset($grupos_respuestas[$grupo])) {
        $grupos_respuestas[$grupo] = [
            'factor' => $respuesta['factor_disc'],
            'preguntas' => []
        ];
    }
    $grupos_respuestas[$grupo]['preguntas'][] = $respuesta;
}

// Configuración de estilos para los perfiles
$colores = [
    'D' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'color' => 'red'],
    'I' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'color' => 'yellow'],
    'S' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'color' => 'green'],
    'C' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'border' => 'border-indigo-300', 'color' => 'indigo']
];

// Descripciones de los perfiles
$descripciones = [
    'D' => [
        'title' => 'Dominancia',
        'alto' => 'Personas con alta Dominancia son decididas, enfocadas en resultados y competitivas. Les gusta tomar el control, enfrentar desafíos y resolver problemas. Pueden ser percibidas como directas o autoritarias.',
        'bajo' => 'Personas con baja Dominancia son cooperativas, evitan conflictos y prefieren consenso. Son pacientes pero pueden tener dificultad para tomar decisiones rápidas o impopulares.'
    ],
    'I' => [
        'title' => 'Influencia',
        'alto' => 'Personas con alta Influencia son entusiastas, sociables y persuasivas. Disfrutan interactuar con otros, son optimistas y buenas para motivar equipos. Pueden ser percibidas como demasiado habladoras o dispersas.',
        'bajo' => 'Personas con baja Influencia son más reservadas, analíticas y prefieren datos sobre emociones. Son buenas para trabajos detallados pero pueden tener dificultad para expresar entusiasmo.'
    ],
    'S' => [
        'title' => 'Estabilidad',
        'alto' => 'Personas con alta Estabilidad son pacientes, leales y consistentes. Excelentes para trabajos rutinarios, son buenos oyentes y mantienen la calma bajo presión. Pueden resistirse al cambio.',
        'bajo' => 'Personas con baja Estabilidad son adaptables, les gusta la variedad y el cambio. Son flexibles pero pueden tener dificultad con rutinas o procesos largos.'
    ],
    'C' => [
        'title' => 'Cumplimiento',
        'alto' => 'Personas con alto Cumplimiento son precisas, metódicas y detallistas. Excelentes para trabajos que requieren exactitud. Pueden ser percibidas como perfeccionistas o demasiado críticas.',
        'bajo' => 'Personas con bajo Cumplimiento son espontáneas, innovadoras y flexibles con las reglas. Buenas para pensar fuera de la caja pero pueden descuidar detalles importantes.'
    ]
];

// Función para determinar el nivel (Alto+/Bajo-/Moderado+/-)
function determinarNivel($total, $percent, $umbral = 50) {
    if ($percent >= $umbral) {
        return $total > 0 ? 'Alto+' : 'Bajo-';
    } elseif ($percent < $umbral) {
        return $total > 0 ? 'Moderado+' : 'Moderado-';
    }
    return 'Indeterminado';
}

// Configuración de umbral para considerar alto/bajo
$umbral_alto = 50;

// Determinar niveles para cada factor
$niveles = [
    'd' => determinarNivel($estudiante['d_total'], $estudiante['d_percent'], $umbral_alto),
    'i' => determinarNivel($estudiante['i_total'], $estudiante['i_percent'], $umbral_alto),
    's' => determinarNivel($estudiante['s_total'], $estudiante['s_percent'], $umbral_alto),
    'c' => determinarNivel($estudiante['c_total'], $estudiante['c_percent'], $umbral_alto)
];

// Determinar perfil dominante (usando valores absolutos)
$perfiles = [
    'D' => abs($estudiante['d_total']),
    'I' => abs($estudiante['i_total']),
    'S' => abs($estudiante['s_total']),
    'C' => abs($estudiante['c_total'])
];
arsort($perfiles);
$perfil_dominante = key($perfiles);

// Mensaje especial si cumple con el perfil ideal
$mensaje_perfil = $estudiante['perfil_ideal'] ?
    "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded'>
        <p class='font-bold'>¡Perfil ideal!</p>
        <p>Este estudiante cumple con las características ideales para la posición.</p>
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
    <title>Detalle del Estudiante</title>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Detalle del Estudiante</h1>
                    <div class="flex items-center space-x-4">
                        <a href="dashboard.php" class="text-white hover:text-gray-200 transition-colors text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver al dashboard
                        </a>
                        <form action="../../php/cerrar_sesion.php" method="post">
                            <button type="submit" class="flex items-center text-white hover:text-gray-200 transition-colors text-sm">
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
        <main class="flex-grow container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <!-- Información del estudiante -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6">
                    <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($estudiante['nombre']); ?></h2>
                    <div class="mt-2 flex flex-wrap items-center gap-4">
                        <span><?php echo htmlspecialchars($estudiante['email']); ?></span>
                        <span class="bg-white/20 rounded-full px-3 py-1 text-sm">
                            Test realizado el <?php echo date('d/m/Y', strtotime($estudiante['fecha_resultado'])); ?>
                        </span>
                        <?php if ($estudiante['perfil_ideal']): ?>
                            <span class="bg-green-500 rounded-full px-3 py-1 text-sm font-medium">
                                Perfil ideal
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Resultados principales -->
                <div class="p-6">
                    <?php echo $mensaje_perfil; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Gráfico radar -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium mb-4 text-center">Perfil DISC</h3>
                            <canvas id="radarChart" height="300"></canvas>
                        </div>
                        
                        <!-- Descripción del perfil -->
                        <div>
                            <h3 class="text-lg font-medium mb-4">Perfil Dominante:
                                <span class="<?php echo $colores[$perfil_dominante]['text']; ?>">
                                    <?php echo $descripciones[$perfil_dominante]['title']; ?>
                                    (<?php echo $perfil_dominante; ?><?php
                                        echo $estudiante[strtolower($perfil_dominante) . '_total'] > 0 ? '+' : '-';
                                    ?>)
                                </span>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                <?php
                                $nivel_dominante = $niveles[strtolower($perfil_dominante)];
                                echo $descripciones[$perfil_dominante][strpos($nivel_dominante, 'Alto') !== false ? 'alto' : 'bajo'];
                                ?>
                            </p>
                            
                            <div class="space-y-3">
                            <?php foreach (['D', 'I', 'S', 'C'] as $factor):
                                $factor_lower = strtolower($factor);
                                $nivel = $niveles[$factor_lower];

                                $color_class = 'gray';
                                if (strpos($nivel, 'Alto') !== false) {
                                    $color_class = 'green';
                                } elseif (strpos($nivel, 'Bajo') !== false) {
                                    $color_class = 'blue';
                                }
                            ?>
                                <div>
                                    <h4 class="font-medium text-gray-800">
                                        <?php echo $descripciones[$factor]['title']; ?> (<?php echo $factor; ?>)
                                        <span class="font-semibold text-<?php echo $color_class; ?>-600">
                                            <?php echo $nivel; ?>
                                        </span>
                                    </h4>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-<?php echo $colores[$factor]['color']; ?>-500 h-2.5 rounded-full"
                                            style="width: <?php echo $estudiante["{$factor_lower}_percent"]; ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">
                                        <?php echo $estudiante["{$factor_lower}_percent"]; ?>%
                                        (Puntuación: <?php echo $estudiante["{$factor_lower}_total"]; ?>)
                                    </span>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recomendaciones -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-8">
                        <h3 class="font-bold text-lg text-blue-800 mb-2">Recomendaciones para el desarrollo</h3>
                        
                        <?php
                        // Definimos recomendaciones específicas para cada nivel
                        $recomendaciones = [
                            'D' => [
                                'alto' => [
                                    'Practicar la paciencia y escucha activa con colegas',
                                    'Considerar más opiniones antes de tomar decisiones importantes',
                                    'Aprender a delegar tareas efectivamente',
                                    'Controlar la impulsividad en situaciones tensas'
                                ],
                                'bajo' => [
                                    'Tomar iniciativa en situaciones que requieren liderazgo',
                                    'Desarrollar mayor confianza en sus decisiones',
                                    'Atreverse a expresar opiniones con firmeza',
                                    'Establecer metas más desafiantes'
                                ],
                                'moderado' => [
                                    'Equilibrar la asertividad con la colaboración',
                                    'Buscar un balance entre liderazgo y trabajo en equipo',
                                    'Desarrollar tanto habilidades directivas como cooperativas'
                                ]
                            ],
                            'I' => [
                                'alto' => [
                                    'Enfocarse en completar tareas antes de empezar nuevas',
                                    'Desarrollar mayor organización en sus actividades',
                                    'Practicar la escucha activa sin interrumpir',
                                    'Controlar el entusiasmo en situaciones formales'
                                ],
                                'bajo' => [
                                    'Participar más activamente en interacciones sociales',
                                    'Expresar emociones con mayor libertad',
                                    'Practicar habilidades de persuasión',
                                    'Compartir ideas con mayor entusiasmo'
                                ],
                                'moderado' => [
                                    'Balancear la sociabilidad con momentos de reflexión',
                                    'Desarrollar tanto habilidades sociales como analíticas'
                                ]
                            ],
                            'S' => [
                                'alto' => [
                                    'Expresar opiniones con mayor asertividad',
                                    'Abrirse a cambios graduales en su rutina',
                                    'Establecer límites claros en las relaciones',
                                    'Tomar decisiones con mayor rapidez cuando sea necesario'
                                ],
                                'bajo' => [
                                    'Desarrollar mayor consistencia en sus acciones',
                                    'Cultivar la paciencia en procesos largos',
                                    'Establecer rutinas más estables',
                                    'Practicar la escucha activa sin necesidad de responder'
                                ],
                                'moderado' => [
                                    'Mantener un equilibrio entre flexibilidad y estabilidad',
                                    'Adaptarse al cambio sin perder consistencia'
                                ]
                            ],
                            'C' => [
                                'alto' => [
                                    'Practicar la flexibilidad ante cambios inesperados',
                                    'Priorizar lo importante sobre lo perfecto',
                                    'Compartir análisis de forma más concisa',
                                    'Aceptar que algunos errores son parte del aprendizaje'
                                ],
                                'bajo' => [
                                    'Desarrollar mayor atención a los detalles',
                                    'Establecer estándares más altos de calidad',
                                    'Seguir procedimientos establecidos cuando sea necesario',
                                    'Documentar más sus procesos y decisiones'
                                ],
                                'moderado' => [
                                    'Balancear la precisión con la flexibilidad',
                                    'Encontrar un punto medio entre perfección y pragmatismo'
                                ]
                            ]
                        ];
                        
                        // Mostramos recomendaciones para cada factor
                        foreach (['D', 'I', 'S', 'C'] as $factor):
                            $factor_lower = strtolower($factor);
                            $nivel_completo = $niveles[$factor_lower];
                            
                            // Determinar si es alto, bajo o moderado
                            if (strpos($nivel_completo, 'Alto') !== false) {
                                $nivel = 'alto';
                            } elseif (strpos($nivel_completo, 'Bajo') !== false) {
                                $nivel = 'bajo';
                            } else {
                                $nivel = 'moderado';
                            }
                        ?>
                            <div class="mb-4">
                                <h4 class="font-medium text-blue-700">
                                    <?php echo $descripciones[$factor]['title']; ?> (<?php echo $factor; ?><?php
                                        echo $estudiante["{$factor_lower}_total"] > 0 ? '+' : '-';
                                    ?>):
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
                            <h4 class="font-medium text-blue-800">Recomendación clave para el perfil dominante (<?php echo $perfil_dominante; ?>):</h4>
                            <p class="text-blue-700 mt-1 pl-2">
                                <?php
                                $nivel_dominante = $niveles[strtolower($perfil_dominante)];
                                if (strpos($nivel_dominante, 'Alto') !== false) {
                                    $nivel = 'alto';
                                } elseif (strpos($nivel_dominante, 'Bajo') !== false) {
                                    $nivel = 'bajo';
                                } else {
                                    $nivel = 'moderado';
                                }
                                echo $recomendaciones[$perfil_dominante][$nivel][0];
                                ?>
                            </p>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex justify-between">
                <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                    Volver al listado
                </a>
                <div class="flex space-x-3">
                    <a href="generar_pdf.php?id=<?php echo $id_estudiante; ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        Generar PDF
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-100 border-t py-4 mt-8">
            <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
                <p>© <?php echo date('Y'); ?> Sistema de Evaluación Psicométrica - Versión 1.0</p>
            </div>
        </footer>
    </div>

    <!-- Script para gráficos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('radarChart').getContext('2d');
            const radarChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Dominancia (D)', 'Influencia (I)', 'Estabilidad (S)', 'Cumplimiento (C)'],
                    datasets: [{
                        label: 'Perfil DISC',
                        data: [
                            <?php echo $estudiante['d_percent']; ?>,
                            <?php echo $estudiante['i_percent']; ?>,
                            <?php echo $estudiante['s_percent']; ?>,
                            <?php echo $estudiante['c_percent']; ?>
                        ],
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            suggestedMin: 0,
                            suggestedMax: 40,
                            ticks: {
                                stepSize: 20,
                                backdropColor: 'rgba(0, 0, 0, 0)'
                            }
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
