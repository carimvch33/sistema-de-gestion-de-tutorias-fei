<?php
require_once '../config/config.php';
// Aseguramos que las variables necesarias están definidas
if (!isset($carreras)) {
    $carreras = [];
}
if (!isset($reportes)) {
    $reportes = [];
}
if (!isset($user)) {
    $user = '';
}
if (!isset($menu)) {
    $menu = BASE_URL . '/cerrarSesion.php';
}
if (!isset($errors)) {
    $errors = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administración de Reportes de Tutoría</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/administrarReportes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="<?= BASE_URL; ?>/libs/DataTables/datatables.min.css" rel="stylesheet">
</head>

<body>
    <div class="header-container">
        <div class="header-left">
            <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-container">
            <?php foreach ($errors as $error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="actions-container">
        <div class="chips-container">
            <?php if(!empty($carreras) && count($carreras) > 1 && count($reportes) > 1): ?>
                <p>Mostrar carreras:</p>
                <?php foreach($carreras as $carrera): ?>
                    <div class="chip" data-carrera="<?= htmlspecialchars($carrera) ?>">
                        <?= htmlspecialchars($carrera) ?>
                    </div>
                <?php endforeach; ?>
                <button id="clearFilters" class="buttonClear" style="display: none;"><i class="fas fa-times"></i></button>
                <?php endif; ?>
            </div>

            <?php if (!$muestraActual) : ?>
                <button class="buttonNew" onclick="location.href='<?= BASE_URL; ?>/administrarReportes.php'">
                    <i class="fas fa-calendar"></i> Período Actual
                </button>
            <?php else : ?>
                <button class="buttonHistory" onclick="location.href = '<?= BASE_URL; ?>/historialReportes.php' "><i class="fas fa-history"></i>
                Historial</button>
                <button class="buttonNew" onclick="location.href = '<?= BASE_URL; ?>/registroReporte.php' "><i class="fas fa-plus"></i>
                Nuevo</button>
            <?php endif; ?>
    </div>

    <div class="table-container">
        <table id="reportesTable">
            <thead>
                <tr>
                    <th class="autoWidthColumn">Carrera</th>
                    <th class="autoWidthColumn">Periodo</th>
                    <th class="autoWidthColumn">Fecha de inicio de tutoría</th>
                    <th class="autoWidthColumn">Fecha de fin de tutoría</th>
                    <th class="autoWidthColumn">Comentario</th>
                    <th class="autoWidthColumn">Problemática</th>
                    <th class="autoWidthColumn">Fecha de creación</th>
                    <th class="autoWidthColumn">Acción</th>
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

                        $problematica = $tieneProblematica > 0 ? "Sí existen problemáticas" : "No existen problemáticas";

                        $comentarioCorto = strlen($row['comentario'] ?? '') > 50
                            ? htmlspecialchars(substr($row['comentario'], 0, 50), ENT_QUOTES, 'UTF-8') . "..."
                            : $comentarioCompleto;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($carrera) ?></td>
                            <td><?= htmlspecialchars($periodo) ?></td>
                            <td><?= htmlspecialchars($fechaInicioTutoria) ?></td>
                            <td><?= htmlspecialchars($fechaFinTutoria) ?></td>
                            <td title="<?= $comentarioCompleto ?>"><?= $comentarioCorto ?></td>
                            <td><?= htmlspecialchars($problematica) ?></td>
                            <td><?= htmlspecialchars($fechaCreacion) ?></td>
                            <td class='action-buttons autoTable'>
                                <button class='edit' data-id-reporte='<?= htmlspecialchars($idReporte) ?>'><i
                                        class='fas fa-edit'></i></button>
                                <button class='delete' data-id-reporte='<?= htmlspecialchars($idReporte) ?>'
                                    data-csrf-token='<?= htmlspecialchars($_SESSION['csrf_token']) ?>'><i
                                        class='fas fa-trash-alt'></i></button>
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
    <script src="<?= BASE_URL; ?>/assets/js/administrarReportes.js"></script>
</body>

</html>