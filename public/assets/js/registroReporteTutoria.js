$(document).ready(function () {
    $("#carrera").select2();

    var experiencias = [];
    var profesores = [];
    var problematicasOptions = [];
    var problematicas = problematicasInject || [];
    var secciones = [];

    $("#carrera").on("focus", function () {
        $(this).data("previous", $(this).val());
    });

    $("#carrera").on("change", function () {
        var previousCareerValue = $(this).data("previous");
        var idCarrera = $(this).val();

        // Si ya hay problemáticas agregadas, confirmar con el usuario
        if ($("#problematicaTable tbody tr").length > 0) {
            Swal.fire({
                title: "Cambiar la carrera",
                text: "Al cambiar la carrera, las problemáticas agregadas se perderán. ¿Deseas continuar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, cambiar",
                cancelButtonText: "No, cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    // El usuario confirma cambiar la carrera
                    // Vaciar la tabla de problemáticas
                    $("#problematicaTable tbody").empty();

                    // Actualizar los datos de la nueva carrera
                    cargarSesionesTutoria(idCarrera);
                } else {
                    // El usuario canceló, restablecer el valor anterior de la carrera
                    $("#carrera")
                        .val(previousCareerValue)
                        .trigger("change.select2");
                }
            });
        } else {
            // Si no hay problemáticas agregadas, simplemente actualizar los datos
            cargarSesionesTutoria(idCarrera);
        }
    });

    function cargarSesionesTutoria(idCarrera) {
        $.ajax({
            url: "getSesionesTutoria.php",
            type: "POST",
            dataType: "json",
            data: {
                idCarrera: idCarrera,
                csrf_token: $('input[name="csrf_token"]').val(),
            },
            success: function (response) {
                if (response.error) {
                    Swal.fire("Error", response.error, "error");
                    return;
                }

                let $select = $("#sesionTutoria");
                $select.empty();
                $select.append(
                    '<option value="" disabled selected>-----Selecciona la sesión de tutoría que reporta-----</option>'
                );

                response.sesiones.forEach(function (sesion) {
                    let fechaMostrar =
                        sesion.fechaInicio !== sesion.fechaFin
                            ? `${sesion.fechaInicio} a ${sesion.fechaFin}`
                            : sesion.fechaInicio;

                    $select.append(
                        `<option value="${sesion.idTutoria}">
                        Sesión #${sesion.numTutoria} ${sesion.modalidad} - ${fechaMostrar} [${sesion.lugar}]
                    </option>`
                    );
                });

                actualizarDatosCarrera(idCarrera);
            },
            error: function () {
                Swal.fire("Error", "Hubo un error al cargar las sesiones de tutoría.", "error");
            }
        });
    }

    function actualizarDatosCarrera(idCarrera) {
        $.ajax({
            url: "getCarreraDatos.php",
            type: "POST",
            dataType: "json",
            data: {
                idCarrera: idCarrera,
                csrf_token: $('input[name="csrf_token"]').val(),
            },
            success: function (response) {
                if (response.error) {
                    Swal.fire("Error", response.error, "error");
                    return;
                }

                experiencias = response.experiencias;
                profesores = response.profesores;
                problematicasOptions = response.problematicas; // FIX (DEF-36): No funcionaba el agregar problematica
                secciones = response.secciones; // Agregamos las secciones
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

    $('input[name="tipo"]').change(function () {
        if ($(this).val() === "ninguno") {
            $(".problematica-table").hide();
            $("#problematicaTable tbody").empty();
        } else {
            $(".problematica-table").show();
        }
    });

    $("#agregarFilaBtn").click(function () {
        var idCarrera = $("#carrera").val();
        if (!idCarrera) {
            Swal.fire(
                "Error",
                "Por favor, selecciona una carrera antes de agregar una problemática.",
                "warning"
            );
            return;
        }

        // Verificar si las variables están cargadas
        if (
            experiencias.length === 0 ||
            profesores.length === 0 ||
            problematicasOptions.length === 0 ||
            secciones.length === 0
        ) {
            Swal.fire(
                "Información",
                "Los datos de la carrera aún no se han cargado. Por favor, espera un momento.",
                "info"
            );
            return;
        }

        // Generar las opciones iniciales de experiencias y profesores
        var experienciasOptions =
            '<option value="" disabled selected>-----Selecciona una experiencia educativa-----</option>';
        experiencias.forEach(function (exp) {
            experienciasOptions += `<option value="${exp.idExperienciaEducativa}">${exp.nombre}</option>`;
        });

        var profesoresOptions =
            '<option value="" disabled selected>-----Selecciona un profesor-----</option>';
        profesores.forEach(function (prof) {
            profesoresOptions += `<option value="${prof.idTutor}">${prof.tutorNombre}</option>`;
        });

        var problematicaOptionsHtml =
            '<option value="" disabled selected>-----Problemática-----</option>';
        problematicasOptions.forEach(function (problematica) {
            problematicaOptionsHtml += `<option value="${problematica.idProblematica}">${problematica.descripcion}</option>`;
        });
        problematicaOptionsHtml += '<option value="otro">Otro</option>';

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
                    <div class="form-group">
                        <select class="form-control problematica-select" name="problematica[]" required>
                            ${problematicaOptionsHtml}
                        </select>
                        <textarea name="otro[]" class="form-control mt-2 otro-textarea" placeholder="Describe la problemática, máximo 500 caracteres" maxlength="500" style="display: none;"></textarea>
                    </div>
                </td>
                <td>
                    <input type="number" name="numAlumnos[]" class="form-control" placeholder="Número de alumnos" min="1" step="1" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger eliminarFila"><i class="fas fa-trash-alt"></i> Eliminar</button>
                </td>
            </tr>
        `;
        $("#problematicaTable tbody").append(nuevaFila);

        var $ultimaFila = $("#problematicaTable tbody tr:last");

        $ultimaFila
            .find(".experiencia-educativa, .profesor-problematica")
            .select2();

        $ultimaFila.find(".experiencia-educativa").change(function () {
            var experienciaId = $(this).val();
            var $profesorSelect = $(this)
                .closest("tr")
                .find(".profesor-problematica");

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
                '<option value="" disabled selected>-----Selecciona un profesor-----</option>';

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
            $profesorSelect.val(null).trigger("change.select2");
        });
    });

    $(document).on("change", ".problematica-select", function () {
        var $fila = $(this).closest("tr");
        var $textarea = $fila.find(".otro-textarea");
        if ($(this).val() === "otro") {
            $textarea.show().prop("required", true);
        } else {
            $textarea.hide().prop("required", false).val("");
        }
    });

    $(document).on("click", ".eliminarFila", function () {
        $(this).closest("tr").remove();
    });

    
    $("#enviar").on("click", function (e) {
        e.preventDefault();
        if (validarFormulario()) {
            $('<input>').attr({
                type: 'hidden',
                name: 'accion',
                value: 'enviar'
            }).appendTo('#form');
            $("#form").submit();
        }
    });

    // FIX: Interceptar ambos botones para validar el numero de alumnos en riesgo <= numAsistencias DEF-33
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

    // FIX: Validación en tiempo real mientras escribe para que numRiesgo <= numAsistencias DEF-33
    $("#numAsistencias, #numRiesgo").on("input blur", function () {
        var numAsistencias = parseInt($("#numAsistencias").val()) || 0;
        var numRiesgo = parseInt($("#numRiesgo").val()) || 0;

        if ($("#numAsistencias").val() && $("#numRiesgo").val()) {
            if (numRiesgo > numAsistencias) {
                $("#numRiesgo").addClass("is-invalid");
                $("#numAsistencias").addClass("is-invalid");
                $("#numRiesgo").attr("title", "No puede ser mayor a " + numAsistencias);
            } else {
                $("#numRiesgo").removeClass("is-invalid");
                $("#numAsistencias").removeClass("is-invalid");
                $("#numRiesgo").removeAttr("title");
            }
        }
    });

    function validarFormulario() {
        var valid = true;
        var errores = [];
        
        var tipoReporte = $('input[name="tipo"]:checked').val();
        var numFilasProblematica = $("#problematicaTable tbody tr").length;

        $("#form [required]").each(function () {
            if ($(this).val() === "" || $(this).val() === null) {
                valid = false;
                $(this).addClass("is-invalid");
                errores.push(
                    'El campo "' +
                        $(this).closest("div").find("label").text().trim() +
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
                "El número de alumnos en riesgo (" + numRiesgo + 
                ") no puede ser mayor al número de alumnos que asistieron (" + 
                numAsistencias + ")."
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

                var $problematicaSelect = $(row).find(".problematica-select");
                if ($problematicaSelect.val() === "otro") {
                    var $otroTextarea = $(row).find(".otro-textarea");
                    if ($otroTextarea.val().trim() === "") {
                        valid = false;
                        $otroTextarea.addClass("is-invalid");
                        errores.push(
                            "Debe describir la problemática en la fila " +
                                (index + 1) +
                                "."
                        );
                    } else {
                        $otroTextarea.removeClass("is-invalid");
                    }
                }
            });
        }

        // FIX (DEF-36): Validar que no haya problemáticas duplicadas 
        if ($('input[name="tipo"]:checked').val() === "problematica") {
            var combinacionesVistas = [];

            $("#problematicaTable tbody tr").each(function (index, row) {
                var experiencia = $(row).find(".experiencia-educativa").val();
                var profesor = $(row).find(".profesor-problematica").val();
                var problematica = $(row).find(".problematica-select").val();
                
                if (experiencia && profesor && problematica && problematica !== "otro") {
                    var combinacion = experiencia + '|' + profesor + '|' + problematica;
                    
                    if (combinacionesVistas.includes(combinacion)) {
                        valid = false;
                        $(row).find(".experiencia-educativa").addClass("is-invalid");
                        $(row).find(".profesor-problematica").addClass("is-invalid");
                        $(row).find(".problematica-select").addClass("is-invalid");
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

    $("#comentario").on("input", function () {
        var maxLength = 500;
        var currentLength = $(this).val().length;

        if (currentLength > maxLength) {
            $(this).val($(this).val().substring(0, maxLength));
            Swal.fire({
                title: "Límite alcanzado",
                text: "Has alcanzado el máximo de 500 caracteres para el campo Comentario.",
                icon: "info",
                timer: 2000,
                showConfirmButton: false,
            });
        }
    });

    $(document).on("input", ".otro-textarea", function () {
        var maxLength = 500;
        var currentLength = $(this).val().length;

        if (currentLength > maxLength) {
            $(this).val($(this).val().substring(0, maxLength));
            alert("Has alcanzado el límite de 500 caracteres.");
        }
    });
});
