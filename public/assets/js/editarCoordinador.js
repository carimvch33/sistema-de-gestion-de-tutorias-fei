$(document).ready(function () {

    $('#carreras').select2({
        placeholder: "Seleccione una o más carreras"
    });

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
    var carrerasSeleccionadas = $('#carreras').val();

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
    if (!carrerasSeleccionadas || carrerasSeleccionadas.length === 0) {
        mensajesError.push("Debe seleccionar al menos una carrera.");
        $('#carreras').addClass("borderRed");
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

    if (!correoInstitucional.endsWith('@uv.mx') && !correoInstitucional.endsWith('@estudiantes.uv.mx')) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>El correo institucional debe terminar en @uv.mx o @estudiantes.uv.mx.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#correoInstitucional').addClass("borderRed");
        return false;
    }

    $('#nombre, #paterno, #materno, #noPersonal, #correoInstitucional, #carreras').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}