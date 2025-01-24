<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'];
$menu = '/menu.php';

$lugar = htmlspecialchars($tutoria['lugar'] ?? '', ENT_QUOTES, 'UTF-8');
$fecha = htmlspecialchars($tutoria['fecha'] ?? '', ENT_QUOTES, 'UTF-8');
$horaInicio = htmlspecialchars($tutoria['horaInicio'] ?? '', ENT_QUOTES, 'UTF-8');
$horaFin = htmlspecialchars($tutoria['horaFin'] ?? '', ENT_QUOTES, 'UTF-8');
$notas = htmlspecialchars($tutoria['nota'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Actualización de Tutorías</title>
    <link rel="stylesheet" href="../assets/css/editarTutoria.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="header-container">
        <div class="header-left">
            <img src="assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='./tutorias.php'"><i class="fas fa-arrow-left"></i>
                Regresar</button>
            <button class="buttonsHead" onclick="location.href='/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-5">
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <form action="actualizarTutoria.php" method="POST" enctype="multipart/form-data" id="form">
            <!-- Campos ocultos -->
            <input type="hidden" name="idTutoria" value="<?php echo htmlspecialchars($idTutoria); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="form-group">
                <label for="carrera">Carrera: <span class="text-danger">*</span></label>
                <select class="form-control" id="carrera" name="carrera" required>
                    <option value="" disabled selected>-----Selecciona una carrera-----</option>
                    <?php foreach ($carreras as $carrera): ?>
                        <?php $selected = ($tutoria['carrera'] == $carrera['idCarrera']) ? 'selected' : ''; ?>
                        <option value="<?= $carrera['idCarrera']; ?>" <?= $selected; ?>>
                            <?= htmlspecialchars($carrera['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="numTutoria">Tutoría: <span class="text-danger">*</span></label>
                <select class="form-control" name="numTutoria" id="numTutoria" required>
                    <option value="" disabled selected>-----Selecciona el numero de tutoría-----</option>
                    <?php
                    $tutoriaOptions = [1, 2, 3];
                    foreach ($tutoriaOptions as $option) {
                        $selected = ($tutoria['numTutoria'] == $option) ? 'selected' : '';
                        echo "<option value='$option' $selected>$option</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Periodo Escolar -->
            <div class="form-group">
                <label for="periodo">Periodo Escolar:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($periodos[0]['periodo']); ?>"
                    readonly>
                <input type="hidden" style="display: none;!" id="periodo" name="periodo" readonly
                    value="<?php echo htmlspecialchars($periodos[0]['idPeriodo']); ?>">
            </div>

            <!-- Modalidad -->
            <div class="form-group">
                <span>Modalidad: <span class="text-danger">*</span></span><br>
                <?php
                $modalidades = ['Presencial', 'Virtual', 'Mixta'];
                foreach ($modalidades as $modalidad) {
                    $checked = ($tutoria['modalidad'] == $modalidad) ? 'checked' : '';
                    echo "<div class='form-check form-check-inline'>
                            <input class='form-check-input' type='radio' name='modalidad' id='$modalidad' value='$modalidad' $checked required>
                            <label class='form-check-label' for='$modalidad'>$modalidad</label>
                          </div>";
                }
                ?>
            </div>

            <!-- Período Atención -->
            <div class="form-group">
                <span>Período Atención: <span class="text-danger">*</span></span><br>
                <?php
                $periodosAtencion = ['Un solo día', 'Más de un día'];
                foreach ($periodosAtencion as $periodoAtencion) {
                    $checked = ($tutoria['periodoAtencion'] == $periodoAtencion) ? 'checked' : '';
                    echo "<div class='form-check'>
                            <input class='form-check-input' type='radio' name='periodoAtencion' id='$periodoAtencion' value='$periodoAtencion' $checked required>
                            <label class='form-check-label' for='$periodoAtencion'>$periodoAtencion</label>
                          </div>";
                }
                ?>
            </div>

            <!-- Lugar -->
            <div class="form-group">
                <label for="lugar">Lugar:</label>
                <input type="text" class="form-control" id="lugar" name="lugar"
                    placeholder="Lugar de tutoría máximo 300 caracteres" maxlength="300" value="<?php echo $lugar; ?>">
            </div>

            <!-- Fecha -->
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $fecha; ?>">
            </div>

            <!-- Hora Inicio y Hora Fin -->
            <div class="form-group">
                <label for="hora_inicio">Hora Inicio:</label>
                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio"
                    value="<?php echo $horaInicio; ?>">
            </div>

            <div class="form-group">
                <label for="hora_final">Hora Final:</label>
                <input type="time" class="form-control" id="hora_final" name="hora_final"
                    value="<?php echo $horaFin; ?>">
            </div>

            <!-- Notas -->
            <div class="form-group">
                <label for="notas">Notas:</label>
                <textarea class="form-control" id="notas" name="notas" rows="3" placeholder="Máximo 500 caracteres"
                    maxlength="500"><?php echo $notas; ?></textarea>
            </div>

            <!-- Archivo -->
            <div class="form-group">
                <label for="archivo_horario">Archivo:</label>
                <?php if (!empty($tutoria['archivo'])): ?>
                    <div class="mb-2">
                        <strong>Archivo Actual:</strong> <?php echo htmlspecialchars($tutoria['archivo']); ?>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control-file widthInput" id="archivo_horario" name="archivo_horario"
                    accept=".pdf,.doc,.docx,.xls,.xlsx">
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Horario</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/editarTutoria.js"></script>
</body>

</html>