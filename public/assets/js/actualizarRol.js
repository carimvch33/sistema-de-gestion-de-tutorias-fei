$(document).ready(function () {
    $("#tutor").select2();
    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var tutor = $('#tutor').val();
    var rol = $('#rol').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    var mensajesError = [];

    if (!tutor) {
        mensajesError.push("Seleccione un profesor.");
        $('#tutor').addClass("borderRed");
        error = true;
    }

    if (!rol) {
        mensajesError.push("Seleccione un rol.");
        $('#rol').addClass("borderRed");
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
        return false;
    }

    $('#tutor, #rol').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}