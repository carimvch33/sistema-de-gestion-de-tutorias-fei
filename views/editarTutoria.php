<?php
require_once '../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ./cerrarSesion.php');
    exit();
}

$user = $_SESSION['user'];
$csrf_token = $_SESSION['csrf_token'];
$menu = BASE_URL . '/menu.php';

$lugar = htmlspecialchars($tutoria['lugar'] ?? '', ENT_QUOTES, 'UTF-8');
$fecha = htmlspecialchars($tutoria['fechaInicio'] ?? '', ENT_QUOTES, 'UTF-8');
$fecha_fin = htmlspecialchars($tutoria['fechaFin'] ?? '', ENT_QUOTES, 'UTF-8');
$notas = htmlspecialchars($tutoria['nota'] ?? '', ENT_QUOTES, 'UTF-8');
$periodoAtencion = $tutoria['periodoAtencion'] ?? '';
$periodoTutoria = $tutoria['periodoTutoria'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Actualización de Tutorías</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/editarTutoria.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="header-container">
        <div class="header-left">
            <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/tutorias.php'"><i class="fas fa-arrow-left"></i>
                Regresar</button>
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
                echo "<p>" . htmlspecialchars($error) . "</p>";
            }
            echo '</div>';
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success">';
            echo "<p>" . htmlspecialchars($_SESSION['message']) . "</p>";
            echo '</div>';
            unset($_SESSION['message']);
        }
        ?>

        <form action="<?= BASE_URL; ?>/actualizarTutoria.php" method="POST" enctype="multipart/form-data" id="form">
            <!-- Campos ocultos -->
            <input type="hidden" name="idTutoria" value="<?php echo htmlspecialchars($idTutoria); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="form-group">
                <label for="periodo">Periodo Escolar:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($periodos[0]['periodo']); ?>"
                    readonly>
                <input type="hidden" style="display: none;" id="periodoE" name="periodo" readonly
                    value="<?php echo htmlspecialchars($periodos[0]['idPeriodo']); ?>">
            </div>

            <div class="form-group">
                <label for="carrera">Carrera: <span class="text-danger">*</span></label>
                <select class="form-control" id="carrera" name="carrera" required>
                    <option value="" disabled>-----Selecciona una carrera-----</option>
                    <?php foreach ($carreras as $carrera): ?>
                        <?php $selected = ($periodoTutoriasActual['carrera'] == $carrera['idCarrera']) ? 'selected' : ''; ?>
                        <option value="<?= $carrera['idCarrera']; ?>" <?= $selected; ?>>
                            <?= htmlspecialchars($carrera['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="periodoTutoria">Periodo de Tutoría: <span class="text-danger">*</span></label>
                <select class="form-control" name="periodoTutoria" id="periodoTutoria" required>
                    <option value="" disabled selected>-----Selecciona un periodo de tutorías-----</option>
                </select>
            </div>

            <div class="form-group">
                <span for="modalidad">Modalidad: <span class="text-danger">*</span></span><br>
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

            <div class="form-group">
                <label for="lugar">Lugar:</label>
                <input type="text" class="form-control" id="lugar" name="lugar"
                placeholder="Lugar de tutoría (máximo 300 caracteres)" maxlength="300">
            </div>

            <div class="form-group">
                <span>Período Atención: <span class="text-danger">*</span></span><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="periodoAtencion" id="un_solo_dia"
                        value="Un solo día" required <?php if($periodoAtencion=="Un solo día") echo 'checked'; ?>>
                    <label class="form-check-label" for="un_solo_dia">Un solo día</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="periodoAtencion" id="mas_de_un_dia"
                        value="Más de un día" required <?php if($periodoAtencion=="Más de un día") echo 'checked'; ?>>
                    <label class="form-check-label" for="mas_de_un_dia">Más de un día</label>
                </div>
            </div>

            <div class="form-group" id="div_fecha" style="<?php if($periodoAtencion=="Más de un día") echo 'display:none;'; ?>">
                <label for="fecha">Fecha:</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $fecha; ?>">
            </div>

            <div class="form-group" id="div_fecha_fin" style="<?php if($periodoAtencion!="Más de un día") echo 'display:none;'; ?>">
                <label for="fecha_fin">Fecha fin:</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
            </div>

            <div class="form-group">
                <label for="notas">Notas:</label>
                <textarea class="form-control" id="notas" name="notas" rows="3" placeholder="Máximo 500 caracteres"
                    maxlength="500"><?php echo $notas; ?></textarea>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Tutoría</button>
                </div>
                <div class="col-md-auto">
                    <input type="file" class="form-control-file widthInput" id="archivo_horario" name="archivo_horario"
                        accept=".pdf,.doc,.docx,.xls,.xlsx">
                    <?php if (!empty($tutoria['archivo'])): ?>
                        <div class="mb-2">
                            <strong>Archivo Actual:</strong> <?php echo htmlspecialchars($tutoria['archivo']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL; ?>/assets/js/editarTutoria.js"></script>
</body>

</html>