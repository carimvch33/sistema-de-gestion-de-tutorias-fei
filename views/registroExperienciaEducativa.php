<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro de Experiencias Educativas</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/registroExperienciaEducativa.css">
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

    <div class="container mt-5" style="padding-bottom: 100px;">
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

        <form action="<?= BASE_URL; ?>/crearExperiencia.php" method="post" enctype="multipart/form-data" id="form">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <h4 class="text-primary border-bottom pb-2 mb-4">1. Datos de la Materia</h4>

            <div class="form-group">
                <label for="nombre">Nombre de experiencia educativa: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    placeholder="Nombre de experiencia educativa" maxlength="200" required>
            </div>

            <div class="form-group">
                <label for="programa">Programa educativo: <span class="text-danger">*</span></label>
                <select class="form-control" id="programa" name="programa" required>
                    <option value="" disabled selected>-----Selecciona el programa educativo-----</option>
                    <?php foreach ($programas as $programa) { ?>
                        <option value="<?php echo $programa['idCarrera']; ?>">
                            <?php echo htmlspecialchars($programa['carrera'] ?? $programa['nombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <h4 class="text-primary border-bottom pb-2 mt-5 mb-4">2. Datos de Asignación Obligatorios</h4>

            <div class="form-group">
                <label for="nrc">NRC de la Sección: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nrc" name="nrc"
                    placeholder="Ej. 98764" maxlength="10" required>
            </div>

            <div class="form-group">
                <label for="idProfesor">Profesor: <span class="text-danger">*</span></label>
                <select class="form-control" id="idProfesor" name="idProfesor" required>
                    <option value="" disabled selected>-----Selecciona un profesor-----</option>
                    <?php foreach ($profesores as $profesor) { ?>
                        <option value="<?php echo $profesor['idTutor']; ?>">
                            <?php echo htmlspecialchars($profesor['profesorNombre'] ?? $profesor['tutorNombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="idPeriodo">Periodo: <span class="text-danger">*</span></label>
                <select class="form-control" id="idPeriodo" name="idPeriodo" required>
                    <option value="" disabled selected>-----Selecciona un periodo-----</option>
                    <?php foreach ($periodos as $periodo) { ?>
                        <option value="<?php echo $periodo['idPeriodo']; ?>">
                            <?php echo htmlspecialchars($periodo['periodo']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group row mt-5">
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-success buttonGreen" id="enviar"><i class="fas fa-save"></i> Registrar Completamente</button>
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
    <script src="<?= BASE_URL; ?>/assets/js/registroExperienciaEducativa.js"></script>
    <script>
        $(document).ready(function() {
            $('#programa, #idProfesor, #idPeriodo').select2({
                placeholder: "Selecciona una opción",
                allowClear: false
            });
        });
    </script>
</body>

</html>