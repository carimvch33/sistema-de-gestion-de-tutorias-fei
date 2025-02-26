<?php

require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rolesPermitidos = [1, 4];
if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
    header('Location: ' . BASE_URL . '/cerrarSesion.php');
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user = $_SESSION['user'];
$menu = BASE_URL . '/menu.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta de Tutorías UV</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/tutorias.css">
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
            <button class="buttonsHead" onclick="location.href='<?php echo $menu; ?>'"><i class="fas fa-home"></i>
                Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="new-button-container">
        <button class="buttonNew" onclick="location.href = '<?= BASE_URL; ?>/registrarTutoria.php' "><i class="fas fa-plus"></i>
            Nuevo</button>
    </div>

    <div class="table-container">
        <table id="tutoriasTable">
            <thead>
                <tr>
                    <th class="autoWidthColumn">Periodo</th>
                    <th class="autoWidthColumn">Carrera</th>
                    <th class="autoWidthColumn">Tutoría</th>
                    <th class="autoWidthColumn">Fecha</th>
                    <th class="autoWidthColumn">Horario</th>
                    <th class="autoWidthColumn">Lugar</th>
                    <th class="autoWidthColumn">Nota</th>
                    <th class="autoWidthColumn">Archivo</th>
                    <th class="autoWidthColumn">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $idTutoria = $row['idTutoria'];
                        $archivo = $row['archivo'];


                        $lugarCompleto = htmlspecialchars($row['lugar'] ?? '', ENT_QUOTES, 'UTF-8');
                        $lugarCorto = strlen($row['lugar'] ?? '') > 20
                            ? htmlspecialchars(substr($row['lugar'], 0, 20), ENT_QUOTES, 'UTF-8') . "..."
                            : $lugarCompleto;

                        $notaCompleto = htmlspecialchars($row['nota'] ?? '', ENT_QUOTES, 'UTF-8');
                        $notaCorto = strlen($row['nota'] ?? '') > 20
                            ? htmlspecialchars(substr($row['nota'], 0, 20), ENT_QUOTES, 'UTF-8') . "..."
                            : $notaCompleto;

                        $archivoRuta = './uploads/' . $archivo;

                        echo "<tr>
                            <td>{$row['periodo']}</td>
                            <td>{$row['carrera']}</td>
                            <td>{$row['tutoria']}</td>
                            <td>{$row['fecha']}</td>
                            <td>{$row['horario']}</td>
                            <td title=\"{$lugarCompleto}\">{$lugarCorto}</td>
                            <td title=\"{$notaCompleto}\">{$notaCorto}</td>";
                        if (!empty($archivo)) {
                            echo "<td><a href='uploads/{$archivo}' download>Descargar</a></td>";
                        } else {
                            echo "<td>No disponible</td>";
                        }
                        echo "<td class='action-buttons autoTable'>
                                <button class='edit' data-id-tutoria='{$idTutoria}'><i class='fas fa-edit'></i></button>
                                <button class='delete' data-id-tutoria='{$idTutoria}' data-csrf-token='{$_SESSION['csrf_token']}'><i class='fas fa-trash-alt'></i></button>
                                <button class='view' data-id-tutoria='{$idTutoria}'><i class='fas fa-eye'></i></button>
                              </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <footer>© Universidad Veracruzana</footer>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL; ?>/libs/DataTables/datatables.min.js"></script>
<script src="<?= BASE_URL; ?>/assets/js/tutorias.js"></script>

</html>