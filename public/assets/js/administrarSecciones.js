$(document).ready(function () {
    $('#seccionesTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-MX.json",
            "emptyTable": "<div class='empty-table-message'>No hay secciones disponibles</div>",
            "zeroRecords": "No se encontraron coincidencias"
        },
        "paging": true,
        "searching": true,
        "ordering": true,
        "pageLength": 10,
        "autoWidth": true,
        "responsive": true,
    });


    $(document).on('click', '.edit', function () {
        var idSeccion = $(this).data('id-seccion');
        var form = $('<form>', {
            'method': 'POST',
            'action': 'actualizarSeccion.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idSeccion',
            'value': idSeccion
        }));
        $('body').append(form);
        form.submit();
    });

    function confirmDelete(idSeccion, csrfToken) {
        Swal.fire({
            title: '¿Estás seguro de eliminar esta sección?',
            text: "Esta acción no se puede revertir.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28AD56',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar la petición AJAX para eliminar la sección
                $.ajax({
                    type: "POST",
                    url: "eliminarSeccion.php",
                    data: {
                        idSeccion: idSeccion,
                        csrf_token: csrfToken
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: '¡Sección eliminada!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error',
                                showConfirmButton: false,
                                timer: 3500
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: '¡Oh no!',
                            text: 'Ha ocurrido un error, intente de nuevo por favor.',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 3500
                        });
                    }
                });
            }
        });
    }

    // Evento para el botón de eliminar
    $(document).on('click', '.delete', function () {
        var idSeccion = $(this).data('id-seccion');
        var csrfToken = $(this).data('csrf-token');
        confirmDelete(idSeccion, csrfToken);
    });
});