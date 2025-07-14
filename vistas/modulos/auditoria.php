<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Historial de Auditoría de Usuarios</h1>
    </div>
  </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="tablaAuditoria" class="table table-bordered table-striped w-100">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>ID Usuario</th>
                                        <th>Tipo Doc.</th>
                                        <th>Número Doc.</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Editado Por</th>
                                        <th>Campo Modificado</th>
                                        <th>Valor Anterior</th>
                                        <th>Valor Nuevo</th>
                                        <th>Fecha Cambio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- El tbody queda vacío porque lo llena DataTables por AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- Moment.js y Daterangepicker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
