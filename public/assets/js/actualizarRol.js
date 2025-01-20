$(document).ready(function () {
    $("#tutor").select2();
    $("#rol").select2();
    $("#carreras").select2({
        width: '100%'
    });

    $('#rol').on('change', function () {
        var selectedRole = $(this).val();
        if (selectedRole == '4') {
            $('#carreras-container').show();
        } else {
            $('#carreras-container').hide();
            $('#carreras').val(null).trigger('change');
        }
    });

    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var tutor = $('#tutor').val();
    var rol = $('#rol').val();
    var carrerasSeleccionadas = $('#carreras').val();

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

    if (rol == '4' && (!carrerasSeleccionadas || carrerasSeleccionadas.length === 0)) {
        mensajesError.push("Debe seleccionar al menos una carrera para el coordinador.");
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

    $('#tutor, #rol, #carreras').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}