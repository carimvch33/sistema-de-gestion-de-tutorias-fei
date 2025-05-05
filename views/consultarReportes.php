<?php
require_once '../config/config.php';

if(!isset($carreras)) {
    $carreras = [];
}
if(!isset($reportes)) {
    $reportes = [];
}
if(!isset($user)) {
    $user = '';
}
if(!isset($menu)) {
    $menu = BASE_URL . '/cerrarSesion.php';
}
if(!isset($errors)) {
    $errors = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta de Reportes de Tutorías</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/consultarReportes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="<?= BASE_URL; ?>/libs/DataTables/datatables.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <div class="header-container">
            <div class="header-left">
                <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
                <div class="welcome-message">Bienvenid@ <?= htmlspecialchars($user); ?></div>
            </div>
            <div class="header-right">
                <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
                <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
            </div>
        </div>
    </header>

    <?php if (!empty($errors)): ?>
        <div class="error-container">
            <?php foreach ($errors as $error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="actions-container">
        <?php if (!$muestraActual): ?>
            <button class="blueButton" onclick="location.href='<?= BASE_URL; ?>/consultarReportes.php'">
                <i class="fas fa-calendar"></i> Período Actual
            </button>
        <?php else: ?>
            <button class="blueButton" onclick="location.href='<?= BASE_URL; ?>/consultarHistorialReportes.php'">
                <i class="fas fa-history"></i> Ver Historial de Reportes
            </button>
        <?php endif; ?>
        <button class="greenButton" id="btnExportarConcentrado"><i class="fas fa-file-alt"></i> Exportar concentrado de reportes</button>
    </div>

    <div class="table-container">
        <table id="reportesTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="autoWidthColumn">Tutor</th>
                    <th class="autoWidthColumn">Carrera</th>
                    <th class="autoWidthColumn">Periodo</th>
                    <th class="autoWidthColumn">Fecha de inicio</th>
                    <th class="autoWidthColumn">Fecha de fin</th>
                    <th class="autoWidthColumn">Comentario</th>
                    <th class="autoWidthColumn">Problemática</th>
                    <th class="autoWidthColumn">Fecha de creación</th>
                    <th class="autoWidthColumn">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reportes)): ?>
                    <?php foreach ($reportes as $row): ?>
                        <?php
                        $idReporte = $row['idReporte'] ?? '';
                        $carrera = $row['carrera'] ?? 'Sin carrera';
                        $periodo = $row['periodo'] ?? 'Sin periodo';
                        $fechaInicioTutoria = $row['fechaInicioTutoria'] ?? 'Sin fecha de inicio';
                        $fechaFinTutoria = $row['fechaFinTutoria'] ?? 'Sin fecha de cierre';
                        $numTutoria = $row['numTutoria'] ?? 'Sin número de tutoría';
                        $numRiesgo = $row['numRiesgo'] ?? 'Sin número de alumnos en riesgo';
                        $comentarioCompleto = htmlspecialchars($row['comentario'] ?? '', ENT_QUOTES, 'UTF-8');
                        $tieneProblematica = $row['tieneProblematica'] ?? 0;
                        $fechaCreacion = $row['fechaCreacion'] ?? 'Sin fecha de creación';
                        $tutor = $row['tutorNombre'] ?? 'Sin tutor';

                        $problematica = $tieneProblematica > 0 ? "Sí existen problemáticas" : "No existen problemáticas";

                        $comentarioCorto = strlen($row['comentario'] ?? '') > 50
                        ? htmlspecialchars(substr($row['comentario'], 0, 50), ENT_QUOTES, 'UTF-8') . "..."
                        : $comentarioCompleto;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($tutor) ?></td>
                            <td><?= htmlspecialchars($carrera) ?></td>
                            <td><?= htmlspecialchars($periodo) ?></td>
                            <td><?= htmlspecialchars($fechaInicioTutoria) ?></td>
                            <td><?= htmlspecialchars($fechaFinTutoria) ?></td>
                            <td title="<?= $comentarioCompleto ?>"><?= $comentarioCorto ?></td>
                            <td><?= htmlspecialchars($problematica) ?></td>
                            <td><?= htmlspecialchars($fechaCreacion) ?></td>
                            <td class='action-buttons autoTable'>
                                <button class="download" data-id-reporte="<?= htmlspecialchars($idReporte) ?>" title="Descargar PDF del reporte" alt="Descargar PDF"><i
                                        class="fas fa-download"></i></button>
                                <button class="view" data-id-reporte="<?= htmlspecialchars($idReporte) ?>"><i
                                        class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>© Universidad Veracruzana</footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= BASE_URL; ?>/libs/DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL; ?>/assets/js/consultarReportes.js"></script>
</body>