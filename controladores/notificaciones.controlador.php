<?php

class ControladorNotificaciones {

    /*=============================================
    LISTAR NOTIFICACIONES NO LEÍDAS 
    =============================================*/
    static public function listarNoLeidas($usuario_id) {
        $tabla = "notificaciones";
        return ModeloNotificaciones::mdlListarNoLeidas($tabla, $usuario_id);
    }

     /*=============================================
    LISTAR NOTIFICACIONES LEIDAS
    =============================================*/
    static public function listarLeidas($usuario_id) {
        $tabla = "notificaciones";
        return ModeloNotificaciones::mdlListarLeidas($tabla, $usuario_id);
    }

    /*=============================================
    LISTAR TODAS LAS NOTIFICACIONES DE UN USUARIO
    =============================================*/
    static public function listarTodas($usuario_id) {
        $tabla = "notificaciones";
        return ModeloNotificaciones::mdlListarTodas($tabla, $usuario_id);
    }

    /*=============================================
    MARCAR NOTIFICACIÓN COMO LEÍDA
    =============================================*/
    static public function marcarComoLeida($id_notificacion, $usuario_id) {
        $tabla = "notificaciones";
        return ModeloNotificaciones::mdlMarcarNotificacionLeida($tabla, $id_notificacion, $usuario_id);
    }

    /*=============================================
    MARCAR TODAS LAS NOTIFICACIONES COMO LEÍDAS
    =============================================*/
    static public function marcarTodasComoLeidas($usuario_id) {
        $tabla = "notificaciones";
        return ModeloNotificaciones::mdlMarcarTodasComoLeidas($tabla, $usuario_id);
    }

    /*=============================================
    CREAR NOTIFICACIÓN
    =============================================*/
    static public function crearNotificacion($usuarioId, $idTipoEvento, $mensaje, $url = null) {
        $tabla = "notificaciones";
        $datos = array(
            "id_usuario"    => $usuarioId,
            "id_tipo_evento"=> $idTipoEvento,
            "mensaje"       => $mensaje,
            "url"           => $url
        );
        return ModeloNotificaciones::mdlIngresarNotificacion($tabla, $datos);
    }

    
    /*=============================================
    EDITAR NOTIFICACION
    =============================================*/
    static public function mdlEditarNotificacion($tabla, $datos) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("UPDATE $tabla SET id_usuario = :id_usuario, id_tipo_evento = :id_tipo_evento, mensaje = :mensaje, url = :url WHERE id_notificaciones = :id_notificaciones");
        $stmt->bindParam(":id_notificaciones", $datos["id_notificaciones"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt->bindParam(":id_tipo_evento", $datos["id_tipo_evento"], PDO::PARAM_INT);
        $stmt->bindParam(":mensaje", $datos["mensaje"], PDO::PARAM_STR);
        $stmt->bindParam(":url", $datos["url"], PDO::PARAM_STR);
        if($stmt->execute()){
            return "ok";
        }else{
            return "error";
        }
    }

    /*=============================================
    ELIMINAR NOTIFICACION
    =============================================*/
    static public function eliminarNotificacion($id_notificacion) {
        $tabla = "notificaciones";
        return ModeloNotificaciones::mdlEliminarNotificacion($tabla, $id_notificacion);
    }

    /*=============================================
    OBTENER DETALLES DE NOTIFICACION
    =============================================*/
    static public function obtenerDetallesNotificacion($id_notificacion){

        $tabla = "notificaciones";

        return ModeloNotificaciones::mdlObtenerDetallesNotificacion($tabla, $id_notificacion);
    }

 
    
    // /*=============================================
    // NOTIFICAR CAMBIO DE ESTADO DE USUARIO
    // =============================================*/
    // static public function notificarCambioEstadoUsuario($idUsuarioEditado, $estadoAnterior, $estadoNuevo) {
    //     if ($estadoAnterior !== $estadoNuevo) {

    //         // Obtener datos del usuario afectado (editado)
    //         $usuarioEditado = ModeloUsuarios::mdlMostrarUsuarios("usuarios", "id_usuario", $idUsuarioEditado);

    //         $nombreAfectado = "Usuario";
    //         if ($usuarioEditado && isset($usuarioEditado["nombre"], $usuarioEditado["apellido"])) {
    //             $nombreAfectado = $usuarioEditado["nombre"] . ' ' . $usuarioEditado["apellido"];
    //         }

    //         // Obtener todos los administradores (rol 9)
    //         $admins = ModeloUsuarios::mdlMostrarUsuarios("usuarios", "rol", 9);

    //         echo "<pre>";
    //         print_r($admins);
    //         echo "</pre>";
    //         exit;

    //         // Asegurarse de que $admins sea siempre un array indexado
    //         if ($admins && isset($admins["id_usuario"])) {
    //             $admins = [ $admins ]; // Envolver si vino como un solo resultado
    //         }

    //         // Notificar a cada administrador
    //         if ($admins) {
    //             foreach ($admins as $adminUser) {
    //                 if (isset($adminUser["id_usuario"])) {
    //                     ControladorNotificaciones::crearNotificacion(
    //                         $adminUser["id_usuario"],
    //                         $idTipoEvento,
    //                         $mensaje,
    //                         $url
    //                     );
    //                 }
    //             }
    //         }

    //         // Notificar también al usuario afectado
    //         ControladorNotificaciones::crearNotificacion(
    //             $idUsuarioEditado,
    //             $idTipoEvento,
    //             $mensaje,
    //             $url
    //         );
    //     }
    //     }
}