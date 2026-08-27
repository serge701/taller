-- Agrega foto de perfil a usuarios
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 002_users_foto.sql
USE taller;

ALTER TABLE users
    ADD COLUMN foto VARCHAR(255) NULL AFTER nivel;
