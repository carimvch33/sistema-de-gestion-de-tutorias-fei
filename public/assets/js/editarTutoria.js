$(document).ready(function () {

    $('#lugar').on('input', function () {
        if ($(this).val().length >= 300) {
            $(this).val($(this).val().substring(0, 300));
            alert('Has alcanzado el máximo de 300 caracteres para el campo Lugar.');
        }
    });

    $('#notas').on('input', function () {
        if ($(this).val().length >= 500) {
            $(this).val($(this).val().substring(0, 500));
            alert('Has alcanzado el máximo de 500 caracteres para el campo Notas.');
        }
    });

    $("#enviar").on("click", function (e) {
        e.preventDefault();
        validarFormulario();
    });

    // Obtener valores iniciales desde atributos data o variables globales
    var idCarrera = $('#carrera').val();
    var idPeriodoTutoria = $('#periodoTutoria').data('selected'); // Se agregará este atributo en el PHP

    // Cargar periodos de tutoría al inicio si hay carrera seleccionada
    if (idCarrera) {
        $.ajax({
            url: "getPeriodoTutorias.php",
            method: "GET",
            data: { idCarrera: idCarrera },
            dataType: "json",
            success: function (tutorias) {
                const select = $("#periodoTutoria");
                select.empty();
                if (tutorias.length > 0) {
                    select.append("<option disabled>-----Selecciona un periodo de tutorías-----</option>");
                    tutorias.forEach((tutoria) => {
                        const texto = tutoria.mismaFecha
                            ? `Sesión #${tutoria.numSesion} — ${tutoria.fechaInicioFormateada}`
                            : `Sesión #${tutoria.numSesion} — ${tutoria.fechaInicioFormateada} a ${tutoria.fechaFinFormateada}`;
                        const selected = tutoria.idPeriodoTutorias == idPeriodoTutoria ? 'selected' : '';
                        select.append(`
                            <option 
                                value="${tutoria.idPeriodoTutorias}" 
                                data-fechainicio="${tutoria.fechaInicio}" 
                                data-fechafin="${tutoria.fechaFin}" 
                                data-mismafecha="${tutoria.mismaFecha}" ${selected}>
                                ${texto}
                            </option>
                        `);
                    });
                } else {
                    select.append("<option disabled selected>No hay periodos de tutorías disponibles</option>");
                }
                select.trigger('change'); // Disparar el evento para actualizar la UI
            },
            error: function () {
                alert('No se pudieron cargar los periodos de tutoría.');
            }
        });
    }

    $("#carrera").on("change", function () {
        const idCarrera = $(this).val();

        if (!idCarrera) return;

        $.ajax({
            url: "getPeriodoTutorias.php",
            method: "GET",
            data: {
                idCarrera: idCarrera,
            },
            dataType: "json",
            success: function (tutorias) {
                const select = $("#periodoTutoria");
                select.empty();
                if (tutorias.length > 0) {
                    select.append(
                        "<option disabled selected>-----Selecciona un periodo de tutorías-----</option>"
                    );
                    tutorias.forEach((tutoria) => {
                        const fechaInicioStr = tutoria.fechaInicioFormateada;
                        const fechaFinStr = tutoria.fechaFinFormateada;
                        const mismaFecha = tutoria.mismaFecha;

                        const texto = mismaFecha
                            ? `Sesión #${tutoria.numSesion} — ${fechaInicioStr}`
                            : `Sesión #${tutoria.numSesion} — ${fechaInicioStr} a ${fechaFinStr}`;

                        select.append(`
                            <option 
                                value="${tutoria.idPeriodoTutorias}" 
                                data-fechainicio="${tutoria.fechaInicio}" 
                                data-fechafin="${tutoria.fechaFin}" 
                                data-mismafecha="${mismaFecha}">
                                ${texto}
                            </option>
                        `);
                    });
                } else {
                    select.append(
                        "<option disabled selected>No hay periodos de tutorías disponibles</option>"
                    );
                }
            },

            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron cargar las tutorías.",
                });
            },
        });
    });

    $("#periodoTutoria").on("change", function () {
        const option = $(this).find(":selected");
        const mismaFecha = option.data("mismafecha");
        const fechaInicio = option.data("fechainicio");

        if (mismaFecha) {
            // Si es de un solo día, ocultar periodo de atención, fecha y fecha fin
            $("#div_fecha").hide();
            $("#div_fecha_fin").hide();
            $('input[name="periodoAtencion"]').prop("checked", false);
            $("#div_fecha").find("label").text("Fecha:");
            $("[name='periodoAtencion']").closest(".form-group").hide();
            $("#fecha").val(fechaInicio);
        } else {
            // Si es de más de un día, mostrar periodo de atención y manejar visibilidad de fechas según selección
            $("[name='periodoAtencion']").closest(".form-group").show();
            // Si ya hay una selección previa de periodoAtencion, disparar el evento para actualizar la UI
            const periodoAtencionSeleccionado = $(
                'input[name="periodoAtencion"]:checked'
            ).val();
            if (periodoAtencionSeleccionado === "Más de un día") {
                $("#div_fecha").show();
                $("#div_fecha").find("label").text("Fecha inicio:");
                $("#div_fecha_fin").show();
            } else if (periodoAtencionSeleccionado === "Un solo día") {
                $("#div_fecha").show();
                $("#div_fecha").find("label").text("Fecha:");
                $("#div_fecha_fin").hide();
            } else {
                // Si no hay selección, ocultar ambos
                $("#div_fecha").hide();
                $("#div_fecha_fin").hide();
            }
        }
    });

    // Manejar cambios en periodo de atención
    $('input[name="periodoAtencion"]').on("change", function () {
        const value = $(this).val();
        if (value === "Más de un día") {
            $("#div_fecha").show();
            $("#div_fecha").find("label").text("Fecha inicio:");
            $("#div_fecha_fin").show();
        } else if (value === "Un solo día") {
            $("#div_fecha").show();
            $("#div_fecha").find("label").text("Fecha:");
            $("#div_fecha_fin").hide();
        }
    });
});

function validarFormulario() {
    const carreraSeleccionada = $("#carrera").val();
    const periodoTutoriaSeleccionada = $("#periodoTutoria").val();
    const modalidadSeleccionada = $('input[name="modalidad"]:checked').val();
    const periodoAtencionVisible = $('[name="periodoAtencion"]')
        .closest(".form-group")
        .is(":visible");
    const periodoAtencionSeleccionado = periodoAtencionVisible
        ? $('input[name="periodoAtencion"]:checked').val()
        : null;
    const fechaVisible = $("#div_fecha").is(":visible");
    const fechaSeleccionada = fechaVisible ? $("#fecha").val() : null;
    const fechaFinVisible = $("#div_fecha_fin").is(":visible");
    const fechaFinSeleccionada = fechaFinVisible ? $("#fecha_fin").val() : null;
    const notas = $("#notas").val();
    const lugar = $("#lugar").val();
    const archivo = $("#archivo_horario").val();

    $(".form-control").removeClass(
        "border border-danger border-2 border-success"
    );

    let error = false;
    let mensajeError = "";

    // Validar campos obligatorios visibles
    if (
        !carreraSeleccionada ||
        !periodoTutoriaSeleccionada ||
        !modalidadSeleccionada
    ) {
        mensajeError +=
            "<p>Todos los campos obligatorios deben ser completados.</p>";
        error = true;
    }
    if (periodoAtencionVisible && !periodoAtencionSeleccionado) {
        mensajeError += "<p>Debes seleccionar un período de atención.</p>";
        error = true;
    }
    if (fechaVisible && !fechaSeleccionada) {
        mensajeError += "<p>Debes seleccionar una fecha válida.</p>";
        error = true;
    }
    if (fechaFinVisible && !fechaFinSeleccionada) {
        mensajeError += "<p>Debes seleccionar una fecha fin válida.</p>";
        error = true;
    }

    // Validar rango de fechas solo si ambos campos están visibles y tienen valor
    if (fechaVisible && fechaSeleccionada) {
        const fechaSeleccionadaDate = new Date($("#fecha").val());
        const fechaInicio = new Date(
            $("#periodoTutoria option:selected").data("fechainicio")
        );
        const fechaFin = new Date(
            $("#periodoTutoria option:selected").data("fechafin")
        );
        fechaSeleccionadaDate.setHours(0, 0, 0, 0);
        fechaInicio.setHours(0, 0, 0, 0);
        fechaFin.setHours(0, 0, 0, 0);
        if (
            fechaSeleccionadaDate < fechaInicio ||
            fechaSeleccionadaDate > fechaFin
        ) {
            mensajeError +=
                "<p>La fecha seleccionada debe estar dentro del periodo de tutoría.</p>";
            error = true;
            $("#fecha").addClass("border border-danger border-2");
        }
    }
    if (
        fechaFinVisible &&
        fechaFinSeleccionada &&
        fechaVisible &&
        fechaSeleccionada
    ) {
        const fechaInicioDate = new Date($("#fecha").val());
        const fechaFinDate = new Date($("#fecha_fin").val());
        if (fechaFinDate < fechaInicioDate) {
            mensajeError +=
                "<p>La fecha fin no puede ser anterior a la fecha inicio.</p>";
            error = true;
            $("#fecha_fin").addClass("border border-danger border-2");
        }
    }

    if (error) {
        Swal.fire({
            title: "¡Error!",
            icon: "error",
            html: mensajeError,
            showConfirmButton: false,
            timer: 3500,
        });
        return;
    }

    $(
        "#carrera, #periodoTutoria, #periodoE, #fecha, #fecha_fin, #notas, #lugar"
    )
        .removeClass("border border-danger border-2")
        .addClass("border border-success border-2");

    if (!fechaFinVisible || !fechaFinSeleccionada) {
        $("#fecha_fin").val($("#fecha").val());
    }

    $("#form").off("submit").submit();
}