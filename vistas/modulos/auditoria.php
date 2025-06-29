<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Historial de Auditoría de Usuarios</h1>
    </div>
  </section>

      <div class="card">
        <div class="card-body">
          <table id="tablaAuditoria" class="table table-bordered table-striped w-100">
            <thead class="bg-dark">
              <tr>
                <th>Tipo Doc.</th>
                <th>Número Doc.</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Editado Por</th>
                <th>Fecha de Cambio</th>
                <th>Detalle</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal Detalle de Auditoría -->
<div class="modal fade" id="modalDetalleAuditoria" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Detalle de Cambios</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="detalleAuditoriaBody"></div>
    </div>
  </div>
</div>

