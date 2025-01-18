$(document).ready(function () {
    $("#profesor").select2();
    $("#programa").select2();
    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var nombre = $('#nombre').val();
    var nrc = $('#nrc').val();
    var profesor = $('#profesor').val();
    var programa = $('#programa').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!nombre || !nrc || !profesor || !programa) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!nombre) $('#nombre').addClass("borderRed");
        if (!nrc) $('#nrc').addClass("borderRed");
        if (!profesor) $('#profesor').addClass("borderRed");
        if (!programa) $('#programa').addClass("borderRed");

        error = true;
    }

    if (error) {
        return;
    }

    $('#nombre, #nrc, #profesor, #programa').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}