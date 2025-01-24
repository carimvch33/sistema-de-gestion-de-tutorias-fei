<?php
define('logo_UV', '/assets/img/UV-fondoObscuro.png');

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
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="container-fluid text-white full-height d-flex justify-content-center align-items-center">
    <div class="row w-100">
        <div class="col-md-5 d-flex justify-content-center left-section">
            <div class="image-container">
                <img src="<?= logo_UV ?>" alt="UV Logo" class="img-fluid large-image">
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-center align-items-center right-section">
            <div class="w-100 px-3">
                <h2 class="text-center loginBig">Registro de Tutorías</h2>
                <h2 class="text-center loginBig">UV</h2>
                <br></br>

                <form action="./index.php" method="post" id="form">
                    <div class="mb-3">
                        <label class="labelDark">Usuario:</label>
                        <input type="text" id="user" name="user" class="form-control small-input"
                            placeholder="Ingrese su usuario/matrícula" required>
                        <p class="text-secondary">Ejem: abcgarcia / zS12345678 / GS12345678</p>
                    </div>
                    <div class="mb-3">
                        <label class="labelDark">Contraseña:</label>
                        <input type="password" id="password" name="password"
                            class="form-control text-white small-input input-white" placeholder="Ingrese su contraseña"
                            required>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary btn-block smaller-btn" name="enviar" id="enviar"
                            >Iniciar Sesión</button>
                    </div>

                    <div class="mt-3 text-center">
                        <p class="text-secondary" style="font-size: 1.2rem;">
                            Consulta el <a
                                href="https://www.uv.mx/fei/files/2018/10/2023_Aviso-de-privacidad-Integral-TA_ET_FEI.pdf"
                                target="_blank">Aviso de Privacidad</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($message)) {
        $text_error = "";
        if ($message == "no_exist") {
            $text_error = "La cuenta institucional no pertenece a la Facultad de Estadística e Infomática.";
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

    <script src="/assets/js/login.js"></script>
</body>

</html>