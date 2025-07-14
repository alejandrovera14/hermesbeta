<?php
require_once "conexion.php";

class ModeloDevoluciones
{
    /*=============================================
    OBTENER ID DE ESTADO POR NOMBRE
    =============================================*/
    static public function mdlObtenerIdEstado($estado) {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT id_estado FROM estados WHERE estado = :estado"
            );
            $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                return $resultado['id_estado'];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error en mdlObtenerIdEstado: " . $e->getMessage());
            return false;
        } finally {
            $stmt = null;
        }
    }

    /*=============================================
    MOSTRAR DEVOLUCIONES (LISTADO)
    =============================================*/
    static public function mdlMostrarDevoluciones($tabla, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare(
                "SELECT p.id_prestamo, 
                        DATE_FORMAT(p.fecha_inicio, '%Y-%m-%d') as fecha_inicio, 
                        DATE_FORMAT(p.fecha_fin, '%Y-%m-%d') as fecha_fin, 
                        p.tipo_prestamo, p.estado_prestamo, 
                        u.numero_documento, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario, u.telefono, 
                        r.nombre_rol,
                        f.codigo as ficha_codigo, 
                        e.equipo_id, e.numero_serie, e.descripcion AS marca_equipo, e.etiqueta AS placa_equipo,
                        c.nombre AS categoria_nombre, 
                        e.id_estado AS estado_del_equipo,
                        dp.estado AS estado_detalle_prestamo
                 FROM $tabla p
                 JOIN usuarios u ON p.usuario_id = u.id_usuario
                 LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario
                 LEFT JOIN roles r ON ur.id_rol = r.id_rol
                 LEFT JOIN aprendices_ficha af ON u.id_usuario = af.id_usuario
                 LEFT JOIN fichas f ON af.id_ficha = f.id_ficha
                 LEFT JOIN detalle_prestamo dp ON p.id_prestamo = dp.id_prestamo
                 LEFT JOIN equipos e ON dp.equipo_id = e.equipo_id
                 LEFT JOIN categorias c ON e.categoria_id = c.categoria_id
                 WHERE p.$item = :$item
                 AND p.estado_prestamo IN ('Prestado')
                 AND e.id_estado = 2"
            );

            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = Conexion::conectar()->prepare(
                "SELECT p.id_prestamo, u.numero_documento, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario, u.telefono,
                        r.nombre_rol,
                        f.codigo as ficha_codigo,
                        DATE_FORMAT(p.fecha_inicio, '%Y-%m-%d') as fecha_inicio,
                        DATE_FORMAT(p.fecha_fin, '%Y-%m-%d') as fecha_fin,
                        p.tipo_prestamo,
                        CASE
                            WHEN p.tipo_prestamo = 'Inmediato' THEN 'Inmediato'
                            ELSE 'Reservado'
                        END as estado_prestamo_display,
                        p.estado_prestamo
                 FROM $tabla p
                 JOIN usuarios u ON p.usuario_id = u.id_usuario
                 LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario
                 LEFT JOIN roles r ON ur.id_rol = r.id_rol
                 LEFT JOIN aprendices_ficha af ON u.id_usuario = af.id_usuario
                 LEFT JOIN fichas f ON af.id_ficha = f.id_ficha
                 WHERE p.estado_prestamo IN ('Prestado')
                 ORDER BY p.fecha_inicio DESC"
            );

            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = null;
    }

    /*=============================================
    MARCAR EQUIPO EN DETALLE_PRESTAMO COMO MANTENIMIENTO Y ROBADO (ACTUALIZANDO ID_ESTADO)
    =============================================*/
    static public function mdlMarcarMantenimientoDetalle($datos){
    try {
        $conexion = Conexion::conectar();          
        $conexion->beginTransaction();

        // 1. Actualizar estado del equipo
        $stmt = $conexion->prepare("UPDATE equipos SET id_estado = :id_estado WHERE equipo_id = :equipo_id");
        $stmt->bindParam(":id_estado", $datos["id_estado"], PDO::PARAM_INT);
        $stmt->bindParam(":equipo_id", $datos["equipo_id"], PDO::PARAM_INT);

        if(!$stmt->execute()){
            $conexion->rollBack();
            error_log("MODELO: Error en execute(): " . json_encode($stmt->errorInfo()));
            return "error";
        }

        // 2. Obtener datos del préstamo para registrar en mantenimiento
        $stmtPrestamo = $conexion->prepare(
            "SELECT usuario_id FROM prestamos WHERE id_prestamo = :id_prestamo"
        );
        $stmtPrestamo->bindParam(":id_prestamo", $datos["id_prestamo"], PDO::PARAM_INT);
        $stmtPrestamo->execute();
        $prestamo = $stmtPrestamo->fetch(PDO::FETCH_ASSOC);

        // 3. Registrar en tabla mantenimiento (insertar o actualizar)
        $stmtMantenimiento = $conexion->prepare(
            "INSERT INTO mantenimiento (equipo_id, id_usuario, id_prestamo, detalles, fecha_inicio) 
            VALUES (:equipo_id, :id_usuario, :id_prestamo, :detalles, NOW())
            ON DUPLICATE KEY UPDATE
            id_usuario = VALUES(id_usuario),
            id_prestamo = VALUES(id_prestamo),
            detalles = VALUES(detalles),
            fecha_inicio = VALUES(fecha_inicio)"
        );

        $detalles = "Equipo enviado a mantenimiento desde devolución";
        $stmtMantenimiento->bindParam(":equipo_id", $datos["equipo_id"], PDO::PARAM_INT);
        $stmtMantenimiento->bindParam(":id_usuario", $prestamo['usuario_id'], PDO::PARAM_INT);
        $stmtMantenimiento->bindParam(":id_prestamo", $datos["id_prestamo"], PDO::PARAM_INT);
        $stmtMantenimiento->bindParam(":detalles", $detalles, PDO::PARAM_STR);

        if(!$stmtMantenimiento->execute()){
            $conexion->rollBack();
            return "error_registro_mantenimiento";
        }

        // 3b. Si el estado es 'baja', penalizar al usuario
        $idEstadoBaja = self::mdlObtenerIdEstado('baja');
        if ($datos["id_estado"] == $idEstadoBaja) {
            $stmtPenalizar = $conexion->prepare(
                "UPDATE usuarios SET condicion = 'penalizado' WHERE id_usuario = :id_usuario"
            );
            $stmtPenalizar->bindParam(":id_usuario", $prestamo['usuario_id'], PDO::PARAM_INT);
            if (!$stmtPenalizar->execute()) {
                $conexion->rollBack();
                return "error_penalizando_usuario";
            }
        }

        // 4. Actualizar detalle préstamo
        if(isset($datos["id_prestamo"])){
            $stmtDetalle = $conexion->prepare(
                "UPDATE detalle_prestamo SET estado = 'Devuelto', 
                fecha_actualizacion = NOW()
                WHERE id_prestamo = :id_prestamo AND equipo_id = :equipo_id"
            );

            $stmtDetalle->bindParam(":id_prestamo", $datos["id_prestamo"], PDO::PARAM_INT);
            $stmtDetalle->bindParam(":equipo_id", $datos["equipo_id"], PDO::PARAM_INT);

            if(!$stmtDetalle->execute()){
                $conexion->rollBack();
                return "error_actualizando_detalle";
            }
        }

        $conexion->commit();
        return "ok";

    } catch (PDOException $e) {
        if(isset($conexion)) $conexion->rollBack();
        error_log("Error en mdlMarcarMantenimientoDetalle: " . $e->getMessage());
        return "error";
    }
}


    /*=============================================
    VERIFICAR SI TODOS LOS EQUIPOS DE UN PRÉSTAMO HAN SIDO DEVUELTOS 
    =============================================*/
    static public function mdlVerificarTodosEquiposDevueltos($idPrestamo){
        $stmt = Conexion::conectar()->prepare(
            "SELECT COUNT(dp.equipo_id) as total_equipos_prestamo,
                    SUM(CASE WHEN est.estado IN ('Mantenimiento', 'baja', 'Disponible') THEN 1 ELSE 0 END) as equipos_procesados
             FROM detalle_prestamo dp
             JOIN equipos e ON dp.equipo_id = e.equipo_id
             JOIN estados est ON e.id_estado = est.id_estado
             WHERE dp.id_prestamo = :id_prestamo"
        );

        $stmt->bindParam(":id_prestamo", $idPrestamo, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if($resultado && $resultado["total_equipos_prestamo"] > 0 && 
           $resultado["total_equipos_prestamo"] == $resultado["equipos_procesados"]){
            return true;
        } else {
            return false;
        }

        $stmt = null;
    }

    /*=============================================
    ACTUALIZAR ESTADO DEL PRÉSTAMO A DEVUELTO Y REGISTRAR FECHA REAL DE DEVOLUCIÓN
    =============================================*/
    static public function mdlActualizarPrestamoDevuelto($idPrestamo){
        $stmt = Conexion::conectar()->prepare(
            "UPDATE prestamos 
             SET estado_prestamo = 'Devuelto', fecha_devolucion_real = NOW() 
             WHERE id_prestamo = :id_prestamo"
        );

        $stmt->bindParam(":id_prestamo", $idPrestamo, PDO::PARAM_INT);

        if($stmt->execute()){
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*============================================= 
    MARCAR EQUIPO EN DETALLE_PRESTAMO COMO DEVUELTO EN BUEN ESTADO (ACTUALIZANDO ID_ESTADO)
    =============================================*/
    static public function mdlMarcarDevueltoBuenEstado($datos){
        $stmtEquipo = Conexion::conectar()->prepare(
            "UPDATE equipos SET id_estado = :id_estado 
            WHERE equipo_id = :equipo_id"
        );
        
        $stmtEquipo->bindParam(":id_estado", $datos["id_estado"], PDO::PARAM_INT);
        $stmtEquipo->bindParam(":equipo_id", $datos["equipo_id"], PDO::PARAM_INT);
        
        if($stmtEquipo->execute()){
            $stmtDetalle = Conexion::conectar()->prepare(
                "UPDATE detalle_prestamo SET estado = 'Devuelto', 
                fecha_actualizacion = NOW()
                WHERE id_prestamo = :id_prestamo AND equipo_id = :equipo_id"
            );
            
            $stmtDetalle->bindParam(":id_prestamo", $datos["id_prestamo"], PDO::PARAM_INT);
            $stmtDetalle->bindParam(":equipo_id", $datos["equipo_id"], PDO::PARAM_INT);
            
            if($stmtDetalle->execute()){
                return "ok";
            } else {
                return "error_actualizando_detalle";
                // Obtain the id_estado for 'Mantenimiento', 'Disponible', or 'baja'
            }
        } else {
            return "error_actualizando_equipo";
        }
        
        $stmtEquipo = null;
        $stmtDetalle = null;
    }

    /*=============================================
    REGISTRAR MOTIVO DE MANTENIMIENTO 
    =============================================*/
    static public function mdlRegistrarMotivoMantenimiento($equipoId, $motivo) {
        try {
            $stmtCheck = Conexion::conectar()->prepare(
                "SELECT Id_mantenimiento FROM mantenimiento WHERE equipo_id = :equipo_id"
            );
            $stmtCheck->bindParam(":equipo_id", $equipoId, PDO::PARAM_INT);
            $stmtCheck->execute();
            
            if($stmtCheck->rowCount() > 0) {
                $stmt = Conexion::conectar()->prepare(
                    "UPDATE mantenimiento SET detalles = :detalles WHERE equipo_id = :equipo_id"
                );
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "INSERT INTO mantenimiento (equipo_id, detalles) VALUES (:equipo_id, :detalles)"
                );
            }
            
            $stmt->bindParam(":equipo_id", $equipoId, PDO::PARAM_INT);
            $stmt->bindParam(":detalles", $motivo, PDO::PARAM_STR);
            
            if($stmt->execute()) {
                return "ok";
            } else {
                error_log("Error SQL: " . json_encode($stmt->errorInfo()));
                return "error";
            }
        } catch (PDOException $e) {
            error_log("Excepción en mdlRegistrarMotivoMantenimiento: " . $e->getMessage());
            return "error";
        } finally {
            $stmt = null;
            $stmtCheck = null;
        }
    }
}
?>