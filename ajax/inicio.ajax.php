<?php

require_once "../controladores/inicio.controlador.php";
require_once "../modelos/inicio.modelo.php";

$tipo = $_GET["tipo"] ?? 'actual';

// Obtener datos del controlador
$datos = ControladorInicio::ctrObtenerPrestamosPorDia($tipo);

// Lista de días en orden
$diasSemana = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];

// Inicializar array con 0 para todos los días
$datosCompletos = [];

foreach ($diasSemana as $dia) {
    $datosCompletos[$dia] = 0;
}

// Rellenar con datos reales
foreach ($datos as $dato) {
    $dia = $dato["dia"];
    $cantidad = $dato["cantidad"];
    $datosCompletos[$dia] = (int)$cantidad;
}

// Formatear para enviar como JSON
$respuesta = [];
foreach ($diasSemana as $dia) {
    $respuesta[] = ["dia" => $dia, "cantidad" => $datosCompletos[$dia]];
}

header('Content-Type: application/json');
echo json_encode($respuesta);
