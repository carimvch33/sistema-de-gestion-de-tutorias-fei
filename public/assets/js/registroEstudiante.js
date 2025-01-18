$(document).ready(function () {
    $("#carrera").select2();
    $("#tutor").select2();
    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var nombre = $('#nombre').val();
    var apellidoPaterno = $('#paterno').val();
    var apellidoMaterno = $('#materno').val();
    var matricula = $('#matricula').val();
    var carrera = $('#carrera').val();
    var correoInstitucional = $('#correoInstitucional').val();
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
        return;
    }

    if (!/^S\d{8}$/.test(matricula)) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>La matrícula debe comenzar con "S" seguida de 8 dígitos.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#matricula').addClass("borderRed");
        return;
    }

    if (!new RegExp("^z" + matricula.toLowerCase() + "@estudiantes\\.uv\\.mx$", "i").test(correoInstitucional)) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>El correo institucional debe ser "zMATRÍCULA@estudiantes.uv.mx".</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#correoInstitucional').addClass("borderRed");
        return;
    }

    $('#nombre, #paterno, #materno, #matricula, #carrera, #correoInstitucional, #tutor').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}