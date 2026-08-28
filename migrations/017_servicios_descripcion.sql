-- Descripción opcional del servicio, para anotar detalles internos (qué incluye,
-- tiempo estimado, notas, etc.). Por ahora solo se usa/muestra en /servicios.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 017_servicios_descripcion.sql
USE taller;

ALTER TABLE servicios
    ADD COLUMN descripcion TEXT NULL AFTER nombre;
