# Scripts SQL para la Base de Datos hermes002

Este archivo contiene instrucciones y consultas SQL que deben ejecutarse en la base de datos `hermes002` para realizar actualizaciones o modificaciones específicas.

**¡IMPORTANTE!**

* Asegúrate de estar conectado a la base de datos `hermes002` antes de ejecutar estas consultas.
* Realiza una copia de seguridad de la base de datos antes de ejecutar cualquier script, por si acaso necesitas revertir los cambios.
* Ejecuta las consultas en el orden en que aparecen en este archivo, si el orden es relevante.


# Módulo de Notificaciones - Sistema Hermes

Este módulo permite la gestión, almacenamiento y visualización de notificaciones dentro del sistema Hermes. Las notificaciones se generan en función de eventos definidos previamente, y permiten mantener informados a los usuarios sobre el estado de sus operaciones o sucesos del sistema.

---

## Descripción General

El sistema está basado en dos tablas principales:

- **tipos_evento_notificacion**: donde se definen los eventos que pueden generar notificaciones.
- **notificaciones**: donde se almacenan las notificaciones generadas para cada usuario.

---

##  Estructura de Base de Datos

### Tabla `tipos_evento_notificacion`

Contiene el catálogo de eventos del sistema.

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id_tipo_evento` | int (PK, AI) | ID único del tipo de evento |
| `nombre` | varchar(255) | Nombre del evento |
| `descripcion` | text | Descripción detallada del evento |
| `tipo_notificacion` | enum('info','warning','danger','success') | Tipo visual de la notificación |
| `prioridad` | int | Prioridad (1=Alta, 2=Media, 3=Baja) |

---

###  Tabla `notificaciones`

Almacena las notificaciones enviadas a los usuarios.

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id_notificaciones` | int (PK, AI) | ID único de la notificación |
| `id_usuario` | int | ID del usuario destinatario |
| `id_tipo_evento` | int | Relación al evento correspondiente |
| `mensaje` | text | Mensaje descriptivo de la notificación |
| `url` | varchar(255) | URL de redirección (opcional por el momento) |
| `leida` | tinyint(1) | Estado de lectura (0 = no leída, 1 = leída) |
| `fecha_creacion` | timestamp | Fecha de creación |
| `fecha_leida` | datetime | Fecha de lectura (opcional) |

---

##  Script de Instalación Completo

A continuación, se detalla el script SQL completo para crear las tablas e insertar los datos iniciales.

###  Creación de tablas

```sql
-- Crear tabla de tipos de eventos
CREATE TABLE `tipos_evento_notificacion` (
  `id_tipo_evento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_notificacion` enum('info','warning','danger','success') DEFAULT 'info',
  `prioridad` int(11) DEFAULT 3 COMMENT 'Prioridad: 1=Alta, 2=Media, 3=Baja',
  PRIMARY KEY (`id_tipo_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Crear tabla de notificaciones
CREATE TABLE `notificaciones` (
  `id_notificaciones` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_tipo_evento` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_leida` datetime DEFAULT NULL,
  PRIMARY KEY (`id_notificaciones`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_tipo_evento` (`id_tipo_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tipos_evento_notificacion` (`id_tipo_evento`, `nombre`, `descripcion`, `tipo_notificacion`, `prioridad`) VALUES
(1, 'Préstamo generado', 'Se generó una solicitud de préstamo', 'info', 2),
(2, 'Préstamo aprobado', 'El préstamo fue aprobado exitosamente', 'success', 1),
(3, 'Préstamo rechazado', 'El préstamo fue rechazado por falta de disponibilidad', 'warning', 1),
(4, 'Usuario inactivo', 'El usuario fue marcado como inactivo', 'danger', 1),
(5, 'Devolución completada', 'El préstamo fue devuelto correctamente', 'success', 3),
(6, 'Suspensión de usuario', 'El usuario ha sido suspendido temporalmente', 'danger', 1),
(7, 'Revisión pendiente', 'El préstamo está pendiente de revisión', 'warning', 2),
(8, 'Nuevo equipo asignado', 'Se asignó un nuevo equipo al usuario', 'success', 2),
(9, 'Error de préstamo', 'Se produjo un error durante el préstamo', 'danger', 1),
(10, 'Solicitud cancelada', 'El préstamo fue cancelado por el usuario', 'warning', 2),
(11, 'Mantenimiento programado', 'Se ha programado mantenimiento del equipo', 'info', 2),
(12, 'Mantenimiento completado', 'El equipo ha pasado mantenimiento exitosamente', 'success', 1),
(13, 'Mantenimiento fallido', 'El mantenimiento del equipo falló', 'danger', 1),
(14, 'Reserva anticipada', 'Se ha realizado una reserva anticipada', 'info', 2),
(15, 'Reserva rechazada', 'La reserva fue rechazada', 'warning', 1),
(16, 'Transferencia de equipo', 'Se transfirió un equipo a otro usuario', 'info', 2),
(17, 'Solicitud de baja', 'El usuario solicitó la baja del equipo', 'warning', 2),
(18, 'Baja aprobada', 'La baja del equipo fue aprobada', 'success', 1),
(19, 'Baja rechazada', 'La baja del equipo fue rechazada', 'danger', 1),
(20, 'Auditoría realizada', 'Se realizó auditoría sobre el equipo', 'info', 3);


INSERT INTO `notificaciones` (`id_usuario`, `id_tipo_evento`, `mensaje`, `url`, `leida`) VALUES 
(1, 1, 'Se generó la solicitud de préstamo del equipo HP Pavilion', 'mis-solicitudes', 0),
(1, 2, 'El préstamo del equipo Lenovo ThinkPad fue aprobado exitosamente', 'mis-solicitudes', 0),
(1, 3, 'El préstamo del monitor Samsung fue rechazado', 'mis-solicitudes', 0),
(1, 4, 'El usuario Andrea Gómez ha sido marcado como inactivo', 'usuarios', 0),
(1, 5, 'La devolución del equipo Dell Inspiron fue completada', 'mis-solicitudes', 0),
(1, 6, 'El usuario Pedro Martínez ha sido suspendido', 'usuarios', 0),
(1, 7, 'Préstamo de herramienta Bosch pendiente de revisión', 'mis-solicitudes', 0),
(1, 8, 'Se asignó el equipo Lenovo IdeaPad al usuario Luis Rodríguez', 'mis-solicitudes', 0),
(1, 9, 'Error en el préstamo de herramienta Makita', 'mis-solicitudes', 0),
(1, 10, 'Solicitud de préstamo de proyector Epson cancelada', 'mis-solicitudes', 0),
(1, 11, 'Mantenimiento programado para equipo Canon', 'mis-solicitudes', 0),
(1, 12, 'Mantenimiento completado en servidor HP Proliant', 'mis-solicitudes', 0),
(1, 13, 'Mantenimiento fallido en equipo Dell', 'mis-solicitudes', 0),
(1, 14, 'Reserva anticipada de equipo MacBook Pro', 'reservas', 0),
(1, 15, 'Reserva rechazada para monitor LG', 'reservas', 0),
(1, 16, 'Transferencia de equipo a usuario Carlos Ruiz', 'mis-solicitudes', 0),
(1, 17, 'Solicitud de baja para impresora Epson', 'mis-solicitudes', 0),
(1, 18, 'Baja de equipo HP aprobada', 'mis-solicitudes', 0),
(1, 19, 'Baja de equipo rechazada por inventario', 'mis-solicitudes', 0),
(1, 20, 'Auditoría realizada sobre el equipo de redes', 'mis-solicitudes', 0);
