-- Quita el campo Placas por completo: el sistema ya no lo captura ni lo muestra.
-- Si se necesita registrar una placa, se anota en el campo de Comentarios.
-- Importa forzando utf8mb4: mysql --default-character-set=utf8mb4 -u root taller < 011_quitar_placas.sql
USE taller;

ALTER TABLE ventas   DROP COLUMN vehiculo_placas;
ALTER TABLE trabajos DROP COLUMN vehiculo_placas;
