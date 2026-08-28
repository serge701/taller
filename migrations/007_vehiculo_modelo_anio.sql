-- Agrega Modelo y Año a los datos de vehículo capturados en ventas y trabajos
-- (antes solo se guardaba marca y color). Los registros existentes quedan con
-- estos campos en NULL — no hay forma de reconstruir ese dato retroactivamente.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 007_vehiculo_modelo_anio.sql
USE taller;

ALTER TABLE ventas
    ADD COLUMN vehiculo_modelo VARCHAR(80) NULL AFTER vehiculo_marca,
    ADD COLUMN vehiculo_anio SMALLINT NULL AFTER vehiculo_modelo;

ALTER TABLE trabajos
    ADD COLUMN vehiculo_modelo VARCHAR(80) NULL AFTER vehiculo_marca,
    ADD COLUMN vehiculo_anio SMALLINT NULL AFTER vehiculo_modelo;
