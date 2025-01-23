$(document).ready(function () {

    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var nombre = $('#nombre').val().trim();
    var paterno = $('#paterno').val().trim();
    var materno = $('#materno').val().trim();
    var correoInstitucional = $('#correoInstitucional').val().trim();
    var password = $('#password').val();
    var confirmPassword = $('#confirm_password').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    var mensajesError = [];

    if (!nombre) {
        mensajesError.push("El campo 'Nombre' es obligatorio.");
        $('#nombre').addClass("borderRed");
        error = true;
    }
    if (!correoInstitucional) {
        mensajesError.push("El campo 'Correo institucional' es obligatorio.");
        $('#correoInstitucional').addClass("borderRed");
        error = true;
    }
    if (!password) {
        mensajesError.push("El campo 'Contraseña' es obligatorio.");
        $('#password').addClass("borderRed");
        error = true;
    }
    if (!confirmPassword) {
        mensajesError.push("El campo 'Confirmar Contraseña' es obligatorio.");
        $('#confirm_password').addClass("borderRed");
        error = true;
    }
    if (password && confirmPassword && password !== confirmPassword) {
        mensajesError.push("Las contraseñas no coinciden.");
        $('#password').addClass("borderRed");
        $('#confirm_password').addClass("borderRed");
        error = true;
    }

    if (error) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>' + mensajesError.join('<br>') + '</p>',
            showConfirmButton: false,
            timer: 3500
        });
        return;
    }

    $('#nombre, #paterno, #materno, #correoInstitucional, #password, #confirm_password').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}