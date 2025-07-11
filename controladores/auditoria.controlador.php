<?php

class AuditoriaControlador {

    /**
     * Mostrar auditoría con opción de filtro por usuario afectado.
     * 
     * @param int|null $idUsuarioAfectado
     * @return array
     */
    public static function ctrMostrarAuditoria($idUsuarioAfectado = null) {
        // Forzar a entero o null
        $idUsuarioAfectado = (is_numeric($idUsuarioAfectado) && $idUsuarioAfectado > 0)
            ? (int)$idUsuarioAfectado
            : null;

        $resultados = AuditoriaModelo::mdlMostrarAuditoria($idUsuarioAfectado);

        if (!$resultados) {
            return [];
        }

        $traducirGenero = function($valor) {
            switch ($valor) {
                case 1: return 'Femenino';
                case 2: return 'Masculino';
                case 3: return 'No declara';
                default: return 'Desconocido';
            }
        };

        foreach ($resultados as &$row) {
            // Traducción del campo 'genero'
            $row['genero'] = $traducirGenero($row['genero']);

            $campos = array_map('trim', explode(';', $row['campo_modificado']));
            $valores_anteriores = array_map('trim', explode(';', $row['valor_anterior']));
            $valores_nuevos = array_map('trim', explode(';', $row['valor_nuevo']));

            // Traducir género dentro del detalle también
            foreach ($campos as $i => $campo) {
                if (strtolower($campo) === 'genero') {
                    if (isset($valores_anteriores[$i])) {
                        $valores_anteriores[$i] = $traducirGenero($valores_anteriores[$i]);
                    }
                    if (isset($valores_nuevos[$i])) {
                        $valores_nuevos[$i] = $traducirGenero($valores_nuevos[$i]);
                    }
                }
            }

            $row['valor_anterior'] = implode('; ', $valores_anteriores);
            $row['valor_nuevo'] = implode('; ', $valores_nuevos);
            $row['nombre_editor'] = !empty($row['nombre_editor']) ? htmlspecialchars($row['nombre_editor']) : 'Sistema';
        }

        return $resultados;
    }
}