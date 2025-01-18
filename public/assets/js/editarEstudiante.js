$(document).ready(function () {
    $("#carrera").select2();
    $("#tutor").select2();

    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var nombre = $('#nombre').val().trim();
    var paterno = $('#paterno').val().trim();
    var materno = $('#materno').val().trim();
    var matricula = $('#matricula').val().trim();
    var carrera = $('#carrera').val();
    var correoInstitucional = $('#correoInstitucional').val().trim();
    var tutor = $('#tutor').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;

    if (!nombre || !matricula || !carrera || !correoInstitucional) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!nombre) $('#nombre').addClass("borderRed");
        if (!matricula) $('#matricula').addClass("borderRed");
        if (!carrera) $('#carrera').addClass("borderRed");
        if (!correoInstitucional) $('#correoInstitucional').addClass("borderRed");

        error = true;
    }

    if (error) {
        return false;
    }

    if (!matricula.startsWith('S')) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>La matrícula debe comenzar con S.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#matricula').addClass("borderRed");
        return;
    }

    if (matricula.length !== 9) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>La matrícula debe tener exactamente 9 caracteres.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#matricula').addClass("borderRed");
        return;
    }

    var expectedEmail = 'z' + matricula.toLowerCase() + '@estudiantes.uv.mx';
    if (correoInstitucional.toLowerCase() !== expectedEmail) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>El correo institucional debe ser "' + expectedEmail + '".</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#correoInstitucional').addClass("borderRed");
        return;
    }

    $('#nombre, #paterno, #materno, #matricula, #carrera, #correoInstitucional, #tutor')
        .removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}