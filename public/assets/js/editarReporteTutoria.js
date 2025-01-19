$(document).ready(function () {
    $("#carrera").select2();

    var initialCareerValue = $('#carrera').val();

    $('#carrera').on('focus', function () {
        $(this).data('previous', $(this).val());
    }).on('change', function () {
        var previousCareerValue = $(this).data('previous');
        var idCarrera = $(this).val();

        Swal.fire({
            title: 'Cambiar la carrera',
            text: 'Al cambiar la carrera, las problemáticas agregadas se perderán. ¿Deseas continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#problematicaTable tbody').empty();

                $('#agregarFilaBtn').prop('disabled', true);

                $.ajax({
                    url: './getCarreraDatos.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        idCarrera: idCarrera,
                        csrf_token: $('input[name="csrf_token"]').val()
                    },
                    success: function (response) {
                        experiencias = response.experiencias;
                        profesores = response.profesores;
                        problematicaOptions = response.problematicas;

                        $('#agregarFilaBtn').prop('disabled', false);

                    },
                    error: function () {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un error al obtener los datos de la carrera seleccionada.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            } else {
                $('#carrera').val(previousCareerValue).trigger('change.select2');
            }
        });
    });

    $(document).on('change', '.problematicaSelect', function () {
        var selectedValue = $(this).val();
        if (selectedValue === 'otro') {
            $(this).closest('td').find('input[name="otro[]"]').show();
        } else {
            $(this).closest('td').find('input[name="otro[]"]').hide();
        }
    });

    $('input[name="tipo"]').on('change', function () {
        if ($(this).val() === 'problematica') {
            $('.problematica-table').show();
        } else {
            $('.problematica-table').hide();
        }
    });

    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });

    $("#agregarFilaBtn").on("click", function () {
        agregarFilaProblematica();
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });
});

function validarFormulario() {
    var carrera = $('#carrera').val();
    var periodo = $('input[name="periodo"]').val();
    var numTutoria = $('#numTutoria').val();
    var fechaInicio = $('#fechaInicio').val();
    var fechaFin = $('#fechaFin').val();
    var numAsistencias = $('#numAsistencias').val();
    var numRiesgo = $('#numRiesgo').val();
    var tipo = $('input[name="tipo"]:checked').val();
    var error = false;

    $('.form-control').removeClass("borderRed");

    if (!carrera || !periodo || !numTutoria || !fechaInicio || !fechaFin || !numAsistencias || !numRiesgo) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>Todos los campos obligatorios deben ser completados.</p>',
            showConfirmButton: false,
            timer: 3500
        });

        if (!carrera) $('#carrera').addClass("borderRed");
        if (!periodo) $('#periodo').addClass("borderRed");
        if (!numTutoria) $('#numTutoria').addClass("borderRed");
        if (!fechaInicio) $('#fechaInicio').addClass("borderRed");
        if (!fechaFin) $('#fechaFin').addClass("borderRed");
        if (!numAsistencias) $('#numAsistencias').addClass("borderRed");
        if (!numRiesgo) $('#numRiesgo').addClass("borderRed");

        error = true;
    }

    if (fechaInicio && fechaFin && new Date(fechaFin) < new Date(fechaInicio)) {
        Swal.fire({
            title: '¡Error!',
            icon: 'error',
            html: '<p>La fecha de fin no puede ser menor que la fecha de inicio.</p>',
            showConfirmButton: false,
            timer: 3500
        });
        $('#fechaFin').addClass("borderRed");
        error = true;
    }

    if (tipo === 'problematica') {
        $('#problematicaTable tbody tr').each(function () {
            var experiencia = $(this).find('select[name="experienciaE[]"]').val();
            var profesor = $(this).find('select[name="profesor[]"]').val();
            var problematica = $(this).find('select[name="problematica[]"]').val();
            if (problematica === 'otro') {
                problematica = $(this).find('input[name="otro[]"]').val();
            }
            var numAlumnos = $(this).find('input[name="numAlumnos[]"]').val();

            if (!experiencia || !profesor || !problematica || !numAlumnos) {
                Swal.fire({
                    title: '¡Error!',
                    icon: 'error',
                    html: '<p>Todos los campos en las problemáticas son obligatorios.</p>',
                    showConfirmButton: false,
                    timer: 3500
                });
                error = true;
                return false;
            }
        });
    }

    if (error) {
        return;
    }

    $("#form").submit();
}



function agregarFilaProblematica() {
    var experienciasOptions = '';
    experienciasOptions += '<option value="" disabled selected>Seleccione una experiencia educativa</option>';
    experiencias.forEach(function (experiencia) {
        experienciasOptions += '<option value="' + experiencia.idExperienciaEducativa + '">' + experiencia.nombre + '</option>';
    });

    var profesoresOptions = '';
    profesoresOptions += '<option value="" disabled selected>Seleccione un profesor</option>';
    profesores.forEach(function (profesor) {
        var nombreCompleto = profesor.tutorNombre;
        profesoresOptions += '<option value="' + profesor.idTutor + '">' + nombreCompleto + '</option>';
    });

    var problematicaOptionsHtml = '';
    problematicaOptionsHtml += '<option value="" disabled selected>Seleccione una problemática</option>';
    problematicaOptions.forEach(function (problematica) {
        problematicaOptionsHtml += '<option value="' + problematica.idProblematica + '">' + problematica.tipoProblematica + '</option>';
    });
    problematicaOptionsHtml += '<option value="otro">Otro</option>';

    var nuevaFila = `
        <tr>
            <td>
                <select name="experienciaE[]" class="form-control" required>
                    ${experienciasOptions}
                </select>
            </td>
            <td>
                <select name="profesor[]" class="form-control select-profesor" required>
                    ${profesoresOptions}
                </select>
            </td>
            <td>
                <select name="problematica[]" class="form-control problematicaSelect" required>
                    ${problematicaOptionsHtml}
                </select>
                <input type="text" name="otro[]" class="form-control" style="display:none;" placeholder="Especificar otra problemática">
            </td>
            <td>
                <input type="number" name="numAlumnos[]" class="form-control" min="0" step="1" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger remove-row">Eliminar</button>
            </td>
        </tr>
    `;
    $('#problematicaTable tbody').append(nuevaFila);
    
    $(".select-profesor").select2();


    $('.problematicaSelect').last().on('change', function () {
        var selectedValue = $(this).val();
        if (selectedValue === 'otro') {
            $(this).closest('td').find('input[name="otro[]"]').show();
        } else {
            $(this).closest('td').find('input[name="otro[]"]').hide();
        }
    });
}