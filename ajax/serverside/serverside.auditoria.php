<?php
require 'serverside.php';
$table_data->get('vista_auditoria', 'id_auditoria', array(
  'tipo_documento',       // 0
  'numero_documento',     // 1
  'nombre',               // 2
  'apellido',             // 3
  'nombre_editor',        // 4
  'fecha_cambio',         // 5
  'campo_modificado',     // 6
  'valor_anterior',       // 7
  'valor_nuevo'           // 8
));