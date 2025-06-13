let tablaAuditoria;

function inicializarTablaAuditoria() {
  tablaAuditoria = $('#tablaAuditoria').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "ajax/serverside/serverside.auditoria.php",
      type: "GET",
      error: function (xhr, error, thrown) {
        console.error("Error AJAX:", thrown);
        alert("Error al cargar los datos de auditoría.");
      }
    },
    columns: [
      { data: 1 }, // tipo_documento
      { data: 2 }, // numero_documento
      { data: 3 }, // nombre
      { data: 4 }, // apellido
      { data: 5 }, // nombre_editor
      { data: 6 }, // fecha_cambio
      {
        data: null,
        render: function (data, type, row) {
          const detalle = {
            campo_modificado: row[7] || '',
            valor_anterior: row[8] || '',
            valor_nuevo: row[9] || ''
          };

          const jsonDetalle = JSON.stringify(detalle).replace(/'/g, "&apos;");

          return `<button class="btn btn-info btn-sm btnDetalle" data-detalle='${jsonDetalle}'><i class="fas fa-eye"></i></button>`;
        }
      },
      {
        data: null,
        visible: false,
        render: function (data, type, row) {
          let campos = (row[7] || '').split(';').map(s => s.trim());
          let valoresAnt = (row[8] || '').split(';').map(s => s.trim());
          let valoresNue = (row[9] || '').split(';').map(s => s.trim());

          return campos.map((campo, i) => `${campo}: ${valoresAnt[i] || ''} → ${valoresNue[i] || ''}`).join(" | ");
        }
      }
    ],
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        text: 'Excel',
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 7] }
      },
      {
        extend: 'csvHtml5',
        text: 'CSV',
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 7] }
      }
    ],
    responsive: true,
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
    order: [[5, 'desc']]
  });
}

$(document).on('click', '.btnDetalle', function () {
  let detalleData = $(this).data('detalle');

  if (!detalleData) {
    alert("No hay datos para mostrar.");
    return;
  }

  if (typeof detalleData === 'string') {
    try {
      detalleData = JSON.parse(detalleData.replace(/&apos;/g, "'"));
    } catch (e) {
      console.error("Error al parsear JSON:", e);
      alert("Error al cargar el detalle de auditoría.");
      return;
    }
  }

  let campos = (detalleData.campo_modificado || '').split(';').map(s => s.trim());
  let valoresAnt = (detalleData.valor_anterior || '').split(';').map(s => s.trim());
  let valoresNue = (detalleData.valor_nuevo || '').split(';').map(s => s.trim());

  let htmlDetalle = '<table class="table table-bordered">';
  htmlDetalle += '<thead><tr><th>Campo Modificado</th><th>Valor Anterior</th><th>Valor Nuevo</th></tr></thead><tbody>';
  for (let i = 0; i < campos.length; i++) {
    htmlDetalle += `<tr><td>${campos[i]}</td><td>${valoresAnt[i] || ''}</td><td>${valoresNue[i] || ''}</td></tr>`;
  }
  htmlDetalle += '</tbody></table>';

  $('#detalleAuditoriaBody').html(htmlDetalle);
  $('#modalDetalleAuditoria').modal('show');
});

$(document).ready(function () {
  inicializarTablaAuditoria();

  // Filtro por nombre de editor
  $('#filtroEditor').on('keyup change', function () {
    tablaAuditoria.column(4).search(this.value).draw();
  });
});
