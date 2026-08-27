<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro de Periodos</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/registroPeriodo.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarPeriodosEscolares.php'"><i class="fas fa-arrow-left"></i> Regresar</button>
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

        <form action="<?= BASE_URL; ?>/crearPeriodo.php" method="post" enctype="multipart/form-data" id="form">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="periodo">Nombre de periodo: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="periodo" name="periodo" placeholder="Nombre de periodo máximo 80 caracteres" maxlength="80">
            </div>

            <div class="form-group">
                <label for="actual">Periodo actual: <span class="text-danger">*</span></label>
                <input type="hidden" name="actual" value="0">
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="actual" name="actual" value="1">
                    <label class="form-check-label" for="actual">
                        Marcar si es el periodo actual
                    </label>
                </div>
            </div>

            <div class="form-section mt-4">
                <h4 class="text-primary border-bottom pb-2">Programación de Sesiones</h4>
                <p class="text-secondary small">Define las fechas para las 3 sesiones de tutoría. Se aplicarán a todas las carreras automáticamente.</p>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Sesión 1</label>
                    </div>
                    <div class="col-md-4">
                        <label for="fechaInicio1">Fecha de inicio:</label>
                        <input type="date" class="form-control" id="fechaInicio1" name="fechaInicio1" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fechaFin1">Fecha de fin:</label>
                        <input type="date" class="form-control" id="fechaFin1" name="fechaFin1" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Sesión 2</label>
                    </div>
                    <div class="col-md-4">
                        <label for="fechaInicio2">Fecha de inicio:</label>
                        <input type="date" class="form-control" id="fechaInicio2" name="fechaInicio2" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fechaFin2">Fecha de fin:</label>
                        <input type="date" class="form-control" id="fechaFin2" name="fechaFin2" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Sesión 3</label>
                    </div>
                    <div class="col-md-4">
                        <label for="fechaInicio3">Fecha de inicio:</label>
                        <input type="date" class="form-control" id="fechaInicio3" name="fechaInicio3" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fechaFin3">Fecha de fin:</label>
                        <input type="date" class="form-control" id="fechaFin3" name="fechaFin3" required>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Periodo</button>
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
    <script src="<?= BASE_URL; ?>/assets/js/registroPeriodo.js"></script>
</body>

</html>