<?php
require_once '../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'];
$menu = BASE_URL . '/menu.php';

$tutorNombre = htmlspecialchars($tutoria['tutorNombre'] ?? '', ENT_QUOTES, 'UTF-8');
$carrera = htmlspecialchars($carrera['carrera'] ?? '', ENT_QUOTES, 'UTF-8');
$numTutoria = htmlspecialchars($numTutoria ?? '', ENT_QUOTES, 'UTF-8');
$periodo = htmlspecialchars($periodo['periodo'] ?? '', ENT_QUOTES, 'UTF-8'); 
$modalidad = htmlspecialchars($modalidad ?? '', ENT_QUOTES, 'UTF-8');
$lugar = htmlspecialchars($tutoria['lugar'] ?? '', ENT_QUOTES, 'UTF-8');
$fechaInicio = htmlspecialchars($tutoria['fechaInicio'] ?? '', ENT_QUOTES, 'UTF-8');
$fechaFin = htmlspecialchars($tutoria['fechaFin'] ?? '', ENT_QUOTES, 'UTF-8');
$notas = htmlspecialchars($tutoria['nota'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Sesión de Tutoría en <?php echo $lugar?></title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/editarTutoria.css">
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
            <button class="buttonsHead" onclick="window.history.back();"><i class="fas fa-arrow-left"></i>
                Regresar</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-3">
        <div class="card p-4 shadow-sm">
            <h3 class="mb-4">Detalles de la Sesión de Tutoría</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Tutor:</strong> <?php echo $tutorNombre; ?></li>
                <li class="list-group-item"><strong>Carrera:</strong> <?php echo $carrera; ?></li>
                <li class="list-group-item"><strong>Número de tutoría:</strong> <?php echo $numTutoria; ?></li>
                <li class="list-group-item"><strong>Periodo:</strong> <?php echo $periodo; ?></li>
                <li class="list-group-item"><strong>Modalidad:</strong> <?php echo $modalidad; ?></li>
                <li class="list-group-item"><strong>Lugar:</strong> <?php echo $lugar; ?></li>

                <li class="list-group-item"><strong>Fecha:</strong>
                    <?php
                    if ($fechaInicio === $fechaFin) {
                        echo $fechaInicio;
                    } else {
                        echo "Del $fechaInicio al $fechaFin";
                    }
                    ?>
                </li>

                <?php if (!empty($notas)): ?>
                    <li class="list-group-item"><strong>Notas:</strong> <?php echo nl2br($notas); ?></li>
                <?php endif; ?>
                <?php if (!empty($tutoria['archivo'])): ?>
                    <li class="list-group-item">
                        <strong>Archivo:</strong> 
                        <?php echo htmlspecialchars($tutoria['archivo']); ?>
                        <a href="<?= BASE_URL; ?>/uploads/<?= $tutoria['archivo']; ?>" download>Descargar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>