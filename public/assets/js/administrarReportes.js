$(document).ready(function () {
    $('#reportesTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-MX.json",
            "emptyTable": "<div class='empty-table-message'>No hay reportes de tutorías disponibles</div>",
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

    function confirmDelete(idReporte, csrfToken) {
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
                    url: "./eliminarReporteTutoria.php",
                    cache: false,
                    data: {
                        idReporte: idReporte,
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
                        });
                        location.reload();
                    }
                });
            }
        });
    }

    function editReporte(idReporte) {
        var form = $('<form>', {
            'method': 'POST',
            'action': './editarReporte.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idReporte',
            'value': idReporte
        }));
        $('body').append(form);
        form.submit();
    }

    function showTutoria(idReporte) {
        var form = $('<form>', {
            'method': 'POST',
            'action': './verReporteTutoria.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idTutoria',
            'value': idReporte
        }));
        $('body').append(form);
        form.submit();
    }

    $(document).on('click', '.delete', function () {
        var idReporte = $(this).data('id-reporte');
        var csrfToken = $(this).data('csrf-token');
        confirmDelete(idReporte, csrfToken);
    });

    $(document).on('click', '.edit', function () {
        var idReporte = $(this).data('id-reporte');
        editReporte(idReporte);
    });

    $(document).on('click', '.view', function () {
        var idReporte = $(this).data('id-reporte');
        showTutoria(idReporte);
    });

    let filtrosSeleccionados = [];

    $(document).on('click', '.chip', function () {
        var filtro = $(this).data('carrera');
        
        if ($(this).hasClass('chip-selected')) {
            filtrosSeleccionados = filtrosSeleccionados.filter(f => f !== filtro);
            $(this).removeClass('chip-selected');
        } else {
            filtrosSeleccionados.push(filtro);
            $(this).addClass('chip-selected');
        }

        let regexFiltro = filtrosSeleccionados.length > 0 ? filtrosSeleccionados.join('|') : '';
        $('#reportesTable').DataTable().column(0).search(regexFiltro, true, false).draw();

        $('#clearFilters').toggle(filtrosSeleccionados.length > 0);
    });

    $('#clearFilters').on('click', function () {
        filtrosSeleccionados = [];
        $('.chip').removeClass('chip-selected');
        $('#reportesTable').DataTable().column(0).search('').draw();
        $(this).hide();
    });
});