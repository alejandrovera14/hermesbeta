<?php
class ControladorDevoluciones {
    /*=============================================
    MOSTRAR DEVOLUCIONES (LISTADO)
    =============================================*/
    static public function ctrMostrarDevoluciones($item, $valor) {
        $tabla = "prestamos";
        $respuesta = ModeloDevoluciones::mdlMostrarDevoluciones($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    MARCAR EQUIPO EN DETALLE_PRESTAMO COMO MANTENIMIENTO
    =============================================*/
    static public function ctrMarcarMantenimientoDetalle($idPrestamo, $idEquipo){
        // Obtener el ID del estado 'Mantenimiento' desde el modelo
        $estadoMantenimiento = ModeloDevoluciones::mdlObtenerIdEstado('Mantenimiento');

        if(!$estadoMantenimiento){
            return "error_estado_no_encontrado";
        }

        $datos = array(
            "equipo_id" => $idEquipo,
            "id_estado" => $estadoMantenimiento,
            "id_prestamo" => $idPrestamo
        );

        $respuestaMarcado = ModeloDevoluciones::mdlMarcarMantenimientoDetalle($datos);

        if($respuestaMarcado == "ok"){
            // Verificar si todos los equipos del préstamo han sido devueltos
            $todosDevueltos = ModeloDevoluciones::mdlVerificarTodosEquiposDevueltos($idPrestamo);

            if($todosDevueltos){
                $respuestaActualizacionPrestamo = ModeloDevoluciones::mdlActualizarPrestamoDevuelto($idPrestamo);
                return ($respuestaActualizacionPrestamo == "ok") ? "ok_prestamo_actualizado" : "error_actualizando_prestamo";
            }
            return "ok";
        }
        return $respuestaMarcado;
    }

    /*============================================= 
    MARCAR EQUIPO EN DETALLE_PRESTAMO COMO DEVUELTO EN BUEN ESTADO (Disponible)
    =============================================*/
    static public function ctrMarcarDevueltoBuenEstado($idPrestamo, $idEquipo){
        // Obtener el ID del estado 'Disponible' desde el modelo
        $estadoDisponible = ModeloDevoluciones::mdlObtenerIdEstado('Disponible');

        if(!$estadoDisponible){
            return "error_estado_no_encontrado";
        }

        $datos = array(
            "equipo_id" => $idEquipo,
            "id_estado" => $estadoDisponible,
            "id_prestamo" => $idPrestamo
        );

        $respuestaMarcado = ModeloDevoluciones::mdlMarcarDevueltoBuenEstado($datos);

        if($respuestaMarcado == "ok"){
            // Verificar si todos los equipos del préstamo han sido devueltos
            $todosDevueltos = ModeloDevoluciones::mdlVerificarTodosEquiposDevueltos($idPrestamo);

            if($todosDevueltos){
                $respuestaActualizacionPrestamo = ModeloDevoluciones::mdlActualizarPrestamoDevuelto($idPrestamo);
                return ($respuestaActualizacionPrestamo == "ok") ? "ok_prestamo_actualizado" : "error_actualizando_prestamo";
            }
            return "ok";
        }
        return $respuestaMarcado;
    }

    /*=============================================
    MARCAR EQUIPO COMO ROBADO (BAJA)
    =============================================*/
    static public function ctrMarcarEquipoRobado($idPrestamo, $idEquipo){
        // Obtener el ID del estado 'baja' desde el modelo
        $estadoBaja = ModeloDevoluciones::mdlObtenerIdEstado('baja');

        if(!$estadoBaja){
            return "error_estado_no_encontrado";
        }

        $datos = array(
            "equipo_id" => $idEquipo,
            "id_estado" => $estadoBaja,
            "id_prestamo" => $idPrestamo
        );

        $respuestaMarcado = ModeloDevoluciones::mdlMarcarMantenimientoDetalle($datos);

        if($respuestaMarcado == "ok"){
            // Verificar si todos los equipos del préstamo han sido devueltos
            $todosDevueltos = ModeloDevoluciones::mdlVerificarTodosEquiposDevueltos($idPrestamo);

            if($todosDevueltos){
                $respuestaActualizacionPrestamo = ModeloDevoluciones::mdlActualizarPrestamoDevuelto($idPrestamo);
                return ($respuestaActualizacionPrestamo == "ok") ? "ok_prestamo_actualizado" : "error_actualizando_prestamo";
            }
            return "ok";
        }
        return $respuestaMarcado;
    }
}
?>