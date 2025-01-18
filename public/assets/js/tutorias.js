$(document).ready(function () {
    $('#tutoriasTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-MX.json",
            "emptyTable": "<div class='empty-table-message'>No hay sesiones de tutoría disponibles</div>",
            "zeroRecords": "No se encontraron coincidencias"
        },
        "paging": true,
        "searching": true,
        "ordering": true,
        "pageLength": 10,
        "autoWidth": true,
        "responsive": true,
        "dom": '<"top-left"l><"top-right"f><"top-left"B>t<"bottom-left"i><"bottom-right"p>r',
        layout: {
            topStart: 'buttons'
        },
        buttons: [
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

    function confirmDelete(idTutoria, csrfToken) {
        Swal.fire({
            title: '¿Estás seguro de eliminar este registro?',
            text: "No podrás revertir esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "./eliminarTutoria.php",
                    cache: false,
                    data: {
                        idTutoria: idTutoria,
                        csrf_token: csrfToken
                    },
                    error: function () {
                        Swal.fire({
                            title: '¡Oh no!',
                            text: 'Ha ocurrido un error, intente de nuevo, por favor.',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 3500
                        });
                    },
                    success: function () {
                        Swal.fire({
                            title: '¡Registro eliminado!',
                            text: 'El registro ha sido eliminado exitosamente.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3500
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            }
        });
    }

    function editTutoria(idTutoria) {
        var form = $('<form>', {
            'method': 'POST',
            'action': './datosTutoria.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idTutoria',
            'value': idTutoria
        }));
        $('body').append(form);
        form.submit();
    }

    $(document).on('click', '.delete', function () {
        var idTutoria = $(this).data('id-tutoria');
        var csrfToken = $(this).data('csrf-token');
        confirmDelete(idTutoria, csrfToken);
    });

    $(document).on('click', '.edit', function () {
        var idTutoria = $(this).data('id-tutoria');
        editTutoria(idTutoria);
    });
});