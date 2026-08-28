-- Agrega el régimen fiscal del negocio a Configuración, para tenerlo junto al
-- RFC/dirección/teléfono ya capturados (útil de cara a una futura facturación).
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 014_configuracion_regimen_fiscal.sql
USE taller;

ALTER TABLE configuracion
    ADD COLUMN regimen_fiscal VARCHAR(100) NULL AFTER rfc;
