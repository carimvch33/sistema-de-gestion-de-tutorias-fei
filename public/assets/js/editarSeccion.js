$(document).ready(function () {
    $("#idProfesor, #idExperienciaEducativa, #idPeriodo").select2();
    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var nrc = $('#nrc').val();
    var idProfesor = $('#idProfesor').val();
    var idExperienciaEducativa = $('#idExperienciaEducativa').val();
    var idPeriodo = $('#idPeriodo').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!nrc || !idProfesor || !idExperienciaEducativa || !idPeriodo) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!nrc) $('#nrc').addClass("borderRed");
        if (!idProfesor) $('#idProfesor').addClass("borderRed");
        if (!idExperienciaEducativa) $('#idExperienciaEducativa').addClass("borderRed");
        if (!idPeriodo) $('#idPeriodo').addClass("borderRed");

        error = true;
    }

    if (error) {
        return;
    }

    $('#nrc, #idProfesor, #idExperienciaEducativa, #idPeriodo').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}