<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Actualización de Reportes de Tutoría</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/editarReporteTutoria.css">

</head>

<body>

    <div class="header-container">
        <div class="header-left">
            <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarReportes.php'"><i
                    class="fas fa-arrow-left"></i> Regresar</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-5">

        <?php if (isset($errors) && !empty($errors)) { ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error) {
                    echo "<p>$error</p>";
                } ?>
            </div>
        <?php } ?>

        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-success">
                <p><?= htmlspecialchars($_SESSION['message']); ?></p>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <form action="<?= BASE_URL; ?>/actualizarReporte.php" method="post"
            enctype="multipart/form-data" id="form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="idReporte" value="<?= htmlspecialchars($idReporte); ?>">

            <div class="form-group">
                <label for="carrera">Carrera: <span class="text-danger">*</span></label>
                <select class="form-control" id="carrera" name="carrera" required>
                    <option value="" disabled selected>-----Selecciona la carrera-----</option>
                    <?php
                    foreach ($carreras as $carreraOption) {
                        $selected = ($reporte['carrera'] == $carreraOption['idCarrera']) ? 'selected' : '';
                        $carreraNombre = htmlspecialchars($carreraOption['nombre'], ENT_QUOTES, 'UTF-8');
                        echo "<option value='{$carreraOption['idCarrera']}' {$selected}>{$carreraNombre}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="periodo_display">Periodo: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="periodo_display"
                    value="<?= htmlspecialchars($periodos[0]['periodo']) ?>" readonly>
                <input type="hidden" name="periodo" value="<?= htmlspecialchars($periodos[0]['idPeriodo']) ?>">
            </div>

            <div class="form-group">
                <label for="numTutoria">Número de Tutoría: <span class="text-danger">*</span></label>
                <select class="form-control" name="numTutoria" id="numTutoria" required>
                    <option value="" disabled selected>-----Selecciona el número de tutoría-----</option>
                    <option value="1" <?= ($reporte['numTutoria'] == 1) ? 'selected' : '' ?>>1</option>
                    <option value="2" <?= ($reporte['numTutoria'] == 2) ? 'selected' : '' ?>>2</option>
                    <option value="3" <?= ($reporte['numTutoria'] == 3) ? 'selected' : '' ?>>3</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fechaInicio">Fecha de Inicio: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fechaInicio" name="fechaInicio"
                    value="<?= htmlspecialchars($reporte['fechaInicioTutoria']); ?>" required>
            </div>

            <div class="form-group">
                <label for="fechaFin">Fecha de Fin: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fechaFin" name="fechaFin"
                    value="<?= htmlspecialchars($reporte['fechaFinTutoria']); ?>" required>
            </div>

            <div class="form-group">
                <label for="numAsistencias">Número de alumnos que asistieron: <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="numAsistencias" name="numAsistencias" min="0" step="1"
                    value="<?= htmlspecialchars($reporte['numAsistencia']); ?>" required>
            </div>

            <div class="form-group">
                <label for="numRiesgo">Número de alumnos en riesgo: <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="numRiesgo" name="numRiesgo" min="0" step="1"
                    value="<?= htmlspecialchars($reporte['numRiesgo']); ?>" required>
            </div>

            <div class="form-group">
                <label>Problemática académica:</label>
                <div>
                    <label>
                        <input type="radio" id="problematica" name="tipo" value="problematica"
                            <?= (!empty($problematicasReporte)) ? 'checked' : ''; ?>> Agregar
                    </label>
                </div>
                <div>
                    <label>
                        <input type="radio" id="ninguno" name="tipo" value="ninguno" <?= (empty($problematicasReporte)) ? 'checked' : ''; ?>> Ninguno
                    </label>
                </div>
            </div>

            <div class="problematica-table" style="display: <?= (!empty($problematicasReporte)) ? 'block' : 'none'; ?>">
                <table id="problematicaTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="autoWidthColumn">Experiencia educativa <span class="text-danger">*</span>
                            </th>
                            <th class="autoWidthColumn">Profesor <span class="text-danger">*</span></th>
                            <th class="autoWidthColumn">Problemática <span class="text-danger">*</span></th>
                            <th class="autoWidthColumn">Número de alumnos <span class="text-danger">*</span></th>
                            <th class="autoWidthColumn">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($problematicasReporte)) {
                            foreach ($problematicasReporte as $index => $problematica) {
                                echo '<tr>';

                                // Experiencia Educativa
                                echo '<td>';
                                echo '<select name="experienciaE[]" class="form-control experiencia-educativa" required>';
                                echo '<option value="" disabled>Seleccione una experiencia educativa</option>';
                                foreach ($experiencias as $experiencia) {
                                    $selected = ($problematica['experienciaEducativa'] == $experiencia['idExperienciaEducativa']) ? 'selected' : '';
                                    echo "<option value='{$experiencia['idExperienciaEducativa']}' {$selected}>{$experiencia['nombre']}</option>";
                                }
                                echo '</select>';
                                echo '</td>';

                                // Profesor
                                echo '<td>';
                                echo '<select name="profesor[]" class="form-control select-profesor profesor-problematica" required>';
                                echo '<option value="" disabled>Seleccione un profesor</option>';
                                foreach ($profesores as $profesor) {
                                    $selected = ($problematica['profesor'] == $profesor['idTutor']) ? 'selected' : '';

                                    $profesorNombreCompleto = htmlspecialchars($profesor['tutorNombre']);

                                    $idProfesorEscaped = htmlspecialchars($profesor['idTutor']);

                                    echo "<option value='{$idProfesorEscaped}' {$selected}>{$profesorNombreCompleto}</option>";
                                }
                                echo '</select>';
                                echo '</td>';

                                echo '<td>';
                                echo '<select name="problematica[]" class="form-control problematicaSelect" required>';
                                echo '<option value="" disabled>Seleccione una problemática</option>';
                                foreach ($listaProblematicas as $probOption) {
                                    $selected = ($problematica['problematica'] == $probOption['idProblematica']) ? 'selected' : '';

                                    $idProblematicaEscaped = htmlspecialchars($probOption['idProblematica']);
                                    $nombreProblematica = htmlspecialchars($probOption['descripcion']);

                                    echo "<option value='{$idProblematicaEscaped}' {$selected}>{$nombreProblematica}</option>";
                                }

                                $selectedOtro = ($problematica['problematica'] === null) ? 'selected' : '';
                                echo "<option value='otro' {$selectedOtro}>Otro</option>";
                                echo '</select>';

                                $otroDisplay = ($problematica['problematica'] === null) ? 'block' : 'none';
                                $otroValue = isset($problematica['otro']) ? htmlspecialchars($problematica['otro']) : '';
                                echo "<input type='text' name='otro[]' class='form-control' style='display:{$otroDisplay};' value='{$otroValue}' placeholder='Especificar otra problemática'>";
                                echo '</td>';

                                echo '<td>';
                                echo '<input type="number" name="numAlumnos[]" class="form-control" min="0" step="1" value="' . htmlspecialchars($problematica['numAlumnos']) . '" required>';
                                echo '</td>';

                                echo '<td><button type="button" class="btn btn-danger remove-row">Eliminar</button></td>';

                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <button type="button" id="agregarFilaBtn" class="btn btn-primary">Agregar problemática</button>
            </div>

            <div class="form-group">
                <label for="comentario">Comentarios:</label>
                <textarea class="form-control" id="comentario" name="comentario" rows="3"
                    maxlength="500"><?= htmlspecialchars($reporte['comentario']); ?></textarea>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" name="accion" class="btn btn-success" id="guardar" value="borrador">Guardar Borrador de Reporte</button>
                    <button type="submit" name="accion" class="btn btn-primary" id="guardar" value="enviar">Enviar Reporte de Tutoría</button>
                </div>
            </div>
        </form>
    </div>

    <footer>
        @ Universidad Veracruzana
    </footer>

    <script>
        var experiencias = <?php echo json_encode($experiencias); ?>;
        var profesores = <?php echo json_encode($profesores); ?>;
        var problematicaOptions = <?php echo json_encode($listaProblematicas); ?>;
        var secciones = <?= json_encode($secciones); ?>;
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?= BASE_URL; ?>/assets/js/editarReporteTutoria.js"></script>

</body>

</html>