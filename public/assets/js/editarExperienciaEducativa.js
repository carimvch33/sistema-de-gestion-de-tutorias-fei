$(document).ready(function () {
    $("#programa").select2();
    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var nombre = $('#nombre').val();
    var programa = $('#programa').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!nombre || !programa) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!nombre) $('#nombre').addClass("borderRed");
        if (!programa) $('#programa').addClass("borderRed");

        error = true;
    }

    if (error) {
        return;
    }

    $('#nombre, #programa').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}