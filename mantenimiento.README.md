///
NUEVA TABLA DE MANTENIMIENTO
///



-- Eliminar columnas anteriores 
ALTER TABLE `mantenimiento` 
DROP COLUMN `tipo_mantenimiento`;

-- Modificar la columna `gravedad` para incluir 'inrecuperable'
ALTER TABLE `mantenimiento` 
MODIFY COLUMN `gravedad` ENUM('ninguno', 'leve', 'grave', 'inrecuperable') DEFAULT 'ninguno';

-- Agregar nuevas columnas 
ALTER TABLE `mantenimiento`
ADD COLUMN `id_prestamo` INT(11) NULL AFTER `gravedad`,
ADD COLUMN `id_usuario` INT(11) NULL AFTER `id_prestamo`,
ADD COLUMN `fecha_inicio` DATETIME NOT NULL DEFAULT NOW() AFTER `id_usuario`,
ADD COLUMN `fecha_fin` DATETIME NULL AFTER `fecha_inicio`;

-- Asegurar la clave primaria y AUTO_INCREMENT
ALTER TABLE `mantenimiento`
MODIFY COLUMN `Id_mantenimiento` INT(11) NOT NULL AUTO_INCREMENT,
ADD PRIMARY KEY (`Id_mantenimiento`);

-- Agregar claves foráneas e índices
ALTER TABLE `mantenimiento`
ADD INDEX (`equipo_id`),
ADD INDEX (`id_prestamo`),
ADD INDEX (`id_usuario`);

