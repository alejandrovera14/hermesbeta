<div class="content-wrapper">
  <!-- Encabezado de la pagina -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Mantenimiento</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">inicio</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Inicio de la tabla -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <table id="tblMantenimiento" class="table table-bordered table-striped">
                <thead class="bg-dark">
                  <tr>
                    <th>ID</th>
                    <th>Numero de serie</th>
                    <th>Etiqueta</th>
                    <th>Descripcion</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $item = null;
                  $valor = null;
                  $mantenimientos = ControladorMantenimiento::ctrMostrarMantenimientos($item, $valor);

                  if (!empty($mantenimientos) && is_array($mantenimientos)) {
                    foreach ($mantenimientos as $key => $value) {
                      echo '
                        <tr>
                          <td>' . $value["equipo_id"] . '</td>
                          <td>' . $value["numero_serie"] . '</td>
                          <td>' . $value["etiqueta"] . '</td>
                          <td>' . $value["descripcion"] . '</td>
                          <td>
                            <div class="btn-group">
                              <button title="Ver detalles" class="btn btn-default btn-sm btnVerDetalles" data-id="' . $value["equipo_id"] . '" data-toggle="modal" data-target="#modalVerDetalles">
                                <i class="fas fa-eye"></i>
                              </button>
                              <button title="Editar mantenimiento" class="btn btn-default btn-sm btnEditarMantenimiento" data-id="' . $value["Id_mantenimiento"] . '" data-toggle="modal" data-target="#modalEditarMantenimiento">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button title="Solicitudes asociadas" class="btn btn-default btn-sm btnSolicitudesMantenimiento" data-id="' . $value["equipo_id"] . '">
                                <i class="fas fa-laptop"></i>
                              </button>
                            </div>
                          </td>
                        </tr>';
                    }
                  } else {
                    echo '<tr><td colspan="6" class="text-center">No hay equipos en mantenimiento</td></tr>';
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

  <!-- Modal Ver Detalles -->
  <div class="modal fade" id="modalVerDetalles">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title">Detalles del Equipo</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Detalles del equipo en mantenimiento irían aquí</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Editar Mantenimiento -->
  <div class="modal fade" id="modalEditarMantenimiento">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title">Editar Mantenimiento</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Formulario para editar mantenimiento iría aquí</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Incluir el archivo JavaScript -->
  <script src="vistas/js/mantenimiento.js"></script>
</div>