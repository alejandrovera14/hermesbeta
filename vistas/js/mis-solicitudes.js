// Ver detalle del préstamo
$(document).on("click", ".btnVerDetalle", function () {
  let idPrestamo = $(this).attr("idPrestamo");
  let datos = new FormData();
  datos.append("accion", "mostrarPrestamo");
  datos.append("idPrestamo", idPrestamo);

  $.ajax({
    url: "ajax/solicitudes.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {

      const estado = respuesta["estado_prestamo"]?.toLowerCase();
      const tipo = respuesta["tipo_prestamo"]?.toLowerCase();
      const firmaCOO = respuesta["firma_coo"];
      const firmaTIC = respuesta["firma_tic"];
      const firmaALM = respuesta["firma_alm"];

      const firmasVacias = (!firmaCOO || firmaCOO === "") &&
                           (!firmaTIC || firmaTIC === "") &&
                           (!firmaALM || firmaALM === "");

      if (estado === "pendiente" && tipo === "reservado" && firmasVacias) {
        $("#btnCancelarSolicitud")
          .removeClass("d-none")
          .attr("data-id", idPrestamo)
          .addClass("btnCancelarPrestamo");
      } else {
        $("#btnCancelarSolicitud")
          .addClass("d-none")
          .removeAttr("data-id")
          .removeClass("btnCancelarPrestamo");
      }

      $("#numeroPrestamo").text(respuesta["id_prestamo"]);
      $("#detalleTipoPrestamo").text(respuesta["tipo_prestamo"]);
      $("#detalleFechaInicio").text(respuesta["fecha_inicio"].split(" ")[0]);
      $("#detalleFechaFin").text(respuesta["fecha_fin"].split(" ")[0]);
      $("#detalleMotivoPrestamo").text(respuesta["motivo"]);
      $("#estadoPrestamo").text(respuesta["estado_prestamo"]);

      $("#estadoCallout").removeClass("callout-success callout-warning callout-danger callout-info");
      switch (respuesta["estado_prestamo"]) {
        case "Autorizado":
        case "Prestado":
          $("#estadoCallout").addClass("callout-success");
          break;
        case "Pendiente":
          $("#estadoCallout").addClass("callout-warning");
          break;
        case "Rechazado":
        case "Devuelto":
          $("#estadoCallout").addClass("callout-danger");
          break;
        case "Tramite":
          $("#estadoCallout").addClass("callout-info");
          break;
      }

      $("#estadoPrestamo").removeClass("badge-success badge-warning badge-danger badge-primary");
      switch (respuesta["estado_prestamo"]) {
        case "Autorizado":
        case "Prestado":
          $("#estadoPrestamo").addClass("badge-success");
          break;
        case "Pendiente":
          $("#estadoPrestamo").addClass("badge-warning");
          break;
        case "Rechazado":
        case "Devuelto":
          $("#estadoPrestamo").addClass("badge-danger");
          break;
        case "Tramite":
          $("#estadoPrestamo").addClass("badge-primary");
          break;
      }

      $.ajax({
        url: "ajax/solicitudes.ajax.php",
        method: "POST",
        data: {
          accion: "mostrarPrestamoDetalle",
          idPrestamoDetalle: idPrestamo,
        },
        dataType: "json",
        success: function (detalles) {
          const tabla = $("#tblDetallePrestamo");

          if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().clear().destroy();
          }

          let tbody = tabla.find("tbody");
          tbody.empty();

          detalles.forEach(function (equipo) {
            tbody.append(
              "<tr>" +
                "<td>" + equipo.equipo_id + "</td>" +
                "<td>" + equipo.categoria + "</td>" +
                "<td>" + equipo.descripcion + "</td>" +
                "<td>" + equipo.etiqueta + "</td>" +
                "<td>" + equipo.numero_serie + "</td>" +
                "<td>" + equipo.ubicacion + "</td>" +
              "</tr>"
            );
          });

          tabla.DataTable({ responsive: true });
          $("#modalMisDetalles").modal("show");
        }
      });
    }
  });
});

// Botón Cancelar Solicitud
$(document).on("click", ".btnCancelarPrestamo", function () {
  let idPrestamo = $(this).attr("data-id");

  Swal.fire({
    title: "¿Estás seguro?",
    text: "Esta acción cancelará el préstamo de forma permanente.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, cancelar",
    cancelButtonText: "No, mantener"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "ajax/solicitudes.ajax.php",
        method: "POST",
        data: {
          accion: "cancelarPrestamo",
          idPrestamo: idPrestamo
        },
        dataType: "json",
        success: function (data) {
          if (data.status === "ok") {
            Swal.fire("Cancelado", data.mensaje, "success");

            // Esperar un momento y recargar toda la página
            setTimeout(() => {
              location.reload();
            }, 1000);

          } else {
            Swal.fire("Error", data.mensaje, "error");
          }
        },
        error: function () {
          Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
        }
      });
    }
  });
});

// Limpiar el botón al cerrar modal manualmente
$('#modalMisDetalles').on('hidden.bs.modal', function () {
  $("#btnCancelarSolicitud")
    .addClass("d-none")
    .removeAttr("data-id")
    .removeClass("btnCancelarPrestamo");

  if ($.fn.DataTable.isDataTable("#tblDetallePrestamo")) {
    $("#tblDetallePrestamo").DataTable().clear().destroy();
  }
});