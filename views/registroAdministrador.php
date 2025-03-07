<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro de Administradores</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/registroAdministrador.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarAdministradores.php'"><i
                    class="fas fa-arrow-left"></i> Regresar</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-3">
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

        <form action="<?= BASE_URL; ?>/crearAdministrador.php" method="post" enctype="multipart/form-data" id="form">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="rol" value="<?= htmlspecialchars($rol) ?>">

            <div class="form-group">
                <label for="nombre">Nombre de administrador: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    placeholder="Nombre de administrador máximo 70 caracteres" maxlength="70">
            </div>

            <div class="form-group">
                <label for="paterno">Apellido paterno:</label>
                <input type="text" class="form-control" id="paterno" name="paterno"
                    placeholder="Apellido paterno máximo 70 caracteres" maxlength="70">
            </div>

            <div class="form-group">
                <label for="materno">Apellido materno:</label>
                <input type="text" class="form-control" id="materno" name="materno"
                    placeholder="Apellido materno máximo 70 caracteres" maxlength="70">
            </div>

            <div class="form-group">
                <label for="correoInstitucional">Correo institucional: <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="correoInstitucional" name="correoInstitucional"
                    placeholder="Correo institucional máximo 50 caracteres" maxlength="50">
            </div>

            <div class="form-group">
                <label for="password">Contraseña: <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña"
                    maxlength="255">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña: <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                    placeholder="Confirmar Contraseña" maxlength="255">
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Administrador</button>
                </div>
            </div>
        </form>
    </div>
    <footer>
        © Universidad Veracruzana
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL; ?>/assets/js/registroAdministrador.js"></script>
</body>

</html>