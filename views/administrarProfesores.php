<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administración de profesores</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/administrarProfesores.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
        </div>
    </div>

    <!-- Contenedor de alertas -->
    <div class="container mt-3" style="width: 82%; margin: 0 auto;">
        <?php
        if (isset($_SESSION['errors'])) {
            echo '<div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 10px;">';
            foreach ($_SESSION['errors'] as $error) {
                echo "<p style='margin: 0;'>$error</p>";
            }
            echo '</div>';
            unset($_SESSION['errors']);
        }

        // ¡AQUÍ ESTÁ LA MAGIA! Cambiamos $_SESSION['message'] por $message
        if (isset($message)) {
            echo '<div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 10px;">';
            echo "<p style='margin: 0;'>{$message}</p>";
            echo '</div>';
        }
        ?>
    </div>

    <!-- Botones de Acción -->
    <div class="new-button-container" style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 20px;">
        
        <form action="<?= BASE_URL; ?>/importarProfesores.php" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 10px; margin: 0;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="file" name="csv_docentes" accept=".csv" required style="font-size: 14px;">
            <button type="submit" class="buttonNew" style="background-color: #17a2b8;" title="Sube un archivo CSV con las columnas NO., Maestro, Correo">
                <i class="fas fa-file-csv"></i> Importar CSV
            </button>
        </form>

        <button class="buttonNew" onclick="location.href = '<?= BASE_URL; ?>/registroProfesor.php' ">
            <i class="fas fa-plus"></i> Nuevo
        </button>
    </div>

    <div class="table-container">
        <table id="profesoresTable">
            <thead>
                <tr>
                    <th class="autoWidthColumn">Nombre de profesor</th>
                    <th class="autoWidthColumn">Número de personal</th>
                    <th class="autoWidthColumn">Correo institucional</th>
                    <th class="autoWidthColumn">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($profesores)) {
                    foreach ($profesores as $row) {
                        $idTutor = $row['idTutor'];
                        echo "<tr>
                                <td>{$row['tutorNombre']}</td>
                                <td>{$row['noPersonal']}</td>
                                <td>{$row['correoInstitucional']}</td>
                                <td class='action-buttons autoTable'>
                                    <button class='edit' data-id-tutor='{$idTutor}'><i class='fas fa-edit'></i></button>
                                    <button class='delete' data-id-tutor='{$idTutor}' data-csrf-token='{$csrf_token}'><i class='fas fa-trash-alt'></i></button>
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
    <script src="<?= BASE_URL; ?>/assets/js/administrarProfesores.js"></script>
</body>

</html>