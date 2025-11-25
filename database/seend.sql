USE moodloop;

-- Evitar fallos por claves foráneas durante el seed
SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- Insertar Roles (idempotente)
-- =========================
INSERT INTO roles (nombre_rol) VALUES
('admin'),
('usuario'),
('usuario_sin_confirma')
ON DUPLICATE KEY UPDATE nombre_rol = VALUES(nombre_rol);

-- Capturar IDs de roles por nombre (variables de sesión)
SET @rol_admin = (SELECT id_rol FROM roles WHERE nombre_rol = 'admin' LIMIT 1);
SET @rol_usuario = (SELECT id_rol FROM roles WHERE nombre_rol = 'usuario' LIMIT 1);
SET @rol_pendiente = (SELECT id_rol FROM roles WHERE nombre_rol = 'usuario_sin_confirma' LIMIT 1);

-- =========================
-- Insertar Usuarios (idempotente)
-- Nota: hashes de contraseña de ejemplo (reemplaza por password_hash en producción)
-- =========================
INSERT INTO usuarios (nombre_usuario, correo, contraseña_hash, biografia, estado_emocional, id_rol, confirmado, baneado, fecha_registro)
VALUES
('admin', 'admin@moodloop.com', '$2y$10$abcdefghijklmnopqrstuv', 'Administrador del sistema', 'neutral', @rol_admin, 1, 0, NOW())
ON DUPLICATE KEY UPDATE
    contraseña_hash = VALUES(contraseña_hash),
    biografia = VALUES(biografia),
    estado_emocional = VALUES(estado_emocional),
    id_rol = @rol_admin,
    confirmado = 1,
    baneado = 0;

INSERT INTO usuarios (nombre_usuario, correo, contraseña_hash, biografia, estado_emocional, id_rol, confirmado, baneado, fecha_registro)
VALUES
('erik', 'erik@moodloop.com', '$2y$10$abcdefghijklmnopqrstuv', 'Desarrollador web en formación', 'motivado', @rol_usuario, 1, 0, NOW())
ON DUPLICATE KEY UPDATE
    contraseña_hash = VALUES(contraseña_hash),
    biografia = VALUES(biografia),
    estado_emocional = VALUES(estado_emocional),
    id_rol = @rol_usuario,
    confirmado = 1,
    baneado = 0;

INSERT INTO usuarios (nombre_usuario, correo, contraseña_hash, biografia, estado_emocional, id_rol, confirmado, baneado, fecha_registro)
VALUES
('maria', 'maria@moodloop.com', '$2y$10$abcdefghijklmnopqrstuv', 'Me encanta compartir frases positivas', 'feliz', @rol_usuario, 1, 0, NOW())
ON DUPLICATE KEY UPDATE
    contraseña_hash = VALUES(contraseña_hash),
    biografia = VALUES(biografia),
    estado_emocional = VALUES(estado_emocional),
    id_rol = @rol_usuario,
    confirmado = 1,
    baneado = 0;

-- Guardar IDs de usuarios para relaciones
SET @uid_admin = (SELECT id_usuario FROM usuarios WHERE correo = 'admin@moodloop.com' LIMIT 1);
SET @uid_erik  = (SELECT id_usuario FROM usuarios WHERE correo = 'erik@moodloop.com' LIMIT 1);
SET @uid_maria = (SELECT id_usuario FROM usuarios WHERE correo = 'maria@moodloop.com' LIMIT 1);

-- =========================
-- Insertar Publicaciones (idempotente por mensaje+usuario)
-- =========================
INSERT IGNORE INTO publicaciones (id_usuario, mensaje, estado_emocional, fecha_hora)
VALUES
(@uid_erik,  'Hoy aprendí a conectar PHP con MySQL 🎉', 'motivado', NOW()),
(@uid_maria, 'La vida es mejor con una sonrisa 😊',     'feliz',    NOW());

-- Capturar IDs de publicaciones recién insertadas
SET @pub_erik = (SELECT id_publicacion FROM publicaciones WHERE id_usuario = @uid_erik AND mensaje = 'Hoy aprendí a conectar PHP con MySQL 🎉' LIMIT 1);
SET @pub_maria = (SELECT id_publicacion FROM publicaciones WHERE id_usuario = @uid_maria AND mensaje = 'La vida es mejor con una sonrisa 😊' LIMIT 1);

-- =========================
-- Insertar Etiquetas (idempotente)
-- =========================
INSERT INTO etiquetas (nombre_etiqueta) VALUES
('programacion'),
('motivacion'),
('felicidad')
ON DUPLICATE KEY UPDATE nombre_etiqueta = VALUES(nombre_etiqueta);

-- IDs de etiquetas
SET @tag_programacion = (SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = 'programacion' LIMIT 1);
SET @tag_motivacion   = (SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = 'motivacion' LIMIT 1);
SET @tag_felicidad    = (SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = 'felicidad' LIMIT 1);

-- =========================
-- Relación Publicaciones - Etiquetas (idempotente)
-- =========================
INSERT IGNORE INTO publicacion_etiqueta (id_publicacion, id_etiqueta) VALUES
(@pub_erik,  @tag_programacion),
(@pub_erik,  @tag_motivacion),
(@pub_maria, @tag_felicidad);

-- =========================
-- Insertar Comentarios (idempotente por combinación única manual)
-- =========================
INSERT IGNORE INTO comentarios (id_publicacion, id_usuario, texto, fecha_hora) VALUES
(@pub_erik,  @uid_maria, '¡Qué bien, Erik! Yo también quiero aprender eso.', NOW()),
(@pub_maria, @uid_erik,  'Totalmente de acuerdo, María 😄', NOW());

-- =========================
-- Insertar Me Gusta (idempotente por UNIQUE)
-- =========================
INSERT IGNORE INTO megusta (id_publicacion, id_usuario, fecha_hora) VALUES
(@pub_erik,  @uid_maria, NOW()),
(@pub_maria, @uid_erik,  NOW());

-- =========================
-- Insertar Seguidores (idempotente por PK compuesta)
-- =========================
INSERT IGNORE INTO seguidores (id_seguidor, id_seguido, fecha_inicio) VALUES
(@uid_erik,  @uid_maria, NOW()),
(@uid_maria, @uid_erik,  NOW());

-- Restaurar comprobación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;
