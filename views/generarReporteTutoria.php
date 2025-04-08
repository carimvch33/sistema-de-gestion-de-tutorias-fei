<?php
ob_start();
require('../vendor/fpdf/fpdf.php');
require_once '../config/config.php';

$user = $_SESSION['user'];
$idTutoria = $_POST['idTutoria'] ?? $_GET['idTutoria'];
$fechaInicio = htmlspecialchars($reporte['fechaInicioTutoria'] ?? '', ENT_QUOTES, 'UTF-8');
$fechaFin = htmlspecialchars($reporte['fechaFinTutoria'] ?? '', ENT_QUOTES, 'UTF-8');
$numTutoria = htmlspecialchars($reporte['numTutoria'] ?? '', ENT_QUOTES, 'UTF-8');
$numAsistencia = htmlspecialchars($reporte['numAsistencia'] ?? '', ENT_QUOTES, 'UTF-8');
$numRiesgo = htmlspecialchars($reporte['numRiesgo'] ?? '', ENT_QUOTES, 'UTF-8');
$comentario = htmlspecialchars($reporte['comentario'] ?? '', ENT_QUOTES, 'UTF-8');
$carrera = htmlspecialchars($reporte['nombreCarrera'] ?? '', ENT_QUOTES, 'UTF-8');
$periodo = htmlspecialchars($reporte['nombrePeriodo'] ?? '', ENT_QUOTES, 'UTF-8');
$tutor = htmlspecialchars($reporte['nombreTutor'] ?? '', ENT_QUOTES, 'UTF-8');
$fechaCreacion = htmlspecialchars($reporte['fechaCreacion'] ?? '', ENT_QUOTES, 'UTF-8');

$problematicasReporte = $problematicasReporte ?? [];
$experiencias = $experiencias ?? [];
$profesores = $profesores ?? [];
$listaProblematicas = $listaProblematicas ?? [];

class PDF extends FPDF {
    function Header() {
        $this->Image('../public/assets/img/UV.jpg', 15, 10, 30);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, mb_convert_encoding('FACULTAD DE ESTADÍSTICA E INFORMÁTICA', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->Cell(0, 10, mb_convert_encoding('REGIÓN XALAPA', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo(), 0, 0, 'C');
    }

    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }    
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 10, mb_convert_encoding("Reporte General de Tutoría ", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);

$yInicio = $pdf->GetY();
$colWidth1 = 130;
$colWidth2 = 70;
$lineHeight = 6; 

$programaEducativoContent = mb_convert_encoding('Programa Educativo: ' . $carrera, 'ISO-8859-1', 'UTF-8');

if (strlen($programaEducativoContent) < 66) {
    $pdf->MultiCell($colWidth1, $lineHeight * 2, $programaEducativoContent, 1);
} else {
    $pdf->MultiCell($colWidth1, $lineHeight, $programaEducativoContent, 1);
}
$yFinal1 = $pdf->GetY();

$pdf->SetXY(140, $yInicio); 
$pdf->MultiCell($colWidth3, $lineHeight * 2, mb_convert_encoding('No. de sesión: ' . $numTutoria . '° Tutoría', 'ISO-8859-1', 'UTF-8'), 1);
$yFinal2 = $pdf->GetY(); 

$maxHeight = max($yFinal1, $yFinal2);

$pdf->SetY($maxHeight);
$yInicio = $pdf->GetY();
$colWidth3 = 60;
$colWidth4 = 60;
$colWidth5 = 70;

$periodoContent = mb_convert_encoding('Periodo: ' . $periodo, 'ISO-8859-1', 'UTF-8');

if (strlen($periodoContent) < 36) {
    $pdf->MultiCell($colWidth3 + 10, $lineHeight * 2, $periodoContent, 1);
} else {
    $pdf->MultiCell($colWidth3 + 10, $lineHeight, $periodoContent, 1);
}
$yFinal3 = $pdf->GetY();

$pdf->SetXY(80, $yInicio);
$pdf->MultiCell($colWidth3, $lineHeight, mb_convert_encoding('Fecha Primer día de tutoría: ' . $fechaInicio, 'ISO-8859-1', 'UTF-8'), 1);
$yFinal4 = $pdf->GetY();

$pdf->SetXY(140, $yInicio);
$pdf->MultiCell($colWidth3, $lineHeight, mb_convert_encoding('Fecha Último día de tutoría: ' . $fechaFin, 'ISO-8859-1', 'UTF-8'), 1);
$yFinal5 = $pdf->GetY();

$maxHeight2 = max($yFinal3, $yFinal4, $yFinal5);
$pdf->SetY($maxHeight2);

$pdf->Ln(5);
$pdf->SetX(20);
$pdf->Cell(0, 10, mb_convert_encoding('Núm. Alumnos que asistieron: ' . $numAsistencia, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
$pdf->SetX(120);
$pdf->Cell(0, 10, mb_convert_encoding('Núm. Alumnos en riesgo: ' . $numRiesgo, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

$pdf->Ln(5);
$pdf->SetX(20);
$pdf->Cell(0, 10, mb_convert_encoding("Problemas académicos detectados:", 'ISO-8859-1', 'UTF-8'), 0, 1);

if (!empty($problematicasReporte)) {    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(225,225,225);
    $pdf->Cell(40, 8, mb_convert_encoding('Experiencia Educativa', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $pdf->Cell(50, 8, mb_convert_encoding('Profesor', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $pdf->Cell(80, 8, mb_convert_encoding('Problemática', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $pdf->Cell(20, 8, mb_convert_encoding('Núm. Alum.', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetFillColor(255, 255, 255);

    foreach ($problematicasReporte as $problematica) {
        $nombreExperiencia = '';
        foreach ($experiencias as $exp) {
            if ($exp['idExperienciaEducativa'] == $problematica['experienciaEducativa']) {
                $nombreExperiencia = $exp['nombre'];
                break;
            }
        }
    
        $experienciaContent = mb_convert_encoding($nombreExperiencia, 'ISO-8859-1', 'UTF-8');
        $profesorContent = mb_convert_encoding($profesores[$problematica['profesor']]['tutorNombre'] ?? '', 'ISO-8859-1', 'UTF-8');
        $problematicaContent = mb_convert_encoding($listaProblematicas[$problematica['problematica']]['descripcion'] ?? '', 'ISO-8859-1', 'UTF-8');
        $numAlumnosContent = mb_convert_encoding($problematica['numAlumnos'] ?? '', 'ISO-8859-1', 'UTF-8');
    
        $w = [40, 50, 80, 20];
        $lineHeight = 6;
    
        $nb1 = $pdf->NbLines($w[0], $experienciaContent);
        $nb2 = $pdf->NbLines($w[1], $profesorContent);
        $nb3 = $pdf->NbLines($w[2], $problematicaContent);
        $nb4 = $pdf->NbLines($w[3], $numAlumnosContent);
        $nb = max($nb1, $nb2, $nb3, $nb4);
        $rowHeight = $lineHeight * $nb;
    
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $rowHeight = $lineHeight * $nb;

        $pdf->MultiCell($w[0], $lineHeight, $experienciaContent, 0, 'L');
        $pdf->SetXY($x + $w[0], $y);

        $pdf->MultiCell($w[1], $lineHeight, $profesorContent, 0, 'L');
        $pdf->SetXY($x + $w[0] + $w[1], $y);

        $pdf->MultiCell($w[2], $lineHeight, $problematicaContent, 0, 'L');
        $pdf->SetXY($x + $w[0] + $w[1] + $w[2], $y);

        $pdf->MultiCell($w[3], $lineHeight, $numAlumnosContent, 0, 'C');

        $pdf->Rect($x, $y, $w[0], $rowHeight);
        $pdf->Rect($x + $w[0], $y, $w[1], $rowHeight);
        $pdf->Rect($x + $w[0] + $w[1], $y, $w[2], $rowHeight);
        $pdf->Rect($x + $w[0] + $w[1] + $w[2], $y, $w[3], $rowHeight);

        $pdf->SetY($y + $rowHeight);
    }     
} else {
    $pdf->Cell(0, 10, mb_convert_encoding("No se detectaron problemáticas", 'ISO-8859-1', 'UTF-8'), 0, 1);
}

$pdf->Ln(5);
$pdf->SetX(20);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, mb_convert_encoding("Comentarios Generales:", 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetFillColor(225, 225, 225);
$pdf->Cell(190,5, mb_convert_encoding('Comentarios', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
$pdf->Ln();
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(255, 255, 255);
if ($comentario == '') {
    $comentario = 'No hay comentarios generales.';
}
$pdf->MultiCell(190, 5, $comentario, 1, 'L', true);
$pdf->Ln(5);

$pdf->Ln(10);
$pdf->Cell(0, 10, '', 0, 1); 

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Atentamente', 0, 1, 'C');

$fmt = new IntlDateFormatter(
    'es_ES',
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    null,
    null,
    "d 'de' MMMM 'de' yyyy"
);
$fechaFormateada = $fmt->format(new DateTime($fechaCreacion));

$pdf->Cell(0, 10, mb_convert_encoding("Xalapa, Ver., a $fechaFormateada", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');


$pdf->Ln(15);

$pdf->Cell(0, 10, mb_convert_encoding('Nombre del Tutor:', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->Cell(0, 10, $tutor, 0, 1, 'C');


ob_end_clean();
$pdf->Output('D', mb_convert_encoding("Reporte_Tutoria_$idTutoria.pdf", 'ISO-8859-1', 'UTF-8'));
exit;
?>
