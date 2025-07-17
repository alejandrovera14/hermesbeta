<?php

include_once "../controladores/solicitudes.controlador.php";
include_once "../modelos/solicitudes.modelo.php";

include_once "../controladores/equipos.controlador.php";
include_once "../modelos/equipos.modelo.php";

class AjaxSolicitudes
{
    public $fechaInicio;
    public $fechaFin;
    public $idEquipoAgregar;
    public $idSolicitante;
    public $equipos;
    public $observaciones;
    public $idPrestamo;
    public $motivo;

        /*=============================================
            TRAER EQUIPOS DISPONIBLES
            EN EL RENGO DE FECHAS DE SOLICITUDES
        =============================================*/


    public function ajaxMostrarEquiposDisponible()
    {
        $respuesta = ControladorSolicitudes::ctrMostrarEquiposDisponible(
            $this->fechaInicio,
            $this->fechaFin
        );
        echo json_encode($respuesta);
    }

    public function ajaxTraerEquipo()
    {
        $respuesta = ControladorEquipos::ctrMostrarEquipos("equipo_id", $this->idEquipoAgregar);
        echo json_encode($respuesta);
    }

    public function ajaxGuardarSolicitud()
    {
        $datos = array(
            "idSolicitante" => $this->idSolicitante,
            "equipos" => $this->equipos,
            "fechaInicio" => $this->fechaInicio,
            "fechaFin" => $this->fechaFin,
            "motivo" => $this->motivo
        );
        $respuesta = ControladorSolicitudes::ctrGuardarSolicitud($datos);
        echo json_encode($respuesta);
    }

    public function ajaxMostrarSolicitudes()
    {
        $respuesta = ControladorSolicitudes::ctrMostrarSolicitudes("usuario_id", $this->idSolicitante);
        echo json_encode($respuesta);
    }

    public function ajaxMostrarPrestamo()
    {
        $respuesta = ControladorSolicitudes::ctrMostrarPrestamo("id_prestamo", $this->idPrestamo);
        echo json_encode($respuesta);
    }

    public function ajaxMostrarPrestamoDetalle()
    {
        $respuesta = ControladorSolicitudes::ctrMostrarPrestamoDetalle("id_prestamo", $this->idPrestamo);
        echo json_encode($respuesta);
    }

    public function ajaxCancelarPrestamo()
    {
        $respuesta = ControladorSolicitudes::ctrCancelarPrestamo($this->idPrestamo);

        if ($respuesta === "ok") {
            echo json_encode([
                "status" => "ok",
                "mensaje" => "✅ Préstamo cancelado correctamente"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "mensaje" => "❌ No se pudo cancelar el préstamo. " . $respuesta
            ]);
        }
    }
}

// Validar la acción que viene por POST
if (isset($_POST["accion"])) {

    $solicitud = new AjaxSolicitudes();

    switch ($_POST["accion"]) {
        case "guardarSolicitud":
            $solicitud->idSolicitante = $_POST["idSolicitante"];
            $solicitud->equipos = json_decode($_POST["equipos"], true);
            $solicitud->fechaInicio = $_POST["fechaInicio"];
            $solicitud->fechaFin = $_POST["fechaFin"];
            $solicitud->motivo = $_POST["motivoSolicitud"];
            $solicitud->ajaxGuardarSolicitud();
            break;

        case "mostrarEquipos":
            $solicitud->fechaInicio = $_POST["fechaInicio"];
            $solicitud->fechaFin = $_POST["fechaFin"];
            $solicitud->ajaxMostrarEquiposDisponible();
            break;

        case "traerEquipo":
            $solicitud->idEquipoAgregar = $_POST["idEquipoAgregar"];
            $solicitud->ajaxTraerEquipo();
            break;

        case "mostrarSolicitudes":
            $solicitud->idSolicitante = $_POST["idUsuario"];
            $solicitud->ajaxMostrarSolicitudes();
            break;

        case "mostrarPrestamo":
            $solicitud->idPrestamo = $_POST["idPrestamo"];
            $solicitud->ajaxMostrarPrestamo();
            break;

        case "mostrarPrestamoDetalle":
            $solicitud->idPrestamo = $_POST["idPrestamoDetalle"];
            $solicitud->ajaxMostrarPrestamoDetalle();
            break;

        case "cancelarPrestamo":
            $solicitud->idPrestamo = $_POST["idPrestamo"];
            $solicitud->ajaxCancelarPrestamo();
            break;

        default:
            echo json_encode(["status" => "error", "mensaje" => "Acción no reconocida"]);
            break;
    }
}