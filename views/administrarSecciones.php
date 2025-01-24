<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administración de Secciones</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/administrarSecciones.css">
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

    <div class="new-button-container">
        <button class="buttonNew" onclick="location.href = '<?= BASE_URL; ?>/registroSeccion.php' "><i class="fas fa-plus"></i>
            Nuevo</button>
    </div>

    <div class="table-container">
        <table id="seccionesTable">
            <thead>
                <tr>
                    <th>NRC</th>
                    <th>Profesor</th>
                    <th>Experiencia Educativa</th>
                    <th>Periodo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($secciones)) {
                    foreach ($secciones as $row) {
                        $idSeccion = $row['id'];
                        echo "<tr>
                                <td>{$row['nrc']}</td>
                                <td>{$row['tutorNombre']}</td>
                                <td>{$row['experiencia']}</td>
                                <td>{$row['periodo']}</td>
                                <td class='action-buttons'>
                                    <button class='edit' data-id-seccion='{$idSeccion}'><i class='fas fa-edit'></i></button>
                                    <button class='delete' data-id-seccion='{$idSeccion}' data-csrf-token='{$csrf_token}'><i class='fas fa-trash-alt'></i></button>
                                </td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>© Universidad Veracruzana</footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= BASE_URL; ?>/libs/DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL; ?>/assets/js/administrarSecciones.js"></script>
</body>

</html>