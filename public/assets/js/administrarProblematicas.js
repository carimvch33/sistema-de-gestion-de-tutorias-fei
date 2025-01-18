$(document).ready(function() {
    $('#problematicaTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-MX.json",
            "emptyTable": "<div class='empty-table-message'>No hay problemáticas disponibles</div>",
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

    function confirmDelete(idProblematica, csrfToken) {
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
                    url: "eliminarProblematica.php",
                    data: {
                        idProblematica: idProblematica,
                        csrf_token: csrfToken
                    },
                    dataType: 'json',
                    error: function() {
                        Swal.fire({
                            title: '¡Oh no!',
                            text: 'Ha ocurrido un error, intente de nuevo, por favor.',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 3500
                        });
                    },
                    success: function(response) {
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

    function editProblematica(idProblematica) {
        var form = $('<form>', {
            'method': 'POST',
            'action': 'editarProblematica.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idProblematica',
            'value': idProblematica
        }));
        $('body').append(form);
        form.submit();
    }

    $(document).on('click', '.delete', function() {
        var idProblematica = $(this).data('id-problematica');
        var csrfToken = $(this).data('csrf-token');
        confirmDelete(idProblematica, csrfToken);
    });

    $(document).on('click', '.edit', function() {
        var idProblematica = $(this).data('id-problematica');
        editProblematica(idProblematica);
    });
});