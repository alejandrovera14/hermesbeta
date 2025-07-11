<?php
session_start();
require_once "../controladores/auditoria.controlador.php";
require_once "../modelos/auditoria.modelo.php";

class AuditoriaAjax {

    public function mostrarAuditoria() {
        header('Content-Type: application/json');

        // Verificar autenticación básica
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso no autorizado']);
            exit;
        }

        // Validar parámetro GET
        $idUsuario = (isset($_GET['id_usuario']) && is_numeric($_GET['id_usuario']) && $_GET['id_usuario'] > 0)
            ? (int)$_GET['id_usuario']
            : null;

        try {
            $data = AuditoriaControlador::ctrMostrarAuditoria($idUsuario);
            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
}

if (isset($_GET['accion']) && $_GET['accion'] === 'mostrarAuditoria') {
    $auditoriaAjax = new AuditoriaAjax();
    $auditoriaAjax->mostrarAuditoria();
}