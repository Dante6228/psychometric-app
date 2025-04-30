<?php

session_start();

// agregar validación de usuario
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['error_test'] = "Debes iniciar sesión para completar el test";
    header('Location: ../user/index.php');
    exit();
}

// Mostrar errores si existen
if (isset($_SESSION['error_test'])) {
    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 animate-fade-in">';
    echo '<p>'.htmlspecialchars($_SESSION['error_test']).'</p>';
    echo '</div>';
    unset($_SESSION['error_test']); // Limpiar el mensaje después de mostrarlo
}

require_once __DIR__ . '/../../php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->connection();

// Obtener las preguntas agrupadas
$stmt = $conn->prepare("SELECT * FROM preguntas ORDER BY grupo_preguntas ASC");
$stmt->execute();
$preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar por grupos
$grupos = [];
foreach ($preguntas as $pregunta) {
    $grupos[$pregunta['grupo_preguntas']][] = $pregunta;
}

// Verificar si el usuario ya completó el test
$id_usuario = $_SESSION['id_usuario'];
$stmt = $conn->prepare("SELECT COUNT(*) FROM respuestas WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);

if ($stmt->fetchColumn() > 0) {
    header("Location: ../../web/welcome/welcome.php?message=errTest");
}

?>

<!DOCTYPE html>
<html lang="es" class="selection:bg-slate-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/output.css">
    <link rel="stylesheet" href="../../styles/general.css">
    <title>Test Psicométrico CLEAVER</title>
    <style>
        .option-card {
            transition: all 0.2s ease;
        }

        .option-card:hover {
            transform: translateY(-2px);
        }

        /* Estilos personalizados para los radio buttons */
        .custom-radio {
            position: relative;
            padding-left: 28px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            margin-right: 15px;
        }

        .custom-radio input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .radio-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            border-radius: 50%;
            border: 2px solid #E5E7EB;
            transition: all 0.3s ease;
        }

        .custom-radio:hover .radio-checkmark {
            border-color: #3B82F6;
        }

        .custom-radio input:checked~.radio-checkmark {
            border-color: #3B82F6;
            background-color: #3B82F6;
        }

        .custom-radio input:checked~.radio-checkmark:after {
            content: "";
            position: absolute;
            display: block;
            top: 4px;
            left: 4px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
        }

        .custom-radio.mas input:checked~.radio-checkmark {
            border-color: #10B981;
            background-color: #10B981;
        }

        .custom-radio.menos input:checked~.radio-checkmark {
            border-color: #111827;
            background-color: #111827;
        }

        .radio-label {
            margin-left: 5px;
            font-weight: 500;
        }

        .mas .radio-label {
            color: #10B981;
        }

        .menos .radio-label {
            color: #111827;
        }

        /* Animaciones para notificaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .animate-fade-out {
            animation: fadeOut 0.3s ease-in forwards;
        }
    </style>
</head>

<body class="bg-soft-white min-h-screen p-4 md:p-8">
    <main class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header con gradiente -->
        <div class="bg-gradient-to-r from-soft-blue to-soft-green p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold">Test Psicométrico CLEAVER</h1>
                    <p class="mt-1 text-soft-white opacity-90">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></p>
                </div>
                <div class="bg-white/20 rounded-full px-3 py-1 text-sm font-medium">
                    Grupo DUAL
                </div>
            </div>
        </div>

        <!-- Panel de instrucciones -->
        <div class="p-6 border-b border-soft-grey">
            <div class="bg-soft-grey/30 p-5 rounded-lg border border-soft-grey">
                <div class="flex items-start">
                    <div class="bg-soft-blue rounded-full p-2 mr-3 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-lg text-hard-grey mb-2">Instrucciones importantes</h2>
                        <p class="text-hard-grey mb-3">Las palabras descriptivas que verá a continuación se encuentran agrupadas en series de cuatro. Examine cada grupo y:</p>
                        <ol class="list-decimal pl-5 space-y-2 text-hard-grey">
                            <li><span class="font-semibold text-soft-green">MARQUE "MAS"</span> en la palabra que mejor describa su forma de ser</li>
                            <li><span class="font-semibold text-hard-grey">MARQUE "MENOS"</span> en la palabra que menos lo describa</li>
                        </ol>
                        <div class="mt-4 bg-white/80 rounded-md p-3 border border-soft-grey/50">
                            <p class="text-sm font-medium text-hard-grey flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-soft-blue mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                ¡Solo una opción MAS y una MENOS por grupo!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor del formulario -->
        <form action="../../php/respuestas/procesar_respuestas.php" method="POST" class="divide-y divide-soft-grey/30">
            <?php foreach ($grupos as $numGrupo => $grupoPreguntas): ?>
                <div class="p-6 group hover:bg-soft-white/50 transition-colors">
                    <div class="flex items-center mb-4">
                        <div class="bg-soft-blue text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">
                            <span class="font-medium"><?php echo $numGrupo; ?></span>
                        </div>
                        <h3 class="font-semibold text-hard-grey">Seleccione en este grupo:</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($grupoPreguntas as $pregunta): ?>
                            <div class="option-card bg-white border border-soft-grey/50 rounded-lg p-4 hover:shadow-md hover:border-soft-blue/50">
                                <div class="flex justify-between items-center">
                                    <p class="font-medium text-hard-grey pr-2"><?php echo htmlspecialchars($pregunta['texto_pregunta']); ?></p>

                                    <div class="flex space-x-4">
                                        <label class="custom-radio mas">
                                            <input type="radio"
                                                name="respuestas[<?php echo $numGrupo; ?>][mas]"
                                                value="<?php echo $pregunta['id_pregunta']; ?>"
                                                onchange="validarSeleccion(<?php echo $numGrupo; ?>)"
                                                required>
                                            <span class="radio-checkmark"></span>
                                            <span class="radio-label">MAS</span>
                                        </label>

                                        <label class="custom-radio menos">
                                            <input type="radio"
                                                name="respuestas[<?php echo $numGrupo; ?>][menos]"
                                                value="<?php echo $pregunta['id_pregunta']; ?>"
                                                onchange="validarSeleccion(<?php echo $numGrupo; ?>)"
                                                required>
                                            <span class="radio-checkmark"></span>
                                            <span class="radio-label">MENOS</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Pie de página con botón de envío -->
            <div class="p-6 bg-soft-white/50 border-t border-soft-grey/30">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-hard-grey/70">
                        <p>Revise que haya seleccionado todas las respuestas requeridas</p>
                    </div>
                    <button type="submit"
                        class="relative px-8 py-3 bg-gradient-to-r from-soft-blue to-soft-green text-white font-medium rounded-lg overflow-hidden group hover:shadow-lg transition-all">
                        <span class="relative z-10 flex items-center">
                            Finalizar Test
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span class="absolute inset-0 bg-gradient-to-r from-soft-blue/90 to-soft-green/90 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script>
        function validarSeleccion(grupo) {
            const grupoInputs = document.querySelectorAll(`input[name^="respuestas[${grupo}]"]`);
            const masInputs = document.querySelectorAll(`input[name="respuestas[${grupo}][mas]"]`);
            const menosInputs = document.querySelectorAll(`input[name="respuestas[${grupo}][menos]"]`);

            masInputs.forEach(inputMas => {
                menosInputs.forEach(inputMenos => {
                    if (inputMas.checked && inputMenos.checked && inputMas.value === inputMenos.value) {
                        const notification = document.createElement('div');
                        notification.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg z-50 animate-fade-in';
                        notification.innerHTML = `
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <strong>¡Error!</strong> No puede seleccionar la misma palabra para MAS y MENOS
                            </div>
                        `;
                        document.body.appendChild(notification);

                        setTimeout(() => {
                            notification.classList.remove('animate-fade-in');
                            notification.classList.add('animate-fade-out');
                            setTimeout(() => notification.remove(), 300);
                        }, 3000);

                        inputMenos.checked = false;
                    }
                });
            });
        }
    </script>
</body>

</html>
