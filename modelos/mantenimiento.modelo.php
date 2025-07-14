<?php

require_once "conexion.php";

class ModeloMantenimiento
{
    // Mostrar mantenimientos
    static public function mdlMostrarMantenimientos($tabla, $item, $valor)
    {
        try {
            if ($item == null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT 
                        e.equipo_id,
                        e.numero_serie,
                        e.etiqueta,
                        e.descripcion,
                        m.id_mantenimiento,
                        m.detalles,
                        m.gravedad,
                        p.usuario_id
                    FROM 
                        equipos e
                    LEFT JOIN 
                        mantenimiento m ON e.equipo_id = m.equipo_id
                    LEFT JOIN 
                        prestamos p ON m.id_prestamo = p.id_prestamo
                    WHERE 
                        e.id_estado = 4;"
                );
                $stmt->execute();
                return $stmt->fetchAll();
            }
        } catch (PDOException $e) {
            return "error";
        } finally {
            $stmt = null;
        }
    }

    // Método para ingresar un nuevo mantenimiento
    static public function mdlFinalizarMantenimiento($equipoId, $gravedad, $detalles) {
        try {
            $db = Conexion::conectar();
    
            // 1. Actualizar mantenimiento
            $stmt = $db->prepare("UPDATE mantenimiento SET gravedad = :gravedad, detalles = :detalles WHERE equipo_id = :equipo_id");
            $stmt->bindParam(":gravedad", $gravedad);
            $stmt->bindParam(":detalles", $detalles);
            $stmt->bindParam(":equipo_id", $equipoId);
            $stmt->execute();
    
            // 2. Obtener usuario asociado (puede no existir)
            $stmt2 = $db->prepare("SELECT id_usuario FROM mantenimiento WHERE equipo_id = :equipo_id");
            $stmt2->bindParam(":equipo_id", $equipoId);
            $stmt2->execute();
            $usuario = $stmt2->fetch(PDO::FETCH_ASSOC);
    
            $estadoEquipo = 1; // por defecto
    
            // 3. Lógica según gravedad
            if ($gravedad == "ninguno") {
                $estadoEquipo = 1;
            } elseif ($gravedad == "leve") {
                $estadoEquipo = 1;
                if ($usuario && $usuario["id_usuario"]) {
                    $stmt3 = $db->prepare("UPDATE usuarios SET condicion = 'advertido' WHERE id_usuario = :id");
                    $stmt3->bindParam(":id", $usuario["id_usuario"]);
                    $stmt3->execute();
                }
            } elseif ($gravedad == "grave") {
                $estadoEquipo = 8;
                if ($usuario && $usuario["id_usuario"]) {
                    $stmt4 = $db->prepare("UPDATE usuarios SET condicion = 'penalizado' WHERE id_usuario = :id");
                    $stmt4->bindParam(":id", $usuario["id_usuario"]);
                    $stmt4->execute();
                }
            } elseif ($gravedad == "inrecuperable") {
                $estadoEquipo = 8;
            }
    
            // 4. Actualizar estado del equipo
            $stmt5 = $db->prepare("UPDATE equipos SET id_estado = :estado WHERE equipo_id = :equipo_id");
            $stmt5->bindParam(":estado", $estadoEquipo);
            $stmt5->bindParam(":equipo_id", $equipoId);
            $stmt5->execute();
    
            return "ok";
    
        } catch (PDOException $e) {
            return "error";
        }
    }
    
}
