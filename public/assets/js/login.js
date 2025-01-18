$(document).ready(function () {
    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });

    $('#user').on("change", function () {
        $(this).removeClass("borderGreen borderRed");
    });

    $('#password').on("change", function () {
        $(this).removeClass("borderGreen borderRed");
    });
});

function validarFormulario() {
    var usuario = $('#user').val();
    var clave = $('#password').val();

    if (usuario.length == 0) {
        Swal.fire({
            icon: 'error',
            html: '<p>Es necesario ingresar su correo/cuenta institucional.</p>',
            showConfirmButton: false,
            timer: 3500
        });
        $('#user').addClass("borderRed");
        return false;
    } else {
        $('#user').addClass("borderGreen");
    }

    if (clave.length == 0) {
        Swal.fire({
            icon: 'error',
            html: '<p>Es necesario ingresar su contraseña.</p>',
            showConfirmButton: false,
            timer: 3500
        });
        $('#password').addClass("borderRed");
        return false;
    } else {
        $('#password').addClass("borderGreen");
    }
    
    $("#form").submit();
}