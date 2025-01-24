<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro de Estudiantes</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/registroEstudiante.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarEstudiantes.php'"><i
                    class="fas fa-arrow-left"></i> Regresar</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
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

        <form action="<?= BASE_URL; ?>/crearEstudiante.php" method="post" enctype="multipart/form-data" id="form">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="nombre">Nombre de estudiante: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    placeholder="Nombre de estudiante máximo 70 caracteres" maxlength="70">
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
                <label for="matricula">Matrícula: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="matricula" name="matricula"
                    placeholder="Matrícula máximo 9 caracteres" maxlength="9">
                <p class="text-secondary">Ejem: S12345678 </p>
            </div>

            <div class="form-group">
                <label for="carrera">Carrera: <span class="text-danger">*</span></label>
                <select class="form-control" id="carrera" name="carrera">
                    <option value="" disabled selected>-----Selecciona una carrera-----</option>
                    <?php foreach ($carreras as $carreraOption) { ?>
                        <option value="<?php echo $carreraOption['idCarrera']; ?>">
                            <?php echo htmlspecialchars($carreraOption['carrera']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="correoInstitucional">Correo institucional: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="correoInstitucional" name="correoInstitucional"
                    placeholder="Correo institucional máximo 45 caracteres" maxlength="45">
                <p class="text-secondary">Ejem: zs12345678@estudiantes.uv.mx</p>
            </div>

            <div class="form-group">
                <label for="tutor">Tutor asignado:</label>
                <select class="form-control" id="tutor" name="tutor">
                    <option value="" disabled selected>-----Selecciona un tutor asignado al estudiante-----</option>
                    <?php foreach ($tutores as $tutorOption) { ?>
                        <option value="<?php echo $tutorOption['idTutor']; ?>">
                            <?php echo htmlspecialchars($tutorOption['profesorNombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Estudiante</button>
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
    <script src="<?= BASE_URL; ?>/assets/js/registroEstudiante.js"></script>
</body>

</html>