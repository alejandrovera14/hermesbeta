<?php

class ControladorMantenimiento{
	// Mostrar mantenimientos
	static public function ctrMostrarMantenimientos($item, $valor)
	{
		$tabla = "mantenimiento";
		$respuesta = ModeloMantenimiento::mdlMostrarMantenimientos($tabla, $item, $valor);
		return $respuesta;
	}

    static public function ctrFinalizarMantenimiento($equipoId, $gravedad, $detalles) {
        return ModeloMantenimiento::mdlFinalizarMantenimiento($equipoId, $gravedad, $detalles);
    }
    
}


