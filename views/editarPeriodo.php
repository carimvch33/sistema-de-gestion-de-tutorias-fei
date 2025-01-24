<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Periodo Escolar</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/editarPeriodo.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>

    <div class="header-container">
        <div class="header-left">
            <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarPeriodosEscolares.php'"><i
                    class="fas fa-arrow-left"></i> Regresar</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-5">
        <?php
        if (isset($_SESSION['errors'])) {
            echo '<div class="alert alert-danger">';
            foreach ($_SESSION['errors'] as $error) {
                echo "<p class='error'>$error</p>";
            }
            echo '</div>';
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success">';
            echo "<p class='success'>{$_SESSION['message']}</p>";
            echo '</div>';
            unset($_SESSION['message']);
        }

        $periodoData = $_SESSION['periodo'];
        ?>

        <form action="<?= BASE_URL; ?>/actualizarPeriodo.php" method="POST" enctype="multipart/form-data" id="form">

            <input type="hidden" name="idPeriodo" value="<?php echo htmlspecialchars($periodoData['idPeriodo']); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="periodo">Nombre de periodo: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="periodo" name="periodo"
                    placeholder="Nombre de periodo máximo 80 caracteres" maxlength="80"
                    value="<?php echo htmlspecialchars($periodoData['periodo']); ?>">
            </div>

            <div class="form-group">
                <label for="actual">Periodo actual: <span class="text-danger">*</span></label>
                <select class="form-control" name="actual" id="actual">
                    <option value="" disabled>-----Selecciona si el periodo es el periodo actual o no-----</option>
                    <option value="1" <?= ($periodoData['actual'] == 1) ? 'selected' : '' ?>>Si</option>
                    <option value="0" <?= ($periodoData['actual'] == 0) ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>
    
    <footer>
        @ Universidad Veracruzana
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL; ?>/assets/js/editarPeriodo.js"></script>
</body>

</html>