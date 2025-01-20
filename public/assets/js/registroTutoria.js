$(document).ready(function () {
    $("#carrera").select2();

    $('#lugar').on('input', function () {
        if ($(this).val().length >= 300) {
            $(this).val($(this).val().substring(0, 300));
            Swal.fire({
                title: 'Límite alcanzado',
                text: 'Has alcanzado el máximo de 300 caracteres para el campo Lugar.',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    $('#notas').on('input', function () {
        if ($(this).val().length >= 500) {
            $(this).val($(this).val().substring(0, 500));
            Swal.fire({
                title: 'Límite alcanzado',
                text: 'Has alcanzado el máximo de 500 caracteres para el campo Notas.',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });
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
    var periodoSeleccionado = $('#periodoE').val();
    var modalidadSeleccionada = $('input[name="modalidad"]:checked').val();
    var periodoAtencionSeleccionado = $('input[name="periodoAtencion"]:checked').val();
    var fecha = $('#fecha').val();
    var horaInicio = $('#hora_inicio').val();
    var horaFinal = $('#hora_final').val();
    var notas = $('#notas').val();
    var lugar = $('#lugar').val();
    var archivo = $('#archivo_horario').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    var mensajeError = '';
    if (!carreraSeleccionada || !tutoriaSeleccionada || !periodoSeleccionado || !modalidadSeleccionada || !periodoAtencionSeleccionado) {
        mensajeError += '<p>Todos los campos obligatorios deben ser completados.</p>';
        error = true;
    }

    if (horaInicio && horaFinal && horaInicio > horaFinal) {
        mensajeError += '<p>La hora final no puede ser anterior a la hora de inicio.</p>';
        error = true;
    }

    if (error) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: mensajeError,
            showConfirmButton: false,
            timer: 3500
        });
        return;
    }

    $('#carrera, #numTutoria, #periodoE, #fecha, #hora_inicio, #hora_final, #notas, #lugar').removeClass("borderRed").addClass("borderGreen");

    $('#form').off('submit').submit();
}