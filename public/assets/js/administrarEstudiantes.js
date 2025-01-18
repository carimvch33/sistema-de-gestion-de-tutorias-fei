$(document).ready(function () {
    $('#estudiantesTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-MX.json",
            "emptyTable": "<div class='empty-table-message'>No hay estudiantes disponibles</div>",
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

    function confirmDelete(idTutorado, csrfToken) {
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
                    url: "eliminarEstudiante.php",
                    data: {
                        idTutorado: idTutorado,
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

    function editTutorado(idTutorado) {
        var form = $('<form>', {
            'method': 'POST',
            'action': 'editarEstudiante.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idTutorado',
            'value': idTutorado
        }));
        $('body').append(form);
        form.submit();
    }

    $(document).on('click', '.delete', function () {
        var idTutorado = $(this).data('id-tutorado');
        var csrfToken = $(this).data('csrf-token');
        confirmDelete(idTutorado, csrfToken);
    });

    $(document).on('click', '.edit', function () {
        var idTutorado = $(this).data('id-tutorado');
        editTutorado(idTutorado);
    });
});