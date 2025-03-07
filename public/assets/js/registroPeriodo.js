$(document).ready(function() {   
    $("#enviar").on("click", function(e) {
        e.preventDefault();
        validarFormulario();
    });
});

function validarFormulario() {
    var periodo = $('#periodo').val();
    var actual = $('#actual').prop('checked'); 

    $('.form-control').removeClass("borderRed borderGreen");

    var error = false;
    if (!periodo) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        $('#periodo').addClass("borderRed");
        error = true;
    }

    if (error) {
        return; 
    }

    $('#periodo').removeClass("borderRed").addClass("borderGreen");

    if (actual) {
        $('#actual').val(1);
    } else {
        $('#actual').val(0);
    }

    $("#form").submit();
}
