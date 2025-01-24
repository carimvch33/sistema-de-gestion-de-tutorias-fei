<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ./cerrarSesion.php');
    exit();
}

$user = $_SESSION['user'];
$csrf_token = $_SESSION['csrf_token'];
$menu = '/menu.php';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Tutoría</title>
    <link rel="stylesheet" href="../assets/css/registroTutoria.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        <form action="crearTutoria.php" method="post" enctype="multipart/form-data" id="form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="carrera">Carrera: <span class="text-danger">*</span></label>
                <select class="form-control" id="carrera" name="carrera" required>
                    <option value="" disabled selected>-----Selecciona una carrera-----</option>
                    <?php foreach ($carreras as $carrera): ?>
                        <option value="<?php echo $carrera['idCarrera']; ?>">
                            <?php echo htmlspecialchars($carrera['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="numTutoria">Tutoría: <span class="text-danger">*</span></label>
                <select class="form-control" name="numTutoria" id="numTutoria" required>
                    <option value="" disabled selected>-----Selecciona el número de tutoría-----</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>

            <div class="form-group">
                <label for="periodo">Periodo Escolar:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($periodos[0]['periodo']); ?>"
                    readonly>
                <input type="hidden" style="display: none;!" id="periodoE" name="periodo" readonly
                    value="<?php echo htmlspecialchars($periodos[0]['idPeriodo']); ?>">
            </div>

            <div class="form-group">
                <span for="modalidad">Modalidad: <span class="text-danger">*</span></span><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="modalidad" id="presencial" value="Presencial"
                        required>
                    <label class="form-check-label" for="presencial">Presencial</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="modalidad" id="virtual" value="Virtual" required>
                    <label class="form-check-label" for="virtual">Virtual</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="modalidad" id="mixta" value="Mixta" required>
                    <label class="form-check-label" for="mixta">Mixta</label>
                </div>
            </div>

            <div class="form-group">
                <span>Período Atención: <span class="text-danger">*</span></span><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="periodoAtencion" id="un_solo_dia"
                        value="Un solo día" required>
                    <label class="form-check-label" for="un_solo_dia">Un solo día</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="periodoAtencion" id="mas_de_un_dia"
                        value="Más de un día" required>
                    <label class="form-check-label" for="mas_de_un_dia">Más de un día (Capture toda la información
                        necesaria en el campo Notas, o bien, agregue un archivo)</label>
                </div>
            </div>

            <div class="form-group">
                <label for="lugar">Lugar:</label>
                <input type="text" class="form-control" id="lugar" name="lugar"
                    placeholder="Lugar de tutoría (máximo 300 caracteres)" maxlength="300">
            </div>

            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" class="form-control" id="fecha" name="fecha" placeholder="dd/mm/aaaa">
            </div>

            <div class="form-group">
                <label for="hora_inicio">Hora Inicio:</label>
                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio">
            </div>

            <div class="form-group">
                <label for="hora_final">Hora Final:</label>
                <input type="time" class="form-control" id="hora_final" name="hora_final">
            </div>

            <div class="form-group">
                <label for="notas">Notas:</label>
                <textarea class="form-control" id="notas" name="notas" rows="3" placeholder="Máximo 500 caracteres"
                    maxlength="500"></textarea>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Tutoría</button>
                </div>
                <div class="col-md-auto">
                    <input type="file" class="form-control-file widthInput" id="archivo_horario" name="archivo_horario"
                        accept=".pdf,.doc,.docx,.xls,.xlsx">
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/registroTutoria.js"></script>
</body>

</html>