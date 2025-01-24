<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro de Secciones</title>
    <link rel="stylesheet" href="assets/css/registroSeccion.css">
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
            <button class="buttonsHead" onclick="location.href='./administrarSecciones.php'"><i
                    class="fas fa-arrow-left"></i> Regresar</button>
            <button class="buttonsHead" onclick="location.href='/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
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

        <form action="/crearSeccion.php" method="post" enctype="multipart/form-data" id="form">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="nrc">NRC de la Sección: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nrc" name="nrc"
                    placeholder="NRC de la Sección (máximo 10 caracteres)" maxlength="10">
            </div>

            <div class="form-group">
                <label for="idProfesor">Profesor: <span class="text-danger">*</span></label>
                <select class="form-control" id="idProfesor" name="idProfesor">
                    <option value="" disabled selected>-----Selecciona un profesor-----</option>
                    <?php foreach ($profesores as $profesor) { ?>
                        <option value="<?php echo $profesor['idTutor']; ?>">
                            <?php echo htmlspecialchars($profesor['profesorNombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="idExperienciaEducativa">Experiencia Educativa: <span class="text-danger">*</span></label>
                <select class="form-control" id="idExperienciaEducativa" name="idExperienciaEducativa">
                    <option value="" disabled selected>-----Selecciona una experiencia educativa-----</option>
                    <?php foreach ($experiencias as $experiencia) { ?>
                        <option value="<?php echo $experiencia['idExperienciaEducativa']; ?>">
                            <?php echo htmlspecialchars($experiencia['nombreEE']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="idPeriodo">Periodo: <span class="text-danger">*</span></label>
                <select class="form-control" id="idPeriodo" name="idPeriodo">
                    <option value="" disabled selected>-----Selecciona un periodo-----</option>
                    <?php foreach ($periodos as $periodo) { ?>
                        <option value="<?php echo $periodo['idPeriodo']; ?>">
                            <?php echo htmlspecialchars($periodo['periodo']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Sección</button>
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
    <script src="assets/js/registroSeccion.js"></script>
</body>

</html>