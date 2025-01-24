
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Actualización de Rol</title>
    <link rel="stylesheet" href="assets/css/actualizarRol.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <div class="header-container">
        <div class="header-left">
            <img src="assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='./menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='./cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="form-container">
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
        ?>

        <form action="/modificarRol.php" method="POST" id="form">
            <div class="form-group">

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <h1>Actualización de rol</h1>
                <label for="tutor">Profesores: <span class="text-danger">*</span></label>
                <select class="form-control" id="tutor" name="tutor">
                    <option value="" disabled selected>-----Selecciona un profesor-----</option>
                    <?php foreach ($tutores as $tutor): ?>
                        <option value="<?= htmlspecialchars($tutor['sesion']) ?>">
                            <?= htmlspecialchars($tutor['tutorNombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="rol">Roles: <span class="text-danger">*</span></label>
                <select class="form-control" id="rol" name="rol">
                    <option value="" disabled selected>-----Selecciona el nuevo rol para el profesor-----</option>
                    <option value="1">Tutor</option>
                    <option value="4">Coordinador de Tutorías</option>
                    <option value="5">Jefe de Carrera</option>
                </select>
            </div>

            <div class="form-group" id="carreras-container" style="display: none;">
                <label for="carreras">Carreras a coordinar: <span class="text-danger">*</span></label>
                <select class="form-control" id="carreras" name="carreras[]" multiple="multiple">
                    <?php foreach ($carreras as $carrera): ?>
                        <option value="<?= htmlspecialchars($carrera['idCarrera']) ?>">
                            <?= htmlspecialchars($carrera['carrera']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Mantén presionada la tecla Ctrl (Cmd en Mac) para seleccionar
                    múltiples opciones.</small>
            </div>

            <button id="enviar" name="enviar" type="submit" class="button buttonAction">Actualizar Rol</button>
        </form>
    </div>

    <footer>
        @ Universidad Veracruzana
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/actualizarRol.js"></script>
</body>

</html>