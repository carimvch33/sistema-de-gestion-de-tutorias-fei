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
    var noPersonal = $('#noPersonal').val().trim();
    var correoInstitucional = $('#correoInstitucional').val().trim();

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

    if (!correoInstitucional.endsWith('@uv.mx') && !correoInstitucional.endsWith('@estudiantes.uv.mx')) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>El correo institucional debe terminar en @uv.mx o @estudiantes.uv.mx.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#correoInstitucional').addClass("borderRed");
        return;
    }

    $('#nombre, #paterno, #materno, #noPersonal, #correoInstitucional').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}