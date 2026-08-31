$(document).ready(function () {
    $("#carrera").select2();

    var experiencias = [];
    var profesores = [];
    var problematicasOptions = [];
    var secciones = [];

    cargarDatosIniciales();

    $("#carrera")
        .on("focus", function () {
            $(this).data("previous", $(this).val());
        })
        .on("change", function () {
            var previousCareerValue = $(this).data("previous");
            var idCarrera = $(this).val();

            Swal.fire({
                title: "Cambiar la carrera",
                text: "Al cambiar la carrera, las problemáticas agregadas se perderán. ¿Deseas continuar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, cambiar",
                cancelButtonText: "No, cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#problematicaTable tbody").empty();
                    cargarSesionesTutoria(idCarrera);
                } else {
                    $("#carrera")
                        .val(previousCareerValue)
                        .trigger("change.select2");
                }
            });
        });

    function cargarSesionesTutoria(idCarrera) {
        var idReporteActual = $('input[name="idReporte"]').val();

        $.ajax({
            url: "./getSesionesTutoria.php",
            type: "POST",
            dataType: "json",
            data: {
            idCarrera: idCarrera,
            idReporteActual: idReporteActual,
            csrf_token: $('input[name="csrf_token"]').val(),
            },
            success: function (response) {
            if (response.error) {
                Swal.fire("Error", response.error, "error");
                return;
            }

            let $select = $("#sesionTutoria");
            let selectedSesion = $select.data("selected");
            $select.empty();
            $select.append(
                '<option value="" disabled selected>-----Selecciona la sesión de tutoría que reporta-----</option>'
            );

            response.sesiones.forEach(function (sesion) {
                let fechaMostrar =
                sesion.fechaInicio !== sesion.fechaFin
                    ? `${sesion.fechaInicio} a ${sesion.fechaFin}`
                    : sesion.fechaInicio;

                let selected =
                selectedSesion && sesion.idTutoria == selectedSesion
                    ? "selected"
                    : "";
                $select.append(
                `<option value="${sesion.idTutoria}" ${selected}>Sesión #${sesion.numTutoria} ${sesion.modalidad} - ${fechaMostrar} [${sesion.lugar}]</option>`
                );
            });

            if (selectedSesion) {
                $select.val(selectedSesion).trigger("change.select2");
            }

            actualizarDatosCarrera(idCarrera, function () {
                inicializarSelectsExistentes();
            });
            },
            error: function () {
            Swal.fire(
                "Error",
                "Hubo un error al cargar las sesiones de tutoría.",
                "error"
            );
            },
        });
    }

    $('input[name="tipo"]').on("change", function () {
        if ($(this).val() === "problematica") {
            $(".problematica-table").show();
        } else {
            $(".problematica-table").hide();
        }
    });

    $("#agregarFilaBtn").on("click", function () {
        agregarFilaProblematica();
    });

    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr").remove();
    });

    $("#enviar").on("click", function (e) {
        e.preventDefault();
        if (validarFormulario()) {
            // Agregar campo oculto con el valor del botón antes de enviar
            $('<input>').attr({
                type: 'hidden',
                name: 'accion',
                value: 'enviar'
            }).appendTo('#form');
            $("#form").submit();
        }
    });

    // FIX: Interceptar botón "Guardar Borrador" para validar el numero de alumnos en riesgo <= numAsistencias DEF-33
    $("#guardar").on("click", function (e) {
        e.preventDefault();
        if (validarFormulario()) {
            // Agregar campo oculto con el valor del botón antes de enviar
            $('<input>').attr({
                type: 'hidden',
                name: 'accion',
                value: 'borrador'
            }).appendTo('#form');
            $("#form").submit();
        }
    });

    // FIX: Validación en tiempo real mientras escribe DEF-33
    $("#numAsistencias, #numRiesgo").on("input blur", function () {
        var numAsistencias = parseInt($("#numAsistencias").val()) || 0;
        var numRiesgo = parseInt($("#numRiesgo").val()) || 0;

        if ($("#numAsistencias").val() && $("#numRiesgo").val()) {
            if (numRiesgo > numAsistencias) {
                $("#numRiesgo").addClass("is-invalid");
                $("#numAsistencias").addClass("is-invalid");
            } else {
                $("#numRiesgo").removeClass("is-invalid");
                $("#numAsistencias").removeClass("is-invalid");
            }
        }
    });

    function cargarDatosIniciales() {
        var idCarrera = $("#carrera").val();
        cargarSesionesTutoria(idCarrera);
    }

    /* FIX [DEF-35]: Agregado parámetro callback para ejecutar inicializarSelectsExistentes después de cargar datos */
    function actualizarDatosCarrera(idCarrera, callback) {
        $.ajax({
            url: "./getCarreraDatos.php",
            type: "POST",
            dataType: "json",
            data: {
                idCarrera: idCarrera,
                csrf_token: $('input[name="csrf_token"]').val(),
            },
            success: function (response) {
                experiencias = response.experiencias;
                profesores = response.profesores;
                problematicasOptions = response.problematicas;
                secciones = response.secciones;
                
                if (typeof callback === 'function') {
                    callback();
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "Hubo un error al obtener los datos de la carrera seleccionada.",
                    icon: "error",
                    confirmButtonText: "Aceptar",
                });
            },
        });
    }

    function inicializarSelectsExistentes() {
        $("#problematicaTable tbody tr").each(function () {
            var $fila = $(this);

            $fila.find("select").select2();

            $fila.find('select[name="experienciaE[]"]').change(function () {
                manejarCambioExperiencia($(this));
            });

            // FIX (DEF-39): Eliminado evento change de profesor que filtraba experiencias educativas
            // Esto permitía que al seleccionar un profesor, solo se mostraran las experiencias que imparte,
            // bloqueando la posibilidad de cambiar libremente la experiencia educativa

            $fila
                .find('select[name="problematica[]"]')
                .change(function () {
                    var selectedValue = $(this).val();
                    if (selectedValue === "otro") {
                        $(this)
                            .closest("td")
                            .find('input[name="otro[]"]')
                            .show();
                    } else {
                        $(this)
                            .closest("td")
                            .find('input[name="otro[]"]')
                            .hide();
                    }
                })
                .trigger("change");
        });
    }

    function agregarFilaProblematica() {
        var experienciasOptions = generarOpcionesExperiencias();
        var profesoresOptions = generarOpcionesProfesores();
        var problematicaOptionsHtml = generarOpcionesProblematicas();

        var nuevaFila = `
            <tr>
                <td>
                    <select name="experienciaE[]" class="form-control experiencia-educativa" required>
                        ${experienciasOptions}
                    </select>
                </td>
                <td>
                    <select name="profesor[]" class="form-control profesor-problematica" required>
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
        $("#problematicaTable tbody").append(nuevaFila);

        var $ultimaFila = $("#problematicaTable tbody tr:last");

        $ultimaFila.find("select").select2();

        $ultimaFila.find('select[name="experienciaE[]"]').change(function () {
            manejarCambioExperiencia($(this));
        });

        // FIX (DEF-39): Eliminado evento change de profesor que filtraba experiencias educativas
        // Esto permitía que al seleccionar un profesor, solo se mostraran las experiencias que imparte,
        // bloqueando la posibilidad de cambiar libremente la experiencia educativa

        $ultimaFila.find('select[name="problematica[]"]').change(function () {
            var selectedValue = $(this).val();
            if (selectedValue === "otro") {
                $(this).closest("td").find('input[name="otro[]"]').show();
            } else {
                $(this).closest("td").find('input[name="otro[]"]').hide();
            }
        });
    }

    function manejarCambioExperiencia($selectExperiencia) {
        var experienciaId = $selectExperiencia.val();
        var $fila = $selectExperiencia.closest("tr");
        var $profesorSelect = $fila.find('select[name="profesor[]"]');

        var profesorSeleccionado = $profesorSelect.val();

        var profesoresFiltrados = secciones
            .filter(function (seccion) {
                return seccion.idExperienciaEducativa == experienciaId;
            })
            .map(function (seccion) {
                return profesores.find(function (profesor) {
                    return profesor.idTutor == seccion.idProfesor;
                });
            });

        var opcionesProfesor =
            '<option value="" disabled>Seleccione un profesor</option>';
        var profesoresUnicos = {};

        profesoresFiltrados.forEach(function (profesor) {
            if (profesor && !profesoresUnicos[profesor.idTutor]) {
                profesoresUnicos[profesor.idTutor] = profesor;
            }
        });

        for (var id in profesoresUnicos) {
            var profesor = profesoresUnicos[id];
            opcionesProfesor += `<option value="${profesor.idTutor}">${profesor.tutorNombre}</option>`;
        }

        $profesorSelect.html(opcionesProfesor).prop("disabled", false);

        // FIX (DEF-39): Siempre resetear el profesor a vacío al cambiar experiencia
        // Esto obliga al usuario a seleccionar explícitamente el profesor
        $profesorSelect.val(null).trigger("change.select2");
    }

    function manejarCambioProfesor($selectProfesor) {
        var profesorId = $selectProfesor.val();
        var $fila = $selectProfesor.closest("tr");
        var $experienciaSelect = $fila.find('select[name="experienciaE[]"]');

        var experienciaSeleccionada = $experienciaSelect.val();

        var experienciasFiltradas = secciones
            .filter(function (seccion) {
                return seccion.idProfesor == profesorId;
            })
            .map(function (seccion) {
                return experiencias.find(function (experiencia) {
                    return (
                        experiencia.idExperienciaEducativa ==
                        seccion.idExperienciaEducativa
                    );
                });
            });

        var opcionesExperiencia =
            '<option value="" disabled>Seleccione una experiencia educativa</option>';
        var experienciasUnicas = {};

        experienciasFiltradas.forEach(function (experiencia) {
            if (
                experiencia &&
                !experienciasUnicas[experiencia.idExperienciaEducativa]
            ) {
                experienciasUnicas[experiencia.idExperienciaEducativa] =
                    experiencia;
            }
        });

        for (var id in experienciasUnicas) {
            var experiencia = experienciasUnicas[id];
            opcionesExperiencia += `<option value="${experiencia.idExperienciaEducativa}">${experiencia.nombre}</option>`;
        }

        $experienciaSelect.html(opcionesExperiencia).prop("disabled", false);

        if (experienciasUnicas[experienciaSeleccionada]) {
            $experienciaSelect
                .val(experienciaSeleccionada)
                .trigger("change.select2");
        } else {
            $experienciaSelect.val(null).trigger("change.select2");
        }
    }

    function generarOpcionesExperiencias() {
        var opciones =
            '<option value="" disabled selected>Seleccione una experiencia educativa</option>';
        experiencias.forEach(function (experiencia) {
            opciones += `<option value="${experiencia.idExperienciaEducativa}">${experiencia.nombre}</option>`;
        });
        return opciones;
    }

    function generarOpcionesProfesores() {
        var opciones =
            '<option value="" disabled selected>Seleccione un profesor</option>';
        profesores.forEach(function (profesor) {
            opciones += `<option value="${profesor.idTutor}">${profesor.tutorNombre}</option>`;
        });
        return opciones;
    }

    function generarOpcionesProblematicas() {
        var opciones =
            '<option value="" disabled selected>Seleccione una problemática</option>';
        problematicasOptions.forEach(function (problematica) {
            opciones += `<option value="${problematica.idProblematica}">${problematica.descripcion}</option>`;
        });
        opciones += '<option value="otro">Otro</option>';
        return opciones;
    }

    function validarFormulario() {
        var valid = true;
        var errores = [];

        $("#form [required]").each(function () {
            if ($(this).val() === "" || $(this).val() === null) {
                valid = false;
                $(this).addClass("is-invalid");
                errores.push(
                    'El campo "' +
                        $(this)
                            .closest(".form-group")
                            .find("label")
                            .text()
                            .trim() +
                        '" es obligatorio.'
                );
            } else {
                $(this).removeClass("is-invalid");
            }
        });

        // FIX: Validar que alumnos en riesgo ≤ alumnos asistentes DEF-33
        var numAsistencias = parseInt($("#numAsistencias").val()) || 0;
        var numRiesgo = parseInt($("#numRiesgo").val()) || 0;

        if (numRiesgo > numAsistencias) {
            valid = false;
            $("#numRiesgo").addClass("is-invalid");
            $("#numAsistencias").addClass("is-invalid");
            errores.push(
                "El número de alumnos en riesgo (" +
                    numRiesgo +
                    ") no puede ser mayor al número de alumnos que asistieron (" +
                    numAsistencias +
                    ")."
            );
        } else {
            $("#numRiesgo").removeClass("is-invalid");
            $("#numAsistencias").removeClass("is-invalid");
        }

        var fechaInicio = $("#fechaInicio").val();
        var fechaFin = $("#fechaFin").val();
        if (
            fechaInicio &&
            fechaFin &&
            new Date(fechaFin) < new Date(fechaInicio)
        ) {
            valid = false;
            $("#fechaFin").addClass("is-invalid");
            errores.push(
                "La fecha de fin no puede ser menor que la fecha de inicio."
            );
        } else {
            $("#fechaFin").removeClass("is-invalid");
        }

        if ($('input[name="tipo"]:checked').val() === "problematica") {
            if ($("#problematicaTable tbody tr").length === 0) {
                valid = false;
                errores.push("Debe agregar al menos una problemática.");
            }

            $("#problematicaTable tbody tr").each(function (index, row) {
                $(row)
                    .find("[required]")
                    .each(function () {
                        if ($(this).val() === "" || $(this).val() === null) {
                            valid = false;
                            $(this).addClass("is-invalid");
                            errores.push(
                                "Todos los campos de la problemática son obligatorios en la fila " +
                                    (index + 1) +
                                    "."
                            );
                        } else {
                            $(this).removeClass("is-invalid");
                        }
                    });

                var $problematicaSelect = $(row).find(
                    'select[name="problematica[]"]'
                );
                if ($problematicaSelect.val() === "otro") {
                    var $otroInput = $(row).find('input[name="otro[]"]');
                    if ($otroInput.val().trim() === "") {
                        valid = false;
                        $otroInput.addClass("is-invalid");
                        errores.push(
                            "Debe describir la problemática en la fila " +
                                (index + 1) +
                                "."
                        );
                    } else {
                        $otroInput.removeClass("is-invalid");
                    }
                }
            });
        }

        // FIX: Validar que no haya problemáticas duplicadas DEF-36
        if ($('input[name="tipo"]:checked').val() === "problematica") {
            var combinacionesVistas = [];

            $("#problematicaTable tbody tr").each(function (index, row) {
                var experiencia = $(row).find('select[name="experienciaE[]"]').val();
                var profesor = $(row).find('select[name="profesor[]"]').val();
                var problematica = $(row).find('select[name="problematica[]"]').val();
                
                if (experiencia && profesor && problematica && problematica !== "otro") {
                    var combinacion = experiencia + '|' + profesor + '|' + problematica;
                    
                    if (combinacionesVistas.includes(combinacion)) {
                        valid = false;
                        $(row).find('select[name="experienciaE[]"]').addClass("is-invalid");
                        $(row).find('select[name="profesor[]"]').addClass("is-invalid");
                        $(row).find('select[name="problematica[]"]').addClass("is-invalid");
                        errores.push(
                            "La problemática en la línea " + (index + 1) + 
                            " está duplicada. Ya existe un registro con la misma Experiencia Educativa, Profesor y Problemática."
                        );
                    } else {
                        combinacionesVistas.push(combinacion);
                    }
                }
            });
        }

        // FIX (DEF-34): Validar que la suma de alumnos en problemáticas ≤ alumnos que asistieron
        if ($('input[name="tipo"]:checked').val() === "problematica") {
            var numAsistencias = parseInt($("#numAsistencias").val()) || 0;
            var sumaAlumnosProblematicas = 0;

            $("#problematicaTable tbody tr").each(function (index, row) {
                var numAlumnos = parseInt($(row).find('input[name="numAlumnos[]"]').val()) || 0;
                sumaAlumnosProblematicas += numAlumnos;
            });

            if (sumaAlumnosProblematicas > numAsistencias) {
                valid = false;
                $("#numAsistencias").addClass("is-invalid");
                $("#problematicaTable tbody tr").each(function (index, row) {
                    $(row).find('input[name="numAlumnos[]"]').addClass("is-invalid");
                });
                errores.push(
                    "La suma de alumnos en las problemáticas (" + sumaAlumnosProblematicas + 
                    ") no puede ser mayor al número de alumnos que asistieron (" + 
                    numAsistencias + ")."
                );
            } else {
                $("#numAsistencias").removeClass("is-invalid");
                $("#problematicaTable tbody tr").each(function (index, row) {
                    $(row).find('input[name="numAlumnos[]"]').removeClass("is-invalid");
                });
            }
        }

        if (!valid && errores.length > 0) {
            Swal.fire({
                title: "Errores en el formulario",
                icon: "error",
                html: errores.join("<br>"),
            });
        }

        return valid;
    }
});
