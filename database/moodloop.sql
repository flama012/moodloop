-- Crear base de datos
CREATE DATABASE IF NOT EXISTS moodloop DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE moodloop;

-- Desactivar claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- Tablas
-- =========================

DROP TABLE IF EXISTS comentarios;
CREATE TABLE comentarios (
  id_comentario INT AUTO_INCREMENT PRIMARY KEY,
  id_publicacion INT NOT NULL,
  id_usuario INT NOT NULL,
  texto VARCHAR(255) NOT NULL,
  fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS etiquetas;
CREATE TABLE etiquetas (
  id_etiqueta INT AUTO_INCREMENT PRIMARY KEY,
  nombre_etiqueta VARCHAR(50) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS megusta;
CREATE TABLE megusta (
  id_megusta INT AUTO_INCREMENT PRIMARY KEY,
  id_publicacion INT NOT NULL,
  id_usuario INT NOT NULL,
  fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY (id_publicacion, id_usuario)
);

DROP TABLE IF EXISTS publicaciones;
CREATE TABLE publicaciones (
  id_publicacion INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  mensaje VARCHAR(500) NOT NULL,
  estado_emocional VARCHAR(50),
  fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS publicacion_etiqueta;
CREATE TABLE publicacion_etiqueta (
  id_publicacion INT NOT NULL,
  id_etiqueta INT NOT NULL,
  PRIMARY KEY (id_publicacion, id_etiqueta)
);

DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre_rol VARCHAR(20) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS seguidores;
CREATE TABLE seguidores (
  id_seguidor INT NOT NULL,
  id_seguido INT NOT NULL,
  fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_seguidor, id_seguido)
);

DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
  correo VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  biografia VARCHAR(255),
  estado_emocional VARCHAR(50),
  id_rol INT NOT NULL,
  confirmado TINYINT(1) DEFAULT 0,
  baneado TINYINT(1) DEFAULT 0,
  token VARCHAR(255),
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Claves foráneas
-- =========================

ALTER TABLE comentarios
  ADD FOREIGN KEY (id_publicacion) REFERENCES publicaciones(id_publicacion) ON DELETE CASCADE,
  ADD FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE;

ALTER TABLE megusta
  ADD FOREIGN KEY (id_publicacion) REFERENCES publicaciones(id_publicacion) ON DELETE CASCADE,
  ADD FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE;

ALTER TABLE publicaciones
  ADD FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE;

ALTER TABLE publicacion_etiqueta
  ADD FOREIGN KEY (id_publicacion) REFERENCES publicaciones(id_publicacion) ON DELETE CASCADE,
  ADD FOREIGN KEY (id_etiqueta) REFERENCES etiquetas(id_etiqueta) ON DELETE CASCADE;

ALTER TABLE seguidores
  ADD FOREIGN KEY (id_seguidor) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  ADD FOREIGN KEY (id_seguido) REFERENCES usuarios(id_usuario) ON DELETE CASCADE;

ALTER TABLE usuarios
  ADD FOREIGN KEY (id_rol) REFERENCES roles(id_rol);

-- =========================
-- Datos iniciales (seed)
-- =========================

-- Roles
INSERT INTO roles (nombre_rol) VALUES
('admin'),
('usuario'),
('usuario_sin_confirma')
ON DUPLICATE KEY UPDATE nombre_rol = VALUES(nombre_rol);

-- Variables de rol
SET @rol_admin = (SELECT id_rol FROM roles WHERE nombre_rol = 'admin');
SET @rol_usuario = (SELECT id_rol FROM roles WHERE nombre_rol = 'usuario');

-- Usuarios
INSERT INTO usuarios (nombre_usuario, correo, password_hash, biografia, estado_emocional, id_rol, confirmado, baneado, token)
VALUES
('admin', 'admin@moodloop.com', '$2y$10$abcdefghijklmnopqrstuv', 'Administrador del sistema', 'neutral', @rol_admin, 1, 0, NULL),
('erik', 'erik@moodloop.com', '$2y$10$abcdefghijklmnopqrstuv', 'Desarrollador web en formación', 'motivado', @rol_usuario, 1, 0, NULL),
('maria', 'maria@moodloop.com', '$2y$10$abcdefghijklmnopqrstuv', 'Me encanta compartir frases positivas', 'feliz', @rol_usuario, 1, 0, NULL)
ON DUPLICATE KEY UPDATE
  password_hash = VALUES(password_hash),
  biografia = VALUES(biografia),
  estado_emocional = VALUES(estado_emocional),
  id_rol = VALUES(id_rol),
  confirmado = VALUES(confirmado),
  baneado = VALUES(baneado),
  token = VALUES(token);

-- Variables de usuario
SET @uid_erik = (SELECT id_usuario FROM usuarios WHERE correo = 'erik@moodloop.com');
SET @uid_maria = (SELECT id_usuario FROM usuarios WHERE correo = 'maria@moodloop.com');

-- Publicaciones
INSERT IGNORE INTO publicaciones (id_usuario, mensaje, estado_emocional)
VALUES
(@uid_erik, 'Hoy aprendí a conectar PHP con MySQL 🎉', 'motivado'),
(@uid_maria, 'La vida es mejor con una sonrisa 😊', 'feliz');

-- Variables de publicación
SET @pub_erik = (SELECT id_publicacion FROM publicaciones WHERE id_usuario = @uid_erik AND mensaje LIKE 'Hoy aprendí%');
SET @pub_maria = (SELECT id_publicacion FROM publicaciones WHERE id_usuario = @uid_maria AND mensaje LIKE 'La vida es mejor%');

-- Etiquetas
INSERT INTO etiquetas (nombre_etiqueta) VALUES
('programacion'),
('motivacion'),
('felicidad')
ON DUPLICATE KEY UPDATE nombre_etiqueta = VALUES(nombre_etiqueta);

-- Variables de etiqueta
SET @tag_programacion = (SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = 'programacion');
SET @tag_motivacion = (SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = 'motivacion');
SET @tag_felicidad = (SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = 'felicidad');

-- Relación publicación-etiqueta
INSERT IGNORE INTO publicacion_etiqueta (id_publicacion, id_etiqueta) VALUES
(@pub_erik, @tag_programacion),
(@pub_erik, @tag_motivacion),
(@pub_maria, @tag_felicidad);

-- Comentarios
INSERT IGNORE INTO comentarios (id_publicacion, id_usuario, texto)
VALUES
(@pub_erik, @uid_maria, '¡Qué bien, Erik! Yo también quiero aprender eso.'),
(@pub_maria, @uid_erik, 'Totalmente de acuerdo, María 😄');

-- Me gusta
INSERT IGNORE INTO megusta (id_publicacion, id_usuario)
VALUES
(@pub_erik, @uid_maria),
(@pub_maria, @uid_erik);

-- Seguidores
INSERT IGNORE INTO seguidores (id_seguidor, id_seguido)
VALUES
(@uid_erik, @uid_maria),
(@uid_maria, @uid_erik);

-- Restaurar claves foráneas
SET FOREIGN_KEY_CHECKS = 1;