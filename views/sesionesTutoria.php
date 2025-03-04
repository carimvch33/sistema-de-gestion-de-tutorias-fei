<?php
require_once '../config/config.php';
if (!isset($user))
    $user = '';
if (!isset($sesiones))
    $sesiones = [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Sesiones de Tutoría - Tutorado</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/sesionesTutoria.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="table-container">
        <table id="tutoriasTable">
            <thead>
                <tr>
                    <th class="autoWidthColumn">Tutor</th>
                    <th class="autoWidthColumn">Carrera</th>
                    <th class="autoWidthColumn">Tutoría</th>
                    <th class="autoWidthColumn">Fecha</th>
                    <th class="autoWidthColumn">Horario</th>
                    <th class="autoWidthColumn">Lugar</th>
                    <th class="autoWidthColumn">Nota</th>
                    <th class="autoWidthColumn">Archivo</th>
                    <th class="autoWidthColumn">Ver Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sesiones)): ?>
                    <?php foreach ($sesiones as $sesion): ?>
                        <?php
                        $lugarCompleto = htmlspecialchars($sesion['lugar'] ?? '', ENT_QUOTES, 'UTF-8');
                        $lugarCorto = strlen($lugarCompleto) > 30 ? substr($lugarCompleto, 0, 30) . '...' : $lugarCompleto;

                        $notaCompleto = htmlspecialchars($sesion['nota'] ?? '', ENT_QUOTES, 'UTF-8');
                        $notaCorto = strlen($notaCompleto) > 30 ? substr($notaCompleto, 0, 30) . '...' : $notaCompleto;

                        $archivoRuta = 'uploads/' . htmlspecialchars($sesion['archivo'] ?? '', ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($sesion['tutorNombre'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($sesion['carrera'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($sesion['tutoria'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($sesion['fecha'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($sesion['horario'] ?? ''); ?></td>
                            <td title="<?= $lugarCompleto; ?>"><?= $lugarCorto; ?></td>
                            <td class="white-space: normal; word-wrap: break-word; word-break: break-word;" title="<?= $notaCompleto; ?>"><?= $notaCorto; ?></td>
                            <td>
                                <?php if (!empty($sesion['archivo'])): ?>
                                    <a href="<?= $archivoRuta; ?>" download>Descargar</a>
                                <?php else: ?>
                                    No disponible
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <button class="view-button" data-id-tutoria='<?= $sesion['idTutoria']; ?>'><i class='fas fa-eye'></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-center">No hay sesiones de tutoría disponibles</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        © Universidad Veracruzana
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= BASE_URL; ?>/libs/DataTables/datatables.min.js"></script>
    <script src="<?= BASE_URL; ?>/assets/js/sesionesTutoria.js"></script>
</body>

</html>