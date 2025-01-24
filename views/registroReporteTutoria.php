<?php
require_once '../config/config.php';
if (!isset($csrf_token))
    $csrf_token = '';
if (!isset($user))
    $user = '';
if (!isset($tutor))
    $tutor = '';
if (!isset($periodoActual))
    $periodoActual = '';
if (!isset($carreras))
    $carreras = [];
if (!isset($menu))
    $menu = './cerrarSesion.php';
if (!isset($errors))
    $errors = [];
if (!isset($problematicas))
    $problematicas = [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro de Reportes de Tutoría</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/registroReporteTutoria.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarReportes.php'"><i
                    class="fas fa-arrow-left"></i> Regresar</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-5">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL; ?>/crearReporte.php" method="post" enctype="multipart/form-data" id="form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="tutor" value="<?= htmlspecialchars($tutor) ?>">

            <div class="form-group">
                <label for="periodo">Periodo escolar: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="periodo" value="<?= htmlspecialchars($periodoActual) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label for="carrera">Carrera: <span class="text-danger">*</span></label>
                <select class="form-control" id="carrera" name="carrera" required>
                    <option value="" disabled selected>-----Selecciona la carrera-----</option>
                    <?php foreach ($carreras as $carreraItem): ?>
                        <option value="<?= htmlspecialchars($carreraItem['idCarrera']) ?>">
                            <?= htmlspecialchars($carreraItem['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="numTutoria">Número de Tutoría: <span class="text-danger">*</span></label>
                <select class="form-control" name="numTutoria" id="numTutoria" required>
                    <option value="" disabled selected>-----Selecciona el número de tutoría-----</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fechaInicio">Fecha de Inicio: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" placeholder="dd/mm/aaaa"
                    required>
            </div>

            <div class="form-group">
                <label for="fechaFin">Fecha de Fin: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fechaFin" name="fechaFin" placeholder="dd/mm/aaaa" required>
            </div>

            <div class="form-group">
                <label for="numAsistencias">Número de alumnos que asistieron: <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="numAsistencias" name="numAsistencias"
                    placeholder="Número total de asistencias de tutorados" min="0" step="1" required>
            </div>

            <div class="form-group">
                <label for="numRiesgo">Número de alumnos en riesgo: <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="numRiesgo" name="numRiesgo"
                    placeholder="Número total de tutorados en riesgo" min="0" step="1" required>
            </div>

            <div class="form-group">
                <label>Problemática académica:</label>
                <div>
                    <label>
                        <input type="radio" name="tipo" value="problematica"> Agregar
                    </label>
                </div>
                <div>
                    <label>
                        <input type="radio" name="tipo" value="ninguno" checked> Ninguno
                    </label>
                </div>
            </div>

            <div class="problematica-table" style="display: none;">
                <table id="problematicaTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="autoWidthColumn">Experiencia educativa <span class="text-danger">*</span></th>
                            <th class="autoWidthColumn">Profesor <span class="text-danger">*</span></th>
                            <th class="autoWidthColumn">Problemática <span class="text-danger">*</span></th>
                            <th class="autoWidthColumn">Número de alumnos <span class="text-danger">*</span></th>
                            <th class="autoWidthColumn">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <button type="button" id="agregarFilaBtn" class="btn btn-primary">Agregar problemática</button>
            </div>

            <div class="form-group">
                <label for="comentario">Comentarios:</label>
                <textarea class="form-control" id="comentario" name="comentario" rows="3"
                    placeholder="Máximo 500 caracteres" maxlength="500"></textarea>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success" id="enviar">Guardar Reporte de Tutoría</button>
                </div>
            </div>
        </form>
    </div>
    <footer>
        © Universidad Veracruzana
    </footer>

    <script>
        var problematicasInject = <?= json_encode($problematicas); ?>;
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL; ?>/assets/js/registroReporteTutoria.js"></script>
</body>

</html>