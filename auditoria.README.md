
📋 Módulo de Auditoría de Usuarios

Este módulo permite llevar un historial detallado de los cambios realizados sobre los usuarios en el sistema. Utiliza arquitectura MVC, MySQL, AJAX y DataTables con modo server-side para una visualización eficiente.

⚠️ Nota importante

Este módulo trabaja en conjunto con el módulo de Usuarios, ya que los registros se generan automáticamente mediante triggers (disparadores) definidos en las tablas usuarios, fichas, sedes y roles. Todos los cambios se almacenan en la tabla auditoria_usuarios, la cual es visualizada a través de la vista vista_auditoria para alimentar el servidor en modo server-side en MySQL.

🧩 Funcionalidades

- Registro automático de cambios en campos clave del usuario (ej. nombre, correo, estado, género, etc.).

- Visualización en tabla dinámica utilizando DataTables.

- Modal de detalles con información del campo modificado, valor anterior y valor nuevo.

- Búsqueda, orden cronológico, y exportación a CSV/Excel.

## 🛠️ Componentes Técnicos

### Base de Datos

- **Tabla:** `auditoria_usuarios`
- **Vista:** `vista_auditoria`  
  Une los datos de `usuarios` (afectado) y `usuarios` (editor) para presentar información amigable como nombres completos.

```sql
CREATE OR REPLACE VIEW vista_auditoria AS
SELECT 
    au.id_auditoria,
    u.tipo_documento,
    u.numero_documento,
    u.nombre,
    u.apellido,
    CONCAT(editor.nombre, ' ', editor.apellido) AS nombre_editor,
    au.fecha_cambio,
    au.campo_modificado,
    au.valor_anterior,
    au.valor_nuevo
FROM auditoria_usuarios au
JOIN usuarios u ON u.id_usuario = au.id_usuario_afectado
JOIN usuarios editor ON editor.id_usuario = au.id_usuario_editor;
```

### Backend

- **Archivo Server-Side:** `serverside.auditoria.php`
- **Consulta principal:** Se basa en `vista_auditoria` para alimentar la tabla vía AJAX.

### Frontend

- **Archivo JavaScript:** `auditoria.js`
- Usa `DataTables` con `serverSide: true`.
- Botón para ver detalles de los cambios.
- Exportación habilitada con botones `CSV` y `Excel`.

## 🔍 Detalles Técnicos

- Se ordena automáticamente por la fecha más reciente (`fecha_cambio DESC`).
- Columnas ocultas (campo modificado, valor anterior y nuevo) se utilizan solo para el modal y exportaciones.
- Campos múltiples modificados se separan por `;` y se dividen dinámicamente en el modal.

## ⚠️ Consideraciones

- El módulo requiere tener correctamente pobladas las relaciones entre usuarios afectados y editores.
- El trigger o la lógica que llena `auditoria_usuarios` debe registrar múltiples campos de ser necesario.


## 🧾 Espacio para la instrucciones de auditoría 

Para registrar un historial de modificaciones en los usuarios, sigue estos pasos para crear la tabla de auditoría y el trigger que registra los cambios automáticamente:

### 1. Crear la tabla de auditoría: `auditoria_usuarios`

```sql
CREATE TABLE auditoria_usuarios (
  id_auditoria INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_usuario_afectado INT(11) NOT NULL,
  id_usuario_editor INT(11) DEFAULT NULL,
  campo_modificado VARCHAR(50) NOT NULL,
  valor_anterior TEXT,
  valor_nuevo TEXT,
  fecha_cambio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Crear el trigger `trg_auditar_usuarios` que registra los cambios en la tabla `usuarios`

```sql
DELIMITER $$

CREATE TRIGGER trg_auditar_usuarios
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
  DECLARE cambios TEXT DEFAULT '';
  DECLARE cambios_anterior TEXT DEFAULT '';
  DECLARE campos TEXT DEFAULT '';
  DECLARE separador VARCHAR(3) DEFAULT '';

  -- Detectar cambios en cada campo
  IF NOT (OLD.tipo_documento <=> NEW.tipo_documento) THEN
    SET cambios = CONCAT(cambios, separador, NEW.tipo_documento);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.tipo_documento);
    SET campos = CONCAT(campos, separador, 'tipo_documento');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.numero_documento <=> NEW.numero_documento) THEN
    SET cambios = CONCAT(cambios, separador, NEW.numero_documento);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.numero_documento);
    SET campos = CONCAT(campos, separador, 'numero_documento');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.nombre <=> NEW.nombre) THEN
    SET cambios = CONCAT(cambios, separador, NEW.nombre);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.nombre);
    SET campos = CONCAT(campos, separador, 'nombre');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.apellido <=> NEW.apellido) THEN
    SET cambios = CONCAT(cambios, separador, NEW.apellido);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.apellido);
    SET campos = CONCAT(campos, separador, 'apellido');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.correo_electronico <=> NEW.correo_electronico) THEN
    SET cambios = CONCAT(cambios, separador, NEW.correo_electronico);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.correo_electronico);
    SET campos = CONCAT(campos, separador, 'correo_electronico');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.nombre_usuario <=> NEW.nombre_usuario) THEN
    SET cambios = CONCAT(cambios, separador, NEW.nombre_usuario);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.nombre_usuario);
    SET campos = CONCAT(campos, separador, 'nombre_usuario');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.telefono <=> NEW.telefono) THEN
    SET cambios = CONCAT(cambios, separador, NEW.telefono);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.telefono);
    SET campos = CONCAT(campos, separador, 'telefono');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.direccion <=> NEW.direccion) THEN
    SET cambios = CONCAT(cambios, separador, NEW.direccion);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.direccion);
    SET campos = CONCAT(campos, separador, 'direccion');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.genero <=> NEW.genero) THEN
    SET cambios = CONCAT(cambios, separador, NEW.genero);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.genero);
    SET campos = CONCAT(campos, separador, 'genero');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.estado <=> NEW.estado) THEN
    SET cambios = CONCAT(cambios, separador, NEW.estado);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.estado);
    SET campos = CONCAT(campos, separador, 'estado');
    SET separador = '; ';
  END IF;

  IF NOT (OLD.condicion <=> NEW.condicion) THEN
    SET cambios = CONCAT(cambios, separador, NEW.condicion);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.condicion);
    SET campos = CONCAT(campos, separador, 'condicion');
    SET separador = '; ';
  END IF;

  -- Insertar registro solo si hubo cambios
  IF cambios <> '' THEN
    INSERT INTO auditoria_usuarios (
      id_usuario_afectado,
      id_usuario_editor,
      campo_modificado,
      valor_anterior,
      valor_nuevo,
      fecha_cambio
    ) VALUES (
      OLD.id_usuario,
      @id_usuario_editor,
      campos,
      cambios_anterior,
      cambios,
      NOW()
    );
  END IF;
END$$

DELIMITER ;
```


### 3. Crear el trigger `trg_auditar_sedes` que registra los cambios en la tabla `sedes`
```sql
DELIMITER $$

CREATE TRIGGER trg_auditar_sedes
AFTER UPDATE ON sedes
FOR EACH ROW
BEGIN
  DECLARE cambios TEXT DEFAULT '';
  DECLARE cambios_anterior TEXT DEFAULT '';
  DECLARE campos TEXT DEFAULT '';
  DECLARE separador VARCHAR(3) DEFAULT '';

  IF NOT (OLD.nombre_sede <=> NEW.nombre_sede) THEN
    SET cambios = CONCAT(cambios, separador, NEW.nombre_sede);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.nombre_sede);
    SET campos = CONCAT(campos, separador, 'nombre_sede');
    SET separador = '; ';
  END IF;

  IF cambios <> '' THEN
    INSERT INTO auditoria_usuarios (
      id_usuario_afectado,
      id_usuario_editor,
      campo_modificado,
      valor_anterior,
      valor_nuevo,
      fecha_cambio
    ) VALUES (
      OLD.id_sede,
      @id_usuario_editor,
      campos,
      cambios_anterior,
      cambios,
      NOW()
    );
  END IF;
END$$

DELIMITER ;
```


### 4. Crear el trigger `trg_auditar_roles` que registra los cambios en la tabla `roles`
```sql
DELIMITER $$

CREATE TRIGGER trg_auditar_roles
AFTER UPDATE ON roles
FOR EACH ROW
BEGIN
  DECLARE cambios TEXT DEFAULT '';
  DECLARE cambios_anterior TEXT DEFAULT '';
  DECLARE campos TEXT DEFAULT '';
  DECLARE separador VARCHAR(3) DEFAULT '';

  IF NOT (OLD.nombre_rol <=> NEW.nombre_rol) THEN
    SET cambios = CONCAT(cambios, separador, NEW.nombre_rol);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.nombre_rol);
    SET campos = CONCAT(campos, separador, 'nombre_rol');
    SET separador = '; ';
  END IF;

  IF cambios <> '' THEN
    INSERT INTO auditoria_usuarios (
      id_usuario_afectado,
      id_usuario_editor,
      campo_modificado,
      valor_anterior,
      valor_nuevo,
      fecha_cambio
    ) VALUES (
      OLD.id_rol,
      @id_usuario_editor,
      campos,
      cambios_anterior,
      cambios,
      NOW()
    );
  END IF;
END$$

DELIMITER ;

```
### 5. Crear el trigger `trg_auditar_fichas` que registra los cambios en la tabla `fichas`
```sql
DELIMITER $$

CREATE TRIGGER trg_auditar_fichas
AFTER UPDATE ON fichas
FOR EACH ROW
BEGIN
  DECLARE cambios TEXT DEFAULT '';
  DECLARE cambios_anterior TEXT DEFAULT '';
  DECLARE campos TEXT DEFAULT '';
  DECLARE separador VARCHAR(3) DEFAULT '';

  IF NOT (OLD.descripcion <=> NEW.descripcion) THEN
    SET cambios = CONCAT(cambios, separador, NEW.descripcion);
    SET cambios_anterior = CONCAT(cambios_anterior, separador, OLD.descripcion);
    SET campos = CONCAT(campos, separador, 'descripcion');
    SET separador = '; ';
  END IF;

  IF cambios <> '' THEN
    INSERT INTO auditoria_usuarios (
      id_usuario_afectado,
      id_usuario_editor,
      campo_modificado,
      valor_anterior,
      valor_nuevo,
      fecha_cambio
    ) VALUES (
      OLD.id_ficha,
      @id_usuario_editor,
      campos,
      cambios_anterior,
      cambios,
      NOW()
    );
  END IF;
END$$

DELIMITER ;

```