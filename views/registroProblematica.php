<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro de Problemáticas Académicas</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/registroProblematica.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarProblematicas.php'"><i class="fas fa-arrow-left"></i> Regresar</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-5">
        <?php
            if (isset($errors)) {
                echo '<div class="alert alert-danger">';
                foreach ($errors as $error) {
                    echo "<p class='error'>$error</p>";
                }
                echo '</div>';
            }

            if (isset($message)) {
                echo '<div class="alert alert-success">';
                echo "<p class='success'>{$message}</p>";
                echo '</div>';
            }
        ?>

        <form action="<?= BASE_URL; ?>/crearProblematica.php" method="post" enctype="multipart/form-data" id="form">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="descripcion">Descripción de la problemática: <span class="text-danger">*</span></label>
                <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción de la problemática máximo 500 caracteres" maxlength="500"></textarea>
            </div>

            <div class="form-group">
                <label for="tipoProblematica">Tipo de problemática: <span class="text-danger">*</span></label>
                <select class="form-control" id="tipoProblematica" name="tipoProblematica">
                    <option value="" disabled selected>-----Selecciona el tipo de problemática-----</option>
                    <?php foreach($tiposProblematica as $tipo) { ?>
                        <option value="<?php echo $tipo['idTipoProblematica']; ?>"><?php echo htmlspecialchars($tipo['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Problemática Académica</button>
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
    <script src="<?= BASE_URL; ?>/assets/js/registroProblematica.js"></script>
</body>

</html>