
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Notificaciones</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
              <li class="breadcrumb-item active">Notificaciones</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <?php
    // Obtener el ID del usuario actual desde la sesión
    $usuarioId = isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : null;
    
    // Obtener las notificaciones leídas del usuario
    $notificaciones = ControladorNotificaciones::listarLeidas($usuarioId);
    ?>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <a href="redactar" class="btn btn-primary btn-block mb-3">Redactar</a>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Carpetas</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <ul class="nav nav-pills flex-column">
                <?php
                // Obtener el total de notificaciones (leídas y no leídas) del usuario
                $totalNotificaciones = 0;
                $todasNotificaciones = ControladorNotificaciones::listarTodas($usuarioId);
                if (!empty($todasNotificaciones)) {
                  $totalNotificaciones = count($todasNotificaciones);
                }
                ?>   
                <li class="nav-item active">
                  <a href="#" class="nav-link">
                    <i class="fas fa-inbox"></i> Notificaciones
                    <span id="count-notificaciones" class="badge bg-success float-right">
                      <?php echo !empty($totalNotificaciones) ? $totalNotificaciones : 0; ?>
                    </span>
                  </a>
                </li>   
                <?php
                // Obtener el total de notificaciones leídas del usuario
                $totalLeidas = 0;
                $notificacionesLeidas = ControladorNotificaciones::listarLeidas($usuarioId);
                if (!empty($notificacionesLeidas)) {
                  $totalLeidas = count($notificacionesLeidas);
                }
                ?>
                <li class="nav-item">
                  <a href="#" class="nav-link" id="btn-leidos">
                    <i class="far fa-envelope-open"></i> Leídos
                    <span class="badge bg-warning float-right"><?php echo $totalLeidas; ?></span>
                  </a>
                </li>   
                <?php
                // Obtener el total de notificaciones no leídas del usuario
                $totalNoLeidas = 0;
                $notificacionesNoLeidas = ControladorNotificaciones::listarNoLeidas($usuarioId);
                if (!empty($notificacionesNoLeidas)) {
                  $totalNoLeidas = count($notificacionesNoLeidas);
                }
                ?>
                <li class="nav-item">
                  <a href="#" class="nav-link" id="btn-no-leidos">
                    <i class="fas fa-envelope"></i> No Leídos
                    <span class="badge bg-primary float-right"><?php echo $totalNoLeidas; ?></span>
                  </a>
                </li>  
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-trash-alt"></i> Papelera
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Etiquetas</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-danger"></i>
                    Importante
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-warning"></i> Advertencia
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-primary"></i>
                    Completado
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Notificaciones</h3>

              <div class="card-tools">
                <div class="input-group input-group-sm">
                  <input type="text" class="form-control" placeholder="Buscar notificaciones">
                  <div class="input-group-append">
                    <div class="btn btn-primary">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm btn-delete-selected">
                    <i class="far fa-trash-alt"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-reply"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-share"></i>
                  </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <div class="float-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
              <!-- notificaciones bandeja de entrada -->
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                  <tbody>
                  <?php
                    // Mostrar todas las notificaciones (leídas y no leídas)
                    $todasNotificaciones = ControladorNotificaciones::listarTodas($usuarioId);
                  ?>
                  <?php if (!empty($todasNotificaciones)): ?>
                    <?php foreach ($todasNotificaciones as $notificacion): ?>
                      <tr id="notificacion-<?php echo htmlspecialchars($notificacion['id_notificaciones']); ?>">
                        <td>
                          <div class="icheck-primary">
                            <input type="checkbox" value="<?php echo htmlspecialchars($notificacion['id_notificaciones']); ?>" id="check<?php echo htmlspecialchars($notificacion['id_notificaciones']); ?>">
                            <label for="check<?php echo htmlspecialchars($notificacion['id_notificaciones']); ?>"></label>
                          </div>
                        </td>
                        <!-- marcar importante o leída/no leída -->
                        <td class="mailbox-star">
                          <?php if ($notificacion['leida'] == 1): ?>
                            <a href="#"><i class="fas fa-star text-warning"></i></a>
                          <?php else: ?>
                            <a href="#"><i class="far fa-star"></i></a>
                          <?php endif; ?>
                        </td>   
                        <!-- Nombre o tipo de evento relacionado con la notificación -->
                        <td class="mailbox-name">
                          <a href="<?php echo htmlspecialchars($notificacion['url']); ?>">
                            <?php echo "Evento #" . htmlspecialchars($notificacion['id_tipo_evento']); ?>
                          </a>
                        </td>
                        <!-- Mensaje principal de la notificación -->
                        <td class="mailbox-subject">
                          <a href="<?php echo htmlspecialchars($notificacion['url']); ?>">
                            <?php echo htmlspecialchars($notificacion['mensaje']); ?>
                          </a>
                        </td>
                        <!-- fecha de creacion de la notificación -->
                        <td class="mailbox-date">
                          <?php echo htmlspecialchars($notificacion['fecha_creacion']); ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center">No hay notificaciones.</td>
                    </tr>
                  <?php endif; ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>   <!-- /.mail-box-messages -->
            </div>   <!-- /.card-body -->
            <div class="card-footer p-0">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle">
                  <i class="far fa-square"></i>
                </button>
                <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm btn-delete-selected">
                  <i class="far fa-trash-alt"></i>
                </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-reply"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-share"></i>
                  </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <div class="float-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>