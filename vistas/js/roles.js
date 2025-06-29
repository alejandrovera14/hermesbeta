    /* ==================================================
    SERVERSIDE ROLES
    ================================================== */

    $('#tblRoles').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/serverside/serverside.roles.php",
            "type": "POST"
        },
        "columns": [
            { "data": null },
            { "data": "nombre_rol" },
            { "data": "descripcion" },
            { "data": "fecha_creacion" },
            { "data": "estado" },
            { "data": null }
        ],
        "columnDefs": [
            {
                "targets": [0],
                "render": function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                "targets": [4],
                "render": function(data, type, row) {
                    if (data === "activo") {
                        return "<button class='btn btn-success btnActivarRol' idRol='" + row.id_rol + "' estadoRol='inactivo' title='Rol activo' data-toggle='tooltip'><i class='fas fa-check'></i></button>";
                    } else {
                        return "<button class='btn btn-danger btnActivarRol' idRol='" + row.id_rol + "' estadoRol='activo' title='Rol inactivo' data-toggle='tooltip'><i class='fas fa-ban'></i></button>";
                    }
                }
            },
            {
                "targets": [-1],
                "render": function(data, type, row) {
                    return "<div class='btn-group'>" +
                        "<button title='Consultar detalles' class='btn btn-default btnConsultarRol' idRol='" + row.id_rol + "' data-toggle='tooltip'><i class='fas fa-eye'></i></button>" +
                        "<button title='Editar rol' class='btn btn-default btnEditarRol' idRol='" + row.id_rol + "' data-toggle='modal' data-target='#modalEditRol'><i class='fas fa-edit'></i></button>" +
                        "<button title='Asignar permisos' class='btn btn-default btnPermisosRol' idRol='" + row.id_rol + "' data-toggle='tooltip'><i class='fas fa-key'></i></button>" +
                    "</div>";
                }
            }
        ],
        "responsive": true,
        "autoWidth": false,
        "lengthChange": true,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "buttons": ["csv", "excel", "pdf"],
        "dom": "lfBrtip"
    });

    $(document).on("click", ".btnEditarRol", function() {
        var idRol = $(this).attr("idRol");
        var datos = new FormData();
        datos.append("idRol", idRol);
        
        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(respuesta) {
                $("#nombreEditRol").val(respuesta["nombre_rol"]);
                $("#descripcionEditRol").val(respuesta["descripcion"]);
                $("#idEditRol").val(respuesta["id_rol"]);
            }
        });
    });

    $(document).on("click", ".btnActivarRol", function() {
        var boton = $(this);
        var idRolActivar = boton.attr("idRol");
        var estadoRol = boton.attr("estadoRol");
        var datos = new FormData();
        datos.append("idRolActivar", idRolActivar);
        datos.append("estadoRol", estadoRol);

        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                if (estadoRol == "inactivo") {
                    boton.removeClass('btn-success').addClass('btn-danger');
                    boton.html('<i class="fas fa-ban"></i>');
                    boton.attr("estadoRol", "activo");
                    boton.attr("title", "Rol inactivo");
                } else {
                    boton.removeClass('btn-danger').addClass('btn-success');
                    boton.html('<i class="fas fa-check"></i>');
                    boton.attr("estadoRol", "inactivo");
                    boton.attr("title", "Rol activo");
                }
                boton.tooltip('hide').tooltip('show');
            }
        });
    });

    // Tooltips
    $('#tblRoles').on('draw.dt', function() {
        $('[data-toggle="tooltip"]').tooltip();
    });