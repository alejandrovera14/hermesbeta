# Scripts SQL para la Base de Datos `hermes002`

Este archivo contiene instrucciones y consultas SQL que deben ejecutarse en la base de datos `hermes002` para realizar actualizaciones o modificaciones específicas.

---

## IMPORTANTE

- Asegúrate de estar conectado a la base de datos `hermes002` antes de ejecutar estas consultas.
- Realiza una copia de seguridad de la base de datos antes de ejecutar cualquier script, por si acaso necesitas revertir los cambios.
- Ejecuta las consultas en el orden en que aparecen en este archivo, si el orden es relevante.

---

## Trigger para la tabla `detalle_prestamo`

Este trigger actualiza el estado del equipo en la tabla `equipos` según el tipo de préstamo (`Inmediato` o `Reservado`) cuando se inserta un nuevo registro en la tabla `detalle_prestamo`.

### Código SQL:

```sql
DELIMITER //

CREATE TRIGGER after_detalle_prestamo_insert
AFTER INSERT ON detalle_prestamo
FOR EACH ROW
BEGIN
    DECLARE v_tipo_prestamo VARCHAR(20);

    SELECT tipo_prestamo INTO v_tipo_prestamo
    FROM prestamos
    WHERE id_prestamo = NEW.id_prestamo;

    IF v_tipo_prestamo = 'Inmediato' THEN
        UPDATE equipos
        SET id_estado = 2
        WHERE equipo_id = NEW.equipo_id;
    ELSEIF v_tipo_prestamo = 'Reservado' THEN
        UPDATE equipos
        SET id_estado = 3
        WHERE equipo_id = NEW.equipo_id;
    END IF;
END;
//

DELIMITER ;

-- Asignar un nuevo permiso a la tabla de permisos, para un funcionamiento de solicitudes
INSERT INTO `permisos`( `id_modulo`, `nombre`, `descripcion`, `estado`) VALUES ('2','Buscar usuario a solicitar','Permite al usuario buscar el solicitante que va a reservar equipos o herramientas','activo');


closes [#95](https://github.com/GermanRz/hermesbeta/issues/95)
trigger creado

DELIMITER $$
USE `hermes002`$$
CREATE DEFINER = CURRENT_USER TRIGGER `hermes002`.`prestamos_AFTER_INSERT` AFTER INSERT ON `prestamos` FOR EACH ROW
BEGIN
    -- Verificar si el préstamo es de tipo "Inmediato"
    IF NEW.tipo_prestamo = 'Inmediato' AND NEW.estado_prestamo = 'Prestado' THEN
        -- Actualizar el estado de los equipos asociados de 1(Disponible) a 2(Prestado)
        UPDATE equipos e
        JOIN detalle_prestamo dp ON e.equipo_id = dp.equipo_id
        SET e.id_estado = 2
        WHERE dp.id_prestamo = NEW.id_prestamo
          AND e.id_estado = 1;
    END IF;

END$$
DELIMITER ;