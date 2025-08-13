// $(document).ready(function () {
//     $('#carrera').select2();

//     var experiencias = [];
//     var profesores = [];
//     var problematicasOptions = [];
//     var problematicas = problematicasInject || [];

//     $('#agregarFilaBtn').click(function () {

//         var $ultimaFila = $('#problematicaTable tbody tr:last');

//         $ultimaFila.find('.experiencia-educativa, .profesor-problematica').select2();

//         $ultimaFila.find('.experiencia-educativa').change(function () {
//             var experienciaId = $(this).val();
//             var $profesorSelect = $(this).closest('tr').find('.profesor-problematica');

//             $profesorSelect.empty().prop('disabled', true);

//             obtenerProfesoresPorExperiencia(experienciaId, $profesorSelect);
//         });

//         $ultimaFila.find('.profesor-problematica').change(function () {
//             var profesorId = $(this).val();
//             var $experienciaSelect = $(this).closest('tr').find('.experiencia-educativa');

//             $experienciaSelect.empty().prop('disabled', true);

//             obtenerExperienciasPorProfesor(profesorId, $experienciaSelect);
//         });
//     });

//     function obtenerProfesoresPorExperiencia(experienciaId, $profesorSelect) {
//         var idCarrera = $('#carrera').val();
//         if (!idCarrera) {
//             Swal.fire('Error', 'Por favor, selecciona una carrera.', 'warning');
//             return;
//         }

//         $.ajax({
//             url: 'getProfesoresPorExperiencia.php',
//             type: 'POST',
//             dataType: 'json',
//             data: {
//                 idExperiencia: experienciaId,
//                 idCarrera: idCarrera,
//                 csrf_token: $('input[name="csrf_token"]').val()
//             },
//             success: function (response) {
//                 if (response.error) {
//                     Swal.fire('Error', response.error, 'error');
//                 } else {
//                     var opciones = '<option value="" disabled selected>-----Selecciona un profesor-----</option>';
//                     response.profesores.forEach(function (profesor) {
//                         opciones += `<option value="${profesor.idTutor}">${profesor.tutorNombre}</option>`;
//                     });
//                     $profesorSelect.html(opciones).prop('disabled', false);
//                 }
//             },
//             error: function () {
//                 Swal.fire('Error', 'Error al obtener los profesores.', 'error');
//             }
//         });
//     }

//     function obtenerExperienciasPorProfesor(profesorId, $experienciaSelect) {
//         var idCarrera = $('#carrera').val();
//         if (!idCarrera) {
//             Swal.fire('Error', 'Por favor, selecciona una carrera.', 'warning');
//             return;
//         }

//         $.ajax({
//             url: 'getExperienciasPorProfesor.php',
//             type: 'POST',
//             dataType: 'json',
//             data: {
//                 idProfesor: profesorId,
//                 idCarrera: idCarrera,
//                 csrf_token: $('input[name="csrf_token"]').val()
//             },
//             success: function (response) {
//                 if (response.error) {
//                     Swal.fire('Error', response.error, 'error');
//                 } else {
//                     var opciones = '<option value="" disabled selected>-----Selecciona una experiencia educativa-----</option>';
//                     response.experiencias.forEach(function (experiencia) {
//                         opciones += `<option value="${experiencia.idExperienciaEducativa}">${experiencia.nombre}</option>`;
//                     });
//                     $experienciaSelect.html(opciones).prop('disabled', false);
//                 }
//             },
//             error: function () {
//                 Swal.fire('Error', 'Error al obtener las experiencias educativas.', 'error');
//             }
//         });
//     }

//     $('#carrera').on('focus', function () {
//         $(this).data('previous', $(this).val());
//     });

//     $('#carrera').on('change', function () {
//         var previousCareerValue = $(this).data('previous');
//         var idCarrera = $(this).val();

//         if ($('#problematicaTable tbody tr').length > 0) {
//             Swal.fire({
//                 title: 'Cambiar la carrera',
//                 text: 'Al cambiar la carrera, las problemáticas agregadas se perderán. ¿Deseas continuar?',
//                 icon: 'warning',
//                 showCancelButton: true,
//                 confirmButtonText: 'Sí, cambiar',
//                 cancelButtonText: 'No, cancelar'
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     $('#problematicaTable tbody').empty();

//                     actualizarDatosCarrera(idCarrera);
//                 } else {
//                     $('#carrera').val(previousCareerValue).trigger('change.select2');
//                 }
//             });
//         } else {
//             // Si no hay problemáticas agregadas, simplemente actualizar los datos
//             actualizarDatosCarrera(idCarrera);
//         }
//     });

//     function actualizarDatosCarrera(idCarrera) {
//         // Realizar una solicitud AJAX para obtener los datos relacionados con la nueva carrera
//         $.ajax({
//             url: 'getCarreraDatos.php',
//             type: 'POST',
//             dataType: 'json',
//             data: {
//                 idCarrera: idCarrera,
//                 csrf_token: $('input[name="csrf_token"]').val()
//             },
//             success: function (response) {
//                 if (response.error) {
//                     Swal.fire('Error', response.error, 'error');
//                     return;
//                 }

//                 experiencias = response.experiencias;
//                 profesores = response.profesores;
//                 problematicasOptions = response.problematicas;
//                 secciones = response.secciones;
//             },
//             error: function () {
//                 Swal.fire({
//                     title: 'Error',
//                     text: 'Hubo un error al obtener los datos de la carrera seleccionada.',
//                     icon: 'error',
//                     confirmButtonText: 'Aceptar'
//                 });
//             }
//         });
//     }

//     $('input[name="tipo"]').change(function () {
//         if ($(this).val() === 'ninguno') {
//             $('.problematica-table').hide();
//             $('#problematicaTable tbody').empty();
//         } else {
//             $('.problematica-table').show();
//         }
//     });

//     function problematicasOptions() {
//         var options = '';
//         problematicas.forEach(function (problematica) {
//             options += `<option value="${problematica.idProblematica}">${problematica.descripcion}</option>`;
//         });
//         return options;
//     }

//     $('#agregarFilaBtn').click(function () {
//         var idCarrera = $('#carrera').val();
//         if (!idCarrera) {
//             Swal.fire('Error', 'Por favor, selecciona una carrera antes de agregar una problemática.', 'warning');
//             return;
//         }

//         // Verificar si las variables están cargadas
//         if (experiencias.length === 0 || profesores.length === 0 || problematicasOptions.length === 0) {
//             Swal.fire('Información', 'Los datos de la carrera aún no se han cargado. Por favor, espera un momento.', 'info');
//             return;
//         }

//         var experienciasOptions = '<option value="" disabled selected>-----Selecciona una experiencia educativa-----</option>';
//         experiencias.forEach(function (exp) {
//             experienciasOptions += `<option value="${exp.idExperienciaEducativa}">${exp.nombre}</option>`;
//         });

//         var profesoresOptions = '<option value="" disabled selected>-----Selecciona un profesor-----</option>';
//         profesores.forEach(function (prof) {
//             profesoresOptions += `<option value="${prof.idTutor}">${prof.tutorNombre}</option>`;
//         });

//         var problematicaOptionsHtml = '<option value="" disabled selected>-----Problemática-----</option>';
//         problematicasOptions.forEach(function (problematica) {
//             problematicaOptionsHtml += `<option value="${problematica.idProblematica}">${problematica.tipoProblematica}</option>`;
//         });
//         problematicaOptionsHtml += '<option value="otro">Otro</option>';

//         var nuevaFila = `
//             <tr>
//                 <td>
//                     <select name="experienciaE[]" class="form-control experiencia-educativa" required>
//                         ${experienciasOptions}
//                     </select>
//                 </td>
//                 <td>
//                     <select name="profesor[]" class="form-control profesor-problematica" required>
//                         ${profesoresOptions}
//                     </select>
//                 </td>
//                 <td>
//                     <div class="form-group">
//                         <select class="form-control problematica-select" name="problematica[]" required>
//                             ${problematicaOptionsHtml}
//                         </select>
//                         <textarea name="otro[]" class="form-control mt-2 otro-textarea" placeholder="Describe la problemática, máximo 500 caracteres" maxlength="500" style="display: none;"></textarea>
//                     </div>
//                 </td>
//                 <td>
//                     <input type="number" name="numAlumnos[]" class="form-control" placeholder="Número de alumnos" min="1" step="1" required>
//                 </td>
//                 <td>
//                     <button type="button" class="btn btn-danger eliminarFila"><i class="fas fa-trash-alt"></i> Eliminar</button>
//                 </td>
//             </tr>
//         `;
//         $('#problematicaTable tbody').append(nuevaFila);

//         // Inicializar Select2 en los nuevos selects si es necesario
//         $('.profesor-problematica').select2();
//     });

//     $(document).on('change', '.problematica-select', function () {
//         var $fila = $(this).closest('tr');
//         var $textarea = $fila.find('.otro-textarea');
//         if ($(this).val() === 'otro') {
//             $textarea.show().prop('required', true);
//         } else {
//             $textarea.hide().prop('required', false).val('');
//         }
//     });

//     $(document).on('click', '.eliminarFila', function () {
//         $(this).closest('tr').remove();
//     });

//     $('#enviar').on('click', function (e) {
//         e.preventDefault();
//         if (validarFormulario()) {
//             $('#form').submit();
//         }
//     });

//     function validarFormulario() {
//         var valid = true;
//         var errores = [];

//         $('#form [required]').each(function () {
//             if ($(this).val() === '') {
//                 valid = false;
//                 $(this).addClass('is-invalid');
//                 errores.push('El campo "' + $(this).prev('label').text() + '" es obligatorio.');
//             } else {
//                 $(this).removeClass('is-invalid');
//             }
//         });

//         var fechaInicio = $('#fechaInicio').val();
//         var fechaFin = $('#fechaFin').val();
//         if (fechaInicio && fechaFin && new Date(fechaFin) < new Date(fechaInicio)) {
//             valid = false;
//             $('#fechaFin').addClass('is-invalid');
//             errores.push('La fecha de fin no puede ser menor que la fecha de inicio.');
//         } else {
//             $('#fechaFin').removeClass('is-invalid');
//         }

//         if ($('input[name="tipo"]:checked').val() === 'problematica') {
//             $('#problematicaTable tbody tr').each(function (index, row) {
//                 $(row).find('[required]').each(function () {
//                     if ($(this).val() === '') {
//                         valid = false;
//                         $(this).addClass('is-invalid');
//                         errores.push('Todos los campos de la problemática son obligatorios en la fila ' + (index + 1) + '.');
//                     } else {
//                         $(this).removeClass('is-invalid');
//                     }
//                 });

//                 var $problematicaSelect = $(row).find('.problematica-select');
//                 if ($problematicaSelect.val() === 'otro') {
//                     var $otroTextarea = $(row).find('.otro-textarea');
//                     if ($otroTextarea.val().trim() === '') {
//                         valid = false;
//                         $otroTextarea.addClass('is-invalid');
//                         errores.push('Debe describir la problemática en la fila ' + (index + 1) + '.');
//                     } else {
//                         $otroTextarea.removeClass('is-invalid');
//                     }
//                 }
//             });
//         }

//         if (!valid && errores.length > 0) {
//             Swal.fire({
//                 title: 'Errores en el formulario',
//                 icon: 'error',
//                 html: errores.join('<br>'),
//             });
//         }

//         return valid;
//     }

//     $('#comentario').on('input', function () {
//         var maxLength = 500;
//         var currentLength = $(this).val().length;

//         if (currentLength > maxLength) {
//             $(this).val($(this).val().substring(0, maxLength));
//             Swal.fire({
//                 title: 'Límite alcanzado',
//                 text: 'Has alcanzado el máximo de 500 caracteres para el campo Comentario.',
//                 icon: 'info',
//                 timer: 2000,
//                 showConfirmButton: false
//             });
//         }
//     });

//     $(document).on('input', '.otro-textarea', function () {
//         var maxLength = 500;
//         var currentLength = $(this).val().length;

//         if (currentLength > maxLength) {
//             $(this).val($(this).val().substring(0, maxLength));
//             alert('Has alcanzado el límite de 500 caracteres.');
//         }
//     });
// });

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
        // Realizar una solicitud AJAX para obtener los datos relacionados con la nueva carrera
        $.ajax({
            url: "getCarreraDatos.php", // Asegúrate de que esta ruta sea correcta
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

                // Actualizar las variables globales con los nuevos datos
                experiencias = response.experiencias;
                profesores = response.profesores;
                problematicasOptions = response.problematicas;
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

            if (profesoresUnicos[profesorSeleccionado]) {
                $profesorSelect
                    .val(profesorSeleccionado)
                    .trigger("change.select2");
            } else {
                $profesorSelect.val(null).trigger("change.select2");
            }
        });

        $ultimaFila.find(".profesor-problematica").change(function () {
            var profesorId = $(this).val();
            var $experienciaSelect = $(this)
                .closest("tr")
                .find(".experiencia-educativa");

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
                '<option value="" disabled selected>-----Selecciona una experiencia educativa-----</option>';

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

            $experienciaSelect
                .html(opcionesExperiencia)
                .prop("disabled", false);

            if (experienciasUnicas[experienciaSeleccionada]) {
                $experienciaSelect
                    .val(experienciaSeleccionada)
                    .trigger("change.select2");
            } else {
                $experienciaSelect.val(null).trigger("change.select2");
            }
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
            $("#form").submit();
        }
    });

    function validarFormulario() {
        var valid = true;
        var errores = [];

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
