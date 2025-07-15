<?php
require_once "../controladores/inicio.controlador.php";
require_once "../modelos/inicio.modelo.php";

class AjaxInicio{

    static public function ajaxObtenerGraficos(){
        $respuesta = ControladorInicio::ctrObtenerEstadosEquipos();
        echo json_encode($respuesta);
        error_log(print_r($respuesta, true));
    }
}