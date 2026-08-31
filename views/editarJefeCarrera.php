<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Jefe de Carrera</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/editarProfesor.css">
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
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/administrarJefesCarrera.php'"><i
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

        <form action="<?= BASE_URL; ?>/actualizarJefeCarrera.php" method="POST" enctype="multipart/form-data" id="form">

            <input type="hidden" name="idTutor" value="<?php echo htmlspecialchars($jefeCarrera['idTutor'] ?? ''); ?>">
            <input type="hidden" name="rol" value="<?php echo htmlspecialchars($jefeCarrera['rol'] ?? 5); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="form-group">
                <label for="nombre">Nombre de Jefe de Carrera: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    placeholder="Nombre de jefe de carrera máximo 70 caracteres" maxlength="70"
                    value="<?php echo htmlspecialchars($jefeCarrera['nombre'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="paterno">Apellido paterno:</label>
                <input type="text" class="form-control" id="paterno" name="paterno"
                    placeholder="Apellido paterno máximo 70 caracteres" maxlength="70"
                    value="<?php echo htmlspecialchars($jefeCarrera['apellidoPaterno'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="materno">Apellido materno:</label>
                <input type="text" class="form-control" id="materno" name="materno"
                    placeholder="Apellido materno máximo 70 caracteres" maxlength="70"
                    value="<?php echo htmlspecialchars($jefeCarrera['apellidoMaterno'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="noPersonal">Número de personal:</label>
                <input type="text" class="form-control" id="noPersonal" name="noPersonal"
                    placeholder="Número de personal máximo 15 caracteres" maxlength="15"
                    value="<?php echo htmlspecialchars($jefeCarrera['noPersonal'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="correoInstitucional">Correo institucional: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="correoInstitucional" name="correoInstitucional"
                    placeholder="Correo institucional máximo 50 caracteres" maxlength="50"
                    value="<?php echo htmlspecialchars($jefeCarrera['correoInstitucional'] ?? ''); ?>">
                <p class="text-secondary">Ejem: zs12345678@estudiantes.uv.mx | asdw5678@uv.mx</p>
            </div>

            <div class="form-group">
                <label for="carreras">Carreras a cargo: <span class="text-danger">*</span></label>
                <select class="form-control" id="carreras" name="carreras[]" multiple="multiple">
                    <?php
                    if (!empty($carreras)) {
                        $carrerasAsignadas = isset($jefeCarrera['carreras']) ? $jefeCarrera['carreras'] : [];
                        foreach ($carreras as $c) {
                            $selected = in_array($c['idCarrera'], $carrerasAsignadas) ? 'selected' : '';
                            echo "<option value='{$c['idCarrera']}' {$selected}>{$c['carrera']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group row">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar">Guardar Jefe de Carrera</button>
                </div>
            </div>
        </form>
    </div>

    <footer>
        © Universidad Veracruzana
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#carrera').select2({ width: '100%' });
        });
    </script>
    <script src="<?= BASE_URL; ?>/assets/js/registroJefeCarrera.js"></script>
</body>

</html>