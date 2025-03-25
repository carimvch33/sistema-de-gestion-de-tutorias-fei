<?php
require_once '../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'];
$menu = BASE_URL . '/menu.php';

$fechaInicio = htmlspecialchars($reporte['fechaInicioTutoria'] ?? '', ENT_QUOTES, 'UTF-8');
$fechaFin = htmlspecialchars($reporte['fechaFinTutoria'] ?? '', ENT_QUOTES, 'UTF-8');
$numTutoria = htmlspecialchars($reporte['numTutoria'] ?? '', ENT_QUOTES, 'UTF-8');
$numAsistencia = htmlspecialchars($reporte['numAsistencia'] ?? '', ENT_QUOTES, 'UTF-8');
$numRiesgo = htmlspecialchars($reporte['numRiesgo'] ?? '', ENT_QUOTES, 'UTF-8');
$comentario = htmlspecialchars($reporte['comentario'] ?? '', ENT_QUOTES, 'UTF-8');
$carrera = htmlspecialchars($reporte['nombreCarrera'] ?? '', ENT_QUOTES, 'UTF-8');
$periodo = htmlspecialchars($reporte['nombrePeriodo'] ?? '', ENT_QUOTES, 'UTF-8');
$tutor = htmlspecialchars($reporte['nombreTutor'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Tutoría></title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/editarReporteTutoria.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="header-container">
        <div class="header-left">
            <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="window.history.back();"><i class="fas fa-arrow-left"></i> Regresar</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-3">
        <div class="card p-4 shadow-sm">
            <h3 class="mb-4">Detalles del Reporte de Tutoría</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Periodo:</strong> <?php echo $periodo; ?></li>
                <li class="list-group-item"><strong>Carrera:</strong> <?php echo $carrera; ?></li>
                <li class="list-group-item"><strong>Tutor:</strong> <?php echo $tutor; ?></li>
                <li class="list-group-item"><strong>Fecha de inicio:</strong> <?php echo $fechaInicio; ?></li>
                <li class="list-group-item"><strong>Fecha de fin:</strong> <?php echo $fechaFin; ?></li>
                <li class="list-group-item"><strong>Número de tutoría:</strong> <?php echo $numTutoria; ?></li>
                <li class="list-group-item"><strong>Asistencias:</strong> <?php echo $numAsistencia; ?></li>
                <li class="list-group-item"><strong>Casos de riesgo:</strong> <?php echo $numRiesgo; ?></li>
                <?php if (!empty($problematicasReporte)): ?>
                    <li class="list-group-item"><strong>Problemáticas:</strong>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="autoWidthColumn">Experiencia Educativa</th>
                                    <th class="autoWidthColumn">Profesor</th>
                                    <th class="autoWidthColumn">Problemática</th>
                                    <th class="autoWidthColumn">Número de alumnos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($problematicasReporte as $index => $problematica) {
                                    echo "<tr>";
                                    $nombreExperiencia = '';
                                    foreach ($experiencias as $exp) {
                                        if ($exp['idExperienciaEducativa'] == $problematica['experienciaEducativa']) {
                                            $nombreExperiencia = $exp['nombre'];
                                            break;
                                        }
                                    }
                                    echo '<td>' . htmlspecialchars($nombreExperiencia, ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td>' . htmlspecialchars($profesores[$problematica['profesor']]['tutorNombre'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td>' . htmlspecialchars($listaProblematicas[$problematica['problematica']]['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td>' . htmlspecialchars($problematica['numAlumnos'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </li>
                    <?php endif; ?>
                </ul>
                <?php if (!empty($comentario)): ?>
                    <li class="list-group-item"><strong>Comentario:</strong> <?php echo nl2br($comentario); ?></li>
                <?php endif; ?>
            </div>
        </div>

</body>

</html>