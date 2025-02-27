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
                    '<h3 class="not-top-heading"> == Visibilidad de columnas</h3>',
                    'colvis'
                ]
            }
        ]
    });

    function showTutoria(idTutoria) {
        var form = $('<form>', {
            'method': 'POST',
            'action': './verTutoria.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idTutoria',
            'value': idTutoria
        }));
        $('body').append(form);
        form.submit();
    }

    $(document).on('click', '.view-button', function () {
        var idTutoria = $(this).data('id-tutoria');
        showTutoria(idTutoria);
    });

});