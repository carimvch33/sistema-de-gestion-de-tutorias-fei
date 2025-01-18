$(document).ready(function () {
    $("#tipoProblematica").select2();

    $('#descripcion').on('input', function () {
        if ($(this).val().length >= 500) {
            $(this).val($(this).val().substring(0, 500));
            Swal.fire({
                title: 'Límite alcanzado',
                text: 'Has alcanzado el máximo de 500 caracteres para el campo Descripción.',
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
    var descripcion = $('#descripcion').val();
    var tipoProblematica = $('#tipoProblematica').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!descripcion || !tipoProblematica) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!descripcion) $('#descripcion').addClass("borderRed");
        if (!tipoProblematica) $('#tipoProblematica').addClass("borderRed");

        error = true;
    }

    if (error) {
        return;
    }

    $('#descripcion, #tipoProblematica').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}