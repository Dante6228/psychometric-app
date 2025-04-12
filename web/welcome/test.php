<?php
session_start();
require_once __DIR__ . '/../../php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->connection();

$stmt = $conn->prepare("SELECT * FROM preguntas ORDER BY orden ASC");
$stmt->execute();
$preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es" class="selection:bg-slate-200">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../styles/output.css">
    <title>Test Psicométrico</title>
</head>
<body class="bg-white p-6">
    <main class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>
        <form action="guardar_respuestas.php" method="POST" class="space-y-6">
            <?php foreach ($preguntas as $pregunta): ?>
                <div class="bg-slate-100 p-4 rounded-lg shadow">
                    <p class="font-semibold"><?php echo htmlspecialchars($pregunta['contenido']); ?></p>
                    <div class="flex gap-4 mt-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label class="flex items-center gap-1">
                                <input type="radio" name="respuestas[<?php echo $pregunta['id']; ?>]" value="<?php echo $i; ?>" required>
                                <?php echo $i; ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Enviar respuestas</button>
        </form>
    </main>
</body>
</html>
