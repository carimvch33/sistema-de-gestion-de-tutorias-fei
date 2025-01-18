$(document).ready(function () {
    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var carrera = $('#carrera').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!carrera.trim()) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#carrera').addClass("borderRed");
        error = true;
    }

    if (error) {
        return;
    }

    $('#carrera').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}