<?php
$item = "id_modulo";
$valor = 8;
$respuesta = ControladorModulos::ctrMostrarModulos($item, $valor);
if ($respuesta["estado"] == "inactivo") {
    echo '<script>
                window.location = "desactivado";
            </script>';
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sedes</h1>
                </div>
                <div class="col-sm-6">
                    <?php
                    if (ControladorValidacion::validarPermisoSesion([24])) {
                        echo '<button class="btn btn-primary float-right" data-toggle="modal" data-target="#modalAddSede">Agregar Sede</button>';
                    }
                    ?>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="tblSedes" class="table table-bordered table-striped table-hover">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Dirección</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $item = null;
                                    $valor = null;
                                    $sedes = ControladorSedes::ctrMostrarSedes($item, $valor);

                                    foreach ($sedes as $key => $value) {
                                        echo '<tr>
                                                <td>'.($key + 1).'</td>
                                                <td>'.$value["nombre_sede"].'</td>
                                                <td>'.$value["direccion"].'</td>
                                                <td>'.$value["descripcion"].'</td>
                                                <td>';
                                        if (ControladorValidacion::validarPermisoSesion([23])) {
                                            if ($value["estado"] == "activa") {
                                                echo '<button class="btn btn-success btnActivarSede" idSede="'.$value["id_sede"].'" estadoSede="inactiva"><i class="fas fa-check"></i></button>';
                                            } else {
                                                echo '<button class="btn btn-danger btnActivarSede" idSede="'.$value["id_sede"].'" estadoSede="activa"><i class="fas fa-ban"></i></button>';
                                            };
                                        } else {
                                            if ($value["estado"] == "activa") {
                                                echo '<button class="btn btn-success disabled"><i class="fas fa-check"></i></button>';
                                            } else {
                                                echo '<button class="btn btn-danger disabled"><i class="fas fa-ban"></i></button>';
                                            };
                                        }
                                        echo '</td>
                                                <td>
                                                    <div class="btn-group">';
                                        if (ControladorValidacion::validarPermisoSesion([23])) {
                                            echo '<button class="btn btn-default btnConsultarSede" idSede="'.$value["id_sede"].'" title="Consultar sede" data-toggle="modal" data-target="#modalConsultarSede"><i class="fas fa-eye"></i></button>
                                                  <button class="btn btn-default btnEditarSede" idSede="'.$value["id_sede"].'" title="Editar sede" data-toggle="modal" data-target="#modalEditSede"><i class="fas fa-edit"></i></button>
                                                  <button class="btn btn-default btnUsuariosSede" idSede="'.$value["id_sede"].'" title="Usuarios de la sede"><i class="fas fa-users"></i></button>';
                                        } else {
                                            echo '<button class="btn btn-default disabled"><i class="fas fa-eye"></i></button>
                                                  <button class="btn btn-default disabled"><i class="fas fa-edit"></i></button>
                                                  <button class="btn btn-default disabled"><i class="fas fa-users"></i></button>';
                                        }
                                        echo '</div>
                                                </td>
                                            </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Add Sede -->
<div class="modal fade" id="modalAddSede" tabindex="-1" role="dialog" aria-labelledby="modalAddSedeLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title" id="modalAddSedeLabel">Agregar Nueva Sede</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="formAddSede" method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombreSede" name="nombreSede" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" id="direccionSede" name="direccionSede" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcionSede" name="descripcionSede" required></textarea>
                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>

                        <?php
                        $crearSede = new ControladorSedes();
                        $crearSede->ctrCrearSede();
                        ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Sede -->
<div class="modal fade" id="modalEditSede" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title" id="modalEditSedeLabel">Editar Sede</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="formEditSede" method="POST">
                        <input type="hidden" id="idEditSede" name="idEditSede">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombreEditSede" name="nombreEditSede" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" id="direccionEditSede" name="direccionEditSede" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcionEditSede" name="descripcionEditSede" required></textarea>
                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Editar</button>
                        </div>

                        <?php
                        $editarSede = new ControladorSedes();
                        $editarSede->ctrEditarSede();
                        ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>