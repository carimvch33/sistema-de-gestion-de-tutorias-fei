$(document).ready(function() {
    $("#enviar").on("click", function(e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var periodo = $('#periodo').val();
    var actual = $('#actual').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!periodo || !actual) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!periodo) $('#periodo').addClass("borderRed");
        if (!actual) $('#actual').addClass("borderRed");

        error = true;
    }

    if (error) {
        return false;
    }

    $('#periodo, #actual').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}