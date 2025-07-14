<?php

require_once "../controladores/notificaciones.controlador.php";
require_once "../modelos/notificaciones.modelo.php";

class NotificacionesAjax
{
    public static function handleRequest()
    {
        if (isset($_POST['accion']) && $_POST['accion'] === 'contadores') {
            self::actualizarContadores();
        } elseif (isset($_POST['accion']) && $_POST['accion'] === 'no_leidas') {
            self::actualizarNoLeidas();
        } elseif (isset($_POST['accion']) && $_POST['accion'] === 'dropdown_notificaciones') {
            self::obtenerNotificacionesDropdown();
        } elseif (isset($_POST['accion']) && $_POST['accion'] === 'marcar_todas_leidas') {
            self::marcarTodasLeidas();
        } elseif (isset($_POST['accion']) && $_POST['accion'] === 'marcar_leida') {
            self::marcarComoLeida();
        } elseif (isset($_POST['id'])) {
            self::eliminarNotificacion();
        } elseif (isset($_POST['ids']) && is_array($_POST['ids'])) {
            self::eliminarMultiplesNotificaciones();
        }
    }

    private static function actualizarContadores()
    {
        session_start();
        $usuarioId = isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : null;
        
        if (!$usuarioId) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit;
        }
        
        $todasNotificaciones = ControladorNotificaciones::listarTodas($usuarioId);
        $totalNotificaciones = !empty($todasNotificaciones) ? count($todasNotificaciones) : 0;
        
        echo json_encode([
            'success' => true,
            'totalNotificaciones' => $totalNotificaciones
        ]);
        exit;
    }

    private static function actualizarNoLeidas()
    {
        session_start();
        $usuarioId = isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : null;
        
        if (!$usuarioId) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit;
        }
        
        $noLeidas = ControladorNotificaciones::listarNoLeidas($usuarioId);
        $totalNoLeidas = !empty($noLeidas) ? count($noLeidas) : 0;
        
        echo json_encode([
            'success' => true,
            'noLeidas' => $totalNoLeidas 
        ]);
        exit;
    }

    private static function obtenerNotificacionesDropdown()
    {
    session_start();
    $usuarioId = isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : null;
    
    if (!$usuarioId) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
    }
    
    try {
        // Obtener directamente las notificaciones NO LEÍDAS
        $notificacionesNoLeidas = ControladorNotificaciones::listarNoLeidas($usuarioId);
        
        if (!empty($notificacionesNoLeidas)) {
            // Ordenar por fecha de creación descendente (más recientes primero)
            usort($notificacionesNoLeidas, function($a, $b) {
                return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
            });
            
            // Tomar solo las primeras 3 no leídas más recientes
            $notificaciones = array_slice($notificacionesNoLeidas, 0, 3);
            
            // Asegurar que todos los campos necesarios estén presentes
            foreach ($notificaciones as &$notif) {
                // Valores por defecto si no existen
                if (!isset($notif['url']) || empty($notif['url'])) {
                    $notif['url'] = '#';
                }
                if (!isset($notif['leida'])) {
                    $notif['leida'] = 0;
                }
                if (!isset($notif['mensaje'])) {
                    $notif['mensaje'] = 'Notificación sin mensaje';
                }
                if (!isset($notif['id_tipo_evento'])) {
                    $notif['id_tipo_evento'] = 'N/A';
                }
                if (!isset($notif['fecha_creacion'])) {
                    $notif['fecha_creacion'] = date('Y-m-d H:i:s');
                }
            }
        } else {
            $notificaciones = [];
        }
        
        echo json_encode([
            'success' => true,
            'notificaciones' => $notificaciones,
            'total' => count($notificaciones),
            'totalNoLeidas' => count($notificacionesNoLeidas) 
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error al obtener notificaciones: ' . $e->getMessage(),
            'notificaciones' => []
        ]);
    }
    
    exit;
}

    private static function marcarTodasLeidas()
    {
        session_start();
        $usuarioId = isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : null;
        
        if (!$usuarioId) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit;
        }
        
        $ok = ControladorNotificaciones::marcarTodasComoLeidas($usuarioId);
        echo json_encode(['success' => $ok]);
        exit;
    }

    private static function marcarComoLeida()
    {
        session_start();
        $usuarioId = isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : null;
        $notificacionId = isset($_POST['id']) ? intval($_POST['id']) : null;
        
        if (!$usuarioId || !$notificacionId) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        
        $ok = ControladorNotificaciones::marcarComoLeida($notificacionId, $usuarioId);
        echo json_encode(['success' => $ok]);
        exit;
    }

    private static function eliminarNotificacion()
    {
        $id = intval($_POST['id']);
        $respuesta = ControladorNotificaciones::eliminarNotificacion($id);

        if ($respuesta === "ok") {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar']);
        }
        exit;
    }

    private static function eliminarMultiplesNotificaciones()
    {
        $todoOk = true;
        $errores = [];
        
        foreach ($_POST['ids'] as $id) {
            $resp = ControladorNotificaciones::eliminarNotificacion(intval($id));
            if ($resp !== "ok") {
                $todoOk = false;
                $errores[] = "Error eliminando notificación ID: " . $id;
            }
        }
        
        if ($todoOk) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'No se pudieron eliminar todas las notificaciones',
                'errors' => $errores
            ]);
        }
        exit;
    }
}

NotificacionesAjax::handleRequest();
?>