<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Actualización de Experiencias Educativas</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/editarExperienciaEducativa.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarExperienciasEducativas.php'"><i
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

        $experienciaData = $_SESSION['experiencia'];
        ?>

        <form action="<?= BASE_URL; ?>/actualizarExperiencia.php" method="POST" enctype="multipart/form-data" id="form">

            <input type="hidden" name="idExperiencia"
                value="<?php echo htmlspecialchars($experienciaData['idExperienciaEducativa']); ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <h4 class="text-primary border-bottom pb-2 mt-5 mb-4">2. Datos de Asignación Obligatorios</h4>

            <div class="form-group">
                <label for="nrc">NRC de la Sección: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nrc" name="nrc"
                    placeholder="Ej. 98764" maxlength="10"
                    value="<?php echo htmlspecialchars($experienciaData['nrc'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="idProfesor">Profesor: <span class="text-danger">*</span></label>
                <select class="form-control" id="idProfesor" name="idProfesor" required>
                    <option value="" disabled>-----Selecciona un profesor-----</option>
                    <?php foreach ($profesores as $profesor) {
                        $selected = (isset($experienciaData['idProfesor']) && $experienciaData['idProfesor'] == $profesor['idTutor']) ? 'selected' : '';
                        echo "<option value='{$profesor['idTutor']}' $selected>" . htmlspecialchars($profesor['profesorNombre'] ?? $profesor['tutorNombre']) . "</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="idPeriodo">Periodo: <span class="text-danger">*</span></label>
                <select class="form-control" id="idPeriodo" name="idPeriodo" required>
                    <option value="" disabled>-----Selecciona un periodo-----</option>
                    <?php foreach ($periodos as $periodo) {
                        $selected = (isset($experienciaData['idPeriodo']) && $experienciaData['idPeriodo'] == $periodo['idPeriodo']) ? 'selected' : '';
                        echo "<option value='{$periodo['idPeriodo']}' $selected>" . htmlspecialchars($periodo['periodo']) . "</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre de experiencia educativa: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    placeholder="Nombre de experiencia educativa máximo 200 caracteres" maxlength="200"
                    value="<?php echo htmlspecialchars($experienciaData['nombre']); ?>">
            </div>

            <div class="form-group">
                <label for="programa">Programa educativo: <span class="text-danger">*</span></label>
                <select class="form-control" id="programa" name="programa">
                    <option value="" disabled>-----Selecciona el programa educativo-----</option>
                    <?php foreach ($programas as $programa) {
                        $selected = ($experienciaData['programaEducativo'] == $programa['idCarrera']) ? 'selected' : '';
                        echo "<option value='{$programa['idCarrera']}' $selected>" . htmlspecialchars($programa['carrera']) . "</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Experiencia
                        Educativa</button>
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
    <script src="<?= BASE_URL; ?>/assets/js/editarExperienciaEducativa.js"></script>
</body>

</html>