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
	MARCAR EQUIPO EN DETALLE_PRESTAMO COMO MANTENIMIENTO (ACTUALIZANDO ID_ESTADO)
	=============================================*/
	static public function ctrMarcarMantenimientoDetalle($idPrestamo, $idEquipo){

		$tabla = "equipos"; // Cambiado de "detalle_prestamo" a "equipos"
		// Asumimos que el id_estado para 'Mantenimiento' es 4.
		// Si es diferente, ajusta este valor.
		$datos = array("equipo_id" => $idEquipo,
					   "id_estado" => 4); // Eliminado id_prestamo de los datos para mdlMarcarMantenimientoDetalle

		$respuestaMarcado = ModeloDevoluciones::mdlMarcarMantenimientoDetalle($datos);

		if($respuestaMarcado == "ok"){
			// Verificar si todos los equipos del préstamo han sido devueltos
			$todosDevueltos = ModeloDevoluciones::mdlVerificarTodosEquiposDevueltos($idPrestamo);

			if($todosDevueltos){
				// Si todos han sido devueltos, actualizar el estado del préstamo
				$respuestaActualizacionPrestamo = ModeloDevoluciones::mdlActualizarPrestamoDevuelto($idPrestamo);
				if($respuestaActualizacionPrestamo == "ok"){
					return "ok_prestamo_actualizado"; // Éxito al marcar equipo y actualizar préstamo
				} else {
					return "error_actualizando_prestamo"; // Error al actualizar el préstamo
				}
			} else {
				return "ok"; // Éxito al marcar el equipo, pero no todos han sido devueltos aún
			}
		} else {
			return $respuestaMarcado; // Retorna "no_change" o "error" del marcado inicial
		}

	}
	/*============================================= 
/*============================================= 
MARCAR EQUIPO EN DETALLE_PRESTAMO COMO DEVUELTO EN BUEN ESTADO
=============================================*/
    static public function ctrMarcarDevueltoBuenEstado($idPrestamo, $idEquipo){
        $datos = array(
            "id_prestamo" => $idPrestamo,
            "equipo_id" => $idEquipo,
            "id_estado" => 1 // 1 = Disponible
        );

        $respuestaMarcado = ModeloDevoluciones::mdlMarcarDevueltoBuenEstado($datos);

        if($respuestaMarcado == "ok"){
            $todosDevueltos = ModeloDevoluciones::mdlVerificarTodosEquiposDevueltos($idPrestamo);

            if($todosDevueltos){
                $respuestaActualizacionPrestamo = ModeloDevoluciones::mdlActualizarPrestamoDevuelto($idPrestamo);
                if($respuestaActualizacionPrestamo == "ok"){
                    return "ok_prestamo_actualizado";
                } else {
                    return "error_actualizando_prestamo";
                }
            } else {
                return "ok";
            }
        } else {
            return $respuestaMarcado;
        }
    }

/*=============================================
MARCAR EQUIPO COMO ROBADO (BAJA)
=============================================*/
    static public function ctrMarcarEquipoRobado($idPrestamo, $idEquipo){
        // Primero obtener el ID del estado 'baja' de la tabla estados
        $stmt = Conexion::conectar()->prepare(
            "SELECT id_estado FROM estados WHERE estado = 'baja'"
        );
        $stmt->execute();
        $estadoBaja = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$estadoBaja){
            return "error_estado_no_encontrado";
        }

        $datos = array(
            "equipo_id" => $idEquipo,
            "id_estado" => $estadoBaja['id_estado'],
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
    MARCAR EQUIPO PARA MANTENIMIENTO (versión mejorada)
    =============================================*/
    static public function ctrMarcarMantenimiento($idPrestamo, $idEquipo) {
        $datosDevolucionMantenimiento = array(
            "equipo_id" => $idEquipo,
            "id_estado" => 'Mantenimiento', // Cambiado a 'Mantenimiento'
        );

        $respuestaMarcado = ModeloDevoluciones::mdlMarcarMantenimientoDetalle($datosDevolucionMantenimiento);

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