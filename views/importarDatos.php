<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Importación de Datos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/importarDatos.css">
</head>

<body>
    <div class="header-container">
        <div class="header-left">
            <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i>
                Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i>
                Cerrar sesión</button>
        </div>
    </div>

    <div class="container mt-5">
        <h2>Importar Datos</h2>

        <?php
        if (isset($message)) {
            echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
        }
        ?>

        <form action="<?= BASE_URL; ?>/importarDatos.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-section">
                <div class="div-border">
                    <h4>Importar Datos de Tutor</h4>
                    <div class="form-group">
                        <label for="archivo_tutor">Archivo XLSX para Tutores:</label>
                        <input type="file" class="form-control-file" id="archivo_tutor" name="archivo_tutor"
                            accept=".csv, .xlsx">
                        <button type="button" class="btn btn-link mt-2" style="color:#000;"
                            onclick="location.href='./uploads/importe_ejemplo/ejemplo_tutor.xlsx'">Descargar
                            Ejemplo</button>
                    </div>
                </div>

                <div class="div-border">
                    <h4>Importar Datos de Tutorados</h4>
                    <div class="form-group">
                        <label for="archivo_tutorado">Archivo XLSX para Tutorados:</label>
                        <input type="file" class="form-control-file" id="archivo_tutorado" name="archivo_tutorado"
                            accept=".csv, .xlsx">
                        <button type="button" class="btn btn-link mt-2" style="color:#000;"
                            onclick="location.href='./uploads/importe_ejemplo/ejemplo_tutorado.xlsx'">Descargar
                            Ejemplo</button>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="div-border">
                    <h4>Importar Datos de Carreras</h4>
                    <div class="form-group">
                        <label for="archivo_carrera">Archivo XLSX para Carreras:</label>
                        <input type="file" class="form-control-file" id="archivo_carrera" name="archivo_carrera"
                            accept=".csv, .xlsx">
                        <button type="button" class="btn btn-link mt-2" style="color:#000;"
                            onclick="location.href='./uploads/importe_ejemplo/ejemplo_carrera.xlsx'">Descargar
                            Ejemplo</button>
                    </div>
                </div>

                <div class="div-border">
                    <h4>Importar Datos de Periodos Escolares</h4>
                    <div class="form-group">
                        <label for="archivo_periodo">Archivo XLSX para Periodos Escolares:</label>
                        <input type="file" class="form-control-file" id="archivo_periodo" name="archivo_periodo"
                            accept=".csv, .xlsx">
                        <button type="button" class="btn btn-link mt-2" style="color:#000;"
                            onclick="location.href='./uploads/importe_ejemplo/ejemplo_periodo.xlsx'">Descargar
                            Ejemplo</button>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="div-border">
                    <h4>Importar Datos de Experiencias Educativas</h4>
                    <div class="form-group">
                        <label for="archivo_experienciaE">Archivo XLSX para Experiencias Educativas:</label>
                        <input type="file" class="form-control-file" id="archivo_experienciaE"
                            name="archivo_experienciaE" accept=".csv, .xlsx">
                        <button type="button" class="btn btn-link mt-2" style="color:#000;"
                            onclick="location.href='./uploads/importe_ejemplo/ejemplo_experiencia_educativa.xlsx'">Descargar
                            Ejemplo</button>
                    </div>
                </div>
            </div>

            <div class="button-container">
                <button type="submit" class="btn button-import">Importar Datos</button>
            </div>
        </form>
    </div>

    <footer>
        © Universidad Veracruzana
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>