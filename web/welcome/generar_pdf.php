<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("Acceso denegado");
}

if ($_SESSION['tipo'] !== 'administrativo') {
    header("Location: ../../web/user/index.php?message=errUser");
    exit();
}

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$id_estudiante = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_estudiante) {
    die("ID de estudiante inválido");
}

// Obtener datos del estudiante
$conexion = new Conexion();
$conn = $conexion->connection();

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

$stmt->execute([$id_estudiante]);
$estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudiante) {
    die("Estudiante no encontrado");
}

// Crear PDF con TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator('Sistema de Evaluación Psicométrica');
$pdf->SetAuthor('Administrador');
$pdf->SetTitle('Informe DISC - ' . $estudiante['nombre']);
$pdf->SetSubject('Resultados del Test DISC');
$pdf->SetKeywords('DISC, psicometría, evaluación');

// Establecer márgenes
$pdf->SetMargins(15, 35, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);

// Configurar fuente por defecto
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Añadir página
$pdf->AddPage();

// ---------------------------------------------------------
// ENCABEZADO CON LOGO
$logoPath = __DIR__ . '/../../img/logo.png';

// Verificar si existe el logo
if (file_exists($logoPath)) {

    $pdf->Image($logoPath, 15, 10, 25, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    $pdf->SetY(15);
} else {
    $pdf->SetY(20);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 5, 'Sistema de Evaluación Psicométrica', 0, 1, 'R');
    $pdf->SetY(25);
}

// Título principal
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(0, 60, 120);
$pdf->Cell(0, 10, 'INFORME DE RESULTADOS DISC', 0, 1, 'C');
$pdf->SetTextColor(0);
$pdf->SetFont('helvetica', 'I', 12);
$pdf->Cell(0, 5, 'Evaluación Psicométrica', 0, 1, 'C');
$pdf->Ln(12);

// Línea decorativa
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor(0, 60, 120);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(10);

// ---------------------------------------------------------
// DATOS DEL ESTUDIANTE
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, '  DATOS DEL ESTUDIANTE', 0, 1, 'L', true);
$pdf->SetFont('helvetica', '', 12);

// Tabla de datos con formato mejorado
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(45, 8, 'Nombre completo:', 0, 0);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, $estudiante['nombre'], 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(45, 8, 'Correo electrónico:', 0, 0);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, $estudiante['email'], 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(45, 8, 'Fecha de evaluación:', 0, 0);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, date('d/m/Y \a \l\a\s H:i', strtotime($estudiante['fecha_resultado'])), 0, 1);
$pdf->Ln(12);

// ---------------------------------------------------------
// PERFIL DOMINANTE
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, '  PERFIL DOMINANTE', 0, 1, 'L', true);

// Color según perfil dominante
$colors = [
    'D' => ['main' => [220, 50, 50], 'light' => [255, 230, 230]],
    'I' => ['main' => [255, 180, 0], 'light' => [255, 245, 215]],
    'S' => ['main' => [50, 150, 50], 'light' => [230, 255, 230]],
    'C' => ['main' => [50, 50, 180], 'light' => [230, 230, 255]]
];

$color = $colors[$estudiante['perfil_dominante'] ?? ['main' => [0, 0, 0], 'light' => [240, 240, 240]]];

// Caja destacada para perfil dominante
$pdf->SetFillColor($color['light'][0], $color['light'][1], $color['light'][2]);
$pdf->SetDrawColor($color['main'][0], $color['main'][1], $color['main'][2]);
$pdf->SetLineWidth(0.5);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor($color['main'][0], $color['main'][1], $color['main'][2]);
$pdf->Cell(0, 12, '  ' . $estudiante['perfil_dominante'] . ' - ' . obtenerNombrePerfil($estudiante['perfil_dominante']), 1, 1, 'L', true);
$pdf->SetTextColor(0);
$pdf->SetFont('helvetica', '', 12);
$pdf->MultiCell(0, 8, obtenerDescripcionPerfil($estudiante['perfil_dominante']), 0, 'J');
$pdf->Ln(10);

// ---------------------------------------------------------
// RESULTADOS POR FACTOR
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, '  RESULTADOS POR FACTOR', 0, 1, 'L', true);
$pdf->Ln(5);

// Tabla de resultados
$header = ['FACTOR DISC', 'PUNTUACIÓN', 'PORCENTAJE'];
$data = [
    ['Dominancia (D)', $estudiante['d_total'], $estudiante['d_percent'] . '%'],
    ['Influencia (I)', $estudiante['i_total'], $estudiante['i_percent'] . '%'],
    ['Estabilidad (S)', $estudiante['s_total'], $estudiante['s_percent'] . '%'],
    ['Cumplimiento (C)', $estudiante['c_total'], $estudiante['c_percent'] . '%']
];

// Configuración de la tabla
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(220, 230, 255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(150, 150, 150);
$pdf->SetLineWidth(0.3);

// Anchos de columna
$w = [80, 50, 50];

// Cabecera
for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 8, $header[$i], 1, 0, 'C', 1);
}
$pdf->Ln();

// Datos con colores según perfil
$pdf->SetFont('helvetica', '', 11);
foreach ($data as $row) {
    $factor = substr($row[0], 0, 1);
    $cellColor = $colors[$factor]['light'] ?? [255, 255, 255];
    
    $pdf->SetFillColor($cellColor[0], $cellColor[1], $cellColor[2]);
    $pdf->Cell($w[0], 8, $row[0], 'LRBT', 0, 'L', true);
    $pdf->Cell($w[1], 8, $row[1], 'LRBT', 0, 'C', true);
    $pdf->Cell($w[2], 8, $row[2], 'LRBT', 0, 'C', true);
    $pdf->Ln();
}
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln(15);

// ---------------------------------------------------------
// RECOMENDACIONES
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, '  RECOMENDACIONES', 0, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 12);
$recomendaciones = generarRecomendaciones($estudiante);
$pdf->MultiCell(0, 8, $recomendaciones, 0, 'J');
$pdf->Ln(10);

// ---------------------------------------------------------
// PIE DE PÁGINA
$pdf->SetY(-20);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(100, 100, 100);

// ---------------------------------------------------------
// FUNCIONES AUXILIARES
function obtenerNombrePerfil($perfil) {
    $nombres = [
        'D' => 'Dominancia',
        'I' => 'Influencia',
        'S' => 'Estabilidad',
        'C' => 'Cumplimiento'
    ];
    return $nombres[$perfil] ?? 'Desconocido';
}

function obtenerDescripcionPerfil($perfil) {
    $descripciones = [
        'D' => 'Perfil de alta Dominancia: Personas decididas, enfocadas en resultados y competitivas. Les gusta tomar el control, enfrentar desafíos y resolver problemas rápidamente. Suelen ser directos y orientados a objetivos.',
        'I' => 'Perfil de alta Influencia: Personas entusiastas, sociables y persuasivas. Disfrutan interactuar con otros, son optimistas y buenas motivadoras. Excelentes para trabajos que requieren comunicación y trabajo en equipo.',
        'S' => 'Perfil de alta Estabilidad: Personas pacientes, leales y consistentes. Excelentes para trabajos rutinarios, son buenos oyentes y mantienen la calma bajo presión. Valoran la seguridad y la armonía.',
        'C' => 'Perfil de alto Cumplimiento: Personas precisas, metódicas y detallistas. Excelentes para trabajos que requieren exactitud y análisis. Suelen ser perfeccionistas y seguir procedimientos establecidos.'
    ];
    return $descripciones[$perfil] ?? 'Descripción no disponible para este perfil.';
}

function generarRecomendaciones($estudiante) {
    $perfil = $estudiante['perfil_dominante'];
    $fortalezas = [
        'D' => "Fortalezas destacadas:\n• Liderazgo y toma de decisiones\n• Enfoque en resultados\n• Capacidad para resolver problemas rápidamente\n\n",
        'I' => "Fortalezas destacadas:\n• Habilidades sociales y comunicación\n• Trabajo en equipo\n• Capacidad de motivación\n\n",
        'S' => "Fortalezas destacadas:\n• Paciencia y consistencia\n• Trabajo bajo presión\n• Capacidad de escucha\n\n",
        'C' => "Fortalezas destacadas:\n• Análisis detallado\n• Precisión en el trabajo\n• Atención al detalle\n\n"
    ];
    
    $areas = [
        'D' => "Áreas de desarrollo:\n1. Practicar la paciencia y escucha activa\n2. Considerar diferentes perspectivas antes de decidir\n3. Delegar tareas de manera efectiva\n4. Controlar la impulsividad en situaciones tensas",
        'I' => "Áreas de desarrollo:\n1. Enfocarse en completar tareas antes de empezar nuevas\n2. Mejorar organización y planificación\n3. Controlar el entusiasmo en situaciones formales\n4. Desarrollar mayor atención a los detalles",
        'S' => "Áreas de desarrollo:\n1. Expresar opiniones con mayor asertividad\n2. Adaptarse a cambios graduales\n3. Establecer límites claros\n4. Tomar decisiones con mayor rapidez cuando sea necesario",
        'C' => "Áreas de desarrollo:\n1. Practicar flexibilidad ante cambios\n2. Priorizar lo importante sobre lo perfecto\n3. Compartir análisis de forma más concisa\n4. Aceptar que algunos errores son parte del aprendizaje"
    ];
    
    $general = "\n\nEste informe ha sido generado automáticamente por el sistema de evaluación. Para una interpretación más detallada, se recomienda consultar con un especialista en psicometría.";
    
    return ($fortalezas[$perfil] ?? '') . ($areas[$perfil] ?? '') . $general;
}

// Salida del PDF
$pdf->Output('Informe_DISC_' . preg_replace('/[^a-z0-9]/i', '_', $estudiante['nombre']) . '.pdf', 'D');
