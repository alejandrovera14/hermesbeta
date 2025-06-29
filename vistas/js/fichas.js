/* ==================================================
SERVERSIDE EQUIPOS
================================================== */

$('#tblFichas').DataTable({
    "processing": true,
    "serverSide": true,
    "sAjaxSource": "ajax/serverside/serverside.fichas.php",
    "columns": [
        { "data": "0" },
        { "data": "1" },
        { "data": "2" },
        { "data": "3" },
        { "data": "4" },
        { "data": "5" },
        { "data": "6" },
        { "data": null }
    ],
    "columnDefs": [
        {
            "targets": [6],
            "render": function(data, type, row) {
                if (data === "activa") {
                    return "<button class='btn btn-success btnActivarFicha' data-id='" + row[0] + "' data-estado='inactiva'><i class='fas fa-check'></i></button>";
                } else {
                    return "<button class='btn btn-danger btnActivarFicha' data-id='" + row[0] + "' data-estado='activa'><i class='fas fa-ban'></i></button>";                    
                }
            }
        },
        {
            "targets": [-1],
            "render": function(data, type, row) {
                return "<div class='btn-group'>" +
                    "<button title='Consultar detalles' class='btn btn-default btnConsultarFicha' idFicha='" + row[0] + "' data-toggle='modal' data-target='#modalConsularFicha'><i class='fas fa-eye'></i></button>" +
                    "<button title='Editar ficha' class='btn btn-default btnEditarFicha' idFicha='" + row[0] + "' data-toggle='modal' data-target='#modalEditFicha'><i class='fas fa-edit'></i></button>" +
                    "<button title='Aprendices de la ficha' class='btn btn-default btnAprendicesFicha' idFicha='" + row[0] + "' data-toggle='modal' data-target='#modalAprendicesFicha'><i class='fas fa-users'></i></button>" +
                "</div>";
            }
        }
    ],
    "responsive": true,
    "autoWidth": false,
    "lengthChange": true,
    "lengthMenu":[10, 25, 50, 100],
    "language": {
        "lengthMenu": "Mostrar _MENU_ registros",
        "zeroRecords": "No se encontraron resultados",
        "info": "Mostrando pagina _PAGE_ de _PAGES_",
        "infoEmpty": "No hay registros disponibles",
        "infoFiltered": "(filtrado de _MAX_ total registros)",
        "search": "Buscar:",
        "paginate": {
          "first":      "Primero",
          "last":       "Ultimo",
          "next":       "Siguiente",
          "previous":   "Anterior"
        },
    },
    "buttons": ["csv", "excel", "pdf"],
    "dom": "lfBrtip"    
});

$(document).on("click", ".btnEditarFicha", function() {
    var idFicha = $(this).attr("idFicha");
    var datos = new FormData();
    datos.append("idFicha", idFicha);
    $.ajax({
        url: "ajax/fichas.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta) {
            $("#idEditFicha").val(respuesta["id_ficha"]);
            $("#editCodigoFicha").val(respuesta["codigo"]);
            $("#editDescripcionFicha").val(respuesta["descripcion"]);
            $("#editFechaInicioFicha").val(respuesta["fecha_inicio"]);
            $("#editFechaFinFicha").val(respuesta["fecha_fin"]);
            
            $("#editSedeFicha").val(respuesta["id_sede"]);
            $("#editSedeFicha").html(respuesta["nombre_sede"]);
        }
    });
});

$(document).on("click", ".btnActivarFicha", function() {
    var idFichaActivar = $(this).data("id");
    var estadoFicha = $(this).data("estado");
    var boton = $(this);

    // Desactivar temporalmente el botón
    boton.prop('disabled', true);
    var textoOriginal = boton.html();
    boton.html('<i class="fas fa-spinner fa-spin"></i>');

    var datos = new FormData();
    datos.append("idFichaActivar", idFichaActivar);
    datos.append("estadoFicha", estadoFicha);

    $.ajax({
        url: "ajax/fichas.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(respuesta) {
            if (respuesta.trim() === "ok") {
                // Cambiar estado visualmente sin recargar
                if (estadoFicha === "activa") {
                    boton.removeClass('btn-danger').addClass('btn-success');
                    boton.html('<i class="fas fa-check"></i>');
                    boton.data('estado', 'inactiva');
                } else {
                    boton.removeClass('btn-success').addClass('btn-danger');
                    boton.html('<i class="fas fa-ban"></i>');
                    boton.data('estado', 'activa');
                }
            } else {
                Swal.fire("Error", "No se pudo cambiar el estado", "error");
            }
            boton.prop('disabled', false);
        },
        error: function() {
            Swal.fire("Error", "Fallo de conexión", "error");
            boton.prop('disabled', false).html(textoOriginal);
        }
    });
});

//tooltips
// Activar tooltips después de cada renderizado de tabla
$('#tblFichas').on('draw.dt', function () {
    $('[data-toggle="tooltip"]').tooltip();
});