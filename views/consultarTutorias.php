<?php
require_once '../config/config.php';
if (!isset($user))
    $user = '';
if (!isset($tutorias))
    $tutorias = [];
if (!isset($csrf_token))
    $csrf_token = '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta de Tutorías UV</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/consultarTutorias.css">
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

    <div class="table-container">
        <table id="tutoriasTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="autoWidthColumn">Tutor</th>
                    <th class="autoWidthColumn">Carrera</th>
                    <th class="autoWidthColumn">Tutoría</th>
                    <th class="autoWidthColumn">Fecha</th>
                    <th class="autoWidthColumn">Lugar</th>
                    <th class="autoWidthColumn">Nota</th>
                    <th class="autoWidthColumn">Archivo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tutorias)): ?>
                    <?php foreach ($tutorias as $tutoria): ?>
                        <?php
                        $lugarCompleto = htmlspecialchars($tutoria['lugar'] ?? '', ENT_QUOTES, 'UTF-8');

                        $notaCompleto = htmlspecialchars($tutoria['nota'] ?? '', ENT_QUOTES, 'UTF-8');
                        $notaCorto = strlen($notaCompleto) > 30 ? substr($notaCompleto, 0, 30) . '...' : $notaCompleto;

                        $archivoRuta = '/uploads/' . htmlspecialchars($tutoria['archivo'] ?? '', ENT_QUOTES, 'UTF-8');

                        $fechaInicio = htmlspecialchars($tutoria['fechaInicio'] ?? '', ENT_QUOTES, 'UTF-8');
                        $fechaFin = htmlspecialchars($tutoria['fechaFin'] ?? '', ENT_QUOTES, 'UTF-8');
                        $fechaMostrar = ($fechaInicio && $fechaFin && $fechaInicio !== $fechaFin)
                            ? "Del $fechaInicio al $fechaFin"
                            : $fechaInicio;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($tutoria['tutorNombre'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($tutoria['carrera'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($tutoria['tutoria'] ?? ''); ?></td>
                            <td><?= $fechaMostrar; ?></td>
                            <td><?= $lugarCompleto; ?></td>
                            <td class="white-space: normal; word-wrap: break-word; word-break: break-word;" title="<?= $notaCompleto; ?>"><?= $notaCorto; ?></td>
                            <td>
                                <?php if (!empty($tutoria['archivo']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $archivoRuta)): ?>
                                    <a href="<?= $archivoRuta; ?>" download>Descargar</a>
                                <?php else: ?>
                                    No disponible
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif ?>
            </tbody>
        </table>
    </div>

    <footer>
        © Universidad Veracruzana
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= BASE_URL; ?>/libs/DataTables/datatables.min.js"></script>
    <script src="<?= BASE_URL; ?>/assets/js/consultarTutorias.js"></script>
</body>

</html>