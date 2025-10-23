<?php
require_once '../config/config.php';

define('fondo', BASE_URL . '/assets/img/bg.png');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
}

$mantenimiento = false;

if ($mantenimiento) {
    header("Location: mantenimiento.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6" id="colImg">
                <div class="row">
                    <div class="col-md-12">
                        <img src="<?= fondo; ?>" alt="Imagen representativa de una tutoría escolar" class="imgLogin row">
                    </div>
                </div>
            </div>
            <div id="divIzq" class="col-sm-12 col-md-6">
                <div class="row float-right">
                    <div class="pleca">
                        <a href="https://www.uv.mx" style="color: white;">Universidad Veracruzana</a>
                    </div>
                </div>
                <div style="width: 100%; height: 100%; display: table;">
                    <div style="display: table-cell; vertical-align: middle;" class="pt-4">
                        <h2 class="text-center pt-4" style="color: #18529D;">Sistema de Gestión de Tutorías</h2>
                        <h4 class="text-center">Facultad de Estadística e Informática</h4>
                        <h2 class="text-center">Inicio de sesión</h2>

                        <form action="<?= BASE_URL; ?>/index.php" method="post" id="form">
                            <div class="row justify-content-center">
                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <label for="user" class="h-3 text">Usuario:</label>
                                        <input type="email" name="user" id="user" class="form-control" placeholder="Ingrese su correo institucional" required>
                                        <small class="text-secondary">Ejem: abcgarcia@uv.mx / zs19024533@estudiantes.uv.mx</small>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <label for="password" class="h-3 text">Contraseña:</label>
                                        <input type="password" name="password" id="password" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="submit" name="enviar" id="enviar" class="btn btn-primary btn-lg">Iniciar sesión</button>
                                    <p class="text-secondary mt-3">
                                        Consulta el <a
                                            href="https://www.uv.mx/fei/files/2018/10/2023_Aviso-de-privacidad-Integral-TA_ET_FEI.pdf"
                                            target="_blank">Aviso de Privacidad</a>
                                    </p>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>    

    <?php
    if (isset($message)) {
        $text_error = "";
        if ($message == "no_exist") {
            $text_error = "La cuenta institucional no pertenece a la Facultad de Estadística e Informática.";
        }

        if ($message == "no_login") {
            $text_error = "Correo/cuenta o contraseña incorrecta, favor de verificar.";
        }

        if ($message == "error") {
            $text_error = "Ha ocurrido un problema, intente de nuevo, si el problema persiste contacte a soporte.";
        }

        if ($text_error != "") {
            echo "<script> Swal.fire({
                    icon: 'error',
                    html: '<p>" . $text_error . "</p>',
                    showConfirmButton: false,
                    timer: 3500
                }); </script>";
        }

        session_destroy();
    }
    ?>

    <script src="<?= BASE_URL; ?>/assets/js/login.js"></script>
</body>

</html>