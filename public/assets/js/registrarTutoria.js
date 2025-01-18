$(document).ready(function () {
    $("#carrera").select2();
    $("#periodo").select2();
    
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
    var periodoSeleccionado = $('#periodo').val();
    var modalidadSeleccionada = $('input[name="modalidad"]:checked').val();
    var periodoAtencionSeleccionado = $('input[name="periodoAtencion"]:checked').val();
    var fecha = $('#fecha').val();
    var horaInicio = $('#hora_inicio').val();
    var horaFinal = $('#hora_final').val();

    $('.form-control').removeClass("borderRed borderGreen");
    $('.form-check-input').removeClass("borderRed");

    var error = false;

    if (!carreraSeleccionada || !tutoriaSeleccionada || !periodoSeleccionado || !modalidadSeleccionada || !periodoAtencionSeleccionado) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!carreraSeleccionada) $('#carrera').addClass("borderRed");
        if (!tutoriaSeleccionada) $('#numTutoria').addClass("borderRed");
        if (!periodoSeleccionado) $('#periodo').addClass("borderRed");
        if (!modalidadSeleccionada) $('input[name="modalidad"]').addClass("borderRed");
        if (!periodoAtencionSeleccionado) $('input[name="periodoAtencion"]').addClass("borderRed");

        error = true;
    }

    if (horaInicio && horaFinal && horaInicio > horaFinal) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>La hora final no puede ser anterior a la hora de inicio.</p>',
            showConfirmButton: false,
            timer: 3500
        });
        error = true;
        $('#hora_inicio, #hora_final').addClass("borderRed");
    }

    if (error) {
        return;
    }

    $('#carrera, #numTutoria, #periodo, #fecha, #hora_inicio, #hora_final, #notas, #lugar').addClass("borderGreen");

    $("#form").submit();
}