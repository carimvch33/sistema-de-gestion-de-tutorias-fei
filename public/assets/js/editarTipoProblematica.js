$(document).ready(function () {

    $('#name').on('input', function () {
        if ($(this).val().length >= 200) {
            $(this).val($(this).val().substring(0, 200));
            Swal.fire({
                title: 'Límite alcanzado',
                text: 'Has alcanzado el máximo de 200 caracteres para el campo Descripción.',
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
    var name = $('#name').val();

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!name) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!name) $('#name').addClass("borderRed");

        error = true;
    }

    if (error) {
        return;
    }

    $('#name').removeClass("borderRed").addClass("borderGreen");

    $("#form").submit();
}