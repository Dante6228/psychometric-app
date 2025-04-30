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

// Obtener lista de estudiantes que han completado el test
$stmt = $conn->prepare("
    SELECT u.id_usuario, u.nombre, u.email, r.fecha_resultado, 
            r.d_percent, r.i_percent, r.s_percent, r.c_percent,
            r.perfil_dominante
    FROM usuarios u
    JOIN resultados_disc r ON u.id_usuario = r.id_usuario
    ORDER BY r.fecha_resultado DESC
");
$stmt->execute();
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas generales
$total_estudiantes = count($estudiantes);
$perfiles = array_column($estudiantes, 'perfil_dominante');
$distribucion_perfiles = array_count_values($perfiles);

// Colores para los perfiles
$colores = [
    'D' => 'bg-red-100 text-red-800',
    'I' => 'bg-yellow-100 text-yellow-800',
    'S' => 'bg-green-100 text-green-800',
    'C' => 'bg-indigo-100 text-indigo-800'
];
?>

<!DOCTYPE html>
<html lang="es" class="selection:bg-slate-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/output.css">
    <link rel="stylesheet" href="../../styles/general.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard Administrativo</title>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Dashboard Administrativo</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm"><?php echo $_SESSION['nombre']; ?></span>
                        <form action="../../php/user/cerrar.php" method="post">
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
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Resumen General</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <!-- Tarjeta Total Estudiantes -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-gray-500 text-sm font-medium">Total de estudiantes</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2"><?php echo $total_estudiantes; ?></div>
                    </div>
                    
                    <!-- Tarjetas de distribución de perfiles -->
                    <?php foreach (['D', 'I', 'S', 'C'] as $perfil): ?>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-gray-500 text-sm font-medium">Perfil <?php echo $perfil; ?></div>
                            <div class="text-3xl font-bold mt-2 <?php echo $colores[$perfil]; ?> rounded-full w-12 h-12 flex items-center justify-center mx-auto">
                                <?php echo $distribucion_perfiles[$perfil] ?? 0; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Gráfico de distribución -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4">Distribución de Perfiles</h3>
                    <canvas id="perfilesChart" height="150"></canvas>
                </div>
            </div>

            <!-- Listado de estudiantes -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">Resultados de Estudiantes</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($estudiantes as $estudiante): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($estudiante['nombre']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        <?php echo htmlspecialchars($estudiante['email']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($estudiante['fecha_resultado'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $colores[$estudiante['perfil_dominante']]; ?>">
                                            <?php echo $estudiante['perfil_dominante']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-1">
                                            <span class="text-xs font-medium text-red-600">D:<?php echo $estudiante['d_percent']; ?>%</span>
                                            <span class="text-xs font-medium text-yellow-500">I:<?php echo $estudiante['i_percent']; ?>%</span>
                                            <span class="text-xs font-medium text-green-600">S:<?php echo $estudiante['s_percent']; ?>%</span>
                                            <span class="text-xs font-medium text-indigo-600">C:<?php echo $estudiante['c_percent']; ?>%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="detalle_estudiante.php?id=<?php echo $estudiante['id_usuario']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                        <form action="../../php/user/eliminar_test.php" method="post" class="inline-block">
                                            <input type="hidden" name="id_usuario" value="<?php echo $estudiante['id_usuario']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
            // Gráfico de distribución de perfiles
            const ctx = document.getElementById('perfilesChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Dominancia (D)', 'Influencia (I)', 'Estabilidad (S)', 'Cumplimiento (C)'],
                    datasets: [{
                        label: 'Cantidad de Estudiantes',
                        data: [
                            <?php echo $distribucion_perfiles['D'] ?? 0; ?>,
                            <?php echo $distribucion_perfiles['I'] ?? 0; ?>,
                            <?php echo $distribucion_perfiles['S'] ?? 0; ?>,
                            <?php echo $distribucion_perfiles['C'] ?? 0; ?>
                        ],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(234, 179, 8, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(99, 102, 241, 0.7)'
                        ],
                        borderColor: [
                            'rgba(239, 68, 68, 1)',
                            'rgba(234, 179, 8, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(99, 102, 241, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
