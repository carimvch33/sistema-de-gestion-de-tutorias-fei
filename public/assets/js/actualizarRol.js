$(document).ready(function () {
    $("#tutor").select2();
    $("#rol").select2();
    $("#carreras").select2({ width: '100%' });

    $('#rol').on('change', function () {
        var selectedRole = $(this).val();
        var carrerasContainer = $('#carreras-container');
        var labelCarreras = $('label[for="carreras"]');
        var selectCarreras = $('#carreras');

        if (selectedRole == '4') {
            carrerasContainer.show();
            labelCarreras.html('Carreras a coordinar: <span class="text-danger">*</span>');
            selectCarreras.select2({ width: '100%', maximumSelectionLength: 0 });

        } else if (selectedRole == '5') {
            carrerasContainer.show();
            labelCarreras.html('Carrera a cargo: <span class="text-danger">*</span>');
            selectCarreras.select2({ width: '100%', maximumSelectionLength: 0 });
            
            if (selectCarreras.val() && selectCarreras.val().length > 1) {
                selectCarreras.val(null).trigger('change');
            }

        } else {
            carrerasContainer.hide();
            selectCarreras.val(null).trigger('change');
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
    $('.select2-selection').removeClass("borderRed");

    var error = false;
    var mensajesError = [];

    if (!tutor) {
        mensajesError.push("Seleccione un profesor.");
        $('#tutor').next('.select2-container').find('.select2-selection').addClass("borderRed");
        error = true;
    }

    if (!rol) {
        mensajesError.push("Seleccione un rol.");
        $('#rol').next('.select2-container').find('.select2-selection').addClass("borderRed");
        error = true;
    }

    if ((rol == '4' || rol == '5') && (!carrerasSeleccionadas || carrerasSeleccionadas.length === 0)) {
        mensajesError.push("Debe seleccionar al menos una carrera para el rol asignado.");
        $('#carreras').next('.select2-container').find('.select2-selection').addClass("borderRed");
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

    $("#form").submit();
}