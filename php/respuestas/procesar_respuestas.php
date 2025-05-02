<?php

session_start();

require_once __DIR__ . '/../conexion.php';

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['error_test'] = "Debes iniciar sesión para completar el test";
    header('Location: ../../web/user/login.php');
    exit();
}

$conexion = new Conexion();
$conn = $conexion->connection();

try {
    // Validar que se enviaron respuestas
    if (empty($_POST['respuestas'])) {
        throw new UnexpectedValueException("No se recibieron respuestas. Por favor completa el test.");
    }

    $conn->beginTransaction();

    // Obtener ID del test CLEAVER
    $stmt = $conn->prepare("SELECT id_test FROM tests WHERE nombre_test = 'CLEAVER' LIMIT 1");
    $stmt->execute();
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        throw new UnexpectedValueException("El test CLEAVER no está configurado en el sistema");
    }

    $id_test = $test['id_test'];
    $id_usuario = $_SESSION['id_usuario'];

    // Verificar si el usuario ya completó el test
    $stmt = $conn->prepare("SELECT COUNT(*) FROM respuestas WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);

    if ($stmt->fetchColumn() > 0) {
        throw new UnexpectedValueException("Ya has completado este test anteriormente.");
    }

    // Inicializar contadores DISC
    $contadores = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
    $errores = [];

    // Procesar cada grupo de respuestas
    foreach ($_POST['respuestas'] as $grupo => $respuestas) {
        // Validar que se seleccionó MAS y MENOS
        if (!isset($respuestas['mas']) || !isset($respuestas['menos'])) {
            $errores[] = "Grupo $grupo: Debes seleccionar una opción MAS y una MENOS";
            continue;
        }

        // Validar que no sea la misma opción
        if ($respuestas['mas'] == $respuestas['menos']) {
            $errores[] = "Grupo $grupo: No puedes seleccionar la misma opción para MAS y MENOS";
            continue;
        }

        // Procesar respuesta MAS
        $stmt = $conn->prepare("SELECT factor_disc FROM preguntas WHERE id_pregunta = ?");
        $stmt->execute([$respuestas['mas']]);
        $factor_mas = $stmt->fetchColumn();

        if (!$factor_mas) {
            $errores[] = "Grupo $grupo: Respuesta MAS inválida";
            continue;
        }

        // Sumar al contador
        $contadores[$factor_mas]++;

        // Insertar respuesta MAS
        $stmt = $conn->prepare("INSERT INTO respuestas (id_usuario, id_pregunta, mas, menos) VALUES (?, ?, 1, 0)");
        $stmt->execute([$id_usuario, $respuestas['mas']]);

        // Procesar respuesta MENOS
        $stmt = $conn->prepare("SELECT factor_disc FROM preguntas WHERE id_pregunta = ?");
        $stmt->execute([$respuestas['menos']]);
        $factor_menos = $stmt->fetchColumn();

        if (!$factor_menos) {
            $errores[] = "Grupo $grupo: Respuesta MENOS inválida";
            continue;
        }

        // Restar al contador
        $contadores[$factor_menos]--;

        // Insertar respuesta MENOS
        $stmt = $conn->prepare("INSERT INTO respuestas (id_usuario, id_pregunta, mas, menos) VALUES (?, ?, 0, 1)");
        $stmt->execute([$id_usuario, $respuestas['menos']]);
    }

    // Si hay errores, cancelar
    if (!empty($errores)) {
        throw new UnexpectedValueException(implode("<br>", $errores));
    }

    // Calcular resultados finales
    $d_total = $contadores['D'];
    $i_total = $contadores['I'];
    $s_total = $contadores['S'];
    $c_total = $contadores['C'];

   // Calcular porcentajes (usando valores absolutos)
    $total = abs($d_total) + abs($i_total) + abs($s_total) + abs($c_total);
    $d_percent = $total > 0 ? round(abs($d_total) / $total * 100) : 0;
    $i_percent = $total > 0 ? round(abs($i_total) / $total * 100) : 0;
    $s_percent = $total > 0 ? round(abs($s_total) / $total * 100) : 0;
    $c_percent = $total > 0 ? round(abs($c_total) / $total * 100) : 0;

    // Determinar perfil dominante
    $perfiles = [
        'D' => abs($d_total),
        'I' => abs($i_total),
        'S' => abs($s_total),
        'C' => abs($c_total)
    ];
    arsort($perfiles);
    $perfil_dominante = key($perfiles);

    // Definir perfil_ideal como booleano (1 o 0)
    $perfil_ideal = ($d_total >= 10 && $i_total >= 8 && $s_total >= 6 && $c_total >= 4) ? 1 : 0;

    // Guardar resultados con todos los campos
    $stmt = $conn->prepare("INSERT INTO resultados_disc
                        (id_usuario, d_total, d_percent, i_total, i_percent,
                            s_total, s_percent, c_total, c_percent,
                            perfil_dominante, perfil_ideal)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $id_usuario,
        $d_total,
        $d_percent,
        $i_total,
        $i_percent,
        $s_total,
        $s_percent,
        $c_total,
        $c_percent,
        $perfil_dominante,
        $perfil_ideal
    ]);

    $conn->commit();

    // Redirigir a resultados
    $_SESSION['test_completed'] = true;
    header('Location: ../../web/welcome/resultados.php');
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    $_SESSION['error_test'] = $e->getMessage();
    header('Location: ../../web/welcome/test.php');
    exit();
}
