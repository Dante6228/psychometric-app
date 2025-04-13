<?php

session_start();

require_once __DIR__ . '/../../php/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../web/user/index.php?message=errPost');
    exit();
}

$conexion = new Conexion();
$conn = $conexion->connection();

try {
    if (empty($_POST['respuestas'])) {
        throw new Exception("No se recibieron respuestas. Por favor completa el test.");
    }

    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT id_test FROM tests WHERE nombre_test = 'CLEAVER' LIMIT 1");
    if (!$stmt->execute()) {
        throw new Exception("Error al consultar el test");
    }

    $test = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$test) {
        throw new Exception("El test CLEAVER no está configurado en el sistema");
    }

    $id_test = $test['id_test'];
    $id_usuario = $_SESSION['id_usuario'];

    // Verificar si el usuario ya completó el test
    $stmt = $conn->prepare("SELECT COUNT(*) FROM respuestas
                            WHERE id_usuario = ?
                            AND id_pregunta IN (SELECT id_pregunta FROM preguntas WHERE id_test = ?)");
    if (!$stmt->execute([$id_usuario, $id_test])) {
        throw new Exception("Error al verificar tests previos");
    }

    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ya has completado este test anteriormente. No puedes repetirlo.");
    }

    $contadores = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
    $errores_grupos = [];

    foreach ($_POST['respuestas'] as $grupo => $respuestas) {
        if (!isset($respuestas['mas']) || !isset($respuestas['menos'])) {
            $errores_grupos[] = "Grupo $grupo: Debes seleccionar una opción MAS y una MENOS";
            continue;
        }

        if ($respuestas['mas'] == $respuestas['menos']) {
            $errores_grupos[] = "Grupo $grupo: No puedes seleccionar la misma opción para MAS y MENOS";
            continue;
        }

        try {
            $stmt = $conn->prepare("SELECT factor_disc FROM preguntas WHERE id_pregunta = ?");
            if (!$stmt->execute([$respuestas['mas']])) {
                throw new Exception("Error al procesar respuesta MAS en grupo $grupo");
            }

            $factor = $stmt->fetchColumn();
            if (!$factor) {
                throw new Exception("Respuesta MAS inválida en grupo $grupo");
            }

            $contadores[$factor]++;

            $stmt = $conn->prepare("INSERT INTO respuestas (id_usuario, id_pregunta, mas, menos, fecha_respuesta)
                                    VALUES (?, ?, 1, 0, NOW())");
            if (!$stmt->execute([$id_usuario, $respuestas['mas']])) {
                throw new Exception("Error al guardar respuesta MAS en grupo $grupo");
            }

            $stmt = $conn->prepare("SELECT factor_disc FROM preguntas WHERE id_pregunta = ?");
            if (!$stmt->execute([$respuestas['menos']])) {
                throw new Exception("Error al procesar respuesta MENOS en grupo $grupo");
            }

            $factor = $stmt->fetchColumn();
            if (!$factor) {
                throw new Exception("Respuesta MENOS inválida en grupo $grupo");
            }

            $contadores[$factor]--;

            $stmt = $conn->prepare("INSERT INTO respuestas (id_usuario, id_pregunta, mas, menos, fecha_respuesta)
                                    VALUES (?, ?, 0, 1, NOW())");
            if (!$stmt->execute([$id_usuario, $respuestas['menos']])) {
                throw new Exception("Error al guardar respuesta MENOS en grupo $grupo");
            }
        } catch (Exception $e) {
            $errores_grupos[] = $e->getMessage();
        }
    }

    if (!empty($errores_grupos)) {
        throw new Exception(implode("<br>", $errores_grupos));
    }

    $d_total = $contadores['D'];
    $i_total = $contadores['I'];
    $s_total = $contadores['S'];
    $c_total = $contadores['C'];

    // Comparar con perfil ideal (implementar tu lógica aquí)
    $perfil_ideal = false; // Cambiar por tu lógica real

    // Convertir a entero para MySQL
    $perfil_ideal_int = $perfil_ideal ? 1 : 0;

    // Guardar resultados
    $stmt = $conn->prepare("INSERT INTO resultados_disc
                        (id_usuario, d_total, i_total, s_total, c_total, perfil_ideal, fecha_calculo)
                        VALUES (?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt->execute([$id_usuario, $d_total, $i_total, $s_total, $c_total, $perfil_ideal_int])) {
        throw new Exception("Error al guardar los resultados finales");
    }

    $conn->commit();

    $_SESSION['test_completed'] = true;
    header('Location: ../../web/welcome/resultados.php');
    exit();
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log("Error en procesar_respuestas: " . $e->getMessage());
    $_SESSION['error_test'] = $e->getMessage();
    header('Location: ../../web/welcome/test.php');
    exit();
}
