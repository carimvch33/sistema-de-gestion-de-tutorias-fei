$(document).ready(function () {
    $('#profesoresTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-MX.json",
            "emptyTable": "<div class='empty-table-message'>No hay profesores disponibles</div>",
            "zeroRecords": "No se encontraron coincidencias"
        },
        "paging": true,
        "searching": true,
        "ordering": true,
        "pageLength": 10,
        "autoWidth": true,
        "responsive": true,
        "dom": '<"top-left"l><"top-right"f><"top-left"B>t<"bottom-left"i><"bottom-right"p>r',
        "buttons": [
            {
                extend: 'collection',
                className: 'custom-html-collection',
                buttons: [
                    '<h3> == Exportar</h3>',
                    'pdf',
                    'print',
                    'excel',
                    'copy',
                    '<h3 class="not-top-heading"> == Visibilidad de columnas</h3>',
                    'colvis'
                ]
            }
        ]
    });

    function confirmDelete(idTutor, csrfToken) {
        Swal.fire({
            title: '¿Estás seguro de eliminar este registro?',
            text: "No podrás revertir esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28AD56',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "eliminarProfesor.php",
                    data: {
                        idTutor: idTutor,
                        csrf_token: csrfToken
                    },
                    dataType: 'json',
                    error: function () {
                        Swal.fire({
                            title: '¡Oh no!',
                            text: 'Ha ocurrido un error, intente de nuevo, por favor.',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 3500
                        });
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: '¡Registro eliminado!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 3500
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
                    }
                });
            }
        });
    }

    function editTutor(idTutor) {
        var form = $('<form>', {
            'method': 'POST',
            'action': 'editarProfesor.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idTutor',
            'value': idTutor
        }));
        $('body').append(form);
        form.submit();
    }

    $(document).on('click', '.delete', function () {
        var idTutor = $(this).data('id-tutor');
        var csrfToken = $(this).data('csrf-token');
        confirmDelete(idTutor, csrfToken);
    });

    $(document).on('click', '.edit', function () {
        var idTutor = $(this).data('id-tutor');
        editTutor(idTutor);
    });
});