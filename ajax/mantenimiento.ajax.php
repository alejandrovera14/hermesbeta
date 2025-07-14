<?php
require_once "../modelos/mantenimiento.modelo.php";

if (isset($_POST["equipoId"]) && isset($_POST["gravedad"]) && isset($_POST["detalles"])) {
    $equipoId = $_POST["equipoId"];
    $gravedad = $_POST["gravedad"];
    $detalles = $_POST["detalles"];

    $respuesta = ModeloMantenimiento::mdlFinalizarMantenimiento($equipoId, $gravedad, $detalles);

    echo $respuesta;
}
