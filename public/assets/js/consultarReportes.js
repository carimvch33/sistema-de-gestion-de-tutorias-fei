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

    function downloadTutoringReport(idReport) {
        var form = $('<form>', {
            'method': 'POST',
            'action': './generarReporteTutoria.php'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'idTutoria',
            'value': idReport
        }));
        $('body').append(form);
        form.submit();
    } 

    function openExportModal() {
        $.ajax({
            url: './getCarrerasPorCoordinador.php',
            method: 'GET',
            dataType: 'json',
            success: function(carreras) {
                if (!carreras.carreras || carreras.carreras.length === 0) {
                    Swal.fire('Error', 'No se pudo cargar la lista de carreras', 'error');
                    return;
                }
            
                let opcionesCarreras = carreras.carreras.map(carrera =>
                    `<option value="${carrera.idCarrera}">${carrera.carrera}</option>`
                ).join('');                         
    
                Swal.fire({
                    title: 'Generar concentrado de reportes',
                    html: `
                        <div style="text-align: left;">
                            <div style="margin-bottom: 1rem;">
                                <label for="numTutoria"><strong>Número de sesión:</strong></label>
                                <select id="numTutoria" class="swal2-input" style="width: 100%;">
                                    <option value="">Selecciona</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
    
                            <div style="margin-bottom: 1rem;">
                                <label for="tipoReporte"><strong>Tipo de reporte:</strong></label>
                                <select id="tipoReporte" class="swal2-input" style="width: 100%;">
                                    <option value="problematicas">Problemas Académicos</option>
                                    <option value="comentarios">Comentarios sobre tutorías</option>
                                </select>
                            </div>
    
                            <div style="margin-bottom: 1rem;">
                                <label for="carreras"><strong>Selecciona carrera(s):</strong></label>
                                <select id="carreras" class="swal2-input" multiple style="width: 100%; height: auto;">
                                    ${opcionesCarreras}
                                </select>
                                <small>Puedes usar Ctrl o Cmd para seleccionar múltiples.</small>
                            </div>
    
                            <div style="margin-bottom: 1rem;">
                                <strong>Formato:</strong><br>
                                <label style="margin-right: 1rem;">
                                    <input type="radio" name="formato" value="pdf" checked> PDF
                                </label>
                                <label>
                                    <input type="radio" name="formato" value="csv"> CSV
                                </label>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Generar',
                    customClass: {
                        confirmButton: 'swal2-confirm greenButton'
                    },
                    focusConfirm: false,
                    preConfirm: () => {
                        const numTutoria = $('#numTutoria').val();
                        const tipoReporte = $('#tipoReporte').val();
                        const formato = $('input[name="formato"]:checked').val();
                        const carreras = $('#carreras').val();

                        if (!numTutoria || !tipoReporte || !formato || !carreras || carreras.length === 0) {
                            Swal.showValidationMessage('Debes completar todos los campos');
                            return false;
                        }

                        return { numTutoria, tipoReporte, formato, carreras };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const { numTutoria, tipoReporte, formato, carreras } = result.value;
                        if (formato === 'csv') {
                            generateReporteCSV(numTutoria, tipoReporte, carreras);
                        } else {
                            generateReportePDF(numTutoria, tipoReporte, carreras);
                        }
                    }
                });
            },
            error: function() {
                Swal.fire('Error', 'No se pudieron obtener las carreras del coordinador', 'error');
            }
        });
    }

    function generateReporteCSV(numTutoria, tipoReporte) {
        var form = $('<form>', {
            method: 'POST',
            action: './generarReporteCSV.php'
        }).append(
            $('<input>', { type: 'hidden', name: 'numTutoria', value: numTutoria }),
            $('<input>', { type: 'hidden', name: 'tipoReporte', value: tipoReporte }),
            $('<input>', { type: 'hidden', name: 'carreras', value: $('#carreras').val() })
        );
    
        $('body').append(form);
        form.submit();
    }

    function generateReportePDF(numTutoria, tipoReporte) {
        var form = $('<form>', {
            method: 'POST',
            action: './generarReportePDF.php'
        }).append(
            $('<input>', { type: 'hidden', name: 'numTutoria', value: numTutoria }),
            $('<input>', { type: 'hidden', name: 'tipoReporte', value: tipoReporte }),
            $('<input>', { type: 'hidden', name: 'carreras', value: $('#carreras').val() })
        );
    
        $('body').append(form);
        form.submit();
    }

    $(document).on('click', '.view', function () {
        var idReporte = $(this).data('id-reporte');
        showTutoria(idReporte);
    });

    $(document).on('click', '.download', function () {
        var idReporte = $(this).data('id-reporte');
        downloadTutoringReport(idReporte);
    });

    $(document).on('click', '#btnExportarConcentrado', function () {
        openExportModal();
    });
});