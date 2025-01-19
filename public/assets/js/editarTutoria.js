$(document).ready(function () {

    $('#lugar').on('input', function () {
        if ($(this).val().length >= 300) {
            $(this).val($(this).val().substring(0, 300));
            alert('Has alcanzado el máximo de 300 caracteres para el campo Lugar.');
        }
    });

    $('#notas').on('input', function () {
        if ($(this).val().length >= 500) {
            $(this).val($(this).val().substring(0, 500));
            alert('Has alcanzado el máximo de 500 caracteres para el campo Notas.');
        }
    });

    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var carreraSeleccionada = $('#carrera').val();
    var tutoriaSeleccionada = $('#numTutoria').val();
    var periodoSeleccionado = $('#periodo').val();
    var modalidadSeleccionada = $('input[name="modalidad"]:checked').val();
    var periodoAtencionSeleccionado = $('input[name="periodoAtencion"]:checked').val();
    var fecha = $('#fecha').val();
    var horaInicio = $('#hora_inicio').val();
    var horaFinal = $('#hora_final').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!carreraSeleccionada || !tutoriaSeleccionada || !periodoSeleccionado || !modalidadSeleccionada || !periodoAtencionSeleccionado) {
        alert('Todos los campos obligatorios deben ser completados.');
        error = true;
    }

    if (horaInicio && horaFinal && horaInicio > horaFinal) {
        alert('La hora final no puede ser anterior a la hora de inicio.');
        error = true;
    }

    if (error) {
        if (!carreraSeleccionada) $('#carrera').addClass("borderRed");
        if (!tutoriaSeleccionada) $('#numTutoria').addClass("borderRed");
        if (!periodoSeleccionado) $('#periodo').addClass("borderRed");
        if (!modalidadSeleccionada) $('input[name="modalidad"]').closest('.form-check').addClass("borderRed");
        if (!periodoAtencionSeleccionado) $('input[name="periodoAtencion"]').closest('.form-check').addClass("borderRed");
        return false;
    }

    $("#form").submit();
}